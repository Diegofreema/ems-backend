<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Messages;
use App\Ems\Scope;
use App\Ems\Viewer;
use Cake\Http\Exception\ForbiddenException;
use Cake\Utility\Text;

/**
 * The viewer-scope (row-level RBAC) policy (document.md §1.4), answered once so
 * route guards and data reads can never disagree. Proves each role resolves to
 * the right set of student/class ids, that an out-of-scope id is refused
 * enumeration-safely, and that RBAC narrowing is layered ON TOP of tenant
 * isolation — a teacher never reaches a same-named class in another school.
 */
class ScopeTest extends EmsDbTestCase
{
    private function scopeFor(string $role, string $userId): Scope
    {
        return new Scope(new Viewer($this->schoolId, $userId, $role, ucfirst($role)), $this->locator);
    }

    public function testOfficerSeesTheWholeSchool(): void
    {
        $scope = $this->scopeFor('administrator', $this->adminId);

        $this->assertNull($scope->studentIds(), 'null means no filter — whole school');
        $this->assertNull($scope->classGroupIds());
        // An officer's access assertion never refuses.
        $scope->assertStudentAccess(Text::uuid());
        $this->addToAssertionCount(1);
    }

    public function testTeacherSeesClassesFromAllThreeMembershipSources(): void
    {
        $teacherId = Text::uuid();
        $teacher = $this->seedUser($this->schoolId, 'teacher', ['link_teacher_id' => $teacherId]);

        // Form-teacher, allocation and timetable each grant a different class.
        $formClass = $this->seedClassGroup($this->schoolId, ['name' => 'JSS 1A', 'form_teacher_id' => $teacherId]);
        $allocClass = $this->seedClassGroup($this->schoolId, ['name' => 'JSS 2B']);
        $this->seedAllocation($this->schoolId, $allocClass, $teacherId);
        $ttClass = $this->seedClassGroup($this->schoolId, ['name' => 'JSS 3C']);
        $this->seedTimetableSlot($this->schoolId, $ttClass, $teacherId);
        // A class the teacher has no link to.
        $strangerClass = $this->seedClassGroup($this->schoolId, ['name' => 'SS 1A']);

        $ids = $this->scopeFor('teacher', $teacher)->classGroupIds();

        sort($ids);
        $expected = [$formClass, $allocClass, $ttClass];
        sort($expected);
        $this->assertSame($expected, $ids);
        $this->assertNotContains($strangerClass, $ids);
    }

    public function testTeacherStudentScopeIsTheRostersOfTheirClasses(): void
    {
        $teacherId = Text::uuid();
        $teacher = $this->seedUser($this->schoolId, 'teacher', ['link_teacher_id' => $teacherId]);
        $this->seedClassGroup($this->schoolId, ['name' => 'JSS 1A', 'form_teacher_id' => $teacherId]);

        $mine = $this->seedStudent($this->schoolId, ['class_group' => 'JSS 1A']);
        $notMine = $this->seedStudent($this->schoolId, ['class_group' => 'SS 1A']);

        $ids = $this->scopeFor('teacher', $teacher)->studentIds();

        $this->assertSame([$mine], $ids);
        $this->assertNotContains($notMine, $ids);
    }

    public function testTeacherCannotReachASameNamedClassInAnotherSchool(): void
    {
        $teacherId = Text::uuid();
        $teacher = $this->seedUser($this->schoolId, 'teacher', ['link_teacher_id' => $teacherId]);
        $mine = $this->seedClassGroup($this->schoolId, ['name' => 'JSS 1A', 'form_teacher_id' => $teacherId]);

        // Another tenant, same class name, same (coincidental) teacher id.
        $other = $this->seedSchool();
        $theirs = $this->seedClassGroup($other, ['name' => 'JSS 1A', 'form_teacher_id' => $teacherId]);

        $ids = $this->scopeFor('teacher', $teacher)->classGroupIds();

        $this->assertSame([$mine], $ids);
        $this->assertNotContains($theirs, $ids);
    }

    public function testParentScopeIsTheirLinkedWards(): void
    {
        $ward = $this->seedStudent($this->schoolId, ['class_group' => 'JSS 1A']);
        $this->seedStudent($this->schoolId, ['class_group' => 'JSS 1A']); // another family's child
        $this->seedClassGroup($this->schoolId, ['name' => 'JSS 1A']);
        $parent = $this->seedUser($this->schoolId, 'parent', ['link_student_ids' => [$ward]]);

        $scope = $this->scopeFor('parent', $parent);

        $this->assertSame([$ward], $scope->studentIds());
        // The ward's class comes along; access to the ward is allowed.
        $this->assertCount(1, $scope->classGroupIds());
        $scope->assertStudentAccess($ward);
        $this->addToAssertionCount(1);
    }

    public function testStudentScopeIsTheirOwnRecord(): void
    {
        $self = $this->seedStudent($this->schoolId);
        $student = $this->seedUser($this->schoolId, 'student', ['link_student_id' => $self]);

        $this->assertSame([$self], $this->scopeFor('student', $student)->studentIds());
    }

    public function testUnlinkedViewerResolvesToAnEmptyScopeNotAnError(): void
    {
        $orphan = $this->seedUser($this->schoolId, 'teacher'); // no link_teacher_id

        $scope = $this->scopeFor('teacher', $orphan);

        $this->assertSame([], $scope->studentIds());
        $this->assertSame([], $scope->classGroupIds());
    }

    public function testAssertStudentAccessRefusesOutOfScopeEnumerationSafely(): void
    {
        $ward = $this->seedStudent($this->schoolId);
        $parent = $this->seedUser($this->schoolId, 'parent', ['link_student_ids' => [$ward]]);
        $scope = $this->scopeFor('parent', $parent);

        // A real-but-not-theirs id and a wholly nonexistent id are refused alike,
        // both with the family 403 (never a 404 that would confirm existence).
        $someoneElse = $this->seedStudent($this->schoolId);
        foreach ([$someoneElse, Text::uuid()] as $forbidden) {
            try {
                $scope->assertStudentAccess($forbidden);
                $this->fail('expected a ForbiddenException for an out-of-scope student');
            } catch (ForbiddenException $e) {
                $this->assertSame(Messages::STUDENT_FORBIDDEN, $e->getMessage());
            }
        }
    }

    public function testAssertClassAccessRefusesAClassNotAssignedToTheTeacher(): void
    {
        $teacherId = Text::uuid();
        $teacher = $this->seedUser($this->schoolId, 'teacher', ['link_teacher_id' => $teacherId]);
        $mine = $this->seedClassGroup($this->schoolId, ['name' => 'JSS 1A', 'form_teacher_id' => $teacherId]);
        $notMine = $this->seedClassGroup($this->schoolId, ['name' => 'SS 1A']);
        $scope = $this->scopeFor('teacher', $teacher);

        $scope->assertClassAccess($mine); // allowed, no throw

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Messages::CLASS_FORBIDDEN);
        $scope->assertClassAccess($notMine);
    }

    public function testApplyStudentFilterNarrowsToScopeBeforePagination(): void
    {
        $ward = $this->seedStudent($this->schoolId);
        $this->seedStudent($this->schoolId); // out of scope
        $parent = $this->seedUser($this->schoolId, 'parent', ['link_student_ids' => [$ward]]);

        $query = $this->locator->get('EmsStudents')->find();
        $filtered = $this->scopeFor('parent', $parent)->applyStudentFilter($query);
        $ids = $filtered->all()->extract('id')->toList();

        $this->assertSame([$ward], $ids);
    }

    public function testApplyStudentFilterMatchesNothingForAnEmptyScope(): void
    {
        $this->seedStudent($this->schoolId);
        $orphan = $this->seedUser($this->schoolId, 'teacher'); // empty scope

        $query = $this->locator->get('EmsStudents')->find();
        $filtered = $this->scopeFor('teacher', $orphan)->applyStudentFilter($query);

        $this->assertSame(0, $filtered->count());
    }
}
