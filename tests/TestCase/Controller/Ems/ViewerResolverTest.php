<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Utility\Text;

/**
 * Proves authorization now reads LIVE server state, not the frozen JWT
 * (security review candidate #2). Each test signs a perfectly valid token and
 * then shows that the account's live `status`, `role`, and `school_id` — not
 * the token's claims — decide the request. This is the revocation the 1h token
 * could never give on its own.
 */
class ViewerResolverTest extends EmsIntegrationTestCase
{
    /** Attach a raw token WITHOUT seeding a user row (for the no-row cases). */
    private function attachToken(string $token): void
    {
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function testDisabledAccountIsRefusedDespiteAValidToken(): void
    {
        // A user who was active when their token was minted, then disabled.
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Del Disabled',
            'email' => 'del@test.school',
            'role' => 'administrator',
            'status' => 'disabled',
            'added_on' => $this->now(),
        ]);
        // Token still claims an active administrator — the signature is valid.
        $this->attachToken($this->token('administrator', $userId, 'Del Disabled'));

        $this->get($this->schoolPath('/students'));

        // 401 (not 403): a disabled credential is a dead session, so the SPA
        // tears it down through the same path as an expired token (candidate #5).
        $this->assertResponseCode(401);
        $this->assertSame(Messages::ACCOUNT_DISABLED, $this->responseJson()['message']);
    }

    public function testDemotionTakesEffectOnTheNextRequest(): void
    {
        // The seeded administrator is demoted in the DB after issuing a token.
        $this->db->update('ems_users', ['role' => 'teacher'], ['id' => $this->adminId]);

        // The token still says administrator; the live row says teacher.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Should Not Exist',
            'email' => 'nope@test.school',
            'role' => 'teacher',
        ]);

        // Evaluated at the LIVE role (teacher) → Policy refuses the invite.
        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACTION_FORBIDDEN, $this->responseJson()['message']);
        $this->assertFalse($this->rowExists('ems_users', ['email' => 'nope@test.school']));
    }

    public function testUnknownPrincipalIsUnauthenticated(): void
    {
        // A validly-signed token whose `sub` resolves to no live row at all.
        $this->attachToken($this->token('administrator', Text::uuid(), 'Ghost'));

        $this->get($this->schoolPath('/students'));

        $this->assertResponseCode(401);
    }

    public function testTokenForAnotherSchoolIsForbidden(): void
    {
        // An active administrator, but of a DIFFERENT school.
        $otherSchoolId = Text::uuid();
        $this->insertRow('ems_schools', [
            'id' => $otherSchoolId,
            'slug' => 'other-' . substr($otherSchoolId, 0, 8),
            'name' => 'Other School',
        ]);
        $otherUserId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $otherUserId,
            'school_id' => $otherSchoolId,
            'name' => 'Otis Other',
            'email' => 'otis@other.school',
            'role' => 'administrator',
            'status' => 'active',
            'added_on' => $this->now(),
        ]);
        $this->attachToken($this->token('administrator', $otherUserId, 'Otis Other'));

        // Addressing THIS school's tenant with the other school's principal.
        $this->get($this->schoolPath('/students'));

        $this->assertResponseCode(403);
        $this->assertSame(Messages::SCHOOL_FORBIDDEN, $this->responseJson()['message']);
    }

    public function testActiveAdministratorStillFlows(): void
    {
        // The positive control: an active, matching principal is let through.
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students'));

        $this->assertResponseOk();
    }

    public function testTenantlessRouteStillEnforcesLiveStatus(): void
    {
        // The one authenticated route with no {schoolId} (/schools/by-slug) is
        // still gated on live status — a disabled account cannot use it either.
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Del Disabled',
            'email' => 'del2@test.school',
            'role' => 'administrator',
            'status' => 'disabled',
            'added_on' => $this->now(),
        ]);
        $this->attachToken($this->token('administrator', $userId, 'Del Disabled'));

        $slug = 'test-' . substr($this->schoolId, 0, 8);
        $this->get('/api/ems/schools/by-slug/' . $slug);

        $this->assertResponseCode(401);
        $this->assertSame(Messages::ACCOUNT_DISABLED, $this->responseJson()['message']);
    }
}
