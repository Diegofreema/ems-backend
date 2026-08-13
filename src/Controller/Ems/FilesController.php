<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Api\Jwt;
use App\Ems\DocumentPolicy;
use App\Ems\Messages;
use App\Ems\Scope;
use App\Ems\Viewer;
use App\Ems\ViewerDenied;
use App\Ems\ViewerResolver;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Throwable;

/**
 * Signed-link redemption (document.md §3.8). Tenant-less: the reader is
 * identified from their own Bearer token, and the ordered checks below live
 * here (not in auth) so each failure returns its exact status and wording.
 *
 * The redemption re-checks access AGAIN — a link issued a minute ago must not
 * open if the reader's permission has since changed.
 */
class FilesController extends AppController
{
    protected array $publicActions = ['download'];

    /**
     * GET /files/{token} — ordered: grant (410) → session (401) →
     * reader-identity (403) → document-exists (404) → access re-check (403) →
     * success (audit + bytes).
     */
    public function download(string $token): Response
    {
        $this->rateLimit('file', 20);
        // (1) Grant lookup.
        $redeemed = $this->storage()->redeemGrant($token);
        if (isset($redeemed['refused'])) {
            $this->fail(410, $redeemed['refused'] === 'expired' ? Messages::LINK_EXPIRED : Messages::LINK_INVALID);
        }
        $grant = $redeemed['grant'];

        // (2) Live session.
        $reader = $this->readerFromToken();
        if ($reader === null) {
            $this->fail(401, Messages::LINK_SIGN_IN);
        }

        // (3) Reader must be the person the link was issued to.
        $sameReader = $reader->userId === (string)$grant->issued_user_id
            && $reader->schoolId === (string)$grant->issued_school_id
            && $reader->role === (string)$grant->issued_role;
        if (!$sameReader) {
            $issuedViewer = new Viewer((string)$grant->issued_school_id, $reader->userId, $reader->role, $reader->name);
            $this->audit()->log(
                $issuedViewer,
                'document.link_refused',
                'document',
                (string)$grant->document_id,
                Messages::LINK_REFUSED_SUMMARY,
            );
            $this->fail(403, Messages::LINK_WRONG_READER);
        }

        // (4) Document may have been deleted since the link was issued.
        $document = $this->fetchTable('EmsDocuments')->find()
            ->where(['id' => $grant->document_id, 'school_id' => $grant->issued_school_id])
            ->first();
        if ($document === null) {
            $this->fail(404, Messages::DOCUMENT_NOT_FOUND);
        }

        // (5) Access re-checked at redemption (permission may have changed).
        $scope = new Scope($reader, $this->getTableLocator());
        DocumentPolicy::assertOwnerAccess($scope, $reader, (string)$document->owner, (string)$document->owner_id);

        // (6) Success — audit the download and hand over the bytes.
        $object = $this->storage()->readObject((string)$document->storage_path);
        $bytes = $object ?? $this->placeholderFor($document);
        $this->audit()->log(
            $reader,
            'document.downloaded',
            'document',
            (string)$document->id,
            sprintf('Opened "%s".', (string)$document->name),
        );

        return $this->json([
            'filename' => (string)$grant->filename,
            'contentType' => $bytes['contentType'],
            'body' => $bytes['body'],
            'sizeBytes' => $object !== null ? (int)$object['sizeBytes'] : (int)$document->size_bytes,
            'documentName' => (string)$document->name,
        ]);
    }

    /**
     * Resolve the reader from their Bearer token, or null when there is no valid
     * EMS session. The token carries only `sub` now (token-slimming), so the
     * reader's school/role/name are read from the LIVE ems_users row via
     * ViewerResolver — the same live-state doctrine as candidate #2. This is
     * strictly better for a reader-bound link: the grant is matched against the
     * reader's CURRENT school/role, so a reader whose access changed since the
     * link was issued is re-evaluated at redemption. A disabled or unknown
     * account resolves to null → 401 (sign in), never a stale claim.
     *
     * pathSchoolId is '' — this tenant-less route applies no school gate here;
     * the school match is the reader-binding check in download() itself.
     */
    private function readerFromToken(): ?Viewer
    {
        $header = (string)$this->request->getHeaderLine('Authorization');
        if (stripos($header, 'bearer ') !== 0) {
            return null;
        }
        try {
            $claims = Jwt::decode(trim(substr($header, 7)), time());
        } catch (Throwable $e) {
            return null;
        }
        if (($claims['type'] ?? null) !== 'ems') {
            return null;
        }

        try {
            return ViewerResolver::resolve($this->getTableLocator()->get('EmsUsers'), $claims, '');
        } catch (ViewerDenied $e) {
            return null;
        }
    }

    /**
     * @return array{contentType:string,body:string}
     */
    private function placeholderFor(EntityInterface $document): array
    {
        $text = sprintf(
            "%s\n\nThis record has no stored file behind it.",
            (string)$document->name,
        );

        return [
            'contentType' => 'text/plain',
            'body' => 'data:text/plain;base64,' . base64_encode($text),
        ];
    }
}
