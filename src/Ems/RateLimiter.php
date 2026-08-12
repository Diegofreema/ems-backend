<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Cache\Cache;

/**
 * A fixed-window request throttle (security review candidate #4). One seam for
 * every brute-forceable public endpoint — sign-in, password reset, invite
 * redemption, signed-link download, public application — so the counting rule,
 * the store, and the refusal live in exactly one auditable place instead of a
 * per-controller copy.
 *
 * Deliberately discriminator-agnostic: it counts a caller-supplied key (an IP,
 * or IP+identifier) within a bucket and knows nothing about HTTP. That keeps it
 * trivially reusable and unit-testable — pass `$now` to drive the clock — and
 * leaves the "what counts as the client" decision (proxy trust, §5) with the
 * caller.
 *
 * Backed by the default Cake\Cache engine (FileEngine on a plain XAMPP box, no
 * Redis required). The file-cache read-modify-write is not atomic, so under
 * heavy concurrency a few extra requests can slip through a window — acceptable
 * for a throttle whose job is to bound, not to serialize.
 */
final class RateLimiter
{
    /** Cache-key prefix; kept containing "throttle" so ops/E2E cache-clears match. */
    private const KEY_PREFIX = 'ems_throttle_';

    /**
     * Record one request against ({@see $bucket}, {@see $discriminator}) and
     * refuse once more than {@see $limit} land inside {@see $window} seconds.
     *
     * @param string $bucket A stable name for the protected endpoint (e.g. 'signin').
     * @param string $discriminator What to count per — typically the client IP.
     * @param int $limit Requests allowed within the window before refusal.
     * @param int $window Window length in seconds.
     * @param int|null $now Unix time, injectable for tests (defaults to time()).
     * @return void
     * @throws \App\Ems\RateLimited 429 (with retry-after seconds) once exceeded.
     */
    public static function hit(string $bucket, string $discriminator, int $limit, int $window, ?int $now = null): void
    {
        $now = $now ?? time();
        $key = self::KEY_PREFIX . $bucket . '_' . substr(hash('sha256', $discriminator), 0, 20);

        $entry = Cache::read($key);
        if (!is_array($entry) || !isset($entry['start'], $entry['count']) || ($now - (int)$entry['start']) > $window) {
            $entry = ['start' => $now, 'count' => 0];
        }
        $entry['count'] = (int)$entry['count'] + 1;
        Cache::write($key, $entry);

        if ($entry['count'] > $limit) {
            // Seconds left in the current fixed window — what the client should
            // wait before the bucket resets. RateLimiter says nothing else about
            // HTTP; AppController renders this as the Retry-After header.
            throw new RateLimited($window - ($now - (int)$entry['start']));
        }
    }
}
