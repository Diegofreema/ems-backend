<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\ORM\Table;
use Cake\Utility\Security;

/**
 * "Remember this device for 30 days" for email 2FA.
 *
 * After a correct sign-in code the user may trust the browser; we mint an opaque
 * token, store only its SHA-256 hash here, and set it in a long-lived httpOnly
 * cookie. A later sign-in that presents a live, matching token for the same
 * account skips the code. Deliberately separate from refresh tokens so trust
 * survives logout — every method takes `now` (unix seconds) to stay a pure
 * function of the clock, like RefreshTokens.
 */
final class TrustedDevices
{
    /** How long a remembered device may skip the code. */
    public const TTL_DAYS = 30;

    /**
     * Mint and store a trust for the account's current device.
     *
     * @return array{token:string, expiresAt:int} The raw token (for the cookie)
     *   and its absolute expiry as a unix timestamp.
     */
    public static function issue(Table $devices, string $userId, ?string $userAgent, int $now): array
    {
        $raw = bin2hex(Security::randomBytes(32));
        $expiresAt = $now + self::TTL_DAYS * 86400;

        $devices->saveOrFail($devices->newEntity([
            'user_id' => $userId,
            'token_hash' => self::hash($raw),
            'user_agent' => $userAgent !== null ? mb_substr($userAgent, 0, 512) : null,
            'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            'last_used_at' => date('Y-m-d H:i:s', $now),
        ]));

        return ['token' => $raw, 'expiresAt' => $expiresAt];
    }

    /**
     * Is the presented device cookie a live trust for THIS account? Scoped to
     * the user so one person's trusted device can never wave another past the
     * code. Refreshes last_used_at so an active device's trust stays warm.
     */
    public static function isTrusted(Table $devices, string $rawToken, string $userId, int $now): bool
    {
        $rawToken = trim($rawToken);
        if ($rawToken === '') {
            return false;
        }
        $row = $devices->find()
            ->where([
                'token_hash' => self::hash($rawToken),
                'user_id' => $userId,
                'expires_at >' => date('Y-m-d H:i:s', $now),
            ])
            ->first();
        if ($row === null) {
            return false;
        }
        $row->last_used_at = date('Y-m-d H:i:s', $now);
        $devices->saveOrFail($row);

        return true;
    }

    /** Forget a single device by its cookie token (idempotent). */
    public static function forget(Table $devices, string $rawToken): void
    {
        $rawToken = trim($rawToken);
        if ($rawToken === '') {
            return;
        }
        $devices->deleteAll(['token_hash' => self::hash($rawToken)]);
    }

    /**
     * Forget every remembered device for the account — used when the user asks
     * to, and automatically when 2FA is switched off (trust is meaningless with
     * no second factor to skip).
     */
    public static function forgetAll(Table $devices, string $userId): void
    {
        $devices->deleteAll(['user_id' => $userId]);
    }

    /** Hash a raw token for storage and lookup. */
    public static function hash(string $raw): string
    {
        return hash('sha256', $raw);
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
