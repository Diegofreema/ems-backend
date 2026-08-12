<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Locator\LocatorInterface;
use Cake\ORM\Query;

/**
 * The viewer-scope policy module (document.md §1.4), answered once so route
 * guards and data reads can never disagree. Port of the frontend's
 * src/mock/access.ts, which is documented as becoming this server module.
 *
 * Scope semantics:
 *  - administrator/registrar/bursar: whole school (scope = null, meaning "no filter")
 *  - teacher: classes where they are form teacher OR hold a subject allocation
 *    OR appear on the timetable; students = the rosters of those classes
 *  - parent: the studentIds linked on their account; student: their own record
 *  - a viewer with no matching link resolves to an EMPTY scope, not an error
 *
 * Every list read filters BEFORE pagination; every detail read asserts scope
 * membership and refuses with the canonical 403 wording.
 */
class Scope
{
    /** @var \App\Ems\Viewer */
    private $viewer;

    /** @var \Cake\ORM\Locator\LocatorInterface */
    private $locator;

    /** @var array|null|false false = not resolved yet */
    private $classGroupIds = false;

    /** @var array|null|false false = not resolved yet */
    private $studentIds = false;

    /** @var \App\Model\Entity\EmsUser|null|false false = not loaded yet */
    private $userRow = false;

    /** @var \App\Ems\Tenant|null */
    private $tenantScope;

    public function __construct(Viewer $viewer, LocatorInterface $locator)
    {
        $this->viewer = $viewer;
        $this->locator = $locator;
    }

    /**
     * The viewer's tenant-scope choke point — reads narrowed to the viewer's
     * school by construction. RBAC narrowing (class/student ids) is layered on
     * top of this. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->viewer->schoolId);
    }

    /**
     * Class-group ids the viewer may see. null = whole school; [] = nothing.
     *
     * @return array<string>|null
     */
    public function classGroupIds(): ?array
    {
        if ($this->classGroupIds !== false) {
            return $this->classGroupIds;
        }
        if ($this->viewer->isOfficer()) {
            return $this->classGroupIds = null;
        }

        if ($this->viewer->role === 'teacher') {
            $teacherId = $this->linkField('link_teacher_id');
            if ($teacherId === null) {
                return $this->classGroupIds = [];
            }
            $ids = [];
            foreach (
                $this->tenant()->query('EmsClassGroups')
                    ->select(['id'])
                    ->where(['form_teacher_id' => $teacherId]) as $row
            ) {
                $ids[$row->id] = true;
            }
            foreach (
                $this->tenant()->query('EmsSubjectAllocations')
                    ->select(['class_group_id'])
                    ->where(['teacher_id' => $teacherId]) as $row
            ) {
                $ids[$row->class_group_id] = true;
            }
            foreach (
                $this->tenant()->query('EmsTimetableSlots')
                    ->select(['class_group_id'])
                    ->where(['teacher_id' => $teacherId]) as $row
            ) {
                $ids[$row->class_group_id] = true;
            }

            return $this->classGroupIds = array_keys($ids);
        }

        // parent / student: the class group(s) their ward(s) currently sit in,
        // matched by class *name* (the contract's join key for rosters).
        $wardIds = $this->studentIds();
        if ($wardIds === null || $wardIds === []) {
            return $this->classGroupIds = [];
        }
        $classNames = $this->tenant()->query('EmsStudents')
            ->select(['class_group'])
            ->distinct(['class_group'])
            ->where(['id IN' => $wardIds])
            ->all()
            ->extract('class_group')
            ->filter()
            ->toList();
        if ($classNames === []) {
            return $this->classGroupIds = [];
        }
        $ids = $this->tenant()->query('EmsClassGroups')
            ->select(['id'])
            ->where(['name IN' => $classNames])
            ->all()
            ->extract('id')
            ->toList();

        return $this->classGroupIds = $ids;
    }

    /**
     * Student ids the viewer may see. null = whole school; [] = nothing.
     *
     * @return array<string>|null
     */
    public function studentIds(): ?array
    {
        if ($this->studentIds !== false) {
            return $this->studentIds;
        }
        if ($this->viewer->isOfficer()) {
            return $this->studentIds = null;
        }

        $role = $this->viewer->role;
        if ($role === 'parent') {
            $ids = $this->linkField('link_student_ids');

            return $this->studentIds = is_array($ids) ? array_values($ids) : [];
        }
        if ($role === 'student') {
            $id = $this->linkField('link_student_id');

            return $this->studentIds = $id === null ? [] : [$id];
        }
        if ($role === 'teacher') {
            // Rosters of the teacher's classes, matched by class name.
            $classIds = $this->classGroupIds();
            if ($classIds === null || $classIds === []) {
                return $this->studentIds = [];
            }
            $names = $this->tenant()->query('EmsClassGroups')
                ->select(['name'])
                ->where(['id IN' => $classIds])
                ->all()
                ->extract('name')
                ->toList();
            if ($names === []) {
                return $this->studentIds = [];
            }
            $ids = $this->tenant()->query('EmsStudents')
                ->select(['id'])
                ->where([
                    'class_group IN' => $names,
                ])
                ->all()
                ->extract('id')
                ->toList();

            return $this->studentIds = $ids;
        }

        return $this->studentIds = [];
    }

    /**
     * Refuse with the canonical family 403 unless the viewer may see this
     * student. Enumeration-safe: an out-of-scope id is refused whether or
     * not the record exists (§1.3).
     */
    public function assertStudentAccess(string $studentId): void
    {
        $ids = $this->studentIds();
        if ($ids === null) {
            return;
        }
        if (!in_array($studentId, $ids, true)) {
            throw new ForbiddenException(Messages::STUDENT_FORBIDDEN);
        }
    }

    /**
     * Refuse with the canonical class 403 unless the viewer may act on this
     * class group.
     */
    public function assertClassAccess(string $classGroupId): void
    {
        $ids = $this->classGroupIds();
        if ($ids === null) {
            return;
        }
        if (!in_array($classGroupId, $ids, true)) {
            throw new ForbiddenException(Messages::CLASS_FORBIDDEN);
        }
    }

    /**
     * Narrow a query over ems_students to the viewer's scope — BEFORE
     * count + pagination. Empty scope matches nothing (never an error).
     */
    public function applyStudentFilter(Query $query): Query
    {
        $ids = $this->studentIds();
        if ($ids === null) {
            return $query;
        }
        if ($ids === []) {
            return $query->where(['1 = 0']);
        }

        return $query->where([$query->getRepository()->getAlias() . '.id IN' => $ids]);
    }

    /**
     * Narrow a query over ems_class_groups to the viewer's scope.
     */
    public function applyClassFilter(Query $query): Query
    {
        $ids = $this->classGroupIds();
        if ($ids === null) {
            return $query;
        }
        if ($ids === []) {
            return $query->where(['1 = 0']);
        }

        return $query->where([$query->getRepository()->getAlias() . '.id IN' => $ids]);
    }

    /**
     * Read a PersonLink column off the viewer's account row.
     *
     * @return mixed
     */
    private function linkField(string $field)
    {
        if ($this->userRow === false) {
            $this->userRow = $this->tenant()->query('EmsUsers')
                ->where(['id' => $this->viewer->userId])
                ->first();
        }

        return $this->userRow->{$field} ?? null;
    }
}
