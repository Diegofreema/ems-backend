<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Self-served registrations must prove their e-mail before first sign-in
 * (§3.18). `email_verified_at` records the proof; `ems_email_verifications`
 * holds the hashed 30-minute single-use link tokens that establish it.
 *
 * Invited accounts never need a row here: redeeming an invite code (or a
 * password-reset code) already proves possession of the mailbox, so those
 * paths stamp `email_verified_at` directly.
 */
final class AddEmailVerification extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_users')
            ->addColumn('email_verified_at', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'password_hash',
            ])
            ->update();

        // Accounts that predate verification are grandfathered as verified —
        // the gate applies to registrations from this point on.
        $this->execute('UPDATE ems_users SET email_verified_at = NOW()');

        // Tokens are stored hashed (SHA-256 hex), like invite codes, so a DB
        // read never yields a usable link.
        $this->table('ems_email_verifications', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('used_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['user_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_email_verifications')->drop()->save();
        $this->table('ems_users')->removeColumn('email_verified_at')->update();
    }
}
