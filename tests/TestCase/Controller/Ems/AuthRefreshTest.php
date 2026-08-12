<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Api\Jwt;
use Cake\Cache\Cache;

/**
 * The refresh-token HTTP flow (§ token-at-rest hardening). Proves that the
 * durable credential lives only in the httpOnly cookie, rotates on every use,
 * tears down on replay or a disabled account, and is revoked on logout — all
 * through the real /api/ems/auth endpoints.
 */
class AuthRefreshTest extends EmsIntegrationTestCase
{
    private const COOKIE = 'ems_refresh';
    private const PASSWORD = 'secret123';

    protected function setUp(): void
    {
        parent::setUp();
        // Throttles are keyed by IP and shared across tests — clear so the
        // signin/refresh buckets don't leak counts between cases.
        Cache::clearAll();
        // The seeded admin needs a password to sign in with.
        $this->db->update(
            'ems_users',
            ['password_hash' => password_hash(self::PASSWORD, PASSWORD_DEFAULT)],
            ['id' => $this->adminId],
        );
    }

    public function testSignInSetsTheHttpOnlyRefreshCookieAndStoresATokenRow(): void
    {
        $this->signIn();

        $this->assertResponseOk();
        $this->assertNotEmpty($this->responseJson()['token'], 'an access token is still returned');

        $cookie = $this->_response->getCookie(self::COOKIE);
        $this->assertNotNull($cookie);
        $this->assertNotEmpty($cookie['value']);
        $this->assertSame('/api/ems/auth', $cookie['path']);
        $this->assertTrue($cookie['httponly']);
        $this->assertTrue($cookie['secure']);
        $this->assertSame('None', $cookie['samesite']);

        // The cookie value never appears in the DB — only its hash.
        $this->assertSame(1, $this->rowCount('ems_refresh_tokens', ['user_id' => $this->adminId]));
        $this->assertFalse($this->rowExists('ems_refresh_tokens', ['token_hash' => $cookie['value']]));
        $this->assertTrue($this->rowExists('ems_refresh_tokens', ['token_hash' => hash('sha256', $cookie['value'])]));
    }

    public function testRefreshRotatesTheCookieAndReturnsAFreshAccessToken(): void
    {
        $v1 = $this->signInCookie();

        $this->postRefresh($v1);

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertNotEmpty($body['token']);
        $this->assertArrayHasKey('user', $body);
        $this->assertArrayHasKey('school', $body);

        $v2 = (string)$this->_response->getCookie(self::COOKIE)['value'];
        $this->assertNotSame('', $v2);
        $this->assertNotSame($v1, $v2, 'the refresh cookie is rotated');

        // The old token is spent; the new one is live.
        $this->assertTrue($this->rowExists('ems_refresh_tokens', [
            'token_hash' => hash('sha256', $v1),
            'used_at IS NOT' => null,
        ]));
        $this->assertTrue($this->rowExists('ems_refresh_tokens', [
            'token_hash' => hash('sha256', $v2),
            'used_at IS' => null,
            'revoked_at IS' => null,
        ]));
    }

    public function testReplayingAnOldCookieRevokesTheFamilyAndKillsTheLiveOne(): void
    {
        $v1 = $this->signInCookie();
        $this->postRefresh($v1);
        $v2 = (string)$this->_response->getCookie(self::COOKIE)['value'];

        // Replay the already-rotated v1 — the theft tripwire.
        $this->postRefresh($v1);
        $this->assertResponseCode(401);

        // The live successor v2 is now dead too.
        $this->postRefresh($v2);
        $this->assertResponseCode(401);
    }

    public function testADisabledAccountCannotRefreshAndTheCookieIsCleared(): void
    {
        $v1 = $this->signInCookie();
        $this->db->update('ems_users', ['status' => 'disabled'], ['id' => $this->adminId]);

        $this->postRefresh($v1);

        $this->assertResponseCode(401);
        // Cookie cleared (expired) so the browser drops it.
        $this->assertSame('', (string)$this->_response->getCookie(self::COOKIE)['value']);
        // The family is revoked, so the cookie is worthless even before it expires.
        $this->assertFalse($this->rowExists('ems_refresh_tokens', ['user_id' => $this->adminId, 'revoked_at IS' => null]));
    }

    public function testLogoutRevokesTheFamilyAndSubsequentRefreshFails(): void
    {
        $v1 = $this->signInCookie();

        $this->cookie(self::COOKIE, $v1);
        $this->post('/api/ems/auth/logout');
        $this->assertResponseCode(204);
        $this->assertSame('', (string)$this->_response->getCookie(self::COOKIE)['value']);

        $this->postRefresh($v1);
        $this->assertResponseCode(401);
    }

    public function testRefreshWithoutACookieIsUnauthenticated(): void
    {
        $this->post('/api/ems/auth/refresh');
        $this->assertResponseCode(401);
    }

    public function testConfiguredFrontendOriginCanUseCredentialedApi(): void
    {
        $this->configRequest(['headers' => ['Origin' => 'http://localhost:5173']]);
        $this->signIn();

        $this->assertResponseOk();
        $this->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
        $this->assertHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function testUnknownBrowserOriginIsRejectedBeforeAuthentication(): void
    {
        $this->configRequest(['headers' => ['Origin' => 'https://attacker.example']]);
        $this->signIn();

        $this->assertResponseCode(403);
        $this->assertSame(0, $this->rowCount('ems_refresh_tokens', []));
        $this->assertFalse($this->_response->hasHeader('Access-Control-Allow-Origin'));
    }

    public function testAccessTokenCarriesOnlyIdentityClaims(): void
    {
        $this->signIn();
        $this->assertResponseOk();

        // Token-slimming: the access token holds ONLY who (`sub`) + which surface
        // (`type`), plus the standard iss/iat/nbf/exp. No role/school/name — those
        // are read live from ems_users, never trusted from the client-held token.
        $claims = Jwt::decode($this->responseJson()['token'], time());
        $this->assertSame('ems', $claims['type']);
        $this->assertSame($this->adminId, $claims['sub']);
        $this->assertEqualsCanonicalizing(
            ['type', 'sub', 'iss', 'iat', 'nbf', 'exp'],
            array_keys($claims),
        );
        $this->assertArrayNotHasKey('role', $claims);
        $this->assertArrayNotHasKey('memberships', $claims);
    }

    // --- helpers ------------------------------------------------------------

    private function signIn(): void
    {
        $this->post('/api/ems/auth/sign-in', ['email' => 'ada@test.school', 'password' => self::PASSWORD]);
    }

    private function signInCookie(): string
    {
        $this->signIn();
        $this->assertResponseOk();

        return (string)$this->_response->getCookie(self::COOKIE)['value'];
    }

    private function postRefresh(string $cookieValue): void
    {
        $this->cookie(self::COOKIE, $cookieValue);
        $this->post('/api/ems/auth/refresh');
    }
}
