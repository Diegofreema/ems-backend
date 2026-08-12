<?php
declare(strict_types=1);

namespace App\Ems;

use App\Model\Table\EmsUsersTable;

/**
 * Builds the request's {@see Viewer} from LIVE server state instead of frozen
 * JWT claims (security review candidate #2). The verified token tells us only
 * WHO is calling (`sub`); everything that carries authority — the account's
 * `status`, its `role`, and the school it belongs to — is read from the
 * `ems_users` row on every request.
 *
 * The consequence is revocation the token alone could never give: a disabled
 * account is refused, and a demoted user is evaluated at their new role, on the
 * very next request rather than whenever the 1h token happens to expire. The
 * claim `role`/`schoolId`/`memberships` are no longer trusted for authorization.
 *
 * Deliberately fail-closed: a `sub` with no live row, a non-active account, or
 * a tenant the user does not belong to each throw {@see ViewerDenied}. There is
 * no path here that trusts the token over the database.
 */
final class ViewerResolver
{
    /** The only account status permitted to authenticate. */
    private const STATUS_ACTIVE = 'active';

    /**
     * @param \App\Model\Table\EmsUsersTable $users The live users table (injected).
     * @param array $claims The verified JWT claims (only `sub` is trusted here).
     * @param string $pathSchoolId The tenant from the URL, or '' for the one
     *   tenant-less authenticated route (`/schools/by-slug/{slug}`).
     * @return \App\Ems\Viewer The principal, built from the live row.
     * @throws \App\Ems\ViewerDenied When live state refuses the caller.
     */
    public static function resolve(EmsUsersTable $users, array $claims, string $pathSchoolId): Viewer
    {
        $userId = (string)($claims['sub'] ?? '');
        if ($userId === '') {
            throw ViewerDenied::unknownPrincipal();
        }

        $row = $users->find()
            ->select(['id', 'school_id', 'role', 'status', 'name'])
            ->where(['id' => $userId])
            ->first();

        if ($row === null) {
            throw ViewerDenied::unknownPrincipal();
        }
        if ((string)$row->status !== self::STATUS_ACTIVE) {
            throw ViewerDenied::accountDisabled();
        }

        $liveSchoolId = (string)$row->school_id;

        // A scoped route may only address the school the live user belongs to.
        // The tenant-less route (pathSchoolId === '') adopts that same school.
        if ($pathSchoolId !== '' && $pathSchoolId !== $liveSchoolId) {
            throw ViewerDenied::schoolForbidden();
        }

        return new Viewer(
            $liveSchoolId,
            $userId,
            (string)$row->role,
            (string)$row->name,
        );
    }
}
