<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Remembered devices that may skip the 2FA e-mail code for 30 days.
 *
 * When a user ticks "remember this device" after a correct code, we set a
 * long-lived httpOnly cookie whose opaque token's SHA-256 hash is stored here.
 * On a later sign-in the cookie is presented; a live, matching row lets the
 * password alone start the session, skipping the code. Deliberately SEPARATE
 * from ems_refresh_tokens so device trust SURVIVES logout — logging out ends the
 * session but the browser stays trusted. Revoked by expiry, by the user
 * forgetting devices, or implicitly when 2FA is disabled.
 */
class CreateEmsTrustedDevices extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_trusted_devices', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('user_agent', 'string', ['limit' => 512, 'null' => true, 'default' => null])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('last_used_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['token_hash'], ['unique' => true])
            ->addIndex(['user_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_trusted_devices')->drop()->save();
    }
}
