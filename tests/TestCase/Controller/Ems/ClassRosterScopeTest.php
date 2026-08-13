<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use Cake\Utility\Text;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Regression for the class-roster IDOR (security review finding #1).
 *
 * `Classes.roster` (and view/allocations/timetable) are `ALL`-tier reads, so a
 * parent/student token passes the coarse Policy gate. Before the fix, the
 * by-id reads resolved the class through the tenant-only `findClass()` and
 * NEVER asserted viewer scope — unlike the write twin `saveRegister()`. A
 * family could therefore read ANY class's roster in their school (full student
 * PII: name, DOB, gender, guardian phone) by its id, and class ids are handed
 * out by the `ALL`-tier Teachers endpoints. The fix routes those reads through
 * `findClassScoped()`, which adds `Scope::assertClassAccess`.
 *
 * These tests prove: the cross-scope read is refused; a family can still reach
 * their own ward's class (no over-restriction); an officer keeps whole-school
 * access; and the sibling reads share the same gate.
 */
final class ClassRosterScopeTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_class_groups',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_users',
        'ems_schools',
    ];

    private string $ownClassId = '';
    private string $otherClassId = '';
    private string $otherStudentId = '';
    private string $parentId = '';

    protected function setUp(): void
    {
        parent::setUp();

        // Two classes in the SAME school (same tenant): the parent's ward sits
        // in "JSS 1A"; "JSS 1B" is a different family's class.
        $this->ownClassId = $this->seedClass('JSS 1A');
        $this->otherClassId = $this->seedClass('JSS 1B');

        $wardId = $this->seedStudent('JSS 1A', 'Amara', 'Ward');
        $this->otherStudentId = $this->seedStudent('JSS 1B', 'Bode', 'Other');

        // A parent linked ONLY to the ward in JSS 1A.
        $this->parentId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $this->parentId,
            'school_id' => $this->schoolId,
            'name' => 'Pat Parent',
            'email' => 'pat.parent@test.school',
            'role' => 'parent',
            'status' => 'active',
            'added_on' => $this->now(),
            'link_kind' => 'parent',
            'link_student_ids' => json_encode([$wardId]),
        ]);
    }

    public function testParentCannotReadAnotherClassRoster(): void
    {
        $this->authAs('parent', $this->parentId, 'Pat Parent');
        $this->get($this->schoolPath('/classes/' . $this->otherClassId . '/roster'));

        $this->assertResponseCode(403);
        $this->assertSame(Messages::CLASS_FORBIDDEN, $this->responseJson()['message']);
        // The other family's PII never left the server.
        $this->assertStringNotContainsString(
            $this->otherStudentId,
            (string)$this->_response->getBody(),
        );
    }

    public function testParentCanStillReadTheirOwnWardsClassRoster(): void
    {
        // The fix must not over-restrict: the family's own ward class is in
        // scope, so this read still succeeds.
        $this->authAs('parent', $this->parentId, 'Pat Parent');
        $this->get($this->schoolPath('/classes/' . $this->ownClassId . '/roster'));

        $this->assertResponseOk();
        $roster = $this->responseJson();
        $this->assertCount(1, $roster);
        $this->assertSame('Amara', $roster[0]['firstName']);
    }

    public function testAdministratorCanStillReadAnyClassRoster(): void
    {
        // Officer scope is whole-school (null): assertClassAccess no-ops, so the
        // fix leaves staff access untouched — the positive control.
        $this->authAsAdmin();
        $this->get($this->schoolPath('/classes/' . $this->otherClassId . '/roster'));

        $this->assertResponseOk();
        $roster = $this->responseJson();
        $this->assertCount(1, $roster);
        $this->assertSame($this->otherStudentId, $roster[0]['id']);
    }

    /**
     * The same gate protects every by-id class read, not just the roster — they
     * all resolve through findClassScoped().
     */
    #[DataProvider('siblingReadProvider')]
    public function testParentCannotReadAnotherClassSiblingEndpoints(string $suffix): void
    {
        $this->authAs('parent', $this->parentId, 'Pat Parent');
        $this->get($this->schoolPath('/classes/' . $this->otherClassId . $suffix));

        $this->assertResponseCode(403);
        $this->assertSame(Messages::CLASS_FORBIDDEN, $this->responseJson()['message']);
    }

    /** @return array<string, array{0:string}> */
    public static function siblingReadProvider(): array
    {
        return [
            'view' => [''],
            'allocations' => ['/allocations'],
            'timetable' => ['/timetable'],
        ];
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
