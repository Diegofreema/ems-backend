<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\AdmissionNumbers;
use App\Ems\Messages;
use App\Ems\Serializer\AdmissionSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\Utility\Text;

/**
 * Admissions — cycles, the application queue, the reviewer state machine, and
 * the enrolment transaction (document.md §3.6).
 *
 * The state machine and numbering are enforced HERE, exactly as a real backend
 * must: a transition is legal only from its declared origin states (409), every
 * decision appends an append-only review, and enrolment turns the accepted
 * applicant into the student without re-keying identity — all in one transaction.
 */
class AdmissionsController extends AppController
{
    /**
     * Legal transitions: action → { from states, destination status }.
     *
     * @var array<string, array{from: array<int, string>, to: string}>
     */
    private const TRANSITIONS = [
        'start_review' => ['from' => ['submitted'], 'to' => 'under_review'],
        'waitlist' => ['from' => ['under_review'], 'to' => 'waitlisted'],
        'offer' => ['from' => ['under_review', 'waitlisted'], 'to' => 'offered'],
        'accept' => ['from' => ['offered'], 'to' => 'accepted'],
        'decline' => ['from' => ['submitted', 'under_review', 'waitlisted', 'offered'], 'to' => 'declined'],
        'withdraw' => ['from' => ['submitted', 'under_review', 'waitlisted', 'offered'], 'to' => 'withdrawn'],
        'mark_expired' => ['from' => ['offered'], 'to' => 'expired'],
    ];

    /**
     * GET /admission-cycles — bounded reference set, opensOn desc.
     */
    public function cycles(): Response
    {
        $rows = $this->tenant()->query('EmsAdmissionCycles')
            ->orderByDesc('opens_on')
            ->orderByDesc('id')
            ->all();

        return $this->json(array_map([AdmissionSerializer::class, 'cycle'], $rows->toList()));
    }

    /**
     * GET /admission-cycles/open — the open cycle whose window contains today.
     */
    public function openCycle(): Response
    {
        $cycle = $this->findOpenCycle();

        return $this->json($cycle === null ? null : AdmissionSerializer::cycle($cycle));
    }

    /**
     * GET /applications — paginated; query matches full name or number.
     */

    /** POST /admissions/cycles — open a new intake (administrator/registrar). */
    public function addCycle(): Response
    {
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));
        $opens = (string)($body['opensOn'] ?? '');
        $closes = (string)($body['closesOn'] ?? '');
        if ($name === '') {
            $this->fail(422, Messages::CYCLE_NAME_REQUIRED);
        }
        if ($opens === '' || $closes === '') {
            $this->fail(422, Messages::CYCLE_DATES_REQUIRED);
        }
        if ($opens >= $closes) {
            $this->fail(422, Messages::CYCLE_DATE_ORDER);
        }
        $cycles = $this->fetchTable('EmsAdmissionCycles');
        $row = $cycles->saveOrFail($cycles->newEntity([
            'school_id' => $this->viewer->schoolId,
            'name' => $name,
            'session' => trim((string)($body['session'] ?? '')),
            'opens_on' => $opens,
            'closes_on' => $closes,
            'status' => 'open',
        ], ['validate' => false]));
        $this->audit()->log(
            $this->viewer,
            'cycle.opened',
            'cycle',
            (string)$row->id,
            sprintf('Opened the admission cycle %s', $name),
        );

        return $this->json(AdmissionSerializer::cycle($row), 201);
    }

    /** PUT /admissions/cycles/{id} — adjust dates/name while it runs. */
    public function editCycle(string $id): Response
    {
        $cycles = $this->fetchTable('EmsAdmissionCycles');
        $row = $this->tenant()->query('EmsAdmissionCycles')->where(['id' => $id])->first();
        if ($row === null) {
            $this->fail(404, Messages::CYCLE_NOT_FOUND);
        }
        $body = $this->body();
        if (array_key_exists('name', $body) && trim((string)$body['name']) !== '') {
            $row->name = trim((string)$body['name']);
        }
        if (array_key_exists('opensOn', $body)) {
            $row->opens_on = (string)$body['opensOn'];
        }
        if (array_key_exists('closesOn', $body)) {
            $row->closes_on = (string)$body['closesOn'];
        }
        if ((string)$row->opens_on >= (string)$row->closes_on) {
            $this->fail(422, Messages::CYCLE_DATE_ORDER);
        }
        $cycles->saveOrFail($row);

        return $this->json(AdmissionSerializer::cycle($row));
    }

    /** POST /admissions/cycles/{id}/close — stop taking applications. */
    public function closeCycle(string $id): Response
    {
        $cycles = $this->fetchTable('EmsAdmissionCycles');
        $row = $this->tenant()->query('EmsAdmissionCycles')->where(['id' => $id])->first();
        if ($row === null) {
            $this->fail(404, Messages::CYCLE_NOT_FOUND);
        }
        $row->status = 'closed';
        $cycles->saveOrFail($row);
        $this->audit()->log(
            $this->viewer,
            'cycle.closed',
            'cycle',
            (string)$row->id,
            sprintf('Closed the admission cycle %s', (string)$row->name),
        );

        return $this->json(AdmissionSerializer::cycle($row));
    }

    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $status = (string)$this->request->getQuery('status', 'all');

        $q = $this->tenant()->query('EmsAdmissionApplications');

        if ($status !== 'all' && $status !== '') {
            $q->where(['status' => $status]);
        }
        if ($query !== '') {
            $like = '%' . $query . '%';
            $q->where(['OR' => [
                "CONCAT(first_name, ' ', last_name) LIKE" => $like,
                'application_number LIKE' => $like,
            ]]);
        }

        $total = $q->count();
        $rows = $q->orderByDesc('submitted_on')->orderByDesc('id')
            ->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([AdmissionSerializer::class, 'application'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    /**
     * GET /applications/summary — all nine status keys, zero-filled.
     */
    public function summary(): Response
    {
        $counts = [
            'submitted' => 0, 'under_review' => 0, 'waitlisted' => 0, 'offered' => 0,
            'accepted' => 0, 'declined' => 0, 'withdrawn' => 0, 'expired' => 0, 'enrolled' => 0,
        ];
        $rows = $this->tenant()->query('EmsAdmissionApplications')
            ->select(['status', 'n' => 'COUNT(*)'])
            ->groupBy(['status'])
            ->all();
        foreach ($rows as $row) {
            if (array_key_exists((string)$row->status, $counts)) {
                $counts[(string)$row->status] = (int)$row->n;
            }
        }

        return $this->json($counts);
    }

    /**
     * GET /applications/{id} — { application, cycle, reviews (decidedOn desc) }.
     */
    public function view(string $id): Response
    {
        $application = $this->findApplication($id);
        $cycle = $this->tenant()->query('EmsAdmissionCycles')
            ->where(['id' => $application->cycle_id])
            ->first();
        $reviews = $this->tenant()->query('EmsApplicationReviews')
            ->where(['application_id' => $id])
            ->orderByDesc('decided_on')
            ->orderByDesc('seq')
            ->all();

        return $this->json([
            'application' => AdmissionSerializer::application($application),
            'cycle' => $cycle === null ? null : AdmissionSerializer::cycle($cycle),
            'reviews' => array_map([AdmissionSerializer::class, 'review'], $reviews->toList()),
        ]);
    }

    /**
     * POST /applications/{id}/review — the reviewer state machine (§3.6).
     */
    public function review(string $id): Response
    {
        $applications = $this->fetchTable('EmsAdmissionApplications');
        $application = $this->findApplication($id);
        $body = $this->body();
        $action = (string)($body['action'] ?? '');
        $note = trim((string)($body['note'] ?? ''));
        $offer = is_array($body['offer'] ?? null) ? $body['offer'] : null;

        if (!isset(self::TRANSITIONS[$action])) {
            $this->fail(409, sprintf(
                Messages::APPLICATION_ILLEGAL_TRANSITION,
                str_replace('_', ' ', (string)$application->status),
            ));
        }
        $rule = self::TRANSITIONS[$action];
        if (!in_array((string)$application->status, $rule['from'], true)) {
            $this->fail(409, sprintf(
                Messages::APPLICATION_ILLEGAL_TRANSITION,
                str_replace('_', ' ', (string)$application->status),
            ));
        }

        $today = FrozenDate::today()->format('Y-m-d');
        if ($action === 'offer') {
            $expiresOn = (string)($offer['expiresOn'] ?? '');
            if ($offer === null || $expiresOn === '') {
                $this->fail(422, Messages::OFFER_EXPIRY_REQUIRED);
            }
            if ($expiresOn <= $today) {
                $this->fail(422, Messages::OFFER_EXPIRY_FUTURE);
            }
            $application->offer = [
                'madeOn' => $today,
                'expiresOn' => $expiresOn,
                'note' => trim((string)($offer['note'] ?? '')),
            ];
        }
        if ($action === 'decline' && $note === '') {
            $this->fail(422, Messages::APPLICATION_DECLINE_REASON);
        }
        if ($action === 'mark_expired') {
            $existing = is_array($application->offer) ? $application->offer : null;
            if ($existing === null || (string)($existing['expiresOn'] ?? '') >= $today) {
                $this->fail(422, Messages::OFFER_NOT_EXPIRED);
            }
        }

        $application->status = $rule['to'];
        $applications->getConnection()->transactional(function () use ($applications, $application, $id, $rule, $note): void {
            $applications->saveOrFail($application);
            $this->appendReview($id, $rule['to'], $note);
        });

        return $this->json(AdmissionSerializer::application($application));
    }

    /**
     * POST /applications/{id}/enrol — the enrolment transaction (§3.6).
     */
    public function enrol(string $id): Response
    {
        $applications = $this->fetchTable('EmsAdmissionApplications');
        $application = $this->findApplication($id);
        if ((string)$application->status !== 'accepted') {
            $this->fail(409, Messages::ENROL_ONLY_ACCEPTED);
        }
        $className = trim((string)($this->body()['classGroup'] ?? ''));
        $target = $this->tenant()->query('EmsClassGroups')
            ->where(['name' => $className])
            ->first();
        if ($target === null) {
            $this->fail(422, Messages::ENROL_CLASS_UNKNOWN);
        }
        if ((string)$target->level !== (string)$application->desired_level) {
            $this->fail(422, sprintf(
                Messages::ENROL_LEVEL_MISMATCH,
                (string)$target->name,
                (string)$target->level,
                (string)$application->desired_level,
            ));
        }

        $applications->getConnection()->transactional(function () use ($applications, $application, $id, $target): void {
            $student = $this->createStudentFromApplication($application, $target);
            $this->createApplicationEnrolment($application, $student, $target);
            $this->createPrimaryGuardian($application, (string)$student->id);
            $carried = $this->transferDocuments($id, (string)$student->id);

            $application->status = 'enrolled';
            $application->student_id = (string)$student->id;
            $applications->saveOrFail($application);

            $suffix = $carried > 0
                ? sprintf(' %d %s moved onto the student record.', $carried, $carried === 1 ? 'document' : 'documents')
                : '';
            $this->appendReview($id, 'enrolled', sprintf('Enrolled into %s.%s', (string)$target->name, $suffix));
        });

        return $this->json(AdmissionSerializer::application($application));
    }

    // --- helpers ---------------------------------------------------------

    private function findApplication(string $id): EntityInterface
    {
        return $this->findOr404('EmsAdmissionApplications', $id, Messages::APPLICATION_NOT_FOUND);
    }

    private function findOpenCycle(): ?EntityInterface
    {
        $today = FrozenDate::today()->format('Y-m-d');

        return $this->tenant()->query('EmsAdmissionCycles')
            ->where([
                'status' => 'open',
                'opens_on <=' => $today,
                'closes_on >=' => $today,
            ])
            ->orderByDesc('opens_on')
            ->first();
    }

    private function appendReview(string $applicationId, string $action, string $note): void
    {
        $reviews = $this->fetchTable('EmsApplicationReviews');
        $reviews->saveOrFail($reviews->newEntity([
            'school_id' => $this->viewer->schoolId,
            'application_id' => $applicationId,
            'reviewer' => $this->viewer->name,
            'action' => $action,
            'note' => $note,
            'decided_on' => FrozenDate::today(),
        ]));
    }

    private function createStudentFromApplication(EntityInterface $application, EntityInterface $target): EntityInterface
    {
        $students = $this->fetchTable('EmsStudents');
        $guardian = is_array($application->guardian) ? $application->guardian : [];

        $student = $students->newEntity([
            'school_id' => $this->viewer->schoolId,
            'admission_number' => AdmissionNumbers::next($this->getTableLocator(), $this->viewer->schoolId),
            'first_name' => (string)$application->first_name,
            'last_name' => (string)$application->last_name,
            'date_of_birth' => $application->date_of_birth,
            'gender' => (string)$application->gender,
            'class_group' => (string)$target->name,
            'class_group_id' => (string)$target->id,
            'status' => 'enrolled',
            'guardian_name' => trim(sprintf('%s %s', $guardian['firstName'] ?? '', $guardian['lastName'] ?? '')),
            'guardian_phone' => (string)($guardian['phone'] ?? ''),
            'enrolled_on' => FrozenDate::today(),
        ], ['validate' => false]);

        return $students->saveOrFail($student);
    }

    /** Keep admissions-created students in the same placement history as direct admissions. */
    private function createApplicationEnrolment(
        EntityInterface $application,
        EntityInterface $student,
        EntityInterface $target,
    ): void {
        $cycle = $this->tenant()->query('EmsAdmissionCycles')
            ->where(['id' => $application->cycle_id])
            ->first();
        if ($cycle === null || trim((string)$cycle->session) === '') {
            $this->fail(422, Messages::ENROL_SESSION_REQUIRED);
        }

        $enrolments = $this->fetchTable('EmsEnrolments');
        $enrolments->saveOrFail($enrolments->newEntity([
            'school_id' => $this->viewer->schoolId,
            'student_id' => (string)$student->id,
            'session' => (string)$cycle->session,
            'class_group' => (string)$target->name,
            'level' => (string)$target->level,
            'started_on' => FrozenDate::today()->format('Y-m-d'),
            'status' => 'active',
        ]));
    }

    private function createPrimaryGuardian(EntityInterface $application, string $studentId): void
    {
        $guardians = $this->fetchTable('EmsGuardians');
        $g = is_array($application->guardian) ? $application->guardian : [];
        $guardians->saveOrFail($guardians->newEntity([
            'school_id' => $this->viewer->schoolId,
            'student_id' => $studentId,
            'first_name' => (string)($g['firstName'] ?? ''),
            'last_name' => (string)($g['lastName'] ?? ''),
            'relationship' => (string)($g['relationship'] ?? 'guardian'),
            'phone' => (string)($g['phone'] ?? ''),
            'email' => (string)($g['email'] ?? ''),
            'occupation' => (string)($g['occupation'] ?? ''),
            'is_primary' => true,
        ], ['validate' => false]));
    }

    /**
     * Copy every application document onto the new student record — bytes and
     * verification state preserved, application copies kept (§3.8 internal).
     */
    private function transferDocuments(string $applicationId, string $studentId): int
    {
        $documents = $this->fetchTable('EmsDocuments');
        $source = $this->tenant()->query('EmsDocuments')
            ->where([
                'owner' => 'application',
                'owner_id' => $applicationId,
            ])
            ->all()
            ->toList();

        foreach ($source as $doc) {
            $newId = Text::uuid();
            $newPath = $this->storage()->storagePathFor($this->viewer->schoolId, 'student', $studentId, $newId);
            $this->storage()->copyObject((string)$doc->storage_path, $newPath);
            $copy = $documents->newEntity([
                'school_id' => $this->viewer->schoolId,
                'owner' => 'student',
                'owner_id' => $studentId,
                'name' => (string)$doc->name,
                'type' => (string)$doc->type,
                'content_type' => (string)$doc->content_type,
                'size_bytes' => (int)$doc->size_bytes,
                'storage_path' => $newPath,
                'uploaded_by' => (string)$doc->uploaded_by,
                'uploaded_on' => $doc->uploaded_on,
                'verification' => (string)$doc->verification,
                'verified_by' => $doc->verified_by,
                'verified_on' => $doc->verified_on,
                'verification_note' => $doc->verification_note,
            ], ['validate' => false]);
            $copy->id = $newId;
            $documents->saveOrFail($copy);
        }

        return count($source);
    }
}
