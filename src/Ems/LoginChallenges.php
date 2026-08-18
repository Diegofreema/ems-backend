<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\I18n\FrozenTime;
use Cake\ORM\Table;

/**
 * Second-factor sign-in codes (email 2FA) — and the same one-time code used to
 * confirm turning 2FA on.
 *
 * A 6-digit code is mailed; only its SHA-256 hash is stored, it expires in 10
 * minutes, is single-use, and each wrong guess burns one of a small attempt
 * budget so an emailed code can never be brute-forced before it dies. Issuing a
 * new code retires the account's earlier unspent ones so only the newest works.
 */
final class LoginChallenges
{
    public const TTL_MINUTES = 10;
    public const MAX_ATTEMPTS = 5;
    private const CODE_DIGITS = 6;

    /**
     * Mint a fresh code for the user, retiring earlier unspent challenges.
     *
     * @return array{challengeId:string,code:string} The opaque challenge id (to
     *   hand the client during sign-in) and the raw code (to e-mail).
     */
    public static function issue(Table $challenges, string $userId): array
    {
        $challenges->updateAll(
            ['used_at' => FrozenTime::now()],
            ['user_id' => $userId, 'used_at IS' => null],
        );

        $code = self::newCode();
        $row = $challenges->saveOrFail($challenges->newEntity([
            'user_id' => $userId,
            'code_hash' => self::hash($code),
            'expires_at' => FrozenTime::now()->addMinutes(self::TTL_MINUTES),
            'attempts' => 0,
        ]));

        return ['challengeId' => (string)$row->id, 'code' => $code];
    }

    /**
     * Verify a code against a specific challenge id (the sign-in path, where the
     * caller is not yet authenticated). Returns the account id on success, or
     * null on any dead/exhausted/wrong challenge. Burns an attempt on a wrong
     * guess; marks the challenge used on success.
     */
    public static function verify(Table $challenges, string $challengeId, string $code): ?string
    {
        if (trim($challengeId) === '') {
            return null;
        }
        $row = $challenges->find()
            ->where(['id' => $challengeId, 'used_at IS' => null, 'expires_at >=' => FrozenTime::now()])
            ->first();

        return self::consume($challenges, $row, $code);
    }

    /**
     * Verify a code against the user's latest live challenge (the enable-2FA
     * confirmation path, where the caller IS authenticated). Returns true on
     * success. Same attempt-budget and single-use rules as verify().
     */
    public static function verifyForUser(Table $challenges, string $userId, string $code): bool
    {
        $row = $challenges->find()
            ->where(['user_id' => $userId, 'used_at IS' => null, 'expires_at >=' => FrozenTime::now()])
            ->orderByDesc('created')
            ->first();

        return self::consume($challenges, $row, $code) !== null;
    }

    /**
     * Shared verify core: enforce the attempt budget, constant-time compare,
     * burn an attempt on a miss, mark used on a hit.
     */
    private static function consume(Table $challenges, mixed $row, string $code): ?string
    {
        if ($row === null || (int)$row->attempts >= self::MAX_ATTEMPTS) {
            return null;
        }
        if (!hash_equals((string)$row->code_hash, self::hash($code))) {
            $row->attempts = (int)$row->attempts + 1;
            $challenges->saveOrFail($row);

            return null;
        }
        $row->used_at = FrozenTime::now();
        $challenges->saveOrFail($row);

        return (string)$row->user_id;
    }

    /** Hash a code for storage and lookup. */
    public static function hash(string $code): string
    {
        return hash('sha256', trim($code));
    }

    /** A zero-padded 6-digit numeric code. */
    private static function newCode(): string
    {
        return str_pad((string)random_int(0, 999999), self::CODE_DIGITS, '0', STR_PAD_LEFT);
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
