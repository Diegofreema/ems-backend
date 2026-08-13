<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Utility\Text;

/**
 * The two dashboard aggregates: GET /dashboard (staff, OFFICER tier) and
 * GET /portal/dashboard (family home). Covers the capability boundary from
 * both sides — staff cannot use the family home, family cannot use the staff
 * dashboard — and that each payload's figures derive from the seeded rows.
 */
class DashboardTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_finance_ledger_events',
        'ems_timetable_slots',
        'ems_attendance_records',
        'ems_attendance_sessions',
        'ems_payments',
        'ems_invoices',
        'ems_admission_applications',
        'ems_admission_cycles',
        'ems_exam_schedules',
        'ems_exams',
        'ems_announcements',
        'ems_academic_terms',
        'ems_academic_sessions',
        'ems_incidents',
        'ems_class_groups',
        'ems_subjects',
        'ems_teachers',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    // --- staff dashboard -----------------------------------------------------

    public function testStaffDashboardAggregatesTheSeededSchool(): void
    {
        $today = date('Y-m-d');

        // People: two enrolled, one applicant, one active teacher, one class.
        $s1 = $this->seedStudent(['first_name' => 'Ada', 'class_group' => 'JSS 1A']);
        $s2 = $this->seedStudent(['first_name' => 'Bola', 'class_group' => 'JSS 1A']);
        $this->seedStudent(['first_name' => 'Cal', 'status' => 'applicant']);
        $this->insertRow('ems_teachers', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId,
            'staff_number' => 'STF-001', 'first_name' => 'Tess', 'last_name' => 'Teacher',
            'email' => 'tess@test.school', 'phone' => '', 'gender' => 'female',
            'subjects' => json_encode([]), 'status' => 'active', 'hired_on' => '2024-01-01',
        ]);
        $classGroupId = Text::uuid();
        $this->insertRow('ems_class_groups', [
            'id' => $classGroupId, 'school_id' => $this->schoolId,
            'name' => 'JSS 1A', 'level' => 'JSS 1', 'capacity' => 30,
        ]);

        // Today's register: one class submitted; present + absent marks.
        $this->insertRow('ems_attendance_sessions', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId,
            'class_group_id' => $classGroupId, 'date' => $today,
            'submitted_by' => 'Tess Teacher', 'submitted_on' => $this->now(),
        ]);
        $this->mark($s1, $today, 'present');
        $this->mark($s2, $today, 'absent');

        // Calendar: an open session whose First term brackets today.
        $sessionId = Text::uuid();
        $this->insertRow('ems_academic_sessions', [
            'id' => $sessionId, 'school_id' => $this->schoolId, 'name' => '2025/2026',
            'starts_on' => '2025-09-01', 'ends_on' => '2026-07-31', 'status' => 'open',
        ]);
        $this->insertRow('ems_academic_terms', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'session_id' => $sessionId,
            'name' => 'First', 'starts_on' => date('Y-m-d', strtotime('-30 days')),
            'ends_on' => date('Y-m-d', strtotime('+30 days')), 'status' => 'open',
        ]);

        // Fees: a First-term invoice of 100 000 with 40 000 collected.
        $inv = $this->seedInvoice($s1, ['term' => 'First', 'total' => 100000]);
        $this->seedPayment($inv, $s1, 40000);

        // Admissions: one open cycle, one submitted application.
        $cycleId = Text::uuid();
        $this->insertRow('ems_admission_cycles', [
            'id' => $cycleId, 'school_id' => $this->schoolId, 'name' => '2026 Entry',
            'session' => '2025/2026', 'opens_on' => '2025-01-01', 'closes_on' => '2026-12-31',
            'status' => 'open',
        ]);
        $this->insertRow('ems_admission_applications', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'cycle_id' => $cycleId,
            'application_number' => 'APP-0001', 'first_name' => 'New', 'last_name' => 'Applicant',
            'desired_level' => 'JSS 1', 'guardian' => json_encode(['name' => 'G', 'phone' => '']),
            'submitted_on' => $today, 'status' => 'submitted',
        ]);

        // An upcoming sitting tomorrow, and one open incident.
        $examId = $this->seedExam('Mid-term Test');
        $this->seedSitting($examId, date('Y-m-d', strtotime('+1 day')));
        $this->insertRow('ems_incidents', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'reference' => 'INC-0001',
            'title' => 'Register anomaly', 'description' => 'x', 'severity' => 'low',
            'data_categories' => json_encode([]), 'status' => 'recorded',
            'discovered_on' => $today, 'recorded_on' => $today, 'recorded_by' => 'Ada Admin',
            'responders' => json_encode([]), 'entries' => json_encode([]),
        ]);

        // A published announcement.
        $this->insertRow('ems_announcements', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId,
            'title' => 'PTA meeting', 'body' => 'Saturday.', 'audience' => 'everyone',
            'category' => 'general', 'status' => 'published', 'author_name' => 'Ada Admin',
            'created_on' => $today, 'published_on' => $today, 'pinned' => 0,
        ]);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/dashboard'));
        $this->assertResponseOk();
        $body = $this->responseJson();

        $this->assertSame(2, $body['people']['enrolled']);
        $this->assertSame(1, $body['people']['applicants']);
        $this->assertSame(1, $body['people']['activeTeachers']);
        $this->assertSame(1, $body['people']['classCount']);

        $this->assertSame(2, $body['attendanceToday']['total']);
        $this->assertSame(1, $body['attendanceToday']['present']);
        $this->assertSame(1, $body['attendanceToday']['absent']);
        $this->assertSame(1, $body['attendanceToday']['registersSubmitted']);

        $this->assertSame('First', $body['term']['name']);
        $this->assertSame('2025/2026', $body['term']['session']);

        $this->assertSame('First', $body['fees']['term']);
        $this->assertSame(100000, $body['fees']['invoiced']);
        $this->assertSame(40000, $body['fees']['collected']);
        $this->assertSame(60000, $body['fees']['outstanding']);

        $this->assertSame(1, $body['admissions']['openCycles']);
        $this->assertSame(1, $body['admissions']['pendingReview']);
        $this->assertSame(1, $body['admissions']['pipeline']['submitted']);

        $this->assertCount(1, $body['upcomingSittings']);
        $this->assertSame('Mid-term Test', $body['upcomingSittings'][0]['examTitle']);
        $this->assertCount(1, $body['announcements']);
        $this->assertSame('PTA meeting', $body['announcements'][0]['title']);
        $this->assertSame(1, $body['openIncidents']);
        $this->assertCount(1, $body['attendanceTrend']);
    }

    public function testFamilyAndTeacherCannotReadTheStaffDashboard(): void
    {
        foreach (['teacher', 'parent', 'student'] as $role) {
            $this->authAs($role, Text::uuid(), ucfirst($role) . ' User');
            $this->get($this->schoolPath('/dashboard'));
            $this->assertResponseCode(403);
            $this->assertSame(Messages::ANALYTICS_FORBIDDEN, $this->responseJson()['message']);
        }
    }

    // --- family dashboard ----------------------------------------------------

    public function testParentDashboardListsWardsInLinkOrder(): void
    {
        $w1 = $this->seedStudent(['first_name' => 'Zara', 'class_group' => 'JSS 1A']);
        $w2 = $this->seedStudent(['first_name' => 'Yemi', 'class_group' => 'JSS 1A']);
        $this->insertRow('ems_class_groups', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId,
            'name' => 'JSS 1A', 'level' => 'JSS 1', 'capacity' => 30,
        ]);
        // Family-audience announcement; a staff-only one must not leak.
        $today = date('Y-m-d');
        foreach ([['For parents', 'parents'], ['For staff', 'teachers']] as [$title, $audience]) {
            $this->insertRow('ems_announcements', [
                'id' => Text::uuid(), 'school_id' => $this->schoolId,
                'title' => $title, 'body' => 'x', 'audience' => $audience,
                'category' => 'general', 'status' => 'published', 'author_name' => 'Ada Admin',
                'created_on' => $today, 'published_on' => $today, 'pinned' => 0,
            ]);
        }

        $parentId = Text::uuid();
        $this->seedLinkedUser($parentId, 'parent', ['link_student_ids' => json_encode([$w2, $w1])]);
        $this->authAs('parent', $parentId, 'Pat Parent');

        $this->get($this->schoolPath('/portal/dashboard'));
        $this->assertResponseOk();
        $body = $this->responseJson();

        $this->assertCount(2, $body['wards']);
        // Link order, not query order.
        $this->assertSame($w2, $body['wards'][0]['student']['id']);
        $this->assertSame($w1, $body['wards'][1]['student']['id']);
        $this->assertArrayHasKey('attendance', $body['wards'][0]);
        $this->assertArrayHasKey('fees', $body['wards'][0]);
        $this->assertArrayHasKey('todayPeriods', $body['wards'][0]);
        $this->assertNotNull($body['wards'][0]['classGroupId']);

        $titles = array_column($body['announcements'], 'title');
        $this->assertContains('For parents', $titles);
        $this->assertNotContains('For staff', $titles);
    }

    public function testStudentDashboardSeesOnlyThemself(): void
    {
        $self = $this->seedStudent(['first_name' => 'Solo']);
        $this->seedStudent(['first_name' => 'Other']);
        $studentUser = Text::uuid();
        $this->seedLinkedUser($studentUser, 'student', ['link_student_id' => $self]);
        $this->authAs('student', $studentUser, 'Solo Student');

        $this->get($this->schoolPath('/portal/dashboard'));
        $this->assertResponseOk();
        $body = $this->responseJson();

        $this->assertCount(1, $body['wards']);
        $this->assertSame($self, $body['wards'][0]['student']['id']);
    }

    public function testStaffCannotUseTheFamilyDashboard(): void
    {
        foreach (['administrator', 'registrar', 'bursar', 'teacher'] as $role) {
            $userId = $role === 'administrator' ? $this->adminId : Text::uuid();
            $this->authAs($role, $userId, ucfirst($role) . ' User');
            $this->get($this->schoolPath('/portal/dashboard'));
            $this->assertResponseCode(403);
            $this->assertSame(Messages::ACTION_FORBIDDEN, $this->responseJson()['message']);
        }
    }

    // --- seed helpers --------------------------------------------------------

    private function seedStudent(array $over = []): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM-' . substr($id, 0, 6),
            'first_name' => $over['first_name'] ?? 'Kid',
            'last_name' => 'Test',
            'date_of_birth' => '2015-01-01',
            'gender' => 'female',
            'class_group' => $over['class_group'] ?? 'JSS 1A',
            'status' => $over['status'] ?? 'enrolled',
            'enrolled_on' => '2025-09-01',
        ]);

        return $id;
    }

    /** An active user row carrying its person link (authAs will find it). */
    private function seedLinkedUser(string $userId, string $role, array $link): void
    {
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => ucfirst($role) . ' Linked',
            'email' => 'link-' . substr($userId, 0, 12) . '@seed.test',
            'role' => $role,
            'status' => 'active',
            'added_on' => $this->now(),
            'link_kind' => $role,
        ] + $link);
    }

    private function seedInvoice(string $studentId, array $over = []): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_invoices', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'invoice_number' => 'INV-' . substr($id, 0, 6),
            'student_id' => $studentId,
            'student_name' => 'Kid Test',
            'class_group' => 'JSS 1A',
            'session' => '2025/2026',
            'term' => $over['term'] ?? 'First',
            'issued_on' => '2025-09-01',
            'due_date' => $over['due_date'] ?? date('Y-m-d', strtotime('+14 days')),
            'line_items' => json_encode([['name' => 'Tuition', 'amount' => $over['total'] ?? 100000, 'kind' => 'charge']]),
            'total' => $over['total'] ?? 100000,
            'status' => 'issued',
        ]);

        return $id;
    }

    private function seedPayment(string $invoiceId, string $studentId, int $amount): void
    {
        $id = Text::uuid();
        $this->insertRow('ems_payments', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'invoice_id' => $invoiceId,
            'student_id' => $studentId,
            'receipt_number' => 'RCP-' . substr($id, 0, 6),
            'amount' => $amount,
            'method' => 'cash',
            'paid_on' => date('Y-m-d'),
            'state' => 'completed',
        ]);
        $this->db->insert('ems_finance_ledger_events', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'invoice_id' => $invoiceId,
            'student_id' => $studentId, 'payment_id' => $id, 'event_type' => 'payment',
            'amount' => $amount, 'provenance' => 'test', 'key_id' => 'test-key-1',
            'previous_hash' => str_repeat('0', 64), 'event_hash' => str_repeat('a', 64),
            'occurred_at' => $this->now(),
        ]);
    }

    private function seedExam(string $title): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_exams', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'title' => $title,
            'session' => '2025/2026',
            'term' => 'First',
            'start_date' => date('Y-m-d', strtotime('+1 day')),
            'end_date' => date('Y-m-d', strtotime('+5 days')),
            'status' => 'scheduled',
            'ca_max' => 40,
            'exam_max' => 60,
        ]);

        return $id;
    }

    private function seedSitting(string $examId, string $date): void
    {
        $subjectId = Text::uuid();
        $this->insertRow('ems_subjects', [
            'id' => $subjectId, 'school_id' => $this->schoolId,
            'name' => 'Subject ' . substr($subjectId, 0, 8), 'active' => 1,
        ]);
        $this->insertRow('ems_exam_schedules', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'exam_id' => $examId,
            'subject_id' => $subjectId,
            'level' => 'JSS 1',
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'venue' => 'Hall A',
        ]);
    }

    private function mark(string $studentId, string $date, string $status): void
    {
        $this->insertRow('ems_attendance_records', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'date' => $date,
            'status' => $status,
        ]);
    }
}
