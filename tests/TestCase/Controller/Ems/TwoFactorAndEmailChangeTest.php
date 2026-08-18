<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\EmailChanges;
use App\Ems\LoginChallenges;
use App\Ems\Messages;
use App\Ems\TrustedDevices;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\Utility\Text;

/**
 * Email two-factor sign-in and login-e-mail changes (document.md §3.18).
 *
 * 2FA is opt-in: enabling mails a confirmation code; once on, a correct password
 * on an untrusted device yields a challenge, not a session, and the mailed code
 * finishes sign-in. "Remember this device" trusts the browser for 30 days.
 * Changing the login e-mail needs the password and a link opened at the NEW
 * address before the swap takes effect.
 */
class TwoFactorAndEmailChangeTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    private const PASSWORD = 'Secret123!';

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
        $this->db->update(
            'ems_users',
            ['password_hash' => password_hash(self::PASSWORD, PASSWORD_DEFAULT), 'modified' => $this->now()],
            ['id' => $this->adminId],
        );
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    // --- Enabling 2FA --------------------------------------------------------

    public function testEnableThenConfirmTurnsOn2fa(): void
    {
        $token = $this->signInToken();

        // Enable mails a code; account is still not 2FA until confirmed.
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/2fa/enable'), []);
        $this->assertResponseOk();
        $this->assertTrue($this->responseJson()['sent']);
        $this->assertSame(0, $this->rowCount('ems_users', ['id' => $this->adminId, 'two_factor_enabled' => true]));

        $code = $this->seededCode();
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/2fa/confirm'), ['code' => $code]);
        $this->assertResponseOk();
        $this->assertTrue($this->responseJson()['twoFactorEnabled']);
        $this->assertSame(1, $this->rowCount('ems_users', ['id' => $this->adminId, 'two_factor_enabled' => true]));
    }

    public function testConfirmRejectsWrongCode(): void
    {
        $token = $this->signInToken();
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/2fa/enable'), []);

        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/2fa/confirm'), ['code' => '000000']);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::TWO_FACTOR_CODE_INVALID, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_users', ['id' => $this->adminId, 'two_factor_enabled' => true]));
    }

    public function testDisableRequiresPasswordAndForgetsDevices(): void
    {
        $this->enable2faDirectly();
        // A remembered device that must be forgotten when 2FA goes off.
        $this->db->insert('ems_trusted_devices', [
            'id' => Text::uuid(),
            'user_id' => $this->adminId,
            'token_hash' => hash('sha256', 'trusted'),
            'expires_at' => date('Y-m-d H:i:s', time() + 86400),
            'created' => $this->now(),
        ]);
        $token = $this->signInTrusted();

        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/2fa/disable'), ['currentPassword' => 'wrong']);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::CURRENT_PASSWORD_WRONG, $this->responseJson()['message']);

        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/2fa/disable'), ['currentPassword' => self::PASSWORD]);
        $this->assertResponseOk();
        $this->assertFalse($this->responseJson()['twoFactorEnabled']);
        $this->assertSame(0, $this->rowCount('ems_users', ['id' => $this->adminId, 'two_factor_enabled' => true]));
        $this->assertSame(0, $this->rowCount('ems_trusted_devices', ['user_id' => $this->adminId]));
    }

    // --- 2FA sign-in ---------------------------------------------------------

    public function testSignInWith2faChallengesThenVerifies(): void
    {
        $this->enable2faDirectly();

        // Correct password → challenge, NOT a session.
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);
        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertTrue($body['twoFactorRequired']);
        $this->assertNotEmpty($body['challengeId']);
        $this->assertArrayNotHasKey('token', $body);
        $this->assertStringContainsString('@test.school', $body['email']);
        $this->assertSame(0, $this->rowCount('ems_refresh_tokens', []));

        // The mailed code finishes sign-in.
        $code = $this->seededCode();
        $this->post('/api/ems/auth/sign-in/verify', [
            'challengeId' => $body['challengeId'],
            'code' => $code,
        ]);
        $this->assertResponseOk();
        $this->assertNotEmpty($this->responseJson()['token']);
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['user_id' => $this->adminId]));
    }

    public function testSignInVerifyRejectsWrongCode(): void
    {
        $this->enable2faDirectly();
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);
        $challengeId = $this->responseJson()['challengeId'];

        $this->post('/api/ems/auth/sign-in/verify', ['challengeId' => $challengeId, 'code' => '999999']);
        $this->assertResponseCode(400);
        $this->assertSame(Messages::TWO_FACTOR_CHALLENGE_INVALID, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_refresh_tokens', []));
    }

    public function testRememberedDeviceSkipsTheCode(): void
    {
        $this->enable2faDirectly();

        // A live trust for this account and a known raw cookie value.
        $raw = 'trusted-cookie-value';
        $this->db->insert('ems_trusted_devices', [
            'id' => Text::uuid(),
            'user_id' => $this->adminId,
            'token_hash' => TrustedDevices::hash($raw),
            'expires_at' => date('Y-m-d H:i:s', time() + 86400),
            'created' => $this->now(),
        ]);

        $this->configRequest(['cookies' => ['ems_device' => $raw]]);
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);
        $this->assertResponseOk();
        // Straight to a session — no challenge.
        $this->assertNotEmpty($this->responseJson()['token'] ?? '');
        $this->assertArrayNotHasKey('twoFactorRequired', $this->responseJson());
    }

    // --- E-mail change -------------------------------------------------------

    public function testEmailChangeRequiresPasswordAndVerifiesNewAddress(): void
    {
        $token = $this->signInToken();

        // Wrong password is refused.
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/email'), [
            'currentPassword' => 'nope',
            'newEmail' => 'ada.new@test.school',
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::CURRENT_PASSWORD_WRONG, $this->responseJson()['message']);

        // Correct password stages the change; the address does NOT swap yet.
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/email'), [
            'currentPassword' => self::PASSWORD,
            'newEmail' => 'ada.new@test.school',
        ]);
        $this->assertResponseOk();
        $this->assertTrue($this->responseJson()['sent']);
        $this->assertSame(1, $this->rowCount('ems_users', ['id' => $this->adminId, 'email' => 'ada@test.school']));

        // Opening the link at the new address performs the swap.
        $raw = 'known-change-token';
        $this->db->update(
            'ems_email_changes',
            ['token' => EmailChanges::hash($raw)],
            ['user_id' => $this->adminId],
        );
        $this->post('/api/ems/auth/email-change/verify', ['token' => $raw]);
        $this->assertResponseOk();
        $this->assertSame('ada.new@test.school', $this->responseJson()['email']);
        $this->assertSame(1, $this->rowCount('ems_users', ['id' => $this->adminId, 'email' => 'ada.new@test.school']));
    }

    public function testEmailChangeRejectsTakenAddress(): void
    {
        $this->ensureUser('teacher', Text::uuid(), 'Other'); // email u-...@seed.test
        $existing = $this->db->selectQuery()->select(['email'])->from('ems_users')
            ->where(['id !=' => $this->adminId])->execute()->fetch('assoc')['email'];

        $token = $this->signInToken();
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/email'), [
            'currentPassword' => self::PASSWORD,
            'newEmail' => $existing,
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::EMAIL_EXISTS, $this->responseJson()['message']);
    }

    public function testEmailChangeVerifyRejectsDeadToken(): void
    {
        $this->post('/api/ems/auth/email-change/verify', ['token' => 'never-issued']);
        $this->assertResponseCode(400);
        $this->assertSame(Messages::EMAIL_CHANGE_LINK_INVALID, $this->responseJson()['message']);
    }

    // --- Helpers -------------------------------------------------------------

    private function enable2faDirectly(): void
    {
        $this->db->update(
            'ems_users',
            ['two_factor_enabled' => true, 'modified' => $this->now()],
            ['id' => $this->adminId],
        );
    }

    /** The raw code for the account's latest live challenge, injected by re-hashing
     *  a known value onto the row the endpoint just created. */
    private function seededCode(string $code = '424242'): string
    {
        $this->db->update(
            'ems_login_challenges',
            ['code_hash' => LoginChallenges::hash($code)],
            ['user_id' => $this->adminId, 'used_at IS' => null],
        );

        return $code;
    }

    private function signInToken(): string
    {
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);

        return (string)($this->responseJson()['token'] ?? '');
    }

    /** Sign in for a 2FA account by completing the challenge, returning the token. */
    private function signInTrusted(): string
    {
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);
        $challengeId = $this->responseJson()['challengeId'];
        $code = $this->seededCode();
        $this->post('/api/ems/auth/sign-in/verify', ['challengeId' => $challengeId, 'code' => $code]);

        return (string)($this->responseJson()['token'] ?? '');
    }

    private function authWith(string $token): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'],
        ]);
    }
}
