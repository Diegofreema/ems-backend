<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * Regression for the rate-limit client-IP key (security review finding #2).
 *
 * Behind a reverse proxy the throttle must key on the REAL client, not the
 * proxy (which would collapse everyone into one bucket — a global DoS) and not
 * a client-supplied X-Forwarded-For value (which an attacker rotates to mint a
 * fresh bucket per request, bypassing every throttle). With `Ems.proxyHops`
 * set, AppController::clientIp() takes the Nth XFF entry from the right and
 * ignores everything to its left.
 *
 * The sign-in bucket is 10 / window; the 11th from one client is refused.
 */
final class ClientIpThrottleTest extends EmsIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        // One trusted load balancer in front: the real client is the last XFF
        // entry (the one the LB observed and appended).
        Configure::write('Ems.proxyHops', 1);
    }

    protected function tearDown(): void
    {
        Cache::clear();
        Configure::delete('Ems.proxyHops');
        parent::tearDown();
    }

    // A UNIQUE e-mail per request so the per-account throttle never interferes:
    // this test isolates the per-IP (client-IP) bucket.

    private function signInFrom(string $xff, int $seq): void
    {
        $this->configRequest(['headers' => ['X-Forwarded-For' => $xff]]);
        $this->post('/api/ems/auth/sign-in', ['email' => "nobody$seq@test.school", 'password' => 'x']);
    }

    public function testRotatingASpoofedLeftmostXffCannotEvadeTheThrottle(): void
    {
        // Every request forges a DIFFERENT leftmost address but the trusted tail
        // (the LB-appended real client) is constant, so all key to 9.9.9.9.
        for ($i = 0; $i < 10; $i++) {
            $this->signInFrom("1.2.3.$i, 9.9.9.9", $i);
            $this->assertResponseCode(401); // within budget: normal bad-credentials
        }

        // The 11th still counts against 9.9.9.9 despite the fresh spoof → 429.
        $this->signInFrom('5.5.5.5, 9.9.9.9', 10);
        $this->assertResponseCode(429);
        $this->assertSame(Messages::RATE_LIMITED, $this->responseJson()['message']);
    }

    public function testADifferentRealClientKeepsItsOwnBudget(): void
    {
        // Exhaust the budget for real client 9.9.9.9.
        for ($i = 0; $i < 11; $i++) {
            $this->signInFrom("1.2.3.$i, 9.9.9.9", $i);
        }
        $this->assertResponseCode(429);

        // A genuinely different client (different trusted tail) is unaffected —
        // per-client isolation still works.
        $this->signInFrom('7.7.7.7, 8.8.8.8', 99);
        $this->assertResponseCode(401);
    }
}
