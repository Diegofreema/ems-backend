<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Utility\Text;

/**
 * Regression for the teacher cross-class read gap (security review finding #5),
 * the same read/write inconsistency as the roster IDOR: `Classes.register`,
 * `Exams.gradesheet` and `Exams.broadsheet` are `ACADEMIC`-tier reads a teacher
 * token passes, but they resolved the class through the tenant-only
 * `findClass()` and never asserted viewer scope — while their write twins
 * (`saveRegister`, `saveGrades`) did. A teacher could therefore read the
 * attendance register and grade/broadsheet marks of ANY class in the school,
 * not just the ones they teach. The fix routes those reads through
 * `findClassScoped()` / adds `assertClassAccess`.
 *
 * A teacher's scope is the classes where they are form teacher (or hold a
 * subject allocation / timetable slot). Here the teacher forms JSS 1A only, so
 * JSS 1B must be refused.
 */
final class TeacherClassScopeTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_exam_grades',
        'ems_exams',
        'ems_attendance_records',
        'ems_attendance_sessions',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_class_groups',
        'ems_teachers',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    private string $ownClassId = '';
    private string $otherClassId = '';
    private string $teacherId = '';
    private string $examId = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacherId = Text::uuid();
        $this->insertRow('ems_teachers', [
            'id' => $this->teacherId,
            'school_id' => $this->schoolId,
            'staff_number' => 'STF-001',
            'first_name' => 'Tunde',
            'last_name' => 'Teacher',
            'email' => 'tunde.teacher@test.school',
            'phone' => '08050000000',
            'gender' => 'male',
            'status' => 'active',
            'hired_on' => '2024-09-01',
        ]);

        // The teacher forms JSS 1A; JSS 1B is someone else's class.
        $this->ownClassId = $this->seedClass('JSS 1A', $this->teacherId);
        $this->otherClassId = $this->seedClass('JSS 1B', null);
        $this->seedStudent('JSS 1B', 'Bode', 'Other');

        // The teacher's account, linked to the teacher record so Scope resolves
        // their class list.
        $this->insertRow('ems_users', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => 'Tunde Teacher',
            'email' => 'tunde.user@test.school',
            'role' => 'teacher',
            'status' => 'active',
            'added_on' => $this->now(),
            'link_kind' => 'teacher',
            'link_teacher_id' => $this->teacherId,
        ]);

        $this->examId = Text::uuid();
        $this->insertRow('ems_exams', [
            'id' => $this->examId,
            'school_id' => $this->schoolId,
            'title' => 'First Term Examination',
            'session' => '2025/2026',
            'term' => 'first',
            'start_date' => '2025-11-01',
            'end_date' => '2025-11-10',
            'status' => 'draft',
            'ca_max' => 40,
            'exam_max' => 60,
        ]);
    }

    private function authAsTeacher(): void
    {
        $userId = (string)$this->db->selectQuery()
            ->select(['id'])->from('ems_users')
            ->where(['role' => 'teacher'])
            ->execute()->fetch('assoc')['id'];
        $this->authAs('teacher', $userId, 'Tunde Teacher');
    }

    public function testTeacherCannotReadAnotherClassRegister(): void
    {
        $this->authAsTeacher();
        $this->get($this->schoolPath(
            '/classes/' . $this->otherClassId . '/register?date=2025-11-05',
        ));

        $this->assertResponseCode(403);
        $this->assertSame(Messages::CLASS_FORBIDDEN, $this->responseJson()['message']);
    }

    public function testTeacherCanStillReadTheirOwnClassRegister(): void
    {
        // No over-restriction: the teacher's own class register still opens.
        $this->authAsTeacher();
        $this->get($this->schoolPath(
            '/classes/' . $this->ownClassId . '/register?date=2025-11-05',
        ));

        $this->assertResponseOk();
        $this->assertSame('2025-11-05', $this->responseJson()['date']);
    }

    public function testTeacherCannotReadAnotherClassGradesheet(): void
    {
        $this->authAsTeacher();
        $this->get($this->schoolPath(
            '/exams/' . $this->examId . '/gradesheet?classId=' . $this->otherClassId . '&subject=Maths',
        ));

        $this->assertResponseCode(403);
        $this->assertSame(Messages::CLASS_FORBIDDEN, $this->responseJson()['message']);
    }

    public function testTeacherCannotReadAnotherClassBroadsheet(): void
    {
        $this->authAsTeacher();
        $this->get($this->schoolPath(
            '/exams/' . $this->examId . '/broadsheet?classId=' . $this->otherClassId,
        ));

        $this->assertResponseCode(403);
        $this->assertSame(Messages::CLASS_FORBIDDEN, $this->responseJson()['message']);
    }

    public function testAdministratorCanStillReadAnyClassRegister(): void
    {
        // Officer scope is whole-school: the fix leaves staff access untouched.
        $this->authAsAdmin();
        $this->get($this->schoolPath(
            '/classes/' . $this->otherClassId . '/register?date=2025-11-05',
        ));

        $this->assertResponseOk();
    }

    private function seedClass(string $name, ?string $formTeacherId): string
    {
        $id = Text::uuid();
        $row = [
            'id' => $id,
            'school_id' => $this->schoolId,
            'name' => $name,
            'level' => $name,
            'capacity' => 30,
        ];
        if ($formTeacherId !== null) {
            $row['form_teacher_id'] = $formTeacherId;
        }
        $this->insertRow('ems_class_groups', $row);

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
            'gender' => 'male',
            'class_group' => $classGroup,
            'status' => 'enrolled',
            'guardian_name' => 'Guardian ' . $lastName,
            'guardian_phone' => '08040000000',
            'enrolled_on' => '2025-09-01',
        ]);

        return $id;
    }
}
