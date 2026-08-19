<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Family onboarding at scale (§3.19): parent portal accounts are now minted
 * from the guardian records the school already keeps, so `ems_users` learns
 * where an account came from and how a no-email family signs in.
 *
 * - `email` becomes nullable: a code-sheet family account has no mailbox.
 *   The unique index stays — MySQL unique indexes admit multiple NULLs.
 * - `phone_key` (digits-normalized via Dedup::phoneKey, nullable, unique) is
 *   the sign-in identifier for accounts without an e-mail address. The
 *   existing `phone` column stays the free-format display field the account
 *   owner edits on the profile surface.
 * - `link_guardian_id` records the guardian row a family account was created
 *   from. Authorization still flows through `link_student_ids`; this column
 *   is provenance and duplicate-prevention for bulk invites.
 */
final class AlterUsersForFamilyOnboarding extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_users')
            ->changeColumn('email', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('phone_key', 'string', [
                'limit' => 32,
                'null' => true,
                'default' => null,
                'after' => 'phone',
            ])
            ->addColumn('link_guardian_id', 'uuid', [
                'null' => true,
                'default' => null,
                'after' => 'link_student_ids',
            ])
            ->addIndex(['phone_key'], ['unique' => true])
            ->addIndex(['link_guardian_id'])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_users')
            ->removeIndex(['link_guardian_id'])
            ->removeIndex(['phone_key'])
            ->removeColumn('link_guardian_id')
            ->removeColumn('phone_key')
            ->changeColumn('email', 'string', ['limit' => 190, 'null' => false])
            ->update();
    }
}
