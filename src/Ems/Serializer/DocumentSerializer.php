<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for documents & signed links (document.md §3.8).
 *
 * `storagePath` is deliberately sent as an empty string — the field exists in
 * the type, but the real private-bucket path is NEVER exposed to a browser
 * (§3.8). No `body`/bytes ever appear here; the file is served only through the
 * redeemed-grant endpoint.
 */
final class DocumentSerializer
{
    public static function one(EntityInterface $d): array
    {
        $out = [
            'id' => (string)$d->id,
            'schoolId' => (string)$d->school_id,
            'owner' => (string)$d->owner,
            'ownerId' => (string)$d->owner_id,
            'name' => (string)$d->name,
            'type' => (string)$d->type,
            'contentType' => (string)$d->content_type,
            'sizeBytes' => (int)$d->size_bytes,
            'storagePath' => '', // never exposed (§3.8)
            'uploadedBy' => (string)$d->uploaded_by,
            'uploadedOn' => Wire::date($d->uploaded_on),
            'verification' => (string)$d->verification,
        ];
        if ($d->verified_by !== null) {
            $out['verifiedBy'] = (string)$d->verified_by;
        }
        if ($d->verified_on !== null) {
            $out['verifiedOn'] = Wire::date($d->verified_on);
        }
        if ($d->verification_note !== null) {
            $out['verificationNote'] = (string)$d->verification_note;
        }

        return $out;
    }

    /**
     * @param array{token:string,expiresAt:int,filename:string} $grant
     */
    public static function signedLink(array $grant, string $documentId): array
    {
        return [
            'token' => $grant['token'],
            'path' => '/files/' . $grant['token'],
            'expiresAt' => $grant['expiresAt'],
            'documentId' => $documentId,
            'filename' => $grant['filename'],
        ];
    }
}
