<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Utility\Text;

/**
 * Pins the register's archival hard-rule at the HTTP boundary: a school can
 * never destroy a student or student-related data.
 *
 *  - DELETE /students/{id} is always refused (409), whether the student has
 *    dependent history or not — the record survives.
 *  - DELETE /guardians/{id} soft-archives: the row is stamped archived_at and
 *    hidden from reads, but is NOT destroyed.
 */
class StudentGuardianDeleteTest extends EmsIntegrationTestCase
{
    private function seedStudent(array $overrides = []): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM-' . substr($id, 0, 6),
            'first_name' => 'Sam',
            'last_name' => 'Student',
            'date_of_birth' => '2012-05-01',
            'gender' => 'male',
            'enrolled_on' => '2026-01-10',
        ] + $overrides);

        return $id;
    }

    private function seedGuardian(string $studentId, array $overrides = []): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_guardians', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'first_name' => 'Gina',
            'last_name' => 'Guardian',
            'relationship' => 'mother',
            'is_primary' => 1,
        ] + $overrides);

        return $id;
    }

    public function testStudentWithDependentsCannotBeDeleted(): void
    {
        $studentId = $this->seedStudent();
        // A dependent record — an enrolment — makes this the "has history" case.
        $this->insertRow('ems_enrolments', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'session' => '2026/2027',
            'class_group' => 'JSS 1A',
            'level' => 'JSS 1',
            'started_on' => '2026-01-10',
            'status' => 'active',
        ]);

        $this->authAsAdmin();
        $this->delete($this->schoolPath('/students/' . $studentId));

        $this->assertResponseCode(409);
        $this->assertSame(Messages::STUDENT_HAS_RECORDS, $this->responseJson()['message']);
        $this->assertTrue(
            $this->rowExists('ems_students', ['id' => $studentId]),
            'The student record must survive a refused delete.',
        );
    }

    public function testStudentWithNoDependentsStillCannotBeDeleted(): void
    {
        $studentId = $this->seedStudent();

        $this->authAsAdmin();
        $this->delete($this->schoolPath('/students/' . $studentId));

        // The rule is unconditional: even a dependency-free student is undeletable.
        $this->assertResponseCode(409);
        $this->assertSame(Messages::STUDENT_HAS_RECORDS, $this->responseJson()['message']);
        $this->assertTrue($this->rowExists('ems_students', ['id' => $studentId]));
    }

    public function testGuardianDeleteArchivesRatherThanDestroys(): void
    {
        $studentId = $this->seedStudent();
        $guardianId = $this->seedGuardian($studentId);

        $this->authAsAdmin();
        $this->delete($this->schoolPath('/guardians/' . $guardianId));

        $this->assertResponseCode(204);
        // The row is NOT gone...
        $this->assertTrue(
            $this->rowExists('ems_guardians', ['id' => $guardianId]),
            'A deleted guardian must be archived, not destroyed.',
        );
        // ...it is stamped archived_at...
        $this->assertTrue(
            $this->rowExists('ems_guardians', ['id' => $guardianId, 'archived_at IS NOT' => null]),
            'The guardian row must carry an archived_at stamp.',
        );

        // ...and it has vanished from the guardians read (default scope hides it).
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $studentId . '/guardians'));
        $this->assertResponseOk();
        $this->assertSame([], $this->responseJson());
    }

    public function testDeletingPrimaryGuardianPromotesTheNextActiveSibling(): void
    {
        $studentId = $this->seedStudent();
        $primaryId = $this->seedGuardian($studentId, ['first_name' => 'Aaron', 'is_primary' => 1]);
        $siblingId = $this->seedGuardian($studentId, ['first_name' => 'Bea', 'is_primary' => 0]);

        $this->authAsAdmin();
        $this->delete($this->schoolPath('/guardians/' . $primaryId));
        $this->assertResponseCode(204);

        // The archived primary is demoted; the remaining active sibling is now primary.
        $this->assertTrue($this->rowExists('ems_guardians', ['id' => $primaryId, 'is_primary' => 0]));
        $this->assertTrue($this->rowExists('ems_guardians', ['id' => $siblingId, 'is_primary' => 1]));
    }
}
