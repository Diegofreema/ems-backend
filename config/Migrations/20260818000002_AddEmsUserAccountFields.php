<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Self-service account fields on ems_users (the Account & security surface).
 *
 * `phone` and `avatar` let an account carry contact detail and a portrait the
 * app shows in place of initials; the avatar is stored inline as a data URL,
 * exactly like the school logo (MEDIUMTEXT), so no file store is needed.
 * `two_factor_enabled` is the opt-in email-code flag — it defaults false so
 * every existing account keeps single-factor sign-in until its owner turns it
 * on, needing no backfill.
 */
class AddEmsUserAccountFields extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_users')
            ->addColumn('phone', 'string', [
                'limit' => 32,
                'null' => true,
                'default' => null,
                'after' => 'email',
            ])
            ->addColumn('avatar', 'text', [
                'limit' => 16777215,
                'null' => true,
                'default' => null,
                'after' => 'phone',
            ])
            ->addColumn('two_factor_enabled', 'boolean', [
                'null' => false,
                'default' => false,
                'after' => 'email_verified_at',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_users')
            ->removeColumn('phone')
            ->removeColumn('avatar')
            ->removeColumn('two_factor_enabled')
            ->update();
    }
}
