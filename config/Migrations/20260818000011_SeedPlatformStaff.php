<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Seed the platform authority: a sentinel "NetPro Platform" school and one
 * platform-staff account, so the demo-requests inbox (CRM-lite) has an operator
 * from day one.
 *
 * Why a sentinel school: every `ems_users` row needs a `school_id` (NOT NULL),
 * but platform staff belong to no tenant. They sit in one internal school and
 * reach only the tenant-less `/platform/*` routes, gated by the `platform_staff`
 * role in App\Ems\Policy — they never address a `/schools/{schoolId}` route
 * (ViewerResolver would refuse a mismatched school anyway).
 *
 * No password is seeded — the account starts with `password_hash = NULL`, and
 * the operator sets one through the ordinary "forgot password" reset flow (which
 * also stamps e-mail verification). Nothing secret is committed. The account's
 * e-mail comes from EMS_PLATFORM_ADMIN_EMAIL (default info@netpro.africa,
 * matching Ems.demoNotifyEmail). Both inserts are guarded so a re-run — or a real
 * account that already claimed the address — is never duplicated or clobbered.
 */
class SeedPlatformStaff extends BaseMigration
{
    /** Fixed ids so the seed is idempotent and referenceable. */
    private const SCHOOL_ID = '0f9d0a11-0000-4000-8000-000000000001';
    private const USER_ID = '0f9d0a11-0000-4000-8000-000000000002';

    public function up(): void
    {
        $email = strtolower(trim((string)env('EMS_PLATFORM_ADMIN_EMAIL', 'info@netpro.africa')));
        if ($email === '') {
            $email = 'info@netpro.africa';
        }
        $emailSql = str_replace("'", "''", $email);
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        $schoolId = self::SCHOOL_ID;
        $existingSchool = $this->fetchRow(sprintf(
            "SELECT id FROM ems_schools WHERE id = '%s' OR slug = 'netpro-platform' LIMIT 1",
            $schoolId,
        ));
        if ($existingSchool === false) {
            $this->execute(sprintf(
                "INSERT INTO ems_schools (id, slug, name, short_name, motto, address, created, modified)
                 VALUES ('%s', 'netpro-platform', 'NetPro Platform', 'NetPro', '', '', '%s', '%s')",
                $schoolId,
                $now,
                $now,
            ));
        } else {
            $schoolId = (string)$existingSchool['id'];
        }

        // Only seed the account if the (globally-unique) address is free. If a
        // real user already holds it, leave them untouched — the deployer can
        // promote an account by hand rather than have a migration overwrite one.
        $existingUser = $this->fetchRow(sprintf(
            "SELECT id FROM ems_users WHERE LOWER(email) = '%s' LIMIT 1",
            $emailSql,
        ));
        if ($existingUser === false) {
            $this->execute(sprintf(
                "INSERT INTO ems_users
                    (id, school_id, name, email, role, status, added_on, password_hash,
                     email_verified_at, two_factor_enabled, created, modified)
                 VALUES
                    ('%s', '%s', 'NetPro Platform', '%s', 'platform_staff', 'active', '%s', NULL,
                     '%s', 0, '%s', '%s')",
                self::USER_ID,
                $schoolId,
                $emailSql,
                $today,
                $now,
                $now,
                $now,
            ));
        }
    }

    public function down(): void
    {
        // Remove only the rows this migration may have inserted (fixed ids); a
        // pre-existing account that happened to hold the address is left alone.
        $this->execute(sprintf("DELETE FROM ems_users WHERE id = '%s'", self::USER_ID));
        $this->execute(sprintf(
            "DELETE FROM ems_schools WHERE id = '%s' AND slug = 'netpro-platform'",
            self::SCHOOL_ID,
        ));
    }
}
