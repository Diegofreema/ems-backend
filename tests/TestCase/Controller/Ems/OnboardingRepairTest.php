<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Invitations;
use App\Ems\Messages;
use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\Utility\Text;

class OnboardingRepairTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    protected function setUp(): void
    {
        parent::setUp();
        // Invitation and verification flows deliver e-mail; mock the provider
        // so the suite never sends real mail (and passes without a live key).
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
    }

    protected const CLEANUP_TABLES = [
        'ems_application_reviews',
        'ems_admission_applications',
        'ems_admission_cycles',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_teachers',
        'ems_sequences',
        'ems_class_groups',
        'ems_academic_sessions',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    public function testSchoolRegistrationRejectsMissingShortName(): void
    {
        $this->post('/api/ems/auth/register-school', [
            'school' => ['name' => 'Incomplete School'],
            'admin' => [
                'name' => 'Admin User',
                'email' => 'admin@incomplete.test',
                'password' => 'safe-password',
            ],
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::SCHOOL_SHORT_NAME_REQUIRED, $this->responseJson()['message']);
        $this->assertFalse($this->rowExists('ems_schools', ['name' => 'Incomplete School']));
    }

    public function testInvitationIsHashedExpiringAndRedeemable(): void
    {
        $teacherId = $this->seedTeacher();
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Tola Teacher',
            'email' => 'tola.teacher@test.school',
            'role' => 'teacher',
            'link' => ['kind' => 'teacher', 'teacherId' => $teacherId],
        ]);

        $this->assertResponseCode(201);
        $this->assertArrayNotHasKey('inviteCode', $this->responseJson());
        $row = $this->db->selectQuery()
            ->select(['id', 'invite_code', 'invite_expires_at'])
            ->from('ems_users')
            ->where(['email' => 'tola.teacher@test.school'])
            ->execute()
            ->fetch('assoc');
        $this->assertSame(64, strlen((string)$row['invite_code']));
        $this->assertGreaterThan(time(), strtotime((string)$row['invite_expires_at']));

        $knownCode = 'ABCD-EFGH';
        $this->db->update('ems_users', [
            'invite_code' => Invitations::hash($knownCode),
            'invite_expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ], ['id' => $row['id']]);
        $this->post('/api/ems/auth/invite/accept', [
            'code' => $knownCode,
            'password' => 'new-password',
        ]);

        $this->assertResponseOk();
        $this->assertTrue($this->rowExists('ems_users', [
            'id' => $row['id'],
            'status' => 'active',
            'link_teacher_id' => $teacherId,
            'invite_code IS' => null,
            'invite_expires_at IS' => null,
        ]));
    }

    public function testTeacherInvitationRequiresAnActiveTeacherRecord(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Unlinked Teacher',
            'email' => 'unlinked.teacher@test.school',
            'role' => 'teacher',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::USER_LINK_REQUIRED, $this->responseJson()['message']);
        $this->assertFalse($this->rowExists('ems_users', ['email' => 'unlinked.teacher@test.school']));
    }

    public function testAdministratorCanRepairAnUnlinkedTeacherAccount(): void
    {
        $teacherId = $this->seedTeacher();
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Existing Teacher',
            'email' => 'existing.teacher@test.school',
            'role' => 'teacher',
            'status' => 'active',
            'added_on' => '2026-08-01',
        ]);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $userId . '/link'), [
            'link' => ['kind' => 'teacher', 'teacherId' => $teacherId],
        ]);

        $this->assertResponseOk();
        $this->assertSame(['kind' => 'teacher', 'teacherId' => $teacherId], $this->responseJson()['link']);
        $this->assertTrue($this->rowExists('ems_users', [
            'id' => $userId,
            'link_kind' => 'teacher',
            'link_teacher_id' => $teacherId,
        ]));
    }

    public function testAdministratorCannotChangeAnUnlinkedStaffAccountToTeacher(): void
    {
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Rita Registrar',
            'email' => 'rita.registrar@test.school',
            'role' => 'registrar',
            'status' => 'active',
            'added_on' => '2026-08-01',
        ]);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $userId . '/role'), ['role' => 'teacher']);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::USER_LINK_REQUIRED, $this->responseJson()['message']);
        $this->assertTrue($this->rowExists('ems_users', ['id' => $userId, 'role' => 'registrar']));
    }

    public function testAdministratorCanChangeStaffAccountToTeacherWithAnActiveTeacherLink(): void
    {
        $teacherId = $this->seedTeacher();
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Bola Bursar',
            'email' => 'bola.bursar@test.school',
            'role' => 'bursar',
            'status' => 'active',
            'added_on' => '2026-08-01',
        ]);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $userId . '/role'), [
            'role' => 'teacher',
            'link' => ['kind' => 'teacher', 'teacherId' => $teacherId],
        ]);

        $this->assertResponseOk();
        $this->assertSame('teacher', $this->responseJson()['role']);
        $this->assertSame(['kind' => 'teacher', 'teacherId' => $teacherId], $this->responseJson()['link']);
    }

    public function testUnlinkedTeacherCannotBeReactivated(): void
    {
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Disabled Teacher',
            'email' => 'disabled.teacher@test.school',
            'role' => 'teacher',
            'status' => 'disabled',
            'added_on' => '2026-08-01',
        ]);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $userId . '/status'), ['status' => 'active']);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::USER_LINK_REQUIRED, $this->responseJson()['message']);
        $this->assertTrue($this->rowExists('ems_users', ['id' => $userId, 'status' => 'disabled']));
    }

    public function testTeacherRecordCannotBeLinkedToMoreThanOneAccount(): void
    {
        $teacherId = $this->seedTeacher();
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'First Teacher Account',
            'email' => 'first.teacher.account@test.school',
            'role' => 'teacher',
            'link' => ['kind' => 'teacher', 'teacherId' => $teacherId],
        ]);
        $this->assertResponseCode(201);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Second Teacher Account',
            'email' => 'second.teacher.account@test.school',
            'role' => 'teacher',
            'link' => ['kind' => 'teacher', 'teacherId' => $teacherId],
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::TEACHER_ACCOUNT_EXISTS, $this->responseJson()['message']);
        $this->assertFalse($this->rowExists('ems_users', ['email' => 'second.teacher.account@test.school']));
    }

    public function testParentInvitationRequiresAnEnrolledTenantStudent(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/users/invite'), [
            'name' => 'Pat Parent',
            'email' => 'pat.parent@test.school',
            'role' => 'parent',
        ]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::USER_LINK_REQUIRED, $this->responseJson()['message']);
        $this->assertFalse($this->rowExists('ems_users', ['email' => 'pat.parent@test.school']));
    }

    public function testAdministratorCanReplaceAndClearParentWardLinks(): void
    {
        $firstStudent = $this->seedStudent('ADM-LINK-1', 'First');
        $secondStudent = $this->seedStudent('ADM-LINK-2', 'Second');
        $parentId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $parentId,
            'school_id' => $this->schoolId,
            'name' => 'Pat Parent',
            'email' => 'linked.parent@test.school',
            'role' => 'parent',
            'status' => 'active',
            'added_on' => '2026-08-11',
            'link_kind' => 'parent',
            'link_student_ids' => json_encode([$firstStudent]),
        ]);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $parentId . '/link'), [
            'link' => ['kind' => 'parent', 'studentIds' => [$secondStudent]],
        ]);
        $this->assertResponseOk();
        $this->assertSame([$secondStudent], $this->responseJson()['link']['studentIds']);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $parentId . '/link'), ['link' => null]);
        $this->assertResponseOk();
        $this->assertArrayNotHasKey('link', $this->responseJson());
        $this->assertTrue($this->rowExists('ems_users', [
            'id' => $parentId,
            'link_kind IS' => null,
            'link_student_ids IS' => null,
        ]));
    }

    public function testAdministratorCannotClearAStudentAccountLink(): void
    {
        $studentId = $this->seedStudent('ADM-STUDENT-1', 'Student');
        $userId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => 'Student Account',
            'email' => 'student.account@test.school',
            'role' => 'student',
            'status' => 'active',
            'added_on' => '2026-08-11',
            'link_kind' => 'student',
            'link_student_id' => $studentId,
        ]);

        $this->authAsAdmin();
        $this->put($this->schoolPath('/users/' . $userId . '/link'), ['link' => null]);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::USER_LINK_REQUIRED, $this->responseJson()['message']);
        $this->assertTrue($this->rowExists('ems_users', [
            'id' => $userId,
            'link_student_id' => $studentId,
        ]));
    }

    public function testDirectAdmissionCreatesStudentGuardiansAndEnrolmentTogether(): void
    {
        $this->insertRow('ems_academic_sessions', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => '2026/2027',
            'starts_on' => '2026-09-01',
            'ends_on' => '2027-07-31',
            'status' => 'open',
        ]);
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students/admit'), $this->directAdmissionBody());

        $this->assertResponseCode(201);
        $studentId = $this->responseJson()['id'];
        $this->assertSame(1, $this->rowCount('ems_students', ['id' => $studentId]));
        $this->assertSame(2, $this->rowCount('ems_guardians', ['student_id' => $studentId]));
        $this->assertSame(1, $this->rowCount('ems_guardians', [
            'student_id' => $studentId,
            'is_primary' => 1,
        ]));
        $this->assertTrue($this->rowExists('ems_enrolments', [
            'student_id' => $studentId,
            'session' => '2026/2027',
            'class_group' => 'JSS 1A',
        ]));
    }

    public function testDirectAdmissionWithoutGuardianCreatesNothing(): void
    {
        $body = $this->directAdmissionBody();
        $body['guardians'] = [];
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students/admit'), $body);

        $this->assertResponseCode(422);
        $this->assertSame(Messages::STUDENT_GUARDIAN_REQUIRED, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_students', ['school_id' => $this->schoolId]));
    }

    public function testApplicationEnrolmentCreatesPlacementHistory(): void
    {
        $cycleId = Text::uuid();
        $applicationId = Text::uuid();
        $this->insertRow('ems_admission_cycles', [
            'id' => $cycleId,
            'school_id' => $this->schoolId,
            'name' => '2026 Intake',
            'session' => '2026/2027',
            'opens_on' => '2026-01-01',
            'closes_on' => '2026-12-31',
            'status' => 'open',
        ]);
        $this->insertRow('ems_class_groups', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => 'JSS 1A',
            'level' => 'JSS 1',
            'stream' => 'A',
            'capacity' => 30,
        ]);
        $this->insertRow('ems_admission_applications', [
            'id' => $applicationId,
            'school_id' => $this->schoolId,
            'cycle_id' => $cycleId,
            'application_number' => 'APP-0001',
            'first_name' => 'Zainab',
            'last_name' => 'Okoro',
            'date_of_birth' => '2014-04-10',
            'gender' => 'female',
            'desired_level' => 'JSS 1',
            'previous_school' => '',
            'guardian' => json_encode([
                'firstName' => 'Amina',
                'lastName' => 'Okoro',
                'relationship' => 'mother',
                'phone' => '08030000000',
                'email' => 'amina@example.test',
            ]),
            'note' => '',
            'submitted_on' => '2026-08-01',
            'status' => 'accepted',
        ]);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/applications/' . $applicationId . '/enrol'), [
            'classGroup' => 'JSS 1A',
        ]);

        $this->assertResponseOk();
        $studentId = $this->responseJson()['studentId'];
        $this->assertTrue($this->rowExists('ems_enrolments', [
            'student_id' => $studentId,
            'session' => '2026/2027',
            'class_group' => 'JSS 1A',
            'level' => 'JSS 1',
            'status' => 'active',
        ]));
    }

    private function directAdmissionBody(): array
    {
        return [
            'student' => [
                'admissionNumber' => 'ADM-1001',
                'firstName' => 'Musa',
                'lastName' => 'Bello',
                'dateOfBirth' => '2014-02-03',
                'gender' => 'male',
                'classGroup' => 'JSS 1A',
                'status' => 'enrolled',
                'enrolledOn' => '2026-08-11',
            ],
            'guardians' => [
                [
                    'firstName' => 'Binta',
                    'lastName' => 'Bello',
                    'relationship' => 'mother',
                    'phone' => '08031111111',
                    'email' => '',
                    'occupation' => '',
                    'isPrimary' => true,
                ],
                [
                    'firstName' => 'Sani',
                    'lastName' => 'Bello',
                    'relationship' => 'father',
                    'phone' => '08032222222',
                    'email' => '',
                    'occupation' => '',
                    'isPrimary' => false,
                ],
            ],
        ];
    }

    private function seedStudent(string $admissionNumber, string $firstName): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'admission_number' => $admissionNumber,
            'first_name' => $firstName,
            'last_name' => 'Student',
            'date_of_birth' => '2014-02-03',
            'gender' => 'other',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'guardian_name' => '',
            'guardian_phone' => '',
            'enrolled_on' => '2026-08-11',
        ]);

        return $id;
    }

    private function seedTeacher(): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_teachers', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'staff_number' => 'STF-1001',
            'first_name' => 'Tola',
            'last_name' => 'Teacher',
            'email' => 'tola.teacher@test.school',
            'phone' => '08030000000',
            'gender' => 'female',
            'subjects' => json_encode([]),
            'status' => 'active',
            'hired_on' => '2026-08-01',
        ]);

        return $id;
    }
}
