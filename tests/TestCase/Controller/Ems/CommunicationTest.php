<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Utility\Text;

/**
 * Communication contract checks at the real HTTP and database boundary.
 */
class CommunicationTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_message_recipients',
        'ems_contact_preferences',
        'ems_notifications',
        'ems_announcements',
        'ems_payments',
        'ems_invoices',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_teachers',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    public function testAnnouncementCreationRejectsInvalidContentAndEnums(): void
    {
        $cases = [
            [['title' => '', 'body' => 'Body', 'audience' => 'everyone', 'category' => 'general'], Messages::ANNOUNCEMENT_TITLE_REQUIRED],
            [['title' => 'Title', 'body' => '  ', 'audience' => 'everyone', 'category' => 'general'], Messages::ANNOUNCEMENT_BODY_REQUIRED],
            [['title' => 'Title', 'body' => 'Body', 'audience' => 'outsiders', 'category' => 'general'], Messages::ANNOUNCEMENT_AUDIENCE_INVALID],
            [['title' => 'Title', 'body' => 'Body', 'audience' => 'everyone', 'category' => 'private'], Messages::ANNOUNCEMENT_CATEGORY_INVALID],
        ];

        foreach ($cases as [$data, $message]) {
            $this->authAsAdmin();
            $this->post($this->schoolPath('/announcements'), ['data' => $data, 'publish' => false]);

            $this->assertResponseCode(422);
            $this->assertSame($message, $this->responseJson()['message']);
        }

        $this->assertSame(0, $this->rowCount('ems_announcements', ['school_id' => $this->schoolId]));
    }

    public function testPreviewAndDeliveryRejectInvalidChannelAndPurpose(): void
    {
        $announcementId = $this->seedAnnouncement();

        $this->authAsAdmin();
        $this->get($this->schoolPath('/announcements/audience-preview?audience=everyone&channel=push&purpose=school_news'));
        $this->assertResponseCode(422);
        $this->assertSame(Messages::COMMS_CHANNEL_INVALID, $this->responseJson()['message']);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/announcements/' . $announcementId . '/deliver'), [
            'channel' => 'email',
            'purpose' => 'marketing',
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::COMMS_PURPOSE_INVALID, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_message_recipients', ['school_id' => $this->schoolId]));
    }

    public function testPreferenceAndAlertSendRejectInvalidEnums(): void
    {
        $teacherId = Text::uuid();
        $this->insertRow('ems_teachers', [
            'id' => $teacherId,
            'school_id' => $this->schoolId,
            'staff_number' => 'STF-001',
            'first_name' => 'Tess',
            'last_name' => 'Teacher',
            'email' => 'tess@test.school',
            'phone' => '08030000000',
            'gender' => 'female',
            'subjects' => json_encode([]),
            'status' => 'active',
            'hired_on' => '2024-01-01',
        ]);
        $teacherUserId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $teacherUserId,
            'school_id' => $this->schoolId,
            'name' => 'Tess Teacher',
            'email' => 'teacher-user@test.school',
            'role' => 'teacher',
            'status' => 'active',
            'added_on' => $this->now(),
            'link_kind' => 'teacher',
            'link_teacher_id' => $teacherId,
        ]);

        $this->authAs('teacher', $teacherUserId, 'Tess Teacher');
        $this->put($this->schoolPath('/me/preferences'), [
            'channel' => 'push',
            'purpose' => 'school_news',
            'enabled' => true,
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::COMMS_CHANNEL_INVALID, $this->responseJson()['message']);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/alerts/send'), ['kind' => 'fee_overdue', 'channel' => 'push']);
        $this->assertResponseCode(422);
        $this->assertSame(Messages::COMMS_CHANNEL_INVALID, $this->responseJson()['message']);
    }

    public function testDeliveryAndRetryReturnNotFoundForUnknownAnnouncement(): void
    {
        $unknown = Text::uuid();

        $this->authAsAdmin();
        $this->get($this->schoolPath('/announcements/' . $unknown . '/delivery'));
        $this->assertResponseCode(404);
        $this->assertSame(Messages::ANNOUNCEMENT_NOT_FOUND, $this->responseJson()['message']);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/announcements/' . $unknown . '/delivery/retry'), []);
        $this->assertResponseCode(404);
        $this->assertSame(Messages::ANNOUNCEMENT_NOT_FOUND, $this->responseJson()['message']);
    }

    public function testAlertSendCreatesRecipientDeliveryRowsAndReportsActualSuccesses(): void
    {
        $studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $studentId,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM-001',
            'first_name' => 'Ayo',
            'last_name' => 'Student',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'enrolled_on' => '2025-09-01',
        ]);
        $guardianId = Text::uuid();
        $this->insertRow('ems_guardians', [
            'id' => $guardianId,
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'first_name' => 'Pat',
            'last_name' => 'Parent',
            'relationship' => 'parent',
            'phone' => '08030000000',
            'email' => 'pat.parent@test.school',
            'is_primary' => 1,
        ]);
        $this->insertRow('ems_invoices', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'invoice_number' => 'INV-001',
            'student_id' => $studentId,
            'student_name' => 'Ayo Student',
            'class_group' => 'JSS 1A',
            'session' => '2025/2026',
            'term' => 'First',
            'issued_on' => '2025-09-01',
            'due_date' => date('Y-m-d', strtotime('-7 days')),
            'line_items' => json_encode([['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge']]),
            'total' => 100000,
            'status' => 'issued',
        ]);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/alerts/send'), ['kind' => 'fee_overdue', 'channel' => 'email']);

        $this->assertResponseOk();
        $notification = $this->responseJson();
        $this->assertSame('fee_reminder', $notification['kind']);
        $this->assertSame(
            $this->rowCount('ems_message_recipients', [
                'school_id' => $this->schoolId,
                'status' => 'sent',
            ]),
            $notification['recipientCount']
        );
        $this->assertSame(1, $this->rowCount('ems_message_recipients', [
            'school_id' => $this->schoolId,
            'person_id' => $guardianId,
        ]));
    }

    public function testEmptyAudienceCanOnlyBeDeliveredOnce(): void
    {
        $announcementId = $this->seedAnnouncement();
        $this->db->update('ems_announcements', ['audience' => 'teachers'], ['id' => $announcementId]);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/announcements/' . $announcementId . '/deliver'), [
            'channel' => 'email',
            'purpose' => 'transactional',
        ]);
        $this->assertResponseOk();
        $this->assertSame(0, $this->responseJson()['total']);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/announcements/' . $announcementId . '/deliver'), [
            'channel' => 'email',
            'purpose' => 'transactional',
        ]);
        $this->assertResponseCode(409);
        $this->assertSame(Messages::DELIVER_ALREADY_SENT, $this->responseJson()['message']);
        $this->assertSame(1, $this->rowCount('ems_notifications', [
            'school_id' => $this->schoolId,
            'kind' => 'announcement',
        ]));
    }

    public function testMultiChildHouseholdIsCountedOnceAndSharesConsent(): void
    {
        [$firstStudent, $firstGuardian] = $this->seedStudentAndGuardian(
            'Ayo',
            'Alex',
            'Parent',
            'family@test.school'
        );
        [$secondStudent, $secondGuardian] = $this->seedStudentAndGuardian(
            'Bola',
            'Pat',
            'Parent',
            'family@test.school'
        );
        $parentUserId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $parentUserId,
            'school_id' => $this->schoolId,
            'name' => 'Pat Parent',
            'email' => 'family@test.school',
            'role' => 'parent',
            'status' => 'active',
            'added_on' => $this->now(),
            'link_kind' => 'parent',
            'link_student_ids' => json_encode([$firstStudent, $secondStudent]),
        ]);

        $this->authAs('parent', $parentUserId, 'Pat Parent');
        $this->put($this->schoolPath('/me/preferences'), [
            'channel' => 'email',
            'purpose' => 'school_news',
            'enabled' => true,
        ]);
        $this->assertResponseOk();
        $this->assertSame($secondGuardian, $this->responseJson()['personId']);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/announcements/audience-preview?audience=parents&channel=email&purpose=school_news'));
        $this->assertResponseOk();
        $preview = $this->responseJson();
        $this->assertSame(1, $preview['total']);
        $this->assertSame(1, $preview['reachable']);
        $this->assertSame(0, $preview['suppressed']);
        $this->assertSame(0, $this->rowCount('ems_contact_preferences', [
            'school_id' => $this->schoolId,
            'person_id' => $firstGuardian,
        ]));
    }

    private function seedAnnouncement(): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_announcements', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'title' => 'Open day',
            'body' => 'Come along.',
            'audience' => 'everyone',
            'category' => 'event',
            'status' => 'published',
            'author_name' => 'Ada Admin',
            'created_on' => date('Y-m-d'),
            'published_on' => date('Y-m-d'),
            'pinned' => 0,
        ]);

        return $id;
    }

    /** @return array{0:string,1:string} */
    private function seedStudentAndGuardian(
        string $studentFirstName,
        string $guardianFirstName,
        string $guardianLastName,
        string $email
    ): array {
        $studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $studentId,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM-' . substr($studentId, 0, 6),
            'first_name' => $studentFirstName,
            'last_name' => 'Student',
            'date_of_birth' => '2015-01-01',
            'gender' => 'female',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'enrolled_on' => '2025-09-01',
        ]);
        $guardianId = Text::uuid();
        $this->insertRow('ems_guardians', [
            'id' => $guardianId,
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'first_name' => $guardianFirstName,
            'last_name' => $guardianLastName,
            'relationship' => 'parent',
            'phone' => '08030000000',
            'email' => $email,
            'is_primary' => 1,
        ]);

        return [$studentId, $guardianId];
    }
}
