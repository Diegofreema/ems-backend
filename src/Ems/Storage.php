<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Http\Exception\HttpException;
use Cake\ORM\Locator\LocatorInterface;

/**
 * Private object store + signed-grant flow (document.md §1.7 / §3.8).
 *
 * Port of the frontend's src/mock/storage.ts, which is documented as becoming
 * this server module. Two invariants:
 *   1. There is no public path to an object. The only way to read bytes is a
 *      grant, and a grant is only issued after a permission decision.
 *   2. A grant is short lived (60s) and bound to (userId, role, schoolId), so a
 *      leaked link is worthless — expired, and it refuses a different reader.
 *
 * Bytes live in ems_document_objects (a stand-in for the bucket); grants live
 * in ems_document_grants. Nothing here ever touches the webroot.
 */
class Storage
{
    public const MAX_UPLOAD_BYTES = 2 * 1024 * 1024;
    public const SIGNED_URL_TTL_MS = 60000;

    /** @var array<int, string> */
    public const ALLOWED_CONTENT_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];

    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Refuse an unacceptable upload BEFORE anything is written (§1.7).
     * A bad file never reaches the bucket and never reaches a reviewer.
     */
    public function assertAcceptable(string $contentType, int $sizeBytes): void
    {
        if (!in_array($contentType, self::ALLOWED_CONTENT_TYPES, true)) {
            $this->fail(422, Messages::FILE_TYPE_REJECTED);
        }
        if ($sizeBytes > self::MAX_UPLOAD_BYTES) {
            $this->fail(413, Messages::FILE_TOO_LARGE);
        }
        if ($sizeBytes <= 0) {
            $this->fail(422, Messages::FILE_EMPTY);
        }
    }

    /**
     * Validate an upload using the bytes supplied, never browser metadata.
     *
     * The EMS browser sends data URLs. Count their decoded payload so the
     * 2 MB contract applies to the file, not its base64 wrapper.
     */
    public function assertAcceptableUpload(string $contentType, string $body): int
    {
        $sizeBytes = $this->uploadBodySize($body);
        $this->assertAcceptable($contentType, $sizeBytes);
        $this->assertBytesMatchType($contentType, $body);

        return $sizeBytes;
    }

    /**
     * The declared content type is caller-supplied, so an allow-listed label is
     * not proof of allow-listed BYTES: an attacker can upload HTML/script (or a
     * polyglot) as "application/pdf". Sniff the leading magic bytes and require
     * they match the type — the same defence the payment-evidence path applies
     * (App\Ems\FinanceSecurity), so a reviewer can never be handed a file whose
     * bytes disagree with its type.
     */
    private function assertBytesMatchType(string $contentType, string $body): void
    {
        $magic = $this->leadingBytes($body);
        $matches = ($contentType === 'application/pdf' && str_starts_with($magic, '%PDF-'))
            || ($contentType === 'image/png' && str_starts_with($magic, "\x89PNG\r\n\x1a\n"))
            || ($contentType === 'image/jpeg' && str_starts_with($magic, "\xff\xd8\xff"));
        if (!$matches) {
            $this->fail(422, Messages::FILE_TYPE_REJECTED);
        }
    }

    /**
     * Decode just enough of an upload to read its magic number. The EMS browser
     * sends a `data:` URL, so unwrap that; a bare body is already raw bytes.
     */
    private function leadingBytes(string $body, int $length = 8): string
    {
        if (!str_starts_with($body, 'data:')) {
            return substr($body, 0, $length);
        }
        $separator = strpos($body, ',');
        if ($separator === false) {
            return substr($body, 0, $length);
        }
        $header = substr($body, 0, $separator);
        $payload = substr($body, $separator + 1);
        if (str_ends_with(strtolower($header), ';base64')) {
            // ceil(length/3)*4 base64 chars cover `length` decoded bytes.
            $chunk = substr($payload, 0, (int)ceil($length / 3) * 4);

            return (string)base64_decode($chunk, false);
        }

        return substr(rawurldecode($payload), 0, $length);
    }

    /** Private bucket layout: school_id/entity/entity_id/file_id. */
    public function storagePathFor(string $schoolId, string $entity, string $entityId, string $fileId): string
    {
        return sprintf('%s/%s/%s/%s', $schoolId, $entity, $entityId, $fileId);
    }

    public function putObject(string $path, string $contentType, int $sizeBytes, string $body): void
    {
        $objects = $this->locator->get('EmsDocumentObjects');
        $existing = $objects->find()->where(['storage_path' => $path])->first();
        $entity = $existing ?? $objects->newEmptyEntity();
        $entity = $objects->patchEntity($entity, [
            'storage_path' => $path,
            'content_type' => $contentType,
            'size_bytes' => $sizeBytes,
            'body' => $body,
        ], ['validate' => false]);
        $objects->saveOrFail($entity);
    }

    /**
     * @return array{contentType:string,sizeBytes:int,body:string}|null
     */
    public function readObject(string $path): ?array
    {
        $row = $this->locator->get('EmsDocumentObjects')->find()
            ->where(['storage_path' => $path])->first();
        if ($row === null) {
            return null;
        }

        return [
            'contentType' => (string)$row->content_type,
            'sizeBytes' => (int)$row->size_bytes,
            'body' => (string)$row->body,
        ];
    }

    public function copyObject(string $fromPath, string $toPath): void
    {
        $source = $this->readObject($fromPath);
        if ($source === null) {
            return;
        }
        $this->putObject($toPath, $source['contentType'], $source['sizeBytes'], $source['body']);
    }

    public function removeObject(string $path): void
    {
        $objects = $this->locator->get('EmsDocumentObjects');
        $row = $objects->find()->where(['storage_path' => $path])->first();
        if ($row !== null) {
            $objects->deleteOrFail($row);
        }
    }

    /**
     * Issue a short-TTL grant bound to a reader. Returns the wire SignedLink
     * fields (token, expiresAt epoch ms, filename) — never the bytes/path.
     *
     * @return array{token:string,expiresAt:int,filename:string}
     */
    public function signGrant(
        string $schoolId,
        string $documentId,
        string $storagePath,
        string $filename,
        Viewer $issuedTo,
    ): array {
        $grants = $this->locator->get('EmsDocumentGrants');
        $issuedAt = $this->nowMs();
        $expiresAt = $issuedAt + self::SIGNED_URL_TTL_MS;
        $token = bin2hex(random_bytes(24));
        $grants->saveOrFail($grants->newEntity([
            'token' => $token,
            'school_id' => $schoolId,
            'document_id' => $documentId,
            'storage_path' => $storagePath,
            'issued_user_id' => $issuedTo->userId,
            'issued_role' => $issuedTo->role,
            'issued_school_id' => $issuedTo->schoolId,
            'filename' => $filename,
            'issued_at' => $issuedAt,
            'expires_at' => $expiresAt,
            'revoked' => false,
        ]));

        return ['token' => $token, 'expiresAt' => $expiresAt, 'filename' => $filename];
    }

    /**
     * Look a grant up. Expired / unknown / revoked all fail closed.
     *
     * @return array{grant:\Cake\Datasource\EntityInterface}|array{refused:string}
     */
    public function redeemGrant(string $token): array
    {
        $grant = $this->locator->get('EmsDocumentGrants')->find()
            ->where(['token' => $token])->first();
        if ($grant === null || (bool)$grant->revoked) {
            return ['refused' => 'unknown'];
        }
        if ($this->nowMs() > (int)$grant->expires_at) {
            return ['refused' => 'expired'];
        }

        return ['grant' => $grant];
    }

    /** Revoke every outstanding grant for an object — a grant must never
     *  outlive the thing it grants (called on document delete). */
    public function revokeGrantsFor(string $storagePath): void
    {
        $this->locator->get('EmsDocumentGrants')->updateAll(
            ['revoked' => true],
            ['storage_path' => $storagePath],
        );
    }

    /** A download filename that says what it is, without leaking a path. */
    public function filenameFor(string $name, string $contentType): string
    {
        $stem = strtolower((string)preg_replace('/[^A-Za-z0-9]+/', '-', $name));
        $stem = trim($stem, '-');

        return ($stem === '' ? 'document' : $stem) . '.' . $this->extensionFor($contentType);
    }

    public function extensionFor(string $contentType): string
    {
        switch ($contentType) {
            case 'application/pdf':
                return 'pdf';
            case 'image/png':
                return 'png';
            case 'image/jpeg':
                return 'jpg';
            default:
                return 'txt';
        }
    }

    private function uploadBodySize(string $body): int
    {
        if (!str_starts_with($body, 'data:')) {
            return strlen($body);
        }

        $separator = strpos($body, ',');
        if ($separator === false) {
            return strlen($body);
        }

        $header = substr($body, 0, $separator);
        $payload = substr($body, $separator + 1);
        $isBase64 = str_ends_with(strtolower($header), ';base64');
        $validBase64 = strlen($payload) % 4 === 0
            && preg_match('/\\A[A-Za-z0-9+\\/]*={0,2}\\z/D', $payload) === 1;
        if (!$isBase64 || !$validBase64) {
            return strlen($body);
        }

        $padding = str_ends_with($payload, '==') ? 2 : (str_ends_with($payload, '=') ? 1 : 0);

        return intdiv(strlen($payload), 4) * 3 - $padding;
    }

    private function nowMs(): int
    {
        return (int)round(microtime(true) * 1000);
    }

    private function fail(int $status, string $message): void
    {
        throw new HttpException($message, $status);
    }
}
