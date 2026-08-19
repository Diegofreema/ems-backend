<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * The one-file onboarding import (§3.17): a students CSV row creates the
 * student AND its primary guardian AND the enrolment placement, admission
 * numbers come from the shared ems_sequences generator, and flagged
 * duplicates can be settled in bulk with accept-flagged.
 */
class ImportsCombinedTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_import_rows',
        'ems_import_batches',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_class_groups',
        'ems_academic_sessions',
        'ems_sequences',
        'ems_users',
        'ems_schools',
    ];

    private const HEADER = 'admission_number,first_name,last_name,date_of_birth,gender,class_group,'
        . 'status,guardian_name,guardian_phone,guardian_relationship,guardian_email,enrolled_on';

    protected function setUp(): void
    {
        parent::setUp();
        $this->insertRow('ems_class_groups', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => 'JSS 1A',
            'level' => 'JSS 1',
            'stream' => 'A',
        ]);
        $this->insertRow('ems_academic_sessions', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => '2026/2027',
            'starts_on' => '2026-09-01',
            'ends_on' => '2027-07-31',
            'status' => 'open',
        ]);
    }

    /** Upload a students file and return the decoded preview. */
    private function upload(string ...$rows): array
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/imports'), [
            'kind' => 'students',
            'filename' => 'register.csv',
            'text' => implode("\n", [self::HEADER, ...$rows]),
        ]);
        $this->assertResponseCode(201);

        return $this->responseJson();
    }

    private function commit(string $batchId): array
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/imports/' . $batchId . '/commit'));
        $this->assertResponseOk();

        return $this->responseJson();
    }

    public function testOneRowCreatesStudentGuardianAndEnrolment(): void
    {
        $preview = $this->upload(
            ',Ada,Lovelace,2012-04-09,female,JSS 1A,,Mary Lovelace,0803 111 2233,mother,mary@fam.test,',
        );
        $this->assertSame('valid', $preview['rows'][0]['check']);

        $done = $this->commit((string)$preview['batch']['id']);
        $this->assertSame(1, $done['batch']['result']['created']);

        // The student, numbered by the shared sequence (no prior students,
        // school short_name is blank → the ADM prefix).
        $this->assertSame(1, $this->rowCount('ems_students', [
            'school_id' => $this->schoolId,
            'first_name' => 'Ada',
            'admission_number' => 'ADM/0001',
            'status' => 'enrolled',
        ]));
        $student = $this->db->selectQuery()->select(['id'])->from('ems_students')
            ->where(['first_name' => 'Ada'])->execute()->fetch('assoc');

        // The guardian record — split name, relationship, e-mail — primary.
        $this->assertSame(1, $this->rowCount('ems_guardians', [
            'student_id' => $student['id'],
            'first_name' => 'Mary',
            'last_name' => 'Lovelace',
            'relationship' => 'mother',
            'email' => 'mary@fam.test',
            'is_primary' => 1,
        ]));

        // The enrolment placement for the open session.
        $this->assertSame(1, $this->rowCount('ems_enrolments', [
            'student_id' => $student['id'],
            'session' => '2026/2027',
            'class_group' => 'JSS 1A',
            'level' => 'JSS 1',
            'status' => 'active',
        ]));

        // The post-commit preview carries the allocated number back in the
        // row's values — that is what the results download is built from.
        $this->assertSame('ADM/0001', $done['rows'][0]['values']['admission_number']);

        // The manual path continues the SAME sequence — no collision.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), [
            'firstName' => 'Grace',
            'lastName' => 'Hopper',
            'dateOfBirth' => '2011-12-09',
            'gender' => 'female',
            'classGroup' => 'JSS 1A',
        ]);
        $this->assertResponseCode(201);
        $this->assertSame('ADM/0002', $this->responseJson()['admissionNumber']);
    }

    public function testABadGuardianEmailMakesTheRowUnreadable(): void
    {
        $preview = $this->upload(
            ',Ada,Lovelace,2012-04-09,female,JSS 1A,,Mary Lovelace,0803 111 2233,mother,not-an-email,',
        );
        $this->assertSame('invalid', $preview['rows'][0]['check']);
        $issues = array_column($preview['rows'][0]['issues'], 'column');
        $this->assertContains('guardian_email', $issues);
    }

    public function testAcceptFlaggedImportsEveryUndecidedDuplicateAsNew(): void
    {
        $this->insertRow('ems_students', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM/0001',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'date_of_birth' => '2012-04-09',
            'gender' => 'female',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'enrolled_on' => '2026-01-10',
        ]);

        $preview = $this->upload(
            ',Ada,Lovelace,2012-04-09,female,JSS 1A,,Mary Lovelace,0803 111 2233,,,',
        );
        $this->assertSame('duplicate', $preview['rows'][0]['check']);
        $this->assertSame('undecided', $preview['rows'][0]['decision']);
        $batchId = (string)$preview['batch']['id'];

        $this->authAsAdmin();
        $this->post($this->schoolPath('/imports/' . $batchId . '/accept-flagged'));
        $this->assertResponseOk();
        $this->assertSame('1', (string)$this->_response->getBody());

        $done = $this->commit($batchId);
        $this->assertSame(1, $done['batch']['result']['created']);
        $this->assertSame(2, $this->rowCount('ems_students', [
            'school_id' => $this->schoolId,
            'first_name' => 'Ada',
        ]));
    }

    public function testMergingNeverClobbersExistingGuardians(): void
    {
        $studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $studentId,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM/0001',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'date_of_birth' => '2012-04-09',
            'gender' => 'female',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'enrolled_on' => '2026-01-10',
        ]);
        $this->insertRow('ems_guardians', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'student_id' => $studentId,
            'first_name' => 'Original',
            'last_name' => 'Guardian',
            'relationship' => 'father',
            'phone' => '0801 000 0000',
            'email' => 'original@fam.test',
            'occupation' => '',
            'is_primary' => 1,
        ]);

        $preview = $this->upload(
            'ADM/0001,Ada,Lovelace,2012-04-09,female,JSS 1A,,Replacement Person,0809 999 9999,mother,new@fam.test,',
        );
        $row = $preview['rows'][0];
        $this->assertSame('duplicate', $row['check']);
        $batchId = (string)$preview['batch']['id'];

        $this->authAsAdmin();
        $this->put(
            $this->schoolPath('/imports/' . $batchId . '/rows/' . $row['id'] . '/decision'),
            ['decision' => 'merge', 'mergeTargetId' => $studentId],
        );
        $this->assertResponseOk();

        $done = $this->commit($batchId);
        $this->assertSame(1, $done['batch']['result']['merged']);

        // The existing guardian record survives untouched; no second row.
        $this->assertSame(1, $this->rowCount('ems_guardians', ['student_id' => $studentId]));
        $this->assertSame(1, $this->rowCount('ems_guardians', [
            'student_id' => $studentId,
            'first_name' => 'Original',
            'email' => 'original@fam.test',
        ]));
    }
}
