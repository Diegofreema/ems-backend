<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Second-factor sign-in challenges (email one-time codes).
 *
 * When a 2FA account passes its password on an untrusted device, sign-in does
 * NOT start a session; it mints a challenge here, e-mails a 6-digit code, and
 * returns the row's opaque id. The client posts (id, code) back to finish. Only
 * the code's SHA-256 hash is stored — never the code — and each wrong guess
 * burns one of a small attempt budget so an emailed code cannot be brute-forced
 * before it expires (~10 minutes). Single-use via `used_at`.
 */
class CreateEmsLoginChallenges extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_login_challenges', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('code_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('attempts', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('used_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['user_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_login_challenges')->drop()->save();
    }
}
