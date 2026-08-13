<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Cache\Cache;
use Cake\Utility\Text;

/**
 * Sign-in hardening (security review findings #3 and #4).
 *
 * #3 — account enumeration: before the fix, `invited`/`disabled` accounts
 * returned distinct 403s (and the miss path skipped password_verify, a timing
 * oracle), so an anonymous prober could learn which e-mails are registered and
 * their state. Now account state is revealed ONLY after a correct password, and
 * every attempt spends one bcrypt.
 *
 * #4 — distributed brute force: a per-account failure throttle bounds guesses
 * against one e-mail across all source IPs, on top of the per-IP bucket. Only
 * failures count, so a correct password is never locked out.
 */
final class SignInSecurityTest extends EmsIntegrationTestCase
{
    private const RIGHT_PASSWORD = 'correct-horse-battery';

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

    private function seedDisabledUser(string $email): void
    {
        $this->insertRow('ems_users', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => 'Dana Disabled',
            'email' => $email,
            'role' => 'registrar',
            'status' => 'disabled',
            'added_on' => $this->now(),
            'password_hash' => password_hash(self::RIGHT_PASSWORD, PASSWORD_DEFAULT),
        ]);
    }

    private function signIn(string $email, string $password, string $remoteAddr = '203.0.113.1'): void
    {
        $this->configRequest(['environment' => ['REMOTE_ADDR' => $remoteAddr]]);
        $this->post('/api/ems/auth/sign-in', ['email' => $email, 'password' => $password]);
    }

    public function testDisabledAccountIsIndistinguishableFromBadCredentialsWithoutThePassword(): void
    {
        // The enumeration probe: a wrong password against a disabled account must
        // look exactly like any other bad login — no "account disabled" leak.
        $this->seedDisabledUser('dana@test.school');
        $this->signIn('dana@test.school', 'wrong-password');

        $this->assertResponseCode(401);
        $this->assertSame(Messages::BAD_CREDENTIALS, $this->responseJson()['message']);
    }

    public function testDisabledAccountStateIsRevealedOnlyWithTheCorrectPassword(): void
    {
        // The legitimate account holder (who knows the password) still gets the
        // helpful, specific message.
        $this->seedDisabledUser('dana@test.school');
        $this->signIn('dana@test.school', self::RIGHT_PASSWORD);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACCOUNT_DISABLED, $this->responseJson()['message']);
    }

    public function testUnknownEmailReturnsTheSameRefusalAsAWrongPassword(): void
    {
        $this->signIn('ghost@test.school', 'whatever');

        $this->assertResponseCode(401);
        $this->assertSame(Messages::BAD_CREDENTIALS, $this->responseJson()['message']);
    }

    public function testPerAccountThrottleBoundsBruteForceAcrossManyIps(): void
    {
        // Ten wrong guesses at ONE e-mail, each from a DIFFERENT IP so the per-IP
        // bucket never trips — this isolates the per-account throttle.
        for ($i = 0; $i < 10; $i++) {
            $this->signIn('victim@test.school', 'wrong', "198.51.100.$i");
            $this->assertResponseCode(401);
        }

        // The 11th failure against the same e-mail is refused even from a fresh IP.
        $this->signIn('victim@test.school', 'wrong', '198.51.100.200');
        $this->assertResponseCode(429);
        $this->assertSame(Messages::RATE_LIMITED, $this->responseJson()['message']);

        // A different account from a fresh IP keeps its own budget.
        $this->signIn('bystander@test.school', 'wrong', '198.51.100.201');
        $this->assertResponseCode(401);
    }

    public function testACorrectPasswordIsNeverLockedOutByPriorFailures(): void
    {
        // An attacker burns the victim's failure budget...
        $this->seedDisabledUser('target@test.school'); // any real account with a known password
        for ($i = 0; $i < 12; $i++) {
            $this->signIn('target@test.school', 'wrong', "198.51.100.$i");
        }

        // ...but the real holder's correct password still gets through to the
        // account-state check (403 disabled here) rather than a 429 lockout.
        $this->signIn('target@test.school', self::RIGHT_PASSWORD, '198.51.100.250');
        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACCOUNT_DISABLED, $this->responseJson()['message']);
    }
}
