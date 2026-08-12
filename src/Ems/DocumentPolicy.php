<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Http\Exception\ForbiddenException;

/**
 * The one place the document access rules live (document.md §3.8), shared by
 * the scoped Documents endpoints and the tenant-less file-redemption endpoint
 * so they can never drift apart.
 *
 *  - student-owned  → student scope (canonical family 403)
 *  - application-owned → administrator/registrar only
 *  - verification   → administrator/registrar only (a bursar may read a student
 *    document but never verify one)
 */
final class DocumentPolicy
{
    /** @var array<int, string> */
    public const REVIEW_ROLES = ['administrator', 'registrar'];

    public static function assertOwnerAccess(Scope $scope, Viewer $viewer, string $owner, string $ownerId): void
    {
        if ($owner === 'student') {
            $scope->assertStudentAccess($ownerId);

            return;
        }
        if (!in_array($viewer->role, self::REVIEW_ROLES, true)) {
            throw new ForbiddenException(Messages::DOCUMENT_ADMISSION_OFFICE);
        }
    }

    public static function assertCanVerify(Viewer $viewer): void
    {
        if (!in_array($viewer->role, self::REVIEW_ROLES, true)) {
            throw new ForbiddenException(Messages::DOCUMENT_VERIFY_OFFICE_ONLY);
        }
    }

    private function __construct()
    {
    }
}
