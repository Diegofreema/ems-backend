<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\Utility\Text;

/**
 * Automatic guardian absence alerts (§3.12/§3.19). A register save that makes a
 * student NEWLY absent alerts the family exactly once per date: a once-only
 * marker row, a portal inbox row per linked parent account, and an e-mail
 * recorded in the staff send log. Corrections that re-flip the same date stay
 * silent; a different date alerts again; the teacher's register note never
 * leaves the school side. The Resend HTTP call is mocked — a test must never
 * hand a message to the real provider.
 */
final class RegisterAbsenceAlertTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    protected const CLEANUP_TABLES = [
        'ems_portal_notifications',
        'ems_absence_alerts',
        'ems_message_recipients',
        'ems_notifications',
        'ems_attendance_records',
        'ems_attendance_sessions',
        'ems_guardians',
        'ems_students',
        'ems_class_groups',
        'ems_users',
        'ems_schools',
    ];

    private string $classId = '';
    private string $studentId = '';
    private string $guardianId = '';
    private string $parentUserId = '';

    protected function setUp(): void
    {
        parent::setUp();
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
        $this->classId = $this->seedClass('JSS 1A');
        $this->studentId = $this->seedStudent('JSS 1A', 'Ada', 'Learner');
        $this->guardianId = $this->seedGuardian($this->studentId, 'Grace', 'Learner', 'grace@family.test');
        $this->parentUserId = $this->seedParentUser([$this->studentId]);
    }

    public function testNewlyAbsentAlertsOncePerDate(): void
    {
        $date = '2025-11-05';

        // 1. First submission marks Ada absent → one alert, everywhere.
        $this->putRegister($date, 'absent', 'Told me the family travelled');
        $this->assertSame(1, $this->rowCount('ems_absence_alerts', [
            'student_id' => $this->studentId, 'date' => $date,
        ]));
        $this->assertSame(1, $this->rowCount('ems_portal_notifications', [
            'user_id' => $this->parentUserId, 'kind' => 'absence_alert', 'date' => $date,
        ]));
        $this->assertSame(1, $this->rowCount('ems_notifications', ['kind' => 'absence_alert']));
        // The e-mail is on the trail: delivered through the (mocked) provider,
        // one attempt, addressed to the primary guardian.
        $this->assertSame(1, $this->rowCount('ems_message_recipients', [
            'person_id' => $this->guardianId, 'status' => 'sent', 'attempts' => 1,
        ]));

        // 2. Corrections that re-flip the same date stay silent: absent →
        // present → absent again is still ONE alert for the date.
        $this->putRegister($date, 'present', '', 'Marked in error');
        $this->putRegister($date, 'absent', '', 'No — she truly is away');
        $this->assertSame(1, $this->rowCount('ems_absence_alerts', [
            'student_id' => $this->studentId, 'date' => $date,
        ]));
        $this->assertSame(1, $this->rowCount('ems_notifications', ['kind' => 'absence_alert']));
        $this->assertSame(1, $this->rowCount('ems_portal_notifications', [
            'user_id' => $this->parentUserId, 'kind' => 'absence_alert',
        ]));

        // 3. A different date is a different absence — it alerts again.
        $this->putRegister('2025-11-06', 'absent', '');
        $this->assertSame(2, $this->rowCount('ems_absence_alerts', ['student_id' => $this->studentId]));
        $this->assertSame(2, $this->rowCount('ems_notifications', ['kind' => 'absence_alert']));
    }

    public function testPresentAndLateDoNotAlert(): void
    {
        $this->putRegister('2025-11-05', 'present', '');
        $this->putRegister('2025-11-06', 'late', '', null);
        $this->assertSame(0, $this->rowCount('ems_absence_alerts', []));
        $this->assertSame(0, $this->rowCount('ems_portal_notifications', []));
        $this->assertSame(0, $this->rowCount('ems_notifications', ['kind' => 'absence_alert']));
    }

    public function testRegisterNoteStaysOutOfTheAlert(): void
    {
        $note = 'Suspected chickenpox — sent home';
        $this->putRegister('2025-11-05', 'absent', $note);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/notifications?kind=absence_alert'));
        $this->assertResponseOk();
        $this->assertStringNotContainsString($note, (string)$this->_response->getBody());

        $this->authAs('parent', $this->parentUserId, 'Grace Learner');
        $this->get($this->schoolPath('/portal/notifications'));
        $this->assertResponseOk();
        $this->assertStringNotContainsString($note, (string)$this->_response->getBody());
    }

    public function testPortalInboxUnreadAndMarkRead(): void
    {
        $this->putRegister('2025-11-05', 'absent', '');

        $this->authAs('parent', $this->parentUserId, 'Grace Learner');
        $this->get($this->schoolPath('/portal/notifications'));
        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(1, $body['unread']);
        $this->assertCount(1, $body['items']);
        $this->assertSame('absence_alert', $body['items'][0]['kind']);
        $this->assertSame($this->studentId, $body['items'][0]['studentId']);
        $this->assertNull($body['items'][0]['readAt']);

        $this->authAs('parent', $this->parentUserId, 'Grace Learner');
        $this->post($this->schoolPath('/portal/notifications/read'));
        $this->assertResponseCode(204);

        $this->authAs('parent', $this->parentUserId, 'Grace Learner');
        $this->get($this->schoolPath('/portal/notifications'));
        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame(0, $body['unread']);
        $this->assertNotNull($body['items'][0]['readAt']);
    }

    /** Submit (or correct) the seeded student's register row for a date. */
    private function putRegister(string $date, string $status, string $note, ?string $reason = null): void
    {
        $this->authAsAdmin();
        $payload = [
            'entries' => [
                ['studentId' => $this->studentId, 'status' => $status, 'note' => $note],
            ],
        ];
        if ($reason !== null) {
            $payload['reason'] = $reason;
        }
        $this->put($this->schoolPath('/classes/' . $this->classId . '/register?date=' . $date), $payload);
        $this->assertResponseCode(204);
    }

    private function seedClass(string $name): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_class_groups', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'name' => $name,
            'level' => $name,
            'capacity' => 30,
        ]);

        return $id;
    }

    private function seedStudent(string $classGroup, string $firstName, string $lastName): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM-' . substr($id, 0, 6),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => '2014-05-10',
            'gender' => 'female',
            'class_group' => $classGroup,
            'status' => 'enrolled',
            'guardian_name' => 'Guardian ' . $lastName,
            'guardian_phone' => '08040000000',
            'enrolled_on' => '2025-09-01',
        ]);

        return $id;
    }

    private function seedGuardian(string $studentId, string $firstName, string $lastName, string $email): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_guardians', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'relationship' => 'mother',
            'phone' => '08040000001',
            'email' => $email,
            'is_primary' => 1,
        ]);

        return $id;
    }

    /** @param array<int,string> $studentIds */
    private function seedParentUser(array $studentIds): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'name' => 'Grace Learner',
            'email' => 'grace.parent@test.school',
            'role' => 'parent',
            'status' => 'active',
            'link_kind' => 'parent',
            'link_student_ids' => json_encode($studentIds),
            'added_on' => $this->now(),
        ]);

        return $id;
    }
}
