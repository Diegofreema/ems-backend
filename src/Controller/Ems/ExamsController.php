<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\ExamSerializer;
use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;

/**
 * Exams — the examination lifecycle, sittings, papers, the grade sheet, the
 * computed broadsheet / report card, and the result-release workflow
 * (document.md §3.1). The heavy read models are built by App\Ems\Academics so
 * the grade sheet, broadsheet, report card and transcript can never disagree;
 * the durability rules (a released version is pinned and never overwritten) are
 * enforced HERE, exactly as a real backend must.
 */
class ExamsController extends AppController
{
    /**
     * GET /exams — paginated; query matches title/session, sort recent|title.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = trim((string)$this->request->getQuery('query', ''));
        $status = (string)$this->request->getQuery('status', 'all');
        $sort = (string)$this->request->getQuery('sort', 'recent');

        $q = $this->tenant()->query('EmsExams');

        if ($status !== 'all' && $status !== '') {
            $q->where(['status' => $status]);
        }
        if ($query !== '') {
            $like = '%' . $query . '%';
            $q->where(['OR' => ['title LIKE' => $like, 'session LIKE' => $like]]);
        }

        $total = $q->count();
        if ($sort === 'title') {
            $q->orderByAsc('title')->orderByDesc('session');
        } else {
            $q->orderByDesc('session')->orderByDesc('start_date');
        }
        $q->orderByDesc('created');
        $rows = $q->limit($params['pageSize'])
            ->offset(($params['page'] - 1) * $params['pageSize'])
            ->all();

        return $this->paginated(
            array_map([ExamSerializer::class, 'exam'], $rows->toList()),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    /**
     * GET /exams/{id}.
     */
    public function view(string $id): Response
    {
        return $this->json(ExamSerializer::exam($this->findExam($id)));
    }

    /**
     * POST /exams — no validation, no audit (mock parity).
     */
    public function add(): Response
    {
        $body = $this->body();
        $exams = $this->fetchTable('EmsExams');
        $exam = $exams->newEntity([
            'school_id' => $this->viewer->schoolId,
            'title' => (string)($body['title'] ?? ''),
            'session' => (string)($body['session'] ?? ''),
            'term' => (string)($body['term'] ?? 'First'),
            'start_date' => (string)($body['startDate'] ?? ''),
            'end_date' => (string)($body['endDate'] ?? ''),
            'status' => (string)($body['status'] ?? 'draft'),
            'ca_max' => (int)($body['caMax'] ?? 40),
            'exam_max' => (int)($body['examMax'] ?? 60),
        ], ['validate' => false]);

        return $this->json(ExamSerializer::exam($exams->saveOrFail($exam)), 201);
    }

    /**
     * GET /exams/{id}/schedules — date → startTime → level → subject.
     */

    /**
     * PUT /exams/{id} — correct title/dates/config until the exam is released.
     * Status is deliberately NOT editable here: the lifecycle only moves through
     * the guarded transitions (start-grading, release, reopen), so a plain edit
     * can never skip grading or publish results without a release record.
     */
    public function edit(string $id): Response
    {
        $exam = $this->findExam($id);
        if ((string)$exam->status === 'published') {
            $this->fail(422, Messages::EXAM_RELEASED_EDIT);
        }
        $body = $this->body();
        foreach (
            ['title' => 'title', 'session' => 'session', 'term' => 'term',
             'startDate' => 'start_date', 'endDate' => 'end_date'] as $in => $col
        ) {
            if (array_key_exists($in, $body)) {
                $exam->{$col} = (string)$body[$in];
            }
        }
        if (array_key_exists('caMax', $body)) {
            $exam->ca_max = (int)$body['caMax'];
        }
        if (array_key_exists('examMax', $body)) {
            $exam->exam_max = (int)$body['examMax'];
        }
        $exams = $this->fetchTable('EmsExams');

        return $this->json(ExamSerializer::exam($exams->saveOrFail($exam)));
    }

    /**
     * POST /exams/{id}/start-grading — officers only; the one forward step the
     * lifecycle was missing. A draft or scheduled exam moves to grading so marks
     * can be entered and, later, released. Grading and published exams refuse:
     * the only way out of published is the audited reopen (never a status edit),
     * which is why edit() never touches status at all.
     */
    public function startGrading(string $id): Response
    {
        $exam = $this->findExam($id);
        $status = (string)$exam->status;
        if ($status !== 'draft' && $status !== 'scheduled') {
            $this->fail(409, Messages::GRADING_ONLY_BEFORE_RELEASE);
        }
        $exam->status = 'grading';
        $this->fetchTable('EmsExams')->saveOrFail($exam);
        $this->audit()->log(
            $this->viewer,
            'exam.grading_started',
            'exam',
            $id,
            sprintf('Started grading %s %s', (string)$exam->title, (string)$exam->session),
        );

        return $this->json(ExamSerializer::exam($exam));
    }

    /** DELETE /exams/{id} — only while nothing hangs off it. */
    public function delete(string $id): Response
    {
        $exam = $this->findExam($id);
        $this->assertNoReferences($id, [
            'EmsExamSchedules' => 'exam_id',
            'EmsExamPapers' => 'exam_id',
            'EmsExamGrades' => 'exam_id',
            'EmsAssessments' => 'exam_id',
        ], Messages::EXAM_HAS_RECORDS);
        $this->fetchTable('EmsExams')->deleteOrFail($exam);

        return $this->response->withStatus(204);
    }

    public function schedules(string $id): Response
    {
        $this->findExam($id);
        $rows = $this->tenant()->query('EmsExamSchedules')
            ->leftJoin(['Subj' => 'ems_subjects'], ['Subj.id = EmsExamSchedules.subject_id'])
            ->where(['EmsExamSchedules.exam_id' => $id])
            ->orderByAsc('EmsExamSchedules.date')
            ->orderByAsc('EmsExamSchedules.start_time')
            ->orderByAsc('EmsExamSchedules.level')
            ->orderByAsc('Subj.name')
            ->all();

        return $this->json(array_map([ExamSerializer::class, 'schedule'], $rows->toList()));
    }

    /**
     * POST /exams/{id}/schedules — one sitting; a subject sits once per level.
     */
    public function addSchedule(string $id): Response
    {
        $exam = $this->findExam($id);
        $body = $this->body();
        $subject = (string)($body['subject'] ?? '');
        $level = (string)($body['level'] ?? '');
        $date = (string)($body['date'] ?? '');
        $schedules = $this->fetchTable('EmsExamSchedules');
        $subjectId = $this->requireSubjectId($subject);

        $duplicate = $this->tenant()->query('EmsExamSchedules')
            ->where([
                'exam_id' => $id,
                'subject_id' => $subjectId,
                'level' => $level,
            ])
            ->count();
        if ($duplicate > 0) {
            $this->fail(422, sprintf(Messages::SCHEDULE_DUPLICATE, $subject, $level));
        }
        if ($date < (string)Wire::date($exam->start_date) || $date > (string)Wire::date($exam->end_date)) {
            $this->fail(422, Messages::SCHEDULE_OUTSIDE_WINDOW);
        }

        $schedule = $schedules->newEntity([
            'school_id' => $this->viewer->schoolId,
            'exam_id' => $id,
            'subject_id' => $subjectId,
            'level' => $level,
            'date' => $date,
            'start_time' => (string)($body['startTime'] ?? ''),
            'end_time' => (string)($body['endTime'] ?? ''),
            'venue' => (string)($body['venue'] ?? ''),
        ], ['validate' => false]);

        return $this->json(ExamSerializer::schedule($schedules->saveOrFail($schedule)), 201);
    }

    /**
     * DELETE /exam-schedules/{id}.
     */
    public function removeSchedule(string $id): Response
    {
        $schedules = $this->fetchTable('EmsExamSchedules');
        $row = $this->tenant()->query('EmsExamSchedules')
            ->where(['id' => $id])
            ->first();
        if ($row === null) {
            $this->fail(404, Messages::SCHEDULE_NOT_FOUND);
        }
        $schedules->deleteOrFail($row);

        return $this->json(null, 204);
    }

    /**
     * GET /exams/{id}/paper — the composed paper resolved against the bank.
     */
    public function paper(string $id): Response
    {
        $exam = $this->findExam($id);
        $subject = (string)$this->request->getQuery('subject', '');
        $level = (string)$this->request->getQuery('level', '');
        $subjectId = $this->requireSubjectId($subject);

        $paperRow = $this->tenant()->query('EmsExamPapers')
            ->where([
                'exam_id' => $id,
                'subject_id' => $subjectId,
                'level' => $level,
            ])
            ->first();

        $bankRows = $this->tenant()->query('EmsQuestions')
            ->where(['subject_id' => $subjectId])
            ->orderByAsc('type') // objective before theory
            ->orderByAsc('marks')
            ->orderByAsc('topic')
            ->all()
            ->toList();
        $bank = array_map([ExamSerializer::class, 'question'], $bankRows);
        $byId = [];
        foreach ($bank as $q) {
            $byId[$q['id']] = $q;
        }

        $questionIds = [];
        if ($paperRow !== null) {
            $ids = $paperRow->question_ids;
            if (is_string($ids)) {
                $ids = json_decode($ids, true);
            }
            $questionIds = is_array($ids) ? $ids : [];
        }
        $questions = [];
        foreach ($questionIds as $qid) {
            if (isset($byId[(string)$qid])) {
                $questions[] = $byId[(string)$qid];
            }
        }
        $totalMarks = 0;
        foreach ($questions as $q) {
            $totalMarks += (int)$q['marks'];
        }

        return $this->json([
            'exam' => ExamSerializer::exam($exam),
            'subject' => $subject,
            'level' => $level,
            'paper' => $paperRow === null ? null : ExamSerializer::paper($paperRow),
            'questions' => $questions,
            'totalMarks' => $totalMarks,
            'bank' => $bank,
        ]);
    }

    /**
     * PUT /exams/{id}/paper — upsert the ordered question selection.
     */
    public function savePaper(string $id): Response
    {
        $this->findExam($id);
        $body = $this->body();
        $subject = (string)($body['subject'] ?? $this->request->getQuery('subject', ''));
        $level = (string)($body['level'] ?? $this->request->getQuery('level', ''));
        $questionIds = is_array($body['questionIds'] ?? null) ? array_map('strval', $body['questionIds']) : [];

        if ($questionIds === []) {
            $this->fail(422, Messages::PAPER_EMPTY);
        }
        $bank = $this->tenant()->query('EmsQuestions')
            ->select(['id'])
            ->all()
            ->extract('id')
            ->toList();
        $bankSet = array_flip(array_map('strval', $bank));
        foreach ($questionIds as $qid) {
            if (!isset($bankSet[$qid])) {
                $this->fail(422, Messages::PAPER_UNKNOWN_QUESTION);
            }
        }

        $papers = $this->fetchTable('EmsExamPapers');
        $subjectId = $this->requireSubjectId($subject);
        $existing = $this->tenant()->query('EmsExamPapers')
            ->where([
                'exam_id' => $id,
                'subject_id' => $subjectId,
                'level' => $level,
            ])
            ->first();
        if ($existing !== null) {
            $existing->question_ids = array_values($questionIds);
            $papers->saveOrFail($existing);

            return $this->json(ExamSerializer::paper($existing));
        }
        $paper = $papers->newEntity([
            'school_id' => $this->viewer->schoolId,
            'exam_id' => $id,
            'subject_id' => $subjectId,
            'level' => $level,
            'question_ids' => array_values($questionIds),
        ], ['validate' => false]);

        return $this->json(ExamSerializer::paper($papers->saveOrFail($paper)));
    }

    /**
     * GET /exams/{id}/gradesheet — classId, subject.
     */
    public function gradesheet(string $id): Response
    {
        $exam = $this->findExam($id);
        $classGroup = $this->findClassScoped((string)$this->request->getQuery('classId', ''));
        $subject = (string)$this->request->getQuery('subject', '');

        return $this->json($this->academicsEngine()->gradesheet($exam, $classGroup, $subject));
    }

    /**
     * PUT /exams/{id}/grades — classId, subject (query), entries (body). A
     * released exam refuses; an assessment-backed offering ignores incoming CA.
     */
    public function saveGrades(string $id): Response
    {
        $classId = (string)$this->request->getQuery('classId', '');
        $subject = (string)$this->request->getQuery('subject', '');
        $this->scope()->assertClassAccess($classId);

        $exam = $this->findExam($id);
        if ((string)$exam->status === 'published') {
            $this->fail(422, Messages::GRADES_PUBLISHED);
        }
        $subjectId = $this->requireSubjectId($subject);
        $caBacked = $this->academicsEngine()->offeringHasAssessments($id, $classId, $subjectId);
        $entries = is_array($this->body()['entries'] ?? null) ? $this->body()['entries'] : [];

        $grades = $this->fetchTable('EmsExamGrades');
        $grades->getConnection()->transactional(function () use ($grades, $entries, $id, $classId, $subjectId, $caBacked): void {
            foreach ($entries as $entry) {
                $studentId = (string)($entry['studentId'] ?? '');
                $ca = array_key_exists('ca', $entry) ? $entry['ca'] : null;
                $paper = array_key_exists('exam', $entry) ? $entry['exam'] : null;
                $existing = $this->tenant()->query('EmsExamGrades')
                    ->where([
                        'exam_id' => $id,
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                    ])
                    ->first();
                if ($existing !== null) {
                    if (!$caBacked) {
                        $existing->ca = $ca;
                    }
                    $existing->exam = $paper;
                    $grades->saveOrFail($existing);
                } else {
                    $grades->saveOrFail($grades->newEntity([
                        'school_id' => $this->viewer->schoolId,
                        'exam_id' => $id,
                        'student_id' => $studentId,
                        'class_group_id' => $classId,
                        'subject_id' => $subjectId,
                        'ca' => $caBacked ? null : $ca,
                        'exam' => $paper,
                    ], ['validate' => false]));
                }
            }
        });

        return $this->json(null, 204);
    }

    /**
     * GET /exams/{id}/broadsheet — classId.
     */
    public function broadsheet(string $id): Response
    {
        $exam = $this->findExam($id);
        $classGroup = $this->findClassScoped((string)$this->request->getQuery('classId', ''));

        return $this->json($this->academicsEngine()->broadsheet($exam, $classGroup));
    }

    /**
     * GET /exams/{id}/report-card/{studentId} — family-scoped.
     */
    public function reportCard(string $id, string $studentId): Response
    {
        $this->scope()->assertStudentAccess($studentId);
        $exam = $this->findExam($id);
        $school = $this->fetchTable('EmsSchools')->find()
            ->where(['id' => $this->viewer->schoolId])->first();
        if ($school === null) {
            $this->fail(404, Messages::SCHOOL_NOT_FOUND);
        }
        $student = $this->tenant()->query('EmsStudents')
            ->where(['id' => $studentId])->first();
        if ($student === null) {
            $this->fail(404, Messages::STUDENT_NOT_FOUND);
        }

        return $this->json($this->academicsEngine()->reportCard($school, $exam, $student));
    }

    /**
     * GET /exams/{id}/releases — newest version first.
     */
    public function releases(string $id): Response
    {
        $this->findExam($id);
        $rows = $this->tenant()->query('EmsResultReleases')
            ->where(['exam_id' => $id])
            ->orderByDesc('version')
            ->all();

        return $this->json(array_map([ExamSerializer::class, 'release'], $rows->toList()));
    }

    /**
     * GET /exams/{id}/release-preview.
     */
    public function releasePreview(string $id): Response
    {
        $this->findExam($id);

        return $this->json($this->academicsEngine()->releasePreview($id));
    }

    /**
     * POST /exams/{id}/release — officers only; pins the active scheme version.
     */
    public function release(string $id): Response
    {
        $exam = $this->findExam($id);
        if ((string)$exam->status !== 'grading') {
            $this->fail(409, Messages::RELEASE_ONLY_GRADING);
        }
        $reason = trim((string)($this->body()['reason'] ?? ''));
        $releases = $this->fetchTable('EmsResultReleases');
        $prior = $this->tenant()->query('EmsResultReleases')
            ->where(['exam_id' => $id])
            ->count();
        $version = $prior + 1;
        $schemeVersion = (int)$this->grading()->activeScheme()['version'];

        $release = $releases->newEntity([
            'school_id' => $this->viewer->schoolId,
            'exam_id' => $id,
            'version' => $version,
            'released_on' => FrozenDate::today(),
            'released_by' => $this->viewer->name,
            'status' => 'released',
            'scheme_version' => $schemeVersion,
            'reason' => $prior > 0 && $reason !== '' ? $reason : null,
        ], ['validate' => false]);

        $exams = $this->fetchTable('EmsExams');
        $releases->getConnection()->transactional(function () use ($releases, $release, $exams, $exam): void {
            $releases->saveOrFail($release);
            $exam->status = 'published';
            $exams->saveOrFail($exam);
        });
        $this->audit()->log(
            $this->viewer,
            'results.released',
            'exam',
            $id,
            sprintf('Released %s %s results (version %d)', (string)$exam->title, (string)$exam->session, $version),
        );

        return $this->json(ExamSerializer::release($release));
    }

    /**
     * POST /exams/{id}/reopen — officers only; supersedes the released row.
     */
    public function reopen(string $id): Response
    {
        $exam = $this->findExam($id);
        if ((string)$exam->status !== 'published') {
            $this->fail(409, Messages::REOPEN_ONLY_PUBLISHED);
        }
        $reason = trim((string)($this->body()['reason'] ?? ''));
        if ($reason === '') {
            $this->fail(422, Messages::REOPEN_REASON);
        }

        $releases = $this->fetchTable('EmsResultReleases');
        $exams = $this->fetchTable('EmsExams');
        $current = $this->tenant()->query('EmsResultReleases')
            ->where(['exam_id' => $id, 'status' => 'released'])
            ->first();
        $releases->getConnection()->transactional(function () use ($releases, $current, $exams, $exam): void {
            if ($current !== null) {
                $current->status = 'superseded';
                $current->superseded_on = FrozenTime::now('UTC');
                $releases->saveOrFail($current);
            }
            $exam->status = 'grading';
            $exams->saveOrFail($exam);
        });
        $this->audit()->log(
            $this->viewer,
            'results.reopened',
            'exam',
            $id,
            sprintf('Reopened %s %s results for correction', (string)$exam->title, (string)$exam->session),
            $reason,
        );

        return $this->json(ExamSerializer::exam($exam));
    }

    // --- helpers ----------------------------------------------------------

    private function findExam(string $id): EntityInterface
    {
        return $this->findOr404('EmsExams', $id, Messages::EXAM_NOT_FOUND);
    }

    private function findClass(string $id): EntityInterface
    {
        return $this->findOr404('EmsClassGroups', $id, Messages::CLASS_NOT_FOUND);
    }

    /**
     * Resolve a class within the tenant AND assert the viewer's scope reaches
     * it (§1.4) — the gradesheet/broadsheet READ counterpart to the
     * assertClassAccess that the saveGrades WRITE already applies, so a teacher
     * can never read another class's marks/rankings by passing its id as the
     * ?classId of an exam they can otherwise see.
     */
    private function findClassScoped(string $id): EntityInterface
    {
        $group = $this->findClass($id);
        $this->scope()->assertClassAccess((string)$group->id);

        return $group;
    }
}
