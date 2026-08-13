<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * The per-student register note (§3.12). A note travels the full loop: written
 * on the register PUT, read back on the register GET, surfaced on the student's
 * attendance read model, and CLEARED when re-submitted empty. The note column
 * always existed and was shown to families; this proves the register write/read
 * path now sets and returns it.
 */
final class RegisterNoteTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_attendance_records',
        'ems_attendance_sessions',
        'ems_students',
        'ems_class_groups',
        'ems_users',
        'ems_schools',
    ];

    private string $classId = '';
    private string $studentId = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->classId = $this->seedClass('JSS 1A');
        $this->studentId = $this->seedStudent('JSS 1A', 'Ada', 'Learner');
    }

    public function testRegisterNoteIsStoredReturnedAndCleared(): void
    {
        $date = '2025-11-05';

        // 1. Submit the register with a per-student note. (configRequest is
        // per-request in this harness, so each call re-authenticates.)
        $this->authAsAdmin();
        $this->put($this->schoolPath('/classes/' . $this->classId . '/register?date=' . $date), [
            'entries' => [
                ['studentId' => $this->studentId, 'status' => 'late', 'note' => 'Arrived 20 minutes late'],
            ],
        ]);
        $this->assertResponseCode(204);

        // 2. The register GET returns the note alongside the status.
        $row = $this->registerRow($date);
        $this->assertSame('late', $row['status']);
        $this->assertSame('Arrived 20 minutes late', $row['note']);

        // 3. The note also reaches the student's attendance read model.
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $this->studentId . '/attendance'));
        $this->assertResponseOk();
        $records = $this->responseJson()['records'];
        $this->assertSame('Arrived 20 minutes late', $records[0]['note']);

        // 4. Re-submitting empty (a correction) clears the stored note.
        $this->authAsAdmin();
        $this->put($this->schoolPath('/classes/' . $this->classId . '/register?date=' . $date), [
            'entries' => [['studentId' => $this->studentId, 'status' => 'present', 'note' => '']],
            'reason' => 'Marked present in error earlier',
        ]);
        $this->assertResponseCode(204);

        $row = $this->registerRow($date);
        $this->assertSame('present', $row['status']);
        $this->assertSame('', $row['note'], 'an emptied note is cleared, not left stale');
    }

    /** The register row for the seeded student on a date. */
    private function registerRow(string $date): array
    {
        $this->authAsAdmin();
        $this->get($this->schoolPath('/classes/' . $this->classId . '/register?date=' . $date));
        $this->assertResponseOk();
        foreach ($this->responseJson()['rows'] as $row) {
            if (($row['student']['id'] ?? '') === $this->studentId) {
                return $row;
            }
        }
        $this->fail('The seeded student was not on the register roster.');
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
}
