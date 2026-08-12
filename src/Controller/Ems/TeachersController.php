<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\SubjectCatalog;
use App\Ems\Messages;
use App\Ems\Serializer\ClassSerializer;
use App\Ems\Serializer\TeacherSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Teachers module (document.md §3.11) plus the teacher day schedule (§3.12).
 * Teacher lists are NOT viewer-scoped.
 */
class TeachersController extends AppController
{
    /**
     * GET /teachers — page, pageSize, query, status|'all', subject|'all',
     * sort: 'name' | 'staff' | 'recent'.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $status = (string)$this->request->getQuery('status', 'all');
        $subject = (string)$this->request->getQuery('subject', 'all');
        $sort = (string)$this->request->getQuery('sort', 'name');

        $teachers = $this->fetchTable('EmsTeachers');
        $q = $this->tenant()->query('EmsTeachers');

        if ($status !== 'all' && $status !== '') {
            $q->where(['status' => $status]);
        }
        if ($subject !== 'all' && $subject !== '') {
            // A filter: an unknown subject name yields no matches (not an error).
            $q->where($teachers->carriesSubjectCondition($this->subjectIdOrNone($subject)));
        }
        if ($query !== '') {
            $like = '%' . $query . '%';
            $or = [
                "CONCAT(first_name, ' ', last_name) LIKE" => $like,
                "CONCAT(last_name, ' ', first_name) LIKE" => $like,
                'staff_number LIKE' => $like,
            ];
            // A search term may be a subject name — match teachers holding any
            // catalogue subject whose name contains it.
            foreach (SubjectCatalog::all($this->viewer->schoolId) as $sid => $name) {
                if (mb_stripos($name, $query) !== false) {
                    $or[] = $teachers->carriesSubjectCondition($sid);
                }
            }
            $q->where(['OR' => $or]);
        }

        $total = $q->count();

        if ($sort === 'staff') {
            $q->orderByAsc('staff_number');
        } elseif ($sort === 'recent') {
            $q->orderByDesc('hired_on')->orderByAsc('last_name');
        } else {
            $q->orderByAsc('last_name')->orderByAsc('first_name');
        }

        $rows = $q->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([TeacherSerializer::class, 'one'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize']
        );
    }

    /**
     * GET /teachers/{id}
     */
    public function view(string $id): Response
    {
        return $this->json(TeacherSerializer::one($this->findTeacher($id)));
    }

    /**
     * GET /teachers/subjects — union of all teachers' subjects, sorted.
     */
    public function subjects(): Response
    {
        // The school's subject catalogue (active entries), the dropdown source.
        $list = $this->tenant()->query('EmsSubjects')
            ->where(['active' => true])
            ->orderByAsc('name')
            ->all()
            ->extract('name')
            ->toList();

        return $this->json(array_values(array_map('strval', $list)));
    }

    /**
     * POST /teachers — TeacherInput.
     */
    public function add(): Response
    {
        $teachers = $this->fetchTable('EmsTeachers');
        $teacher = $teachers->saveOrFail($teachers->newEntity($this->teacherData()));

        return $this->json(TeacherSerializer::one($teacher), 201);
    }

    /**
     * PUT /teachers/{id} — full replace.
     */
    public function edit(string $id): Response
    {
        $teachers = $this->fetchTable('EmsTeachers');
        $teacher = $this->findTeacher($id);
        $teacher = $teachers->patchEntity($teacher, $this->teacherData(), ['validate' => false]);
        $teachers->saveOrFail($teacher);

        return $this->json(TeacherSerializer::one($teacher));
    }

    /**
     * DELETE /teachers/{id} — idempotent; allocations and timetable keep a
     * dangling teacherId which renders as "Unassigned".
     */
    /** POST /teachers/{id}/mark-former — archive an employment record. */
    public function markFormer(string $id): Response
    {
        $teachers = $this->fetchTable('EmsTeachers');
        $teacher = $this->tenant()->query('EmsTeachers')
            ->where(['id' => $id])
            ->first();
        if ($teacher === null) {
            $this->fail(404, Messages::TEACHER_NOT_FOUND);
        }
        $teacher->status = 'former';
        $teachers->saveOrFail($teacher);

        return $this->json(TeacherSerializer::one($teacher));
    }

    public function delete(string $id): Response
    {
        $teachers = $this->fetchTable('EmsTeachers');
        $teacher = $this->tenant()->query('EmsTeachers')
            ->where(['id' => $id])
            ->first();
        if ($teacher !== null) {
            // Employment history is archived, not destroyed: while classes
            // reference the teacher (allocations, timetable, form-teacher),
            // deletion is refused — mark the teacher as former instead.
            $this->assertNoReferences($id, [
                'EmsSubjectAllocations' => 'teacher_id',
                'EmsTimetableSlots' => 'teacher_id',
                'EmsClassGroups' => 'form_teacher_id',
            ], Messages::TEACHER_HAS_RECORDS);
            $teachers->deleteOrFail($teacher);
        }

        return $this->json(null, 204);
    }

    /**
     * GET /teachers/{id}/assignments — from subject allocations, sorted
     * className then subject.
     */
    public function assignments(string $id): Response
    {
        $teacher = $this->findTeacher($id);

        $allocations = $this->tenant()->query('EmsSubjectAllocations')
            ->where(['teacher_id' => $teacher->id])
            ->all();
        $classNames = $this->classNamesById();

        $out = [];
        foreach ($allocations as $allocation) {
            $out[] = [
                'classGroupId' => (string)$allocation->class_group_id,
                'className' => $classNames[(string)$allocation->class_group_id] ?? '',
                'subject' => (string)$allocation->subject,
            ];
        }
        usort($out, function (array $a, array $b) {
            return [$a['className'], $a['subject']] <=> [$b['className'], $b['subject']];
        });

        return $this->json($out);
    }

    /**
     * GET /teachers/{id}/day?day=Mon — that teacher's slots for the day,
     * period asc, times from the shared grid (§3.12).
     */
    public function day(string $id): Response
    {
        $teacher = $this->findTeacher($id);
        $day = (string)$this->request->getQuery('day', 'Mon');

        $slots = $this->tenant()->query('EmsTimetableSlots')
            ->where([
                'teacher_id' => $teacher->id,
                'day' => $day,
            ])
            ->orderByAsc('period')
            ->all();
        $classNames = $this->classNamesById();

        $out = [];
        foreach ($slots as $slot) {
            $period = (int)$slot->period;
            $times = ClassSerializer::PERIODS[$period] ?? ['', ''];
            $out[] = [
                'period' => $period,
                'start' => $times[0],
                'end' => $times[1],
                'subject' => (string)$slot->subject,
                'className' => $classNames[(string)$slot->class_group_id] ?? '',
                'classGroupId' => (string)$slot->class_group_id,
            ];
        }

        return $this->json($out);
    }

    private function findTeacher(string $id): EntityInterface
    {
        return $this->findOr404('EmsTeachers', $id, Messages::TEACHER_NOT_FOUND);
    }

    /**
     * TeacherInput → storage columns.
     */
    private function teacherData(): array
    {
        $body = $this->body();
        $subjects = $body['subjects'] ?? [];

        return [
            'school_id' => $this->viewer->schoolId,
            'staff_number' => trim((string)($body['staffNumber'] ?? '')),
            'first_name' => trim((string)($body['firstName'] ?? '')),
            'last_name' => trim((string)($body['lastName'] ?? '')),
            'email' => trim((string)($body['email'] ?? '')),
            'phone' => trim((string)($body['phone'] ?? '')),
            'gender' => (string)($body['gender'] ?? ''),
            'subjects' => is_array($subjects)
                ? array_values(array_map(
                    fn ($name) => SubjectCatalog::requireId($this->viewer->schoolId, (string)$name),
                    $subjects,
                ))
                : [],
            'status' => (string)($body['status'] ?? 'active'),
            'hired_on' => (string)($body['hiredOn'] ?? '') !== ''
                ? (string)$body['hiredOn']
                : FrozenDate::today()->format('Y-m-d'),
        ];
    }

    /**
     * @return array<string, string> class_group_id → name
     */
    private function classNamesById(): array
    {
        $names = [];
        foreach (
            $this->tenant()->query('EmsClassGroups')
                ->select(['id', 'name']) as $row
        ) {
            $names[(string)$row->id] = (string)$row->name;
        }

        return $names;
    }
}
