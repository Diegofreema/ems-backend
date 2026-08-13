<?php
declare(strict_types=1);

namespace App\Ems;

use App\Model\Table\EmsRefreshTokensTable;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\Utility\Security;
use Cake\Utility\Text;

/**
 * The rotating, reuse-detecting refresh-token engine (§ security candidate —
 * token-at-rest hardening). A deep module: the whole lifecycle of the durable
 * credential — mint, rotate, detect theft, revoke — sits behind three static
 * methods, so AuthController never touches a hash, a family, or a clock.
 *
 * Invariants the interface guarantees:
 *  - The raw token exists only in the caller's hand and the httpOnly cookie;
 *    the database stores only its SHA-256 hash, so a leaked DB row is inert.
 *  - Every rotate() burns the presented token and mints a fresh one in the same
 *    family. Presenting an already-burned token (a replay) revokes the family —
 *    a stolen-token tripwire that logs out thief and victim together.
 *  - Every method takes `now` (unix seconds) so behaviour is a pure function of
 *    the clock and unit-testable without sleeping.
 */
final class RefreshTokens
{
    /** Fallback lifetime if Jwt.refreshTtl is unset: 14 days. */
    private const DEFAULT_TTL = 60 * 60 * 24 * 14;

    /**
     * Mint a brand-new refresh token, starting a new family (a new sign-in), or
     * continuing an existing one when rotating.
     *
     * @return array{token:string, expiresAt:int} The raw token (to put in the
     *   cookie) and its absolute expiry as a unix timestamp.
     */
    public static function issue(
        EmsRefreshTokensTable $tokens,
        string $userId,
        int $now,
        ?string $familyId = null,
    ): array {
        $raw = bin2hex(Security::randomBytes(32));
        $expiresAt = $now + (int)Configure::read('Jwt.refreshTtl', self::DEFAULT_TTL);

        $tokens->saveOrFail($tokens->newEntity([
            'user_id' => $userId,
            'token_hash' => self::hash($raw),
            'family_id' => $familyId ?? Text::uuid(),
            'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            'created' => date('Y-m-d H:i:s', $now),
        ]));

        return ['token' => $raw, 'expiresAt' => $expiresAt];
    }

    /**
     * Validate the presented token and rotate it: burn it, mint its successor in
     * the same family. Throws RefreshDenied on any dead token, and on a replayed
     * (already-used or revoked) token ALSO revokes the whole family first.
     *
     * @return array{token:string, expiresAt:int, userId:string} The successor
     *   token, its expiry, and the account it belongs to.
     */
    public static function rotate(EmsRefreshTokensTable $tokens, string $rawToken, int $now): array
    {
        $row = self::findByRaw($tokens, $rawToken);
        if ($row === null) {
            throw RefreshDenied::dead();
        }

        // A token presented after it was already rotated away (or explicitly
        // revoked) is the classic sign of theft: the legitimate client rotated
        // to a successor, so whoever is replaying the old one is not them. Burn
        // the entire lineage — every token in the family.
        if ($row->used_at !== null || $row->revoked_at !== null) {
            self::revokeFamily($tokens, (string)$row->family_id, $now);
            throw RefreshDenied::dead();
        }

        if (self::expiryOf($row) <= $now) {
            throw RefreshDenied::dead();
        }

        $row->used_at = date('Y-m-d H:i:s', $now);
        $tokens->saveOrFail($row);

        $issued = self::issue($tokens, (string)$row->user_id, $now, (string)$row->family_id);

        return $issued + ['userId' => (string)$row->user_id];
    }

    /**
     * Revoke the family the presented token belongs to (logout, or the disabled-
     * account path). Idempotent and silent on an unknown token — logging out is
     * never an error.
     */
    public static function revoke(EmsRefreshTokensTable $tokens, string $rawToken, int $now): void
    {
        $row = self::findByRaw($tokens, $rawToken);
        if ($row === null) {
            return;
        }
        self::revokeFamily($tokens, (string)$row->family_id, $now);
    }

    private static function findByRaw(EmsRefreshTokensTable $tokens, string $rawToken): ?EntityInterface
    {
        $rawToken = trim($rawToken);
        if ($rawToken === '') {
            return null;
        }

        return $tokens->find()->where(['token_hash' => self::hash($rawToken)])->first();
    }

    private static function revokeFamily(EmsRefreshTokensTable $tokens, string $familyId, int $now): void
    {
        $tokens->updateAll(
            ['revoked_at' => date('Y-m-d H:i:s', $now)],
            ['family_id' => $familyId, 'revoked_at IS' => null],
        );
    }

    private static function hash(string $raw): string
    {
        return hash('sha256', $raw);
    }

    /** The row's expiry as a unix timestamp, whether it read back as FrozenTime or a string. */
    private static function expiryOf(EntityInterface $row): int
    {
        $exp = $row->expires_at;

        return $exp instanceof FrozenTime ? $exp->getTimestamp() : (int)strtotime((string)$exp);
    }
}
