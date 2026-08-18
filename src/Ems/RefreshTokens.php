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
        ?string $userAgent = null,
        ?string $ip = null,
    ): array {
        $raw = bin2hex(Security::randomBytes(32));
        $expiresAt = $now + (int)Configure::read('Jwt.refreshTtl', self::DEFAULT_TTL);
        $familyId = $familyId ?? Text::uuid();

        $tokens->saveOrFail($tokens->newEntity([
            'user_id' => $userId,
            'token_hash' => self::hash($raw),
            'family_id' => $familyId,
            // Device metadata for the "active sessions" list (§3.18). Set once
            // per family at sign-in and carried forward on every rotation, so a
            // session keeps its identity as its token rotates.
            'user_agent' => $userAgent !== null ? mb_substr($userAgent, 0, 512) : null,
            'ip' => $ip,
            'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            'last_used_at' => date('Y-m-d H:i:s', $now),
            'created' => date('Y-m-d H:i:s', $now),
        ]));

        return ['token' => $raw, 'expiresAt' => $expiresAt, 'familyId' => $familyId];
    }

    /**
     * Validate the presented token and rotate it: burn it, mint its successor in
     * the same family. Throws RefreshDenied on any dead token, and on a replayed
     * (already-used or revoked) token ALSO revokes the whole family first.
     *
     * @return array{token:string, expiresAt:int, familyId:string, userId:string}
     *   The successor token, its expiry, its family (stable across rotation),
     *   and the account it belongs to.
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

        $issued = self::issue(
            $tokens,
            (string)$row->user_id,
            $now,
            (string)$row->family_id,
            $row->user_agent !== null ? (string)$row->user_agent : null,
            $row->ip !== null ? (string)$row->ip : null,
        );

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

    /**
     * The account's active sign-in sessions, one per token family, for the
     * "active sessions" list (§3.18). A family is active while its live tip
     * (the un-rotated, un-revoked, un-expired token) exists; device metadata and
     * activity come from the family's rows. The family whose id is
     * `$currentFamilyId` (carried in the caller's access token) is flagged
     * `current`. Sorted current-first, then most-recently-active.
     *
     * @return array<int, array{id:string,userAgent:?string,ip:?string,firstSeenAt:?string,lastActiveAt:?string,current:bool}>
     */
    public static function sessions(
        EmsRefreshTokensTable $tokens,
        string $userId,
        int $now,
        string $currentFamilyId,
    ): array {
        $rows = $tokens->find()
            ->where(['user_id' => $userId, 'revoked_at IS' => null, 'expires_at >' => date('Y-m-d H:i:s', $now)])
            ->orderBy(['created' => 'ASC'])
            ->all();

        $byFamily = [];
        foreach ($rows as $row) {
            $family = (string)$row->family_id;
            if (!isset($byFamily[$family])) {
                $byFamily[$family] = [
                    'firstSeen' => $row->created,
                    'lastActive' => $row->last_used_at ?? $row->created,
                    'userAgent' => $row->user_agent,
                    'ip' => $row->ip,
                    'live' => false,
                ];
            }
            $activity = $row->last_used_at ?? $row->created;
            if ($activity !== null && self::tsOf($activity) > self::tsOf($byFamily[$family]['lastActive'])) {
                $byFamily[$family]['lastActive'] = $activity;
            }
            // The live tip (never rotated away) carries the authoritative device
            // metadata and proves the family is still an active session.
            if ($row->used_at === null) {
                $byFamily[$family]['live'] = true;
                if ($row->user_agent !== null) {
                    $byFamily[$family]['userAgent'] = $row->user_agent;
                }
                if ($row->ip !== null) {
                    $byFamily[$family]['ip'] = $row->ip;
                }
            }
        }

        $out = [];
        foreach ($byFamily as $family => $info) {
            if (!$info['live']) {
                continue;
            }
            $out[] = [
                'id' => $family,
                'userAgent' => $info['userAgent'] !== null ? (string)$info['userAgent'] : null,
                'ip' => $info['ip'] !== null ? (string)$info['ip'] : null,
                'firstSeenAt' => self::isoOf($info['firstSeen']),
                'lastActiveAt' => self::isoOf($info['lastActive']),
                'current' => $family === $currentFamilyId,
            ];
        }

        usort($out, static function (array $a, array $b): int {
            if ($a['current'] !== $b['current']) {
                return $a['current'] ? -1 : 1;
            }

            return strcmp((string)$b['lastActiveAt'], (string)$a['lastActiveAt']);
        });

        return $out;
    }

    /**
     * Revoke one family, but only if it belongs to `$userId` — so a user can
     * never sign out another account's device by guessing a family id. Returns
     * false when no such family exists for the user (the action answers 404).
     */
    public static function revokeFamilyFor(
        EmsRefreshTokensTable $tokens,
        string $userId,
        string $familyId,
        int $now,
    ): bool {
        if (!$tokens->exists(['user_id' => $userId, 'family_id' => $familyId])) {
            return false;
        }
        self::revokeFamilyScoped($tokens, $userId, $familyId, $now);

        return true;
    }

    /**
     * Revoke every one of the account's families EXCEPT `$keepFamilyId` — the
     * "sign out all other devices" action, and the automatic sweep after a
     * password change or reset.
     */
    public static function revokeOthersFor(
        EmsRefreshTokensTable $tokens,
        string $userId,
        string $keepFamilyId,
        int $now,
    ): void {
        $tokens->updateAll(
            ['revoked_at' => date('Y-m-d H:i:s', $now)],
            ['user_id' => $userId, 'family_id !=' => $keepFamilyId, 'revoked_at IS' => null],
        );
    }

    private static function revokeFamilyScoped(
        EmsRefreshTokensTable $tokens,
        string $userId,
        string $familyId,
        int $now,
    ): void {
        $tokens->updateAll(
            ['revoked_at' => date('Y-m-d H:i:s', $now)],
            ['user_id' => $userId, 'family_id' => $familyId, 'revoked_at IS' => null],
        );
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

    /** A stored datetime (FrozenTime or string) as a unix timestamp; 0 if null. */
    private static function tsOf(mixed $value): int
    {
        if ($value === null) {
            return 0;
        }

        return $value instanceof FrozenTime ? $value->getTimestamp() : (int)strtotime((string)$value);
    }

    /** A stored datetime (FrozenTime or string) as an ISO-8601 string, or null. */
    private static function isoOf(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof FrozenTime) {
            return $value->format(DATE_ATOM);
        }
        $ts = strtotime((string)$value);

        return $ts === false ? null : date(DATE_ATOM, $ts);
    }
}
