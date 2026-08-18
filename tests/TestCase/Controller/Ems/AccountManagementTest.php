<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Api\Jwt;
use App\Ems\Messages;
use Cake\Cache\Cache;
use Cake\Utility\Text;

/**
 * Self-service Account & security — profile, password and active sessions
 * (document.md §3.18). Every action operates on the authenticated viewer's own
 * account (Policy SELF tier); there is no id to address someone else's.
 */
class AccountManagementTest extends EmsIntegrationTestCase
{
    private const PASSWORD = 'Secret123!';
    // A real 1×1 PNG, so the avatar validator's base64/size checks pass.
    private const AVATAR =
        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

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

    // --- Profile -------------------------------------------------------------

    public function testShowReturnsOwnAccountWithSecurityFields(): void
    {
        $this->authAsAdmin();
        $this->get($this->schoolPath('/me/account'));

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame('ada@test.school', $body['email']);
        $this->assertFalse($body['twoFactorEnabled']);
        // Never leak credential material.
        $this->assertArrayNotHasKey('passwordHash', $body);
    }

    public function testUpdateProfileSetsNamePhoneAndAvatar(): void
    {
        $this->authAsAdmin();
        $this->put($this->schoolPath('/me/account/profile'), [
            'name' => 'Ada A.',
            'phone' => '+234 802 111 2222',
            'avatar' => self::AVATAR,
        ]);

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame('Ada A.', $body['name']);
        $this->assertSame('+234 802 111 2222', $body['phone']);
        $this->assertSame(self::AVATAR, $body['avatar']);
        $this->assertSame(1, $this->rowCount('ems_users', ['id' => $this->adminId, 'name' => 'Ada A.']));
    }

    public function testUpdateProfileRejectsEmptyNameAndBadPhone(): void
    {
        $this->authAsAdmin();
        $this->put($this->schoolPath('/me/account/profile'), ['name' => '   ']);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::PROFILE_NAME_REQUIRED, $this->responseJson()['message']);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/me/account/profile'), ['name' => 'Ada', 'phone' => 'not-a-number!!']);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::PHONE_INVALID, $this->responseJson()['message']);
    }

    public function testUpdateProfileRejectsOversizeAvatar(): void
    {
        $this->authAsAdmin();
        // ~600 KB of decoded bytes — over the 512 KB cap.
        $huge = 'data:image/png;base64,' . base64_encode(str_repeat('A', 600 * 1024));
        $this->put($this->schoolPath('/me/account/profile'), ['name' => 'Ada', 'avatar' => $huge]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::AVATAR_TOO_LARGE, $this->responseJson()['message']);
    }

    public function testAnyRoleManagesTheirOwnAccount(): void
    {
        // A family role (student) is a SELF-tier member and may read its account.
        $studentId = Text::uuid();
        $this->authAs('student', $studentId, 'Sam Student');
        $this->get($this->schoolPath('/me/account'));
        $this->assertResponseOk();
        $this->assertSame($studentId, $this->responseJson()['id']);
    }

    public function testUnauthenticatedIsRefused(): void
    {
        $this->get($this->schoolPath('/me/account'));
        $this->assertResponseCode(401);
    }

    // --- Password ------------------------------------------------------------

    public function testChangePasswordRequiresCorrectCurrentPassword(): void
    {
        $this->primePassword();
        $token = $this->signInToken();
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/password'), [
            'currentPassword' => 'wrong-password',
            'newPassword' => 'BrandNew123!',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::CURRENT_PASSWORD_WRONG, $this->responseJson()['message']);
    }

    public function testChangePasswordRejectsSamePasswordAndShortPassword(): void
    {
        $this->primePassword();
        $token = $this->signInToken();

        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/password'), [
            'currentPassword' => self::PASSWORD,
            'newPassword' => self::PASSWORD,
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::PASSWORD_SAME_AS_OLD, $this->responseJson()['message']);

        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/password'), [
            'currentPassword' => self::PASSWORD,
            'newPassword' => 'short',
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::PASSWORD_MIN, $this->responseJson()['message']);
    }

    public function testChangePasswordSucceedsAndSignsOutOtherDevicesOnly(): void
    {
        $this->primePassword();

        // A second, other-device session for the same account.
        $otherFamily = Text::uuid();
        $this->seedRefreshFamily($otherFamily);

        $token = $this->signInToken();
        $sid = $this->sidOf($token);

        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/password'), [
            'currentPassword' => self::PASSWORD,
            'newPassword' => 'BrandNew123!',
        ]);
        $this->assertResponseOk();
        $this->assertTrue($this->responseJson()['changed']);

        // New password works; old one no longer does.
        $this->assertSame(1, $this->rowCount('ems_users', ['id' => $this->adminId]));
        $stored = $this->db->selectQuery()->select(['password_hash'])->from('ems_users')
            ->where(['id' => $this->adminId])->execute()->fetch('assoc');
        $this->assertTrue(password_verify('BrandNew123!', (string)$stored['password_hash']));

        // Other device revoked; this device kept.
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['family_id' => $otherFamily, 'revoked_at IS NOT' => null]));
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['family_id' => $sid, 'revoked_at IS' => null]));
    }

    // --- Sessions ------------------------------------------------------------

    public function testSessionsListsDevicesAndFlagsCurrent(): void
    {
        $this->primePassword();
        $otherFamily = Text::uuid();
        $this->seedRefreshFamily($otherFamily, 'Firefox', '10.0.0.9');

        $token = $this->signInToken();
        $sid = $this->sidOf($token);

        $this->authWith($token);
        $this->get($this->schoolPath('/me/account/sessions'));
        $this->assertResponseOk();
        $items = $this->responseJson()['items'];
        $this->assertCount(2, $items);

        $current = array_values(array_filter($items, static fn(array $s): bool => $s['current']));
        $this->assertCount(1, $current);
        $this->assertSame($sid, $current[0]['id']);
    }

    public function testRevokeOneSessionScopedToOwner(): void
    {
        $this->primePassword();
        $otherFamily = Text::uuid();
        $this->seedRefreshFamily($otherFamily);

        // A DIFFERENT account's session must be untouchable.
        $strangerId = Text::uuid();
        $this->ensureUser('teacher', $strangerId, 'Tunde Teacher');
        $strangerFamily = Text::uuid();
        $this->seedRefreshFamily($strangerFamily, null, null, $strangerId);

        $token = $this->signInToken();
        $this->authWith($token);
        $this->delete($this->schoolPath('/me/account/sessions/' . $otherFamily));
        $this->assertResponseOk();
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['family_id' => $otherFamily, 'revoked_at IS NOT' => null]));

        // Someone else's family id is a 404, never a revocation.
        $this->authWith($token);
        $this->delete($this->schoolPath('/me/account/sessions/' . $strangerFamily));
        $this->assertResponseCode(404);
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['family_id' => $strangerFamily, 'revoked_at IS' => null]));
    }

    public function testRevokeOtherSessionsKeepsCurrent(): void
    {
        $this->primePassword();
        $a = Text::uuid();
        $b = Text::uuid();
        $this->seedRefreshFamily($a);
        $this->seedRefreshFamily($b);

        $token = $this->signInToken();
        $sid = $this->sidOf($token);
        $this->authWith($token);
        $this->post($this->schoolPath('/me/account/sessions/revoke-others'), []);
        $this->assertResponseOk();

        $this->assertSame(2, $this->rowCount('ems_refresh_tokens', ['family_id IN' => [$a, $b], 'revoked_at IS NOT' => null]));
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['family_id' => $sid, 'revoked_at IS' => null]));
    }

    // --- Helpers -------------------------------------------------------------

    private function primePassword(): void
    {
        $this->db->update(
            'ems_users',
            ['password_hash' => password_hash(self::PASSWORD, PASSWORD_DEFAULT), 'modified' => $this->now()],
            ['id' => $this->adminId],
        );
    }

    private function signInToken(): string
    {
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);

        return (string)($this->responseJson()['token'] ?? '');
    }

    private function authWith(string $token): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'],
        ]);
    }

    private function sidOf(string $token): string
    {
        return (string)(Jwt::decode($token, time())['sid'] ?? '');
    }

    /** Seed one live refresh-token family (a device session) — bypasses insertRow,
     *  as ems_refresh_tokens has no `modified` column. */
    private function seedRefreshFamily(
        string $familyId,
        ?string $userAgent = null,
        ?string $ip = null,
        ?string $userId = null,
    ): void {
        $this->db->insert('ems_refresh_tokens', [
            'id' => Text::uuid(),
            'user_id' => $userId ?? $this->adminId,
            'token_hash' => hash('sha256', $familyId . '-tip'),
            'family_id' => $familyId,
            'user_agent' => $userAgent,
            'ip' => $ip,
            'expires_at' => date('Y-m-d H:i:s', time() + 86400),
            'last_used_at' => date('Y-m-d H:i:s', time() - 60),
            'created' => $this->now(),
        ]);
    }
}
