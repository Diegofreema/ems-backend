<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\RateLimited;
use App\Ems\RateLimiter;
use Cake\Cache\Cache;
use Cake\Http\Exception\HttpException;
use Cake\TestSuite\TestCase;

/**
 * The test surface of the App\Ems\RateLimiter seam. Because the limiter is
 * discriminator-agnostic and takes an injectable clock (`$now`), the whole
 * fixed-window contract is exercisable without HTTP: counts accumulate, the
 * (limit+1)-th request throws 429, a window rollover restores the budget, and
 * distinct buckets/discriminators never share a counter.
 */
class RateLimiterTest extends TestCase
{
    private const NOW = 1_700_000_000;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    public function testAllowsRequestsUpToTheLimit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('allow', '1.2.3.4', 5, 300, self::NOW);
        }
        $this->assertTrue(true, 'the 5th request within a limit of 5 must not throw');
    }

    public function testThrowsOnceOverTheLimit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('over', '1.2.3.4', 5, 300, self::NOW);
        }
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(429);
        RateLimiter::hit('over', '1.2.3.4', 5, 300, self::NOW); // the 6th
    }

    public function testRefusalCarriesSecondsUntilTheWindowResets(): void
    {
        // Fill the budget at NOW, then exceed it 100s into the 300s window.
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('retry', '1.2.3.4', 5, 300, self::NOW);
        }
        try {
            RateLimiter::hit('retry', '1.2.3.4', 5, 300, self::NOW + 100);
            $this->fail('the over-limit request must throw');
        } catch (RateLimited $e) {
            // 300s window, 100s elapsed → 200s left. Still a 429 HttpException.
            $this->assertSame(429, $e->getCode());
            $this->assertSame(200, $e->retryAfter);
        }
    }

    public function testRetryAfterIsNeverBelowOne(): void
    {
        // Exceed the budget at the very last second of the window.
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('edge', '1.2.3.4', 5, 300, self::NOW);
        }
        try {
            RateLimiter::hit('edge', '1.2.3.4', 5, 300, self::NOW + 300);
            $this->fail('the over-limit request must throw');
        } catch (RateLimited $e) {
            $this->assertGreaterThanOrEqual(1, $e->retryAfter);
        }
    }

    public function testWindowRolloverRestoresTheBudget(): void
    {
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('roll', '1.2.3.4', 5, 300, self::NOW);
        }
        // One second past the window → a fresh window, no throw.
        RateLimiter::hit('roll', '1.2.3.4', 5, 300, self::NOW + 301);
        $this->assertTrue(true);
    }

    public function testDistinctDiscriminatorsCountSeparately(): void
    {
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('disc', '1.1.1.1', 5, 300, self::NOW);
        }
        // A different client has its own budget.
        RateLimiter::hit('disc', '2.2.2.2', 5, 300, self::NOW);
        $this->assertTrue(true);
    }

    public function testDistinctBucketsCountSeparately(): void
    {
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('bucket_a', '1.1.1.1', 5, 300, self::NOW);
        }
        // Same client, different endpoint → independent counter.
        RateLimiter::hit('bucket_b', '1.1.1.1', 5, 300, self::NOW);
        $this->assertTrue(true);
    }
}
