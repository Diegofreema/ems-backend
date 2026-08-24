<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\Utility\Text;

/**
 * Proves the App\Ems\Policy gate is actually WIRED at the spine — not just that
 * the table is right (PolicyTest covers the table), but that a real request
 * with a low-privilege token is refused 403 before the action body runs, and
 * that a legitimate token still gets through. These are the headline
 * escalations the security review found.
 *
 * The capability gate runs in beforeFilter, before any id is resolved, so the
 * denial tests can use a throwaway id — the request never reaches the action.
 */
class PolicyEnforcementTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // The invite positive-control delivers e-mail; mock the provider so
        // the suite never sends real mail (and passes without a live key).
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
    }

    /** A parent's token — self-contained; the account row need not exist. */
    private function authAsParent(): void
    {
        $this->authAs('parent', Text::uuid(), 'Pat Parent');
    }

    public function testParentCannotInviteUsers(): void
    {
        $this->authAsParent();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Mal Icious',
            'email' => 'mal@test.school',
            'role' => 'administrator',
        ]);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACTION_FORBIDDEN, $this->responseJson()['message']);
        // The self-promotion never happened.
        $this->assertFalse($this->rowExists('ems_users', ['email' => 'mal@test.school']));
    }

    public function testNonAdministratorCannotChangeAnotherUsersRole(): void
    {
        $otherId = Text::uuid();
        $this->ensureUser('teacher', $otherId, 'Tunde Teacher');
        $this->authAsParent();

        $this->put($this->schoolPath('/users/' . $otherId . '/role'), ['role' => 'bursar']);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACTION_FORBIDDEN, $this->responseJson()['message']);
        $this->assertTrue($this->rowExists('ems_users', ['id' => $otherId, 'role' => 'teacher']));
    }

    public function testParentCannotEditAStudentRecord(): void
    {
        $this->authAsParent();
        $this->put($this->schoolPath('/students/' . Text::uuid()), ['firstName' => 'Rewritten']);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACTION_FORBIDDEN, $this->responseJson()['message']);
    }

    public function testParentCannotCreateFeeStructures(): void
    {
        $this->authAsParent();
        $this->post($this->schoolPath('/fee-structures'), ['name' => 'Bogus']);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::ACTION_FORBIDDEN, $this->responseJson()['message']);
    }

    public function testParentCannotReleaseResults(): void
    {
        $this->authAsParent();
        $this->post($this->schoolPath('/exams/' . Text::uuid() . '/release'), []);

        $this->assertResponseCode(403);
        // The surface keeps its documented refusal even for a family caller.
        $this->assertSame(Messages::RELEASE_NOT_TEACHER, $this->responseJson()['message']);
    }

    public function testTeacherStillCannotReleaseResults(): void
    {
        // The old ¬teacher rule is subsumed by the MANAGE tier — teachers are
        // blocked with the same documented message as before.
        $this->authAs('teacher', Text::uuid(), 'Tunde Teacher');
        $this->post($this->schoolPath('/exams/' . Text::uuid() . '/release'), []);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::RELEASE_NOT_TEACHER, $this->responseJson()['message']);
    }

    public function testAdministratorCanStillInvite(): void
    {
        // The positive control: capability passes for an administrator, so the
        // request reaches the action and creates the invited user.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'New Teacher',
            'email' => 'new.teacher@test.school',
            'role' => 'teacher',
        ]);

        $this->assertResponseOk();
        $this->assertTrue($this->rowExists('ems_users', ['email' => 'new.teacher@test.school']));
    }

    public function testAdministratorCannotInvitePlatformStaff(): void
    {
        // platform_staff is a vendor-operator role that belongs to no tenant, so
        // even an administrator cannot mint one through a school invite. The role
        // is not in UsersController::ROLES, so the action rejects it with 422.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Vendor Operator',
            'email' => 'operator@test.school',
            'role' => 'platform_staff',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::USER_ROLE_INVALID, $this->responseJson()['message']);
        $this->assertFalse($this->rowExists('ems_users', ['email' => 'operator@test.school']));
    }

    public function testAdministratorCannotChangeTheirOwnRole(): void
    {
        $this->authAsAdmin();

        $this->put($this->schoolPath('/users/' . $this->adminId . '/role'), ['role' => 'teacher']);

        $this->assertResponseCode(403);
        $this->assertSame(Messages::SELF_ROLE_CHANGE_FORBIDDEN, $this->responseJson()['message']);
        $this->assertTrue($this->rowExists('ems_users', [
            'id' => $this->adminId,
            'role' => 'administrator',
        ]));
    }

    public function testAdministratorCanStillChangeAnotherUsersRole(): void
    {
        $otherId = Text::uuid();
        $this->ensureUser('teacher', $otherId, 'Tunde Teacher');
        $this->authAsAdmin();

        $this->put($this->schoolPath('/users/' . $otherId . '/role'), ['role' => 'bursar']);

        $this->assertResponseOk();
        $this->assertSame('bursar', $this->responseJson()['role']);
        $this->assertTrue($this->rowExists('ems_users', ['id' => $otherId, 'role' => 'bursar']));
    }
}
