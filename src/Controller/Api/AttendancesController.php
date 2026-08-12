<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Attendances API — REST CRUD plus the teacher "take attendance" workflow,
 * ported from the web AttendancesController::take() (the roster + bulk save),
 * with the teacher resolved from the JWT instead of the session.
 *
 * CRUD + filters: ?student_id=&teacher_id=&department_id=&class_arm_id=
 *                 &attendance_date=&status=
 * Custom:
 *   GET  /api/v1/attendances/roster?date=YYYY-MM-DD
 *        -> current teacher's class arms, their admitted students, and any
 *           attendance already recorded for that date.
 *   POST /api/v1/attendances/mark
 *        body { "attendance_date": "YYYY-MM-DD",
 *               "records": [ { "student_id": 1, "status": "present", "notes": "" }, ... ] }
 *        -> replaces attendance for that date across the teacher's class arms
 *           (students omitted from records default to "absent", as the web does).
 */
class AttendancesController extends CrudController
{
    /**
     * @var array<int, string>
     */
    protected array $searchFields = ['status', 'notes'];

    /**
     * Roster for the current teacher to take attendance on a given date.
     *
     * @return \Cake\Http\Response
     */
    public function roster()
    {
        $this->request->allowMethod(['get']);
        $teacher = $this->currentTeacher();
        if ($teacher === null) {
            return $this->respondError('No teacher is linked to the current user.', 403);
        }

        $date = (string)$this->request->getQuery('date') ?: date('Y-m-d');
        $classArmIds = $this->teacherClassArmIds($teacher->id);
        $students = $this->classArmStudents($classArmIds);
        $existing = $this->existingAttendanceMap($classArmIds, $date);

        $roster = [];
        foreach ($students as $s) {
            $prior = $existing[$s->id] ?? null;
            $roster[] = [
                'student_id' => $s->id,
                'fname' => $s->fname,
                'lname' => $s->lname,
                'regno' => $s->regno,
                'class_arm_id' => $s->class_arm_id,
                'department_id' => $s->department_id,
                'status' => $prior->status ?? null,
                'notes' => $prior->notes ?? null,
            ];
        }

        return $this->respond([
            'teacher' => ['id' => $teacher->id],
            'attendance_date' => $date,
            'class_arm_ids' => $classArmIds,
            'students' => $roster,
        ]);
    }

    /**
     * Bulk save attendance for the current teacher's class arms on a date.
     *
     * @return \Cake\Http\Response
     */
    public function mark()
    {
        $this->request->allowMethod(['post']);
        $teacher = $this->currentTeacher();
        if ($teacher === null) {
            return $this->respondError('No teacher is linked to the current user.', 403);
        }

        $date = trim((string)$this->request->getData('attendance_date'));
        if ($date === '') {
            return $this->respondError('attendance_date is required.', 422);
        }

        // Map submitted records by student_id.
        $submitted = [];
        foreach ((array)$this->request->getData('records') as $rec) {
            if (isset($rec['student_id'])) {
                $submitted[(int)$rec['student_id']] = [
                    'status' => (string)($rec['status'] ?? 'absent'),
                    'notes' => (string)($rec['notes'] ?? ''),
                ];
            }
        }

        $classArmIds = $this->teacherClassArmIds($teacher->id);
        if (!$classArmIds) {
            return $this->respondError('No class arms are assigned to this teacher.', 422);
        }
        $students = $this->classArmStudents($classArmIds);

        // Replace existing attendance for this date + class arms (mirrors web).
        $this->Model->deleteAll([
            'class_arm_id IN' => $classArmIds,
            'attendance_date' => $date,
        ]);

        $entities = [];
        foreach ($students as $s) {
            $entry = $submitted[$s->id] ?? ['status' => 'absent', 'notes' => ''];
            $entities[] = $this->Model->newEntity([
                'student_id' => $s->id,
                'teacher_id' => $teacher->id,
                'department_id' => $s->department_id,
                'class_arm_id' => $s->class_arm_id,
                'attendance_date' => $date,
                'status' => $entry['status'],
                'notes' => $entry['notes'],
            ]);
        }

        if (!$this->Model->saveMany($entities)) {
            return $this->respondError('Could not save attendance.', 422);
        }

        return $this->respond([
            'attendance_date' => $date,
            'saved' => count($entities),
        ], 201);
    }

    /**
     * The Teacher entity linked to the current JWT user, or null.
     *
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function currentTeacher()
    {
        $userId = $this->currentUserId();
        if ($userId === null) {
            return null;
        }

        return $this->fetchTable('Teachers')->find()
            ->where(['user_id' => $userId])
            ->first();
    }

    /**
     * Active class-arm ids where the teacher is the class teacher.
     *
     * @param int $teacherId Teacher id.
     * @return array<int, int>
     */
    protected function teacherClassArmIds(int $teacherId): array
    {
        return $this->fetchTable('ClassArms')->find()
            ->select(['id'])
            ->where(['class_teacher_id' => $teacherId, 'ClassArms.status' => 'active'])
            ->all()
            ->extract('id')
            ->toList();
    }

    /**
     * Admitted students in the given class arms.
     *
     * @param array<int, int> $classArmIds Class arm ids.
     * @return \Cake\Datasource\ResultSetInterface|array
     */
    protected function classArmStudents(array $classArmIds)
    {
        if (!$classArmIds) {
            return [];
        }

        return $this->fetchTable('Students')->find()
            ->select(['id', 'fname', 'lname', 'regno', 'class_arm_id', 'department_id'])
            ->where(['class_arm_id IN' => $classArmIds, 'Students.status' => 'Admitted'])
            ->order(['fname' => 'ASC', 'lname' => 'ASC'])
            ->all();
    }

    /**
     * Existing attendance for a date keyed by student_id.
     *
     * @param array<int, int> $classArmIds Class arm ids.
     * @param string $date Attendance date.
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    protected function existingAttendanceMap(array $classArmIds, string $date): array
    {
        if (!$classArmIds) {
            return [];
        }
        $map = [];
        $rows = $this->Model->find()
            ->where(['class_arm_id IN' => $classArmIds, 'attendance_date' => $date])
            ->all();
        foreach ($rows as $row) {
            $map[$row->student_id] = $row;
        }

        return $map;
    }
}
