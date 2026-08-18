<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\EmailVerifications;
use App\Ems\Invitations;
use App\Ems\Messages;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

/**
 * E-mail verification for self-served registrations (§3.18).
 *
 * A school creator cannot sign in until the mailed 30-minute link is opened;
 * an unverified sign-in attempt re-sends a fresh link; opening the link
 * starts the first session and triggers the welcome e-mail. Invited accounts
 * and reset-code redemptions are stamped verified by those flows instead —
 * their codes already arrived through the mailbox.
 */
class EmailVerificationTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear(); // per-address verify_send budgets must not leak across tests
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    // --- Helpers ------------------------------------------------------------

    private function registerSchool(string $email = 'founder@new.school'): array
    {
        $this->post('/api/ems/auth/register-school', [
            'school' => ['name' => 'Verification Academy', 'shortName' => 'VA'],
            'admin' => ['name' => 'Fola Founder', 'email' => $email, 'password' => 'Sup3rSecret'],
        ]);

        return $this->responseJson();
    }

    private function founderId(string $email = 'founder@new.school'): string
    {
        $row = $this->db->selectQuery()->select(['id'])->from('ems_users')
            ->where(['email' => $email])->execute()->fetch('assoc');

        return (string)$row['id'];
    }

    /** Seed a verification row with a KNOWN raw token (stored hashed, as production does). */
    private function seedToken(string $userId, string $raw, string $expiresAt, ?string $usedAt = null): void
    {
        $this->db->insert('ems_email_verifications', [
            'id' => Text::uuid(),
            'user_id' => $userId,
            'token' => EmailVerifications::hash($raw),
            'expires_at' => $expiresAt,
            'used_at' => $usedAt,
            'created' => $this->now(),
        ]);
    }

    // --- Registration -------------------------------------------------------

    public function testRegisterCreatesUnverifiedAccountAndMailsALink(): void
    {
        $body = $this->registerSchool();

        // No session starts: the response asks for verification instead of
        // returning AuthResult, and no refresh cookie is set.
        $this->assertResponseOk();
        $this->assertTrue((bool)($body['verificationRequired'] ?? false));
        $this->assertSame('founder@new.school', $body['email']);
        $this->assertArrayNotHasKey('token', $body);
        $this->assertSame(0, $this->rowCount('ems_refresh_tokens', []));

        // The account exists, active but unverified, with a live hashed token.
        $this->assertSame(1, $this->rowCount('ems_users', [
            'email' => 'founder@new.school', 'status' => 'active', 'email_verified_at IS' => null,
        ]));
        $this->assertSame(1, $this->rowCount('ems_email_verifications', [
            'user_id' => $this->founderId(), 'used_at IS' => null,
        ]));
    }

    public function testUnverifiedSignInIsRefusedAndResendsAFreshLink(): void
    {
        $this->registerSchool();
        $userId = $this->founderId();

        $this->post('/api/ems/auth/sign-in', [
            'email' => 'founder@new.school', 'password' => 'Sup3rSecret',
        ]);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::EMAIL_UNVERIFIED, $this->responseJson()['message']);
        // A fresh link was issued and the registration-time one retired: only
        // the newest token is live.
        $this->assertSame(2, $this->rowCount('ems_email_verifications', ['user_id' => $userId]));
        $this->assertSame(1, $this->rowCount('ems_email_verifications', [
            'user_id' => $userId, 'used_at IS' => null,
        ]));
    }

    public function testWrongPasswordStaysGeneric401EvenWhenUnverified(): void
    {
        // Account state must never leak to a caller without the password.
        $this->registerSchool();

        $this->post('/api/ems/auth/sign-in', [
            'email' => 'founder@new.school', 'password' => 'not-the-password',
        ]);

        $this->assertResponseCode(401);
        $this->assertSame(Messages::BAD_CREDENTIALS, $this->responseJson()['message']);
    }

    // --- Verification -------------------------------------------------------

    public function testVerifyEmailStampsAccountStartsSessionAndBurnsToken(): void
    {
        $this->registerSchool();
        $userId = $this->founderId();
        $raw = bin2hex(random_bytes(16));
        $this->seedToken($userId, $raw, FrozenTime::now()->addMinutes(20)->format('Y-m-d H:i:s'));

        $this->post('/api/ems/auth/verify-email', ['token' => $raw]);

        // A full AuthResult: the first real session starts here.
        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame('Fola Founder', $body['user']['name']);
        $this->assertSame('Verification Academy', $body['school']['name']);
        $this->assertNotEmpty($body['token']);
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', []));

        // Verified, and the link is burned.
        $this->assertSame(1, $this->rowCount('ems_users', [
            'id' => $userId, 'email_verified_at IS NOT' => null,
        ]));
        $this->assertSame(0, $this->rowCount('ems_email_verifications', [
            'token' => EmailVerifications::hash($raw), 'used_at IS' => null,
        ]));

        // And the verified account can now sign in normally.
        $this->post('/api/ems/auth/sign-in', [
            'email' => 'founder@new.school', 'password' => 'Sup3rSecret',
        ]);
        $this->assertResponseOk();
    }

    public function testExpiredUsedAndUnknownTokensShareOneMessage(): void
    {
        $this->registerSchool();
        $userId = $this->founderId();

        $expired = bin2hex(random_bytes(16));
        $this->seedToken($userId, $expired, FrozenTime::now()->subMinutes(1)->format('Y-m-d H:i:s'));
        $used = bin2hex(random_bytes(16));
        $this->seedToken($userId, $used, FrozenTime::now()->addMinutes(20)->format('Y-m-d H:i:s'), $this->now());

        foreach ([$expired, $used, 'not-a-real-token', ''] as $token) {
            $this->post('/api/ems/auth/verify-email', ['token' => $token]);
            $this->assertResponseCode(400);
            $this->assertSame(Messages::VERIFY_LINK_INVALID, $this->responseJson()['message']);
        }

        // None of the failures verified the account.
        $this->assertSame(1, $this->rowCount('ems_users', [
            'id' => $userId, 'email_verified_at IS' => null,
        ]));
    }

    // --- Resend -------------------------------------------------------------

    public function testResendIsEnumerationSafeAndOnlySendsForUnverifiedAccounts(): void
    {
        $this->registerSchool();
        $userId = $this->founderId();

        // Unknown address, already-verified address, and the real unverified
        // one all answer the same { sent: true }.
        foreach (['nobody@nowhere.test', 'ada@test.school', 'founder@new.school'] as $email) {
            $this->post('/api/ems/auth/verify-email/resend', ['email' => $email]);
            $this->assertResponseOk();
            $this->assertTrue((bool)$this->responseJson()['sent']);
        }

        // But only the unverified account gained a fresh token (register + one
        // resend = 2 rows for the founder; the seeded admin has none).
        $this->assertSame(2, $this->rowCount('ems_email_verifications', ['user_id' => $userId]));
        $this->assertSame(2, $this->rowCount('ems_email_verifications', []));
    }

    public function testPerAddressSendBudgetIsCapped(): void
    {
        // Register (send #1) then resends: the budget is 3 per window, so rows
        // stop growing after the third send even though the answer stays 200.
        $this->registerSchool();
        $userId = $this->founderId();

        for ($i = 0; $i < 3; $i++) {
            $this->post('/api/ems/auth/verify-email/resend', ['email' => 'founder@new.school']);
            $this->assertResponseOk();
        }

        $this->assertSame(3, $this->rowCount('ems_email_verifications', ['user_id' => $userId]));
    }

    // --- Invited accounts skip verification ---------------------------------

    public function testInviteAcceptStampsVerifiedWithoutASeparateStep(): void
    {
        // The invite code reached the mailbox — that IS the verification.
        $inviteeId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $inviteeId,
            'school_id' => $this->schoolId,
            'name' => 'Tunde Teacher',
            'email' => 'tunde@test.school',
            'role' => 'teacher',
            'status' => 'invited',
            'added_on' => $this->now(),
            'invite_code' => Invitations::hash('WXYZ-2345'),
            'invite_expires_at' => FrozenTime::now()->addHours(24)->format('Y-m-d H:i:s'),
        ]);

        $this->post('/api/ems/auth/invite/accept', ['code' => 'WXYZ-2345', 'password' => 'Sup3rSecret']);
        $this->assertResponseOk();
        $this->assertSame(1, $this->rowCount('ems_users', [
            'id' => $inviteeId, 'status' => 'active', 'email_verified_at IS NOT' => null,
        ]));

        // And they sign straight in — no verification gate.
        $this->post('/api/ems/auth/sign-in', [
            'email' => 'tunde@test.school', 'password' => 'Sup3rSecret',
        ]);
        $this->assertResponseOk();
    }
}
