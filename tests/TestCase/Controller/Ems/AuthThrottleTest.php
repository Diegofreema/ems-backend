<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Cache\Cache;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

/**
 * Proves the App\Ems\RateLimiter is actually WIRED onto the public auth surface
 * (candidate #4), and that reset codes gain a per-code guess cap on top of the
 * per-IP throttle — the two together make the 6-digit code un-brute-forceable.
 */
class AuthThrottleTest extends EmsIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear(); // each test starts with empty throttle counters
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    private function seedReset(string $code, int $attempts = 0): string
    {
        $id = Text::uuid();
        // Direct insert: ems_password_resets has no `modified` column, so we
        // bypass the base insertRow() helper (which would add one).
        $this->db->insert('ems_password_resets', [
            'id' => $id,
            'user_id' => $this->adminId,
            'code' => $code,
            'expires_at' => FrozenTime::now()->addMinutes(30)->format('Y-m-d H:i:s'),
            'attempts' => $attempts,
            'created' => $this->now(),
        ]);

        return $id;
    }

    public function testInviteLookupIsRateLimited(): void
    {
        // invite_lookup allows 10 per window; the 11th from one IP → 429.
        $got429 = false;
        for ($i = 0; $i < 12; $i++) {
            $this->post('/api/ems/auth/invite/lookup', ['code' => 'NOPE' . $i]);
            if ($this->_response->getStatusCode() === 429) {
                $got429 = true;
                break;
            }
        }
        $this->assertTrue($got429, 'invite/lookup should 429 after its limit');
        $this->assertSame(Messages::RATE_LIMITED, $this->responseJson()['message']);
        // The refusal tells the client when to retry (§ candidate #4 polish).
        $retryAfter = $this->_response->getHeaderLine('Retry-After');
        $this->assertNotSame('', $retryAfter, 'a 429 must carry a Retry-After header');
        $this->assertGreaterThanOrEqual(1, (int)$retryAfter);
    }

    public function testResetCodeIsDeadAfterMaxWrongGuesses(): void
    {
        $resetId = $this->seedReset('654321');

        // Five wrong guesses. Clear the IP throttle each time so this isolates
        // the per-CODE cap (not the per-IP limit, which also guards this route).
        for ($i = 0; $i < 5; $i++) {
            Cache::clear();
            $this->post('/api/ems/auth/reset/confirm', [
                'email' => 'ada@test.school',
                'code' => '000000',
                'password' => 'newpassw0rd',
            ]);
            $this->assertResponseCode(400);
            $this->assertSame(Messages::RESET_INVALID, $this->responseJson()['message']);
        }

        // Budget spent → even the CORRECT code is refused now.
        Cache::clear();
        $this->post('/api/ems/auth/reset/confirm', [
            'email' => 'ada@test.school',
            'code' => '654321',
            'password' => 'newpassw0rd',
        ]);
        $this->assertResponseCode(400);
        $this->assertSame(Messages::RESET_INVALID, $this->responseJson()['message']);
        $this->assertTrue(
            $this->rowExists('ems_password_resets', ['id' => $resetId, 'attempts >=' => 5]),
            'each wrong guess must have burned an attempt',
        );
    }

    public function testCorrectCodeWithinBudgetResetsThePassword(): void
    {
        $resetId = $this->seedReset('112233');

        $this->post('/api/ems/auth/reset/confirm', [
            'email' => 'ada@test.school',
            'code' => '112233',
            'password' => 'brandnewpass',
        ]);

        $this->assertResponseOk();
        // The correct code is single-use: it is burned on success.
        $this->assertTrue(
            $this->rowExists('ems_password_resets', ['id' => $resetId, 'used_at IS NOT' => null]),
        );
    }
}
