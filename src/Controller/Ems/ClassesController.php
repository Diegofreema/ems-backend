<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\ClassSerializer;
use App\Ems\Serializer\StudentSerializer;
use App\Ems\SubjectCatalog;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;

/**
 * Classes, timetable & attendance registers (document.md §3.12).
 *
 * Rosters and student counts match enrolled students by class NAME — a
 * contract-mandated join key, deliberately not an FK.
 */
class ClassesController extends AppController
{
    /**
     * GET /classes — page, pageSize, query, level|'all', sort: 'name'|'size'.
     * Viewer class-scoped BEFORE pagination. A school's class list is a small
     * bounded set, so summaries are assembled in PHP (which also matches the
     * mock's exact sort semantics).
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $level = (string)$this->request->getQuery('level', 'all');
        $sort = (string)$this->request->getQuery('sort', 'name');

        $q = $this->tenant()->query('EmsClassGroups');
        $q = $this->scope()->applyClassFilter($q);
        if ($level !== 'all' && $level !== '') {
            $q->where(['EmsClassGroups.level' => $level]);
        }
        if ($query !== '') {
            $like = '%' . $query . '%';
            $q->where(['OR' => [
                'EmsClassGroups.name LIKE' => $like,
                'EmsClassGroups.level LIKE' => $like,
            ]]);
        }

        $groups = $q->all()->toList();
        $summaries = $this->summarize($groups);

        if ($sort === 'size') {
            usort($summaries, function (array $a, array $b) {
                return [$b['studentCount'], $a['name']] <=> [$a['studentCount'], $b['name']];
            });
        } else {
            usort($summaries, function (array $a, array $b) {
                return strcasecmp($a['name'], $b['name']);
            });
        }

        $total = count($summaries);
        $pageRows = array_slice($summaries, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated($pageRows, $total, $params['page'], $params['pageSize']);
    }

    /**
     * GET /classes/{id} — ClassSummary.
     */
    public function view(string $id): Response
    {
        $group = $this->findClassScoped($id);
        $summary = $this->summarize([$group]);

        return $this->json($summary[0]);
    }

    /**
     * GET /classes/levels — distinct levels, sorted.
     */
    public function levels(): Response
    {
        $levels = $this->tenant()->query('EmsClassGroups')
            ->select(['level'])
            ->distinct(['level'])
            ->all()
            ->extract('level')
            ->toList();
        sort($levels, SORT_STRING);

        return $this->json(array_values($levels));
    }

    /**
     * GET /classes/{id}/roster — enrolled only, "lastName firstName" asc.
     */
    public function roster(string $id): Response
    {
        $group = $this->findClassScoped($id);

        return $this->json(array_map(
            [StudentSerializer::class, 'one'],
            $this->rosterRows($group),
        ));
    }

    /**
     * GET /classes/{id}/allocations — AllocationView[], sorted by subject.
     */
    public function allocations(string $id): Response
    {
        $group = $this->findClassScoped($id);
        $teacherNames = $this->teacherNamesById();

        $rows = $this->tenant()->query('EmsSubjectAllocations')
            ->leftJoin(['Subj' => 'ems_subjects'], ['Subj.id = EmsSubjectAllocations.subject_id'])
            ->where([
                'EmsSubjectAllocations.class_group_id' => $group->id,
            ])
            ->orderByAsc('Subj.name')
            ->all();

        $out = [];
        foreach ($rows as $row) {
            $out[] = ClassSerializer::allocation($row, $teacherNames[(string)$row->teacher_id] ?? null);
        }

        return $this->json($out);
    }

    /**
     * GET /classes/{id}/timetable — TimetableSlotView[].
     */
    public function timetable(string $id): Response
    {
        $group = $this->findClassScoped($id);
        $teacherNames = $this->teacherNamesById();

        $slotQuery = $this->tenant()->query('EmsTimetableSlots')
            ->where(['class_group_id' => $group->id]);
        $rows = $slotQuery
            ->orderByAsc($slotQuery->newExpr("FIELD(day, 'Mon', 'Tue', 'Wed', 'Thu', 'Fri')"))
            ->orderByAsc('period')
            ->all();

        $out = [];
        foreach ($rows as $row) {
            $out[] = ClassSerializer::slot($row, $teacherNames[(string)$row->teacher_id] ?? null);
        }

        return $this->json($out);
    }

    /**
     * GET /classes/{id}/register?date=YYYY-MM-DD — full enrolled roster,
     * default status 'present' for unsubmitted dates (§3.12).
     */
    public function register(string $id): Response
    {
        $group = $this->findClassScoped($id);
        $date = (string)$this->request->getQuery('date', '');
        if ($date === '') {
            $this->fail(400, 'A register needs a date.');
        }

        $statuses = [];
        $notes = [];
        foreach (
            $this->tenant()->query('EmsAttendanceRecords')
                ->where(['date' => $date]) as $record
        ) {
            $statuses[(string)$record->student_id] = (string)$record->status;
            $notes[(string)$record->student_id] = (string)($record->note ?? '');
        }

        $rows = [];
        foreach ($this->rosterRows($group) as $student) {
            $rows[] = [
                'student' => StudentSerializer::one($student),
                'status' => $statuses[(string)$student->id] ?? 'present',
                'note' => $notes[(string)$student->id] ?? '',
            ];
        }

        $session = $this->findAttendanceSession((string)$group->id, $date);

        return $this->json([
            'classGroup' => ClassSerializer::group($group),
            'date' => $date,
            'rows' => $rows,
            'session' => $session === null ? null : ClassSerializer::attendanceSession($session),
        ]);
    }

    /**
     * PUT /classes/{id}/register?date=… { entries: {studentId,status}[], reason? }
     *
     * Class access required. First submission creates the AttendanceSession;
     * a re-submission is a correction and needs a reason — the trail lives on
     * the session row, not in the audit log.
     */
    public function saveRegister(string $id): Response
    {
        $group = $this->findClassScoped($id);

        $body = $this->body();
        $date = (string)($this->request->getQuery('date') ?? $body['date'] ?? '');
        if ($date === '') {
            $this->fail(400, 'A register needs a date.');
        }
        $entries = is_array($body['entries'] ?? null) ? $body['entries'] : [];
        $reason = trim((string)($body['reason'] ?? ''));

        $sessions = $this->fetchTable('EmsAttendanceSessions');
        $records = $this->fetchTable('EmsAttendanceRecords');
        $session = $this->findAttendanceSession((string)$group->id, $date);

        if ($session !== null && $reason === '') {
            $this->fail(422, Messages::REGISTER_CORRECTION_REASON);
        }

        $viewer = $this->viewer;
        $schoolId = $viewer->schoolId;
        $sessions->getConnection()->transactional(
            function () use ($sessions, $records, $session, $group, $date, $entries, $reason, $viewer, $schoolId): void {
                $now = FrozenTime::now('UTC');
                if ($session === null) {
                    $sessions->saveOrFail($sessions->newEntity([
                        'school_id' => $schoolId,
                        'class_group_id' => (string)$group->id,
                        'date' => $date,
                        'submitted_by' => $viewer->name,
                        'submitted_on' => $now,
                        'corrections' => [],
                    ]));
                } else {
                    $corrections = is_string($session->corrections)
                        ? (array)json_decode($session->corrections, true)
                        : (array)$session->corrections;
                    $corrections[] = [
                        'by' => $viewer->name,
                        'on' => $now->format('Y-m-d\TH:i:s\Z'),
                        'reason' => $reason,
                    ];
                    $session->corrections = array_values($corrections);
                    $sessions->saveOrFail($session);
                }

                // Upsert one attendance row per (studentId, date).
                foreach ($entries as $entry) {
                    $studentId = (string)($entry['studentId'] ?? '');
                    $status = (string)($entry['status'] ?? '');
                    if ($studentId === '' || $status === '') {
                        continue;
                    }
                    // A per-student note (e.g. "Left early — dental appointment").
                    // Optional and capped to the column width; an emptied note
                    // clears the stored one rather than leaving a stale value.
                    $note = mb_substr(trim((string)($entry['note'] ?? '')), 0, 255);
                    $existing = $this->tenant()->query('EmsAttendanceRecords')
                        ->where([
                            'student_id' => $studentId,
                            'date' => $date,
                        ])
                        ->first();
                    if ($existing !== null) {
                        $existing->status = $status;
                        $existing->note = $note === '' ? null : $note;
                        $records->saveOrFail($existing);
                    } else {
                        $records->saveOrFail($records->newEntity([
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'date' => $date,
                            'status' => $status,
                            'note' => $note === '' ? null : $note,
                        ]));
                    }
                }
            },
        );

        return $this->json(null, 204);
    }

    /** POST /classes — create a class group (administrator/registrar). */
    public function add(): Response
    {
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));
        if ($name === '') {
            $this->fail(422, Messages::CLASS_NAME_REQUIRED);
        }
        $groups = $this->fetchTable('EmsClassGroups');
        if (
            $this->tenant()->exists('EmsClassGroups', [
                'LOWER(name)' => mb_strtolower($name),
            ])
        ) {
            $this->fail(422, Messages::CLASS_EXISTS);
        }
        $row = $groups->saveOrFail($groups->newEntity([
            'school_id' => $this->viewer->schoolId,
            'name' => $name,
            'level' => trim((string)($body['level'] ?? '')),
            'stream' => trim((string)($body['stream'] ?? '')),
            'form_teacher_id' => (string)($body['formTeacherId'] ?? '') !== ''
                ? (string)$body['formTeacherId']
                : null,
            'capacity' => (int)($body['capacity'] ?? 0),
        ]));
        $this->audit()->log(
            $this->viewer,
            'class.created',
            'class',
            (string)$row->id,
            sprintf('Created the class %s', $name),
        );

        return $this->json($this->summarize([$row])[0], 201);
    }

    /** PUT /classes/{id} — students reference the class by NAME, so a rename
     *  cascades to their records inside the same transaction. */
    public function edit(string $id): Response
    {
        $group = $this->findClass($id);
        $body = $this->body();
        $name = trim((string)($body['name'] ?? (string)$group->name));
        if ($name === '') {
            $this->fail(422, Messages::CLASS_NAME_REQUIRED);
        }
        $groups = $this->fetchTable('EmsClassGroups');
        $clash = $this->tenant()->query('EmsClassGroups')
            ->where([
                'LOWER(name)' => mb_strtolower($name),
                'id !=' => $group->id,
            ])
            ->count();
        if ($clash > 0) {
            $this->fail(422, Messages::CLASS_EXISTS);
        }
        $was = (string)$group->name;
        $group->name = $name;
        if (array_key_exists('level', $body)) {
            $group->level = trim((string)$body['level']);
        }
        if (array_key_exists('stream', $body)) {
            $group->stream = trim((string)$body['stream']);
        }
        if (array_key_exists('formTeacherId', $body)) {
            $group->form_teacher_id = (string)$body['formTeacherId'] !== ''
                ? (string)$body['formTeacherId']
                : null;
        }
        if (array_key_exists('capacity', $body)) {
            $group->capacity = (int)$body['capacity'];
        }
        $groups->getConnection()->transactional(function () use ($groups, $group, $was, $name): void {
            $groups->saveOrFail($group);
            if ($was !== $name) {
                $this->fetchTable('EmsStudents')->updateAll(
                    ['class_group' => $name],
                    ['school_id' => $this->viewer->schoolId, 'class_group' => $was],
                );
            }
        });
        if ($was !== $name) {
            $this->audit()->log(
                $this->viewer,
                'class.renamed',
                'class',
                (string)$group->id,
                sprintf('Renamed the class %s to %s', $was, $name),
            );
        }

        return $this->json($this->summarize([$group])[0]);
    }

    /** DELETE /classes/{id} — refused while the class has students or history. */
    public function delete(string $id): Response
    {
        $group = $this->findClass($id);
        // Students reference their class by NAME (denormalized placement), the
        // academic tables by class-group id — check the name first, then the
        // id-keyed references through the shared guard.
        if (
            $this->tenant()->exists('EmsStudents', [
                'class_group' => (string)$group->name,
            ])
        ) {
            $this->fail(409, Messages::CLASS_IN_USE);
        }
        $this->assertNoReferences((string)$group->id, [
            'EmsExamGrades' => 'class_group_id',
            'EmsAssessments' => 'class_group_id',
            'EmsAttendanceSessions' => 'class_group_id',
            'EmsSubjectAllocations' => 'class_group_id',
            'EmsTimetableSlots' => 'class_group_id',
        ], Messages::CLASS_IN_USE);
        $name = (string)$group->name;
        $this->fetchTable('EmsClassGroups')->deleteOrFail($group);
        $this->audit()->log(
            $this->viewer,
            'class.deleted',
            'class',
            $id,
            sprintf('Deleted the class %s', $name),
        );

        return $this->response->withStatus(204);
    }

    /** POST /classes/{id}/allocations — assign a subject (and teacher) to a class. */
    public function addAllocation(string $id): Response
    {
        $group = $this->findClass($id);
        $body = $this->body();
        $subjectId = (string)($body['subjectId'] ?? '');
        if ($subjectId === '') {
            $subjectId = SubjectCatalog::requireId($this->viewer->schoolId, (string)($body['subject'] ?? ''));
        }
        $allocations = $this->fetchTable('EmsSubjectAllocations');
        if (
            $this->tenant()->exists('EmsSubjectAllocations', [
                'class_group_id' => (string)$group->id,
                'subject_id' => $subjectId,
            ])
        ) {
            $this->fail(422, Messages::ALLOCATION_EXISTS);
        }
        $row = $allocations->saveOrFail($allocations->newEntity([
            'school_id' => $this->viewer->schoolId,
            'class_group_id' => (string)$group->id,
            'subject_id' => $subjectId,
            'teacher_id' => (string)($body['teacherId'] ?? '') !== '' ? (string)$body['teacherId'] : null,
        ], ['validate' => false]));

        return $this->json(ClassSerializer::allocation($row, $this->teacherNamesById()[(string)$row->teacher_id] ?? null), 201);
    }

    /** PUT /classes/{id}/allocations/{allocationId} — change the teacher. */
    public function editAllocation(string $id, string $allocationId): Response
    {
        $this->findClass($id);
        $allocations = $this->fetchTable('EmsSubjectAllocations');
        $row = $this->tenant()->query('EmsSubjectAllocations')
            ->where(['class_group_id' => $id, 'id' => $allocationId])
            ->first();
        if ($row === null) {
            $this->fail(404, Messages::ALLOCATION_NOT_FOUND);
        }
        $body = $this->body();
        $row->teacher_id = (string)($body['teacherId'] ?? '') !== '' ? (string)$body['teacherId'] : null;
        $allocations->saveOrFail($row);

        return $this->json(ClassSerializer::allocation($row, $this->teacherNamesById()[(string)$row->teacher_id] ?? null));
    }

    /** DELETE /classes/{id}/allocations/{allocationId}. */
    public function deleteAllocation(string $id, string $allocationId): Response
    {
        $this->findClass($id);
        $allocations = $this->fetchTable('EmsSubjectAllocations');
        $row = $this->tenant()->query('EmsSubjectAllocations')
            ->where(['class_group_id' => $id, 'id' => $allocationId])
            ->first();
        if ($row !== null) {
            $allocations->deleteOrFail($row);
        }

        return $this->response->withStatus(204);
    }

    /** POST /classes/{id}/timetable — schedule a subject into a period. */
    public function addSlot(string $id): Response
    {
        $group = $this->findClass($id);
        $body = $this->body();
        $day = (string)($body['day'] ?? '');
        $period = (int)($body['period'] ?? 0);
        $subjectId = (string)($body['subjectId'] ?? '');
        if ($subjectId === '') {
            $subjectId = SubjectCatalog::requireId($this->viewer->schoolId, (string)($body['subject'] ?? ''));
        }
        $teacherId = (string)($body['teacherId'] ?? '') !== '' ? (string)$body['teacherId'] : null;
        $slots = $this->fetchTable('EmsTimetableSlots');
        if (
            $this->tenant()->exists('EmsTimetableSlots', [
                'class_group_id' => (string)$group->id,
                'day' => $day,
                'period' => $period,
            ])
        ) {
            $this->fail(422, Messages::SLOT_TAKEN);
        }
        $this->assertTeacherFree($teacherId, $day, $period, null);
        $row = $slots->saveOrFail($slots->newEntity([
            'school_id' => $this->viewer->schoolId,
            'class_group_id' => (string)$group->id,
            'day' => $day,
            'period' => $period,
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
        ], ['validate' => false]));

        return $this->json(ClassSerializer::slot($row, $this->teacherNamesById()[(string)$row->teacher_id] ?? null), 201);
    }

    /** PUT /classes/{id}/timetable/{slotId} — change subject or teacher. */
    public function editSlot(string $id, string $slotId): Response
    {
        $this->findClass($id);
        $slots = $this->fetchTable('EmsTimetableSlots');
        $row = $this->tenant()->query('EmsTimetableSlots')
            ->where(['class_group_id' => $id, 'id' => $slotId])
            ->first();
        if ($row === null) {
            $this->fail(404, Messages::SLOT_NOT_FOUND);
        }
        $body = $this->body();
        if (isset($body['subjectId']) || isset($body['subject'])) {
            $subjectId = (string)($body['subjectId'] ?? '');
            $row->subject_id = $subjectId !== ''
                ? $subjectId
                : SubjectCatalog::requireId($this->viewer->schoolId, (string)($body['subject'] ?? ''));
        }
        if (array_key_exists('teacherId', $body)) {
            $teacherId = (string)($body['teacherId'] ?? '') !== '' ? (string)$body['teacherId'] : null;
            $this->assertTeacherFree($teacherId, (string)$row->day, (int)$row->period, (string)$row->id);
            $row->teacher_id = $teacherId;
        }
        $slots->saveOrFail($row);

        return $this->json(ClassSerializer::slot($row, $this->teacherNamesById()[(string)$row->teacher_id] ?? null));
    }

    /** DELETE /classes/{id}/timetable/{slotId}. */
    public function deleteSlot(string $id, string $slotId): Response
    {
        $this->findClass($id);
        $slots = $this->fetchTable('EmsTimetableSlots');
        $row = $this->tenant()->query('EmsTimetableSlots')
            ->where(['class_group_id' => $id, 'id' => $slotId])
            ->first();
        if ($row !== null) {
            $slots->deleteOrFail($row);
        }

        return $this->response->withStatus(204);
    }

    /** A teacher cannot stand in two classrooms in the same period. */
    private function assertTeacherFree(?string $teacherId, string $day, int $period, ?string $exceptSlotId): void
    {
        if ($teacherId === null) {
            return;
        }
        $conditions = [
            'teacher_id' => $teacherId,
            'day' => $day,
            'period' => $period,
        ];
        if ($exceptSlotId !== null) {
            $conditions['id !='] = $exceptSlotId;
        }
        if ($this->tenant()->exists('EmsTimetableSlots', $conditions)) {
            $this->fail(422, Messages::TEACHER_DOUBLE_BOOKED);
        }
    }

    private function findClass(string $id): EntityInterface
    {
        return $this->findOr404('EmsClassGroups', $id, Messages::CLASS_NOT_FOUND);
    }

    /**
     * Resolve a class within the tenant AND assert the viewer's scope reaches
     * it (§1.4). The by-id READS (roster, view, allocations, timetable) share
     * this with the register WRITE (saveRegister) so the scope check can never
     * be forgotten on one side: without it a family or out-of-class teacher can
     * read any one class's roster — full student PII — by its id, even though
     * the class endpoints are an `ALL`-tier read. findClass() alone is tenant-
     * scoped only; this is its viewer-scoped counterpart.
     */
    private function findClassScoped(string $id): EntityInterface
    {
        $group = $this->findClass($id);
        $this->scope()->assertClassAccess((string)$group->id);

        return $group;
    }

    private function findAttendanceSession(string $classGroupId, string $date): ?EntityInterface
    {
        return $this->tenant()->query('EmsAttendanceSessions')
            ->where([
                'class_group_id' => $classGroupId,
                'date' => $date,
            ])
            ->first();
    }

    /**
     * Enrolled students of a class, matched by class NAME, sorted
     * "lastName firstName" asc.
     *
     * @return array<\Cake\Datasource\EntityInterface>
     */
    private function rosterRows(EntityInterface $group): array
    {
        return $this->tenant()->query('EmsStudents')
            ->where([
                'class_group' => $group->name,
                'status' => 'enrolled',
            ])
            ->orderByAsc('last_name')
            ->orderByAsc('first_name')
            ->all()
            ->toList();
    }

    /**
     * ClassSummary rows for a set of class groups: one grouped query for
     * enrolled counts, one for form-teacher names.
     *
     * @param array<\Cake\Datasource\EntityInterface> $groups
     */
    private function summarize(array $groups): array
    {
        if ($groups === []) {
            return [];
        }

        $names = array_map(function (EntityInterface $g) {
            return (string)$g->name;
        }, $groups);
        $counts = [];
        $countQuery = $this->tenant()->query('EmsStudents');
        $rows = $countQuery
            ->select(['class_group', 'count' => $countQuery->func()->count('*')])
            ->where([
                'status' => 'enrolled',
                'class_group IN' => $names,
            ])
            ->groupBy('class_group')
            ->enableHydration(false)
            ->all();
        foreach ($rows as $row) {
            $counts[(string)$row['class_group']] = (int)$row['count'];
        }

        $teacherNames = $this->teacherNamesById();

        $out = [];
        foreach ($groups as $group) {
            $out[] = ClassSerializer::summary(
                $group,
                $counts[(string)$group->name] ?? 0,
                $group->form_teacher_id === null
                    ? null
                    : ($teacherNames[(string)$group->form_teacher_id] ?? null),
            );
        }

        return $out;
    }

    /**
     * @return array<string, string> teacher_id → "First Last"
     */
    private function teacherNamesById(): array
    {
        $names = [];
        foreach (
            $this->tenant()->query('EmsTeachers')
                ->select(['id', 'first_name', 'last_name']) as $row
        ) {
            $names[(string)$row->id] = trim($row->first_name . ' ' . $row->last_name);
        }

        return $names;
    }
}
