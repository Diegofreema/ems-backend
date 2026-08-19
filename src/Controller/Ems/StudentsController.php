<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\AdmissionNumbers;
use App\Ems\Enrolments;
use App\Ems\Imports;
use App\Ems\Messages;
use App\Ems\Serializer\StudentSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Students module (document.md §3.10). No audit events in this module.
 */
class StudentsController extends AppController
{
    /**
     * GET /students — page, pageSize, query, status|'all', classGroup|'all',
     * sort: 'name'|'admission'|'recent'. Viewer-scoped BEFORE pagination.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $status = (string)$this->request->getQuery('status', 'all');
        $classGroup = (string)$this->request->getQuery('classGroup', 'all');
        $sort = (string)$this->request->getQuery('sort', 'name');

        $q = $this->tenant()->query('EmsStudents');
        $q = $this->scope()->applyStudentFilter($q);

        if ($status !== 'all' && $status !== '') {
            $q->where(['EmsStudents.status' => $status]);
        }
        if ($classGroup !== 'all' && $classGroup !== '') {
            $q->where(['EmsStudents.class_group' => $classGroup]);
        }
        if ($query !== '') {
            $like = '%' . $query . '%';
            $q->where(['OR' => [
                "CONCAT(EmsStudents.first_name, ' ', EmsStudents.last_name) LIKE" => $like,
                "CONCAT(EmsStudents.last_name, ' ', EmsStudents.first_name) LIKE" => $like,
                'EmsStudents.admission_number LIKE' => $like,
                'EmsStudents.class_group LIKE' => $like,
            ]]);
        }

        $total = $q->count();

        if ($sort === 'admission') {
            $q->orderByAsc('EmsStudents.admission_number');
        } elseif ($sort === 'recent') {
            $q->orderByDesc('EmsStudents.enrolled_on')->orderByAsc('EmsStudents.last_name');
        } else {
            // 'name' = "lastName firstName" asc.
            $q->orderByAsc('EmsStudents.last_name')->orderByAsc('EmsStudents.first_name');
        }

        $rows = $q->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all()->toList();
        $statuses = $this->feesEngine()->statusesForStudents(array_map(static fn($s) => (string)$s->id, $rows));
        $items = array_map(static fn($s) => StudentSerializer::one($s) + ['financeStatus' => $statuses[(string)$s->id] ?? 'not_invoiced'], $rows);

        return $this->paginated(
            $items,
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    /**
     * GET /students/{id} — scope assert first (enumeration-safe 403), then 404.
     */
    public function view(string $id): Response
    {
        $student = $this->findStudent($id);
        $status = $this->feesEngine()->statusesForStudents([$id]);

        return $this->json(StudentSerializer::one($student) + ['financeStatus' => $status[$id] ?? 'not_invoiced']);
    }

    /**
     * GET /students/class-groups — distinct classGroup values, sorted.
     */
    public function classGroups(): Response
    {
        $values = $this->tenant()->query('EmsStudents')
            ->select(['class_group'])
            ->distinct(['class_group'])
            ->where(['class_group !=' => ''])
            ->all()
            ->extract('class_group')
            ->toList();
        sort($values, SORT_STRING);

        return $this->json(array_values($values));
    }

    /**
     * POST /students — StudentInput. Side effect (§3.10): when status is
     * `enrolled` and a current (open) session exists, also creates an active
     * Enrolment for that session — one transaction.
     */
    public function add(): Response
    {
        $students = $this->fetchTable('EmsStudents');
        $data = $this->validatedStudentData();

        $student = $students->getConnection()->transactional(function () use ($students, $data) {
            $student = $students->saveOrFail($students->newEntity($data));
            $this->createEnrolmentIfCurrent($student);

            return $student;
        });

        return $this->json(StudentSerializer::one($student), 201);
    }

    /**
     * POST /students/admit — create the student and every drafted guardian as
     * one unit. A failed guardian save rolls the student and enrolment back.
     */
    public function admit(): Response
    {
        $body = $this->body();
        $studentInput = is_array($body['student'] ?? null) ? $body['student'] : [];
        $guardianInputs = is_array($body['guardians'] ?? null) ? $body['guardians'] : [];
        if ($guardianInputs === []) {
            $this->fail(422, Messages::STUDENT_GUARDIAN_REQUIRED);
        }
        foreach ($guardianInputs as $guardianInput) {
            if (
                !is_array($guardianInput)
                || trim((string)($guardianInput['firstName'] ?? '')) === ''
                || trim((string)($guardianInput['lastName'] ?? '')) === ''
                || trim((string)($guardianInput['phone'] ?? '')) === ''
            ) {
                $this->fail(422, Messages::GUARDIAN_DETAILS_REQUIRED);
            }
        }

        $requestedPrimary = array_search(true, array_map(
            static fn(array $guardian): bool => (bool)($guardian['isPrimary'] ?? false),
            $guardianInputs,
        ), true);
        $primaryIndex = $requestedPrimary === false ? 0 : $requestedPrimary;
        $primary = $guardianInputs[$primaryIndex];
        $studentInput['guardianName'] = trim(sprintf(
            '%s %s',
            (string)($primary['firstName'] ?? ''),
            (string)($primary['lastName'] ?? ''),
        ));
        $studentInput['guardianPhone'] = trim((string)($primary['phone'] ?? ''));

        $students = $this->fetchTable('EmsStudents');
        $student = $students->getConnection()->transactional(function () use (
            $students,
            $studentInput,
            $guardianInputs,
            $primaryIndex,
        ) {
            $student = $students->saveOrFail($students->newEntity($this->validatedStudentData($studentInput)));
            $this->createEnrolmentIfCurrent($student);

            $guardians = $this->fetchTable('EmsGuardians');
            foreach ($guardianInputs as $index => $guardianInput) {
                $guardians->saveOrFail($guardians->newEntity(
                    $this->guardianData($guardianInput, (string)$student->id)
                    + ['is_primary' => $index === $primaryIndex],
                ));
            }
            $this->syncPrimaryContact((string)$student->id);

            return $student;
        });

        return $this->json(StudentSerializer::one($student), 201);
    }

    /**
     * PUT /students/{id} — full merge-replace.
     */
    public function edit(string $id): Response
    {
        $students = $this->fetchTable('EmsStudents');
        $student = $this->findStudent($id);
        $student = $students->patchEntity($student, $this->validatedStudentData(null, $id), ['validate' => false]);
        $students->saveOrFail($student);

        return $this->json(StudentSerializer::one($student));
    }

    /** POST /students/{id}/withdraw — the named archival action (Q10): the
     *  record survives with status "withdrawn"; nothing is destroyed. */
    public function withdraw(string $id): Response
    {
        $students = $this->fetchTable('EmsStudents');
        $student = $this->findStudent($id);
        $student->status = 'withdrawn';
        $students->saveOrFail($student);
        $this->audit()->log(
            $this->viewer,
            'student.withdrawn',
            'student',
            $id,
            sprintf('Withdrew %s %s from the register', (string)$student->first_name, (string)$student->last_name),
        );

        return $this->json(StudentSerializer::one($student));
    }

    /**
     * DELETE /students/{id} — always refused (409). A student's record is
     * regulated data that a school can never hard-delete: withdrawal (a status
     * change, see withdraw()) archives the student, and duplicate-merge is the
     * only path that ever removes a record from the register. There is no
     * dependent-record exemption — even a just-created student is undeletable —
     * so the endpoint refuses unconditionally without a lookup.
     */
    public function delete(string $id): Response
    {
        $this->fail(409, Messages::STUDENT_HAS_RECORDS);
    }

    /**
     * POST /students/{id}/class { classGroupId } — updates the live placement
     * only (enrolment history is written by promotion, not here). Prefers the
     * canonical class id; the legacy `{ classGroup }` name is still accepted.
     */
    public function assignClass(string $id): Response
    {
        $students = $this->fetchTable('EmsStudents');
        $student = $this->findStudent($id);
        [$classGroupId, $className] = $this->resolveClassPlacement($this->body());
        $student->class_group_id = $classGroupId;
        $student->class_group = $className;
        $students->saveOrFail($student);

        return $this->json(StudentSerializer::one($student));
    }

    /**
     * GET /students/{id}/guardians — primary first, then firstName asc.
     */
    public function guardians(string $id): Response
    {
        $student = $this->findStudent($id);

        $rows = $this->tenant()->query('EmsGuardians')
            ->where(['student_id' => $student->id])
            ->orderByDesc('is_primary')
            ->orderByAsc('first_name')
            ->all();

        return $this->json(array_map([StudentSerializer::class, 'guardian'], $rows->toList()));
    }

    /**
     * POST /students/{id}/guardians — GuardianInput. The first guardian is
     * always primary; an explicit primary demotes the others; the student's
     * denormalized contact is always re-synced (§3.10).
     */
    public function addGuardian(string $id): Response
    {
        $student = $this->findStudent($id);
        $guardians = $this->fetchTable('EmsGuardians');
        $body = $this->body();
        if (
            trim((string)($body['firstName'] ?? '')) === ''
            || trim((string)($body['lastName'] ?? '')) === ''
            || trim((string)($body['phone'] ?? '')) === ''
        ) {
            $this->fail(422, Messages::GUARDIAN_DETAILS_REQUIRED);
        }

        $existingCount = $this->tenant()->query('EmsGuardians')
            ->where(['student_id' => $student->id])
            ->count();
        $isPrimary = $existingCount === 0 ? true : (bool)($body['isPrimary'] ?? false);

        $guardian = $guardians->getConnection()->transactional(function () use ($guardians, $student, $body, $isPrimary) {
            if ($isPrimary) {
                $this->demoteOtherPrimaries((string)$student->id, null);
            }
            $guardian = $guardians->saveOrFail($guardians->newEntity(
                $this->guardianData($body, (string)$student->id) + ['is_primary' => $isPrimary],
            ));
            $this->syncPrimaryContact((string)$student->id);

            return $guardian;
        });

        return $this->json(StudentSerializer::guardian($guardian), 201);
    }

    /**
     * GET /students/{id}/attendance — { records (date desc), summary (derived) }.
     */
    public function attendance(string $id): Response
    {
        $student = $this->findStudent($id);

        $records = $this->tenant()->query('EmsAttendanceRecords')
            ->where(['student_id' => $student->id])
            ->orderByDesc('date')
            ->all()
            ->toList();

        return $this->json([
            'records' => array_map([StudentSerializer::class, 'attendanceRecord'], $records),
            'summary' => StudentSerializer::attendanceSummary($records),
        ]);
    }

    /**
     * GET /students/{id}/academics — "{session} {term}" desc.
     */
    public function academics(string $id): Response
    {
        $student = $this->findStudent($id);

        $rows = $this->tenant()->query('EmsAcademicTermRecords')
            ->where(['student_id' => $student->id])
            ->all()
            ->toList();
        usort($rows, function ($a, $b) {
            return strcmp(
                $b->session . ' ' . $b->term,
                $a->session . ' ' . $a->term,
            );
        });

        return $this->json(array_map([StudentSerializer::class, 'academicRecord'], $rows));
    }

    /**
     * GET /students/{id}/enrolments — session desc, startedOn desc.
     */
    public function enrolments(string $id): Response
    {
        $student = $this->findStudent($id);

        $rows = $this->tenant()->query('EmsEnrolments')
            ->where(['student_id' => $student->id])
            ->orderByDesc('session')
            ->orderByDesc('started_on')
            ->all();

        return $this->json(array_map([StudentSerializer::class, 'enrolment'], $rows->toList()));
    }

    /**
     * Scope assert first (403 says nothing about existence), then 404.
     */
    private function findStudent(string $id): EntityInterface
    {
        // Scope assert first (403 says nothing about existence), then the
        // shared tenant-scoped 404 lookup.
        $this->scope()->assertStudentAccess($id);

        return $this->findOr404('EmsStudents', $id, Messages::STUDENT_NOT_FOUND);
    }

    /**
     * StudentInput → validated storage columns (422s instead of 500s on bad
     * input). A blank admission number auto-allocates the school's next one
     * on create, and keeps the current one on edit; a supplied number must be
     * unique within the school.
     */
    private function validatedStudentData(?array $body = null, ?string $editId = null): array
    {
        $data = $this->studentData($body);
        if ($data['first_name'] === '' || $data['last_name'] === '') {
            $this->fail(422, Messages::STUDENT_NAME_REQUIRED);
        }

        $dob = (string)$data['date_of_birth'];
        if (!Imports::isRealDate($dob)) {
            $this->fail(422, Messages::STUDENT_DOB_INVALID);
        }
        if ($dob > FrozenDate::today()->format('Y-m-d')) {
            $this->fail(422, Messages::STUDENT_DOB_FUTURE);
        }

        $gender = mb_strtolower(trim((string)$data['gender']));
        if ($gender !== '' && !in_array($gender, ['female', 'male', 'other'], true)) {
            $this->fail(422, Messages::STUDENT_GENDER_INVALID);
        }
        $data['gender'] = $gender;

        $status = mb_strtolower(trim((string)$data['status']));
        $status = $status === '' ? 'enrolled' : $status;
        if (!in_array($status, ['enrolled', 'applicant', 'graduated', 'withdrawn'], true)) {
            $this->fail(422, Messages::STUDENT_STATUS_INVALID);
        }
        $data['status'] = $status;

        if (!Imports::isRealDate((string)$data['enrolled_on'])) {
            $this->fail(422, Messages::STUDENT_ENROLLED_ON_INVALID);
        }

        $admission = (string)$data['admission_number'];
        if ($admission === '') {
            if ($editId !== null) {
                unset($data['admission_number']);
            } else {
                $data['admission_number'] = AdmissionNumbers::next($this->getTableLocator(), $this->viewer->schoolId);
            }
        } else {
            $taken = $this->tenant()->query('EmsStudents')->where(['admission_number' => $admission]);
            if ($editId !== null) {
                $taken = $taken->where(['id !=' => $editId]);
            }
            if ($taken->count() > 0) {
                $this->fail(422, Messages::ADMISSION_NUMBER_TAKEN);
            }
        }

        return $data;
    }

    /**
     * StudentInput → storage columns.
     */
    private function studentData(?array $body = null): array
    {
        $body ??= $this->body();
        [$classGroupId, $className] = $this->resolveClassPlacement($body);

        return [
            'school_id' => $this->viewer->schoolId,
            'admission_number' => trim((string)($body['admissionNumber'] ?? '')),
            'first_name' => trim((string)($body['firstName'] ?? '')),
            'last_name' => trim((string)($body['lastName'] ?? '')),
            'date_of_birth' => (string)($body['dateOfBirth'] ?? ''),
            'gender' => (string)($body['gender'] ?? ''),
            'class_group_id' => $classGroupId,
            'class_group' => $className,
            'status' => (string)($body['status'] ?? 'enrolled'),
            'guardian_name' => trim((string)($body['guardianName'] ?? '')),
            'guardian_phone' => trim((string)($body['guardianPhone'] ?? '')),
            'enrolled_on' => (string)($body['enrolledOn'] ?? '') !== ''
                ? (string)$body['enrolledOn']
                : FrozenDate::today()->format('Y-m-d'),
        ];
    }

    /** Map a guardian request into tenant-scoped storage columns. */
    private function guardianData(array $body, string $studentId): array
    {
        return [
            'school_id' => $this->viewer->schoolId,
            'student_id' => $studentId,
            'first_name' => trim((string)($body['firstName'] ?? '')),
            'last_name' => trim((string)($body['lastName'] ?? '')),
            'relationship' => (string)($body['relationship'] ?? 'guardian'),
            'phone' => trim((string)($body['phone'] ?? '')),
            'email' => trim((string)($body['email'] ?? '')),
            'occupation' => trim((string)($body['occupation'] ?? '')),
        ];
    }

    /**
     * Resolve a request body's class placement into
     * [class_group_id|null, class_group_name].
     *
     * Prefers the canonical `classGroupId` (422 if it names no class); otherwise
     * falls back to the legacy `classGroup` name — resolved to an id when it
     * matches a class, else kept as free text with a null id (which every
     * student-side read still resolves through its name).
     *
     * @return array{0: ?string, 1: string}
     */
    private function resolveClassPlacement(array $body): array
    {
        $id = trim((string)($body['classGroupId'] ?? ''));
        if ($id !== '') {
            $class = $this->tenant()->query('EmsClassGroups')
                ->where(['id' => $id])
                ->first();
            if ($class === null) {
                $this->fail(422, Messages::STUDENT_CLASS_UNKNOWN);
            }

            return [(string)$class->id, (string)$class->name];
        }

        $name = trim((string)($body['classGroup'] ?? ''));
        if ($name === '') {
            return [null, ''];
        }
        $class = $this->tenant()->query('EmsClassGroups')
            ->where(['name' => $name])
            ->orderByAsc('created')
            ->first();

        return [$class !== null ? (string)$class->id : null, $name];
    }

    /**
     * §3.10 side effect: an enrolled student gains an active enrolment in the
     * current open session. The class name and level are taken from the linked
     * class row (admin-defined levels); a still-unlinked student falls back to
     * the class name minus its trailing stream letter ("JSS 1A" → "JSS 1").
     */
    private function createEnrolmentIfCurrent(EntityInterface $student): void
    {
        Enrolments::createIfCurrent($this->getTableLocator(), $this->viewer->schoolId, $student);
    }

    /**
     * Clear is_primary on every guardian of the student except $keepId.
     */
    private function demoteOtherPrimaries(string $studentId, ?string $keepId): void
    {
        $guardians = $this->fetchTable('EmsGuardians');
        $conditions = [
            'school_id' => $this->viewer->schoolId,
            'student_id' => $studentId,
            'is_primary' => true,
        ];
        if ($keepId !== null) {
            $conditions['id !='] = $keepId;
        }
        $guardians->updateAll(['is_primary' => false], $conditions);
    }

    /**
     * Re-sync the student's denormalized guardianName/guardianPhone from the
     * primary guardian — after EVERY guardian mutation (§3.10).
     */
    private function syncPrimaryContact(string $studentId): void
    {
        $primary = $this->tenant()->query('EmsGuardians')
            ->where([
                'student_id' => $studentId,
                'is_primary' => true,
            ])
            ->first();

        $students = $this->fetchTable('EmsStudents');
        $student = $this->tenant()->query('EmsStudents')
            ->where(['id' => $studentId])
            ->first();
        if ($student === null) {
            return;
        }
        $student->guardian_name = $primary === null
            ? ''
            : trim($primary->first_name . ' ' . $primary->last_name);
        $student->guardian_phone = $primary === null ? '' : (string)$primary->phone;
        $students->saveOrFail($student);
    }
}
