<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * The manual student path now validates its input (§3.10): bad requests are
 * 422s with the contract sentences instead of 500s, blank admission numbers
 * auto-allocate from the shared sequence, and supplied ones must be unique.
 */
class StudentsValidationTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_class_groups',
        'ems_academic_sessions',
        'ems_sequences',
        'ems_users',
        'ems_schools',
    ];

    private function validInput(array $overrides = []): array
    {
        return $overrides + [
            'firstName' => 'Ada',
            'lastName' => 'Lovelace',
            'dateOfBirth' => '2012-04-09',
            'gender' => 'female',
            'classGroup' => 'JSS 1A',
        ];
    }

    public function testAMissingDateOfBirthIsA422NotA500(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput(['dateOfBirth' => '']));
        $this->assertResponseCode(422);
        $this->assertSame(
            'Write the date of birth as YYYY-MM-DD, for example 2012-04-09.',
            $this->responseJson()['message'],
        );

        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput(['dateOfBirth' => '2099-01-01']));
        $this->assertResponseCode(422);
        $this->assertSame('The date of birth is in the future.', $this->responseJson()['message']);
    }

    public function testNamesAreRequired(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput(['lastName' => '  ']));
        $this->assertResponseCode(422);
        $this->assertSame('The student needs a first and last name.', $this->responseJson()['message']);
    }

    public function testEnumsAreChecked(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput(['gender' => 'unknownish']));
        $this->assertResponseCode(422);
        $this->assertSame('Gender must be female, male or other.', $this->responseJson()['message']);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput(['status' => 'expelled']));
        $this->assertResponseCode(422);
        $this->assertSame(
            'Status must be enrolled, applicant, graduated or withdrawn.',
            $this->responseJson()['message'],
        );
    }

    public function testBlankAdmissionNumberAutoAllocatesAndDuplicatesAreRefused(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput());
        $this->assertResponseCode(201);
        $first = $this->responseJson();
        $this->assertSame('ADM/0001', $first['admissionNumber']);

        // A supplied number that is already taken is refused.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/students'), $this->validInput([
            'firstName' => 'Grace',
            'admissionNumber' => 'ADM/0001',
        ]));
        $this->assertResponseCode(422);
        $this->assertSame(
            'A student with this admission number already exists.',
            $this->responseJson()['message'],
        );

        // Editing a student keeps its number when the field comes back blank.
        $this->authAsAdmin();
        $this->put($this->schoolPath('/students/' . $first['id']), $this->validInput([
            'admissionNumber' => '',
            'firstName' => 'Adaeze',
        ]));
        $this->assertResponseOk();
        $this->assertSame('ADM/0001', $this->responseJson()['admissionNumber']);
    }

    public function testAddGuardianRequiresNameAndPhone(): void
    {
        $studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $studentId,
            'school_id' => $this->schoolId,
            'admission_number' => 'T/0001',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'date_of_birth' => '2012-04-09',
            'gender' => 'female',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'enrolled_on' => '2026-01-10',
        ]);

        $this->authAsAdmin();
        $this->post($this->schoolPath('/students/' . $studentId . '/guardians'), [
            'firstName' => 'Mary',
            'lastName' => '',
            'phone' => '',
        ]);
        $this->assertResponseCode(422);
        $this->assertSame(
            'Every guardian needs a first name, last name, and phone number.',
            $this->responseJson()['message'],
        );
    }
}
