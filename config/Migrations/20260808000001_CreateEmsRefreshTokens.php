<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Refresh-token store for the EMS surface (§ security candidate — token-at-rest
 * hardening). The durable credential now lives here, server-side and HASHED,
 * delivered to the browser only inside an httpOnly cookie — so JS (and any XSS)
 * can never read a reusable credential at rest.
 *
 * A row holds the SHA-256 hash of an opaque random token (never the token), an
 * expiry, and a `family_id` grouping every token rotated from one sign-in. Every
 * /auth/refresh rotates: the presented row is stamped `used_at` and a fresh token
 * is issued in the same family. If a token that was already rotated away is
 * presented again (a replay — the hallmark of a stolen token), the whole family
 * is revoked, logging out thief and victim alike.
 */
class CreateEmsRefreshTokens extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_refresh_tokens', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('family_id', 'uuid', ['null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('used_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('revoked_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['token_hash'], ['unique' => true])
            ->addIndex(['user_id'])
            ->addIndex(['family_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_refresh_tokens')->drop()->save();
    }
}
