<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * Family portal onboarding (§3.19): the plan groups primary guardians into one
 * prospective account per family, creation runs in capped chunks, no-email
 * families get one-time codes, and the phone number on the code sheet becomes
 * a sign-in identifier.
 *
 * The test environment has no Resend key, so a delivery attempt throws — which
 * exercises the keep-the-account 'failed' path (the raw code still comes back
 * for the printed sheet).
 */
class FamilyInvitesTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_class_groups',
        'ems_academic_sessions',
        'ems_sequences',
        'ems_refresh_tokens',
        'ems_users',
        'ems_schools',
    ];

    /** @return array{studentId:string, guardianId:string} */
    private function seedFamily(
        string $first,
        string $last,
        string $guardianEmail,
        string $guardianPhone,
        string $status = 'enrolled',
    ): array {
        $studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $studentId,
            'school_id' => $this->schoolId,
            'admission_number' => 'T/' . substr($studentId, 0, 4),
            'first_name' => $first,
            'last_name' => $last,
            'date_of_birth' => '2012-01-01',
            'gender' => 'female',
            'class_group' => 'JSS 1A',
            'status' => $status,
            'enrolled_on' => '2026-01-10',
        ]);
        $guardianId = Text::uuid();
        $this->insertRow('ems_guardians', [
            'id' => $guardianId,
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'first_name' => 'Parent',
            'last_name' => $last,
            'relationship' => 'mother',
            'phone' => $guardianPhone,
            'email' => $guardianEmail,
            'occupation' => '',
            'is_primary' => 1,
        ]);

        return ['studentId' => $studentId, 'guardianId' => $guardianId];
    }

    public function testPlanGroupsAFamilyBySharedEmailIntoOneTarget(): void
    {
        $a = $this->seedFamily('Sade', 'Okafor', 'okafor@family.test', '0803 111 2222');
        $b = $this->seedFamily('Bayo', 'Okafor', 'OKAFOR@family.test', '0803 111 2222');
        $this->seedFamily('Tola', 'Ade', '', '0805 333 4444'); // code-only
        $this->seedFamily('Ghost', 'Gone', 'gone@family.test', '', 'withdrawn'); // out of scope

        $this->authAsAdmin();
        $this->get($this->schoolPath('/family-invites/plan'));
        $this->assertResponseOk();
        $plan = $this->responseJson();

        $this->assertSame(1, $plan['counts']['invitable']);
        $this->assertSame(1, $plan['counts']['codeOnly']);
        $this->assertCount(2, $plan['targets']);

        $family = null;
        foreach ($plan['targets'] as $t) {
            if ($t['email'] === 'okafor@family.test') {
                $family = $t;
            }
        }
        $this->assertNotNull($family);
        $this->assertCount(2, $family['students']);
        $studentIds = array_column($family['students'], 'id');
        $this->assertContains($a['studentId'], $studentIds);
        $this->assertContains($b['studentId'], $studentIds);
    }

    public function testCreateMintsOneAccountWithUnionOfWards(): void
    {
        $a = $this->seedFamily('Sade', 'Okafor', 'okafor@family.test', '0803 111 2222');
        $this->seedFamily('Bayo', 'Okafor', 'okafor@family.test', '0803 111 2222');

        $this->authAsAdmin();
        $this->post($this->schoolPath('/family-invites'), ['guardianIds' => [$a['guardianId']]]);
        $this->assertResponseCode(201);
        $result = $this->responseJson()['results'][0];

        // No mail key in tests → delivery fails, the account is KEPT, and the
        // raw code is returned so the school can still hand access over.
        $this->assertSame('failed', $result['status']);
        $this->assertNotEmpty($result['code']);
        $this->assertSame(1, $this->rowCount('ems_users', [
            'email' => 'okafor@family.test',
            'role' => 'parent',
            'status' => 'invited',
            'link_guardian_id' => $a['guardianId'],
        ]));

        // Both wards on the one account.
        $row = $this->db->selectQuery()->select(['link_student_ids'])->from('ems_users')
            ->where(['email' => 'okafor@family.test'])->execute()->fetch('assoc');
        $this->assertCount(2, (array)json_decode((string)$row['link_student_ids'], true));

        // A repeat send reports the family as already covered.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/family-invites'), ['guardianIds' => [$a['guardianId']]]);
        $this->assertResponseCode(201);
        $this->assertSame('exists', $this->responseJson()['results'][0]['status']);
    }

    public function testCodeOnlyFamilyActivatesAndSignsInByPhone(): void
    {
        $fam = $this->seedFamily('Tola', 'Ade', '', '+234 805 333 4444');

        $this->authAsAdmin();
        $this->post($this->schoolPath('/family-invites'), ['guardianIds' => [$fam['guardianId']]]);
        $this->assertResponseCode(201);
        $result = $this->responseJson()['results'][0];
        $this->assertSame('code', $result['status']);
        $code = (string)$result['code'];
        $this->assertNotSame('', $code);

        // The account has no mailbox; its phone digits are the identifier.
        $this->assertSame(1, $this->rowCount('ems_users', [
            'role' => 'parent',
            'email IS' => null,
            'phone_key' => '8053334444',
        ]));

        // Redeem the code (public route, no auth header needed).
        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/api/ems/auth/invite/accept', ['code' => $code, 'password' => 'FamilyPass1']);
        $this->assertResponseOk();

        // Sign in with the PHONE NUMBER, in a different written format.
        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/api/ems/auth/sign-in', ['email' => '0805-333-4444', 'password' => 'FamilyPass1']);
        $this->assertResponseOk();
        $this->assertSame('parent', $this->responseJson()['user']['role']);
    }

    public function testBatchOverTwentyFiveIsRefused(): void
    {
        $this->authAsAdmin();
        $ids = array_map(fn() => Text::uuid(), range(1, 26));
        $this->post($this->schoolPath('/family-invites'), ['guardianIds' => $ids]);
        $this->assertResponseCode(422);
        $this->assertSame('Send invitations in batches of 25 or fewer.', $this->responseJson()['message']);
    }

    public function testRegistrarMayRunItButTeachersMayNot(): void
    {
        $this->seedFamily('Sade', 'Okafor', 'okafor@family.test', '0803 111 2222');

        $registrarId = Text::uuid();
        $this->authAs('registrar', $registrarId, 'Rita Registrar');
        $this->get($this->schoolPath('/family-invites/plan'));
        $this->assertResponseOk();

        $teacherId = Text::uuid();
        $this->authAs('teacher', $teacherId, 'Tunde Teacher');
        $this->get($this->schoolPath('/family-invites/plan'));
        $this->assertResponseCode(403);
    }

    public function testResendReturnsAFreshCodeAndResetsAnActiveFamilyPassword(): void
    {
        $fam = $this->seedFamily('Tola', 'Ade', '', '0805 333 4444');

        $this->authAsAdmin();
        $this->post($this->schoolPath('/family-invites'), ['guardianIds' => [$fam['guardianId']]]);
        $userId = (string)$this->responseJson()['results'][0]['userId'];
        $firstCode = (string)$this->responseJson()['results'][0]['code'];

        // Resend while still invited → a NEW code; the old one is dead.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/' . $userId . '/invite/resend'));
        $this->assertResponseOk();
        $resent = $this->responseJson();
        $this->assertSame('code', $resent['status']);
        $this->assertNotSame($firstCode, $resent['code']);

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/api/ems/auth/invite/accept', ['code' => $firstCode, 'password' => 'FamilyPass1']);
        $this->assertResponseCode(404);

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/api/ems/auth/invite/accept', ['code' => (string)$resent['code'], 'password' => 'FamilyPass1']);
        $this->assertResponseOk();

        // The account is ACTIVE now; resend still works as the forgot-password
        // path for a phone-only parent: new code → new password.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/' . $userId . '/invite/resend'));
        $this->assertResponseOk();
        $resetCode = (string)$this->responseJson()['code'];

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/api/ems/auth/invite/accept', ['code' => $resetCode, 'password' => 'NewFamilyPass2']);
        $this->assertResponseOk();

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->post('/api/ems/auth/sign-in', ['email' => '08053334444', 'password' => 'NewFamilyPass2']);
        $this->assertResponseOk();

        // A staff account has no invitation to resend once active.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/' . $this->adminId . '/invite/resend'));
        $this->assertResponseCode(422);
        $this->assertSame('This account has no invitation to resend.', $this->responseJson()['message']);
    }
}
