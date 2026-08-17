<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * The `staff` CSV import kind (document.md §3.17). A registrar migrating from
 * another EMS brings the staff directory onto `ems_teachers` the same staged,
 * duplicate-reviewed way students and guardians arrive: a file is checked, held
 * rows wait for a decision, and only a commit writes the register. Staff numbers
 * auto-allocate when blank; subject names resolve against the school catalogue;
 * this is the directory, never login access. Companion to the students/guardians
 * paths exercised elsewhere.
 */
final class StaffImportTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_import_rows',
        'ems_import_batches',
        'ems_audit_events',
        'ems_teachers',
        'ems_subjects',
        'ems_users',
        'ems_schools',
    ];

    private string $registrarId;
    private string $mathId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrarId = Text::uuid();
        // Only subjects already in the catalogue may be assigned on import.
        $this->mathId = $this->seedSubject('Mathematics');
        $this->seedSubject('Physics');
    }

    public function testTemplateListsTheStaffColumns(): void
    {
        $this->authAs('registrar', $this->registrarId, 'Rita Registrar');
        $this->get($this->schoolPath('/imports/template?kind=staff'));
        $this->assertResponseOk();

        $body = $this->responseJson();
        $this->assertSame('staff-import-template.csv', $body['filename']);
        $this->assertStringContainsString('Staff import template', $body['content']);
        foreach (['staff_number', 'first_name', 'last_name', 'subjects', 'hired_on'] as $column) {
            $this->assertStringContainsString($column, $body['content']);
        }
    }

    public function testCommitCreatesStaffAllocatesNumbersAndResolvesSubjects(): void
    {
        // Grace states her own number and two catalogue subjects; Henry leaves
        // everything but his name blank — the school fills in the rest.
        $csv = "staff_number,first_name,last_name,email,phone,gender,subjects,status,hired_on\n"
            . "STF/010,Grace,Green,grace@school.test,+234 803 1112223,female,Mathematics; Physics,active,2024-09-01\n"
            . ',Henry,Hill,,,,,,';

        $preview = $this->upload($csv);
        $this->assertResponseCode(201);
        $this->assertCount(2, $preview['rows']);
        $this->assertSame('valid', $preview['rows'][0]['check']);
        $this->assertSame('valid', $preview['rows'][1]['check']);

        $this->commit((string)$preview['batch']['id']);
        $this->assertResponseOk();
        $committed = $this->responseJson();
        $this->assertSame(2, $committed['batch']['result']['created']);
        $this->assertSame(2, $this->rowCount('ems_teachers', ['school_id' => $this->schoolId]));

        $grace = $this->teacher('Grace');
        $this->assertSame('STF/010', $grace['staff_number']);
        $this->assertSame('active', $grace['status']);
        $this->assertSame('2024-09-01', substr((string)$grace['hired_on'], 0, 10));
        $subjects = (array)json_decode((string)$grace['subjects'], true);
        $this->assertContains($this->mathId, $subjects, 'Subject names resolve to catalogue ids.');
        $this->assertCount(2, $subjects);

        // A blank number is allocated after the highest one already present.
        $henry = $this->teacher('Henry');
        $this->assertSame('STF/011', $henry['staff_number']);
        $this->assertSame('active', $henry['status']);
        $this->assertSame(date('Y-m-d'), substr((string)$henry['hired_on'], 0, 10));
    }

    public function testUnknownSubjectIsFlaggedAndRejectedOnCommit(): void
    {
        $csv = "first_name,last_name,subjects\nIvy,Ike,Astrophysics";

        $preview = $this->upload($csv);
        $this->assertResponseCode(201);
        $this->assertSame('invalid', $preview['rows'][0]['check']);
        $this->assertSame('subjects', $preview['rows'][0]['issues'][0]['column']);

        // A commit still succeeds; the unusable row is rejected, nothing written.
        $this->commit((string)$preview['batch']['id']);
        $this->assertResponseOk();
        $result = $this->responseJson()['batch']['result'];
        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['rejected']);
        $this->assertSame(0, $this->rowCount('ems_teachers', ['school_id' => $this->schoolId]));
    }

    public function testDuplicateStaffNumberIsHeldThenMergedNonDestructively(): void
    {
        $existingId = Text::uuid();
        $this->insertRow('ems_teachers', [
            'id' => $existingId,
            'school_id' => $this->schoolId,
            'staff_number' => 'STF/001',
            'first_name' => 'Grace',
            'last_name' => 'Green',
            'email' => 'grace@old.test',
            'phone' => '',
            'gender' => 'female',
            'status' => 'active',
            'hired_on' => '2020-01-01',
        ]);

        // Same staff number → held for review, not written blind.
        $csv = "staff_number,first_name,last_name,email\nSTF/001,Grace,Green,grace@new.test";
        $preview = $this->upload($csv);
        $this->assertResponseCode(201);
        $row = $preview['rows'][0];
        $this->assertSame('duplicate', $row['check']);
        $this->assertSame($existingId, $row['matches'][0]['targetId']);

        // Committing with the question still open is refused.
        $batchId = (string)$preview['batch']['id'];
        $this->commit($batchId);
        $this->assertResponseCode(422);

        // Point the row at the existing record and commit: a non-destructive
        // merge updates the e-mail and keeps the one directory row.
        $this->authAs('registrar', $this->registrarId, 'Rita Registrar');
        $this->put(
            $this->schoolPath("/imports/{$batchId}/rows/{$row['id']}/decision"),
            ['decision' => 'merge', 'mergeTargetId' => $existingId],
        );
        $this->assertResponseOk();

        $this->commit($batchId);
        $this->assertResponseOk();
        $this->assertSame(1, $this->responseJson()['batch']['result']['merged']);
        $this->assertSame(1, $this->rowCount('ems_teachers', ['school_id' => $this->schoolId]));
        $this->assertSame('grace@new.test', $this->teacher('Grace')['email']);
    }

    // --- helpers -------------------------------------------------------------

    /** Seed a catalogue subject for this test's school; returns its id. */
    private function seedSubject(string $name): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_subjects', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'name' => $name,
            'active' => 1,
        ]);

        return $id;
    }

    /** @return array<string, mixed> */
    private function upload(string $csv): array
    {
        $this->authAs('registrar', $this->registrarId, 'Rita Registrar');
        $this->post($this->schoolPath('/imports'), [
            'kind' => 'staff',
            'filename' => 'staff.csv',
            'text' => $csv,
        ]);

        return $this->responseJson();
    }

    private function commit(string $batchId): void
    {
        $this->authAs('registrar', $this->registrarId, 'Rita Registrar');
        $this->post($this->schoolPath("/imports/{$batchId}/commit"));
    }

    /** @return array<string, mixed> */
    private function teacher(string $firstName): array
    {
        return (array)$this->db->selectQuery()
            ->select(['staff_number', 'status', 'subjects', 'email', 'hired_on'])
            ->from('ems_teachers')
            ->where(['school_id' => $this->schoolId, 'first_name' => $firstName])
            ->execute()->fetch('assoc');
    }
}
