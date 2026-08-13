<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\ExamSerializer;
use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Assessments — teacher-created continuous assessment (document.md §3.2). A
 * teacher assigned to a class+subject creates assessments with their own maxima
 * and enters a score per enrolled student; the term's CA is DERIVED from these
 * by App\Ems\Academics, never typed straight onto the grade sheet. The
 * enrolled-only and teacher-assignment rules are enforced HERE at the seam, so
 * editing a URL cannot get a teacher into a class that is not theirs.
 */
class AssessmentsController extends AppController
{
    /**
     * Legal status transitions: from → [allowed destinations].
     *
     * @var array<string, array<int, string>>
     */
    private const TRANSITIONS = [
        'draft' => ['open'],
        'open' => ['closed'],
        'closed' => ['open', 'published'], // open = an authorised reopen
        'published' => [],
    ];

    /** @var array<int, string> Statuses in which scores may still be edited. */
    private const SCORE_EDITABLE = ['draft', 'open'];

    /**
     * GET /assessments — examId, classGroupId, subject → AssessmentSummary[].
     */

    /** PUT /assessments/{id} — correct fields while no scores exist. */
    public function edit(string $id): Response
    {
        $assessments = $this->fetchTable('EmsAssessments');
        $row = $this->tenant()->query('EmsAssessments')
            ->where(['id' => $id])
            ->first();
        if ($row === null) {
            $this->fail(404, Messages::ASSESSMENT_NOT_FOUND);
        }
        // Reach: a teacher may only edit an assessment of their own class —
        // the same S:cls narrowing add/setStatus/saveScores already apply.
        $this->scope()->assertClassAccess((string)$row->class_group_id);
        $this->assertNoScores($id);
        $body = $this->body();
        if (array_key_exists('name', $body)) {
            $row->name = (string)$body['name'];
        }
        if (array_key_exists('maximum', $body)) {
            $row->maximum = (int)round((float)$body['maximum']);
        }
        if (array_key_exists('dueOn', $body)) {
            $row->due_on = $body['dueOn'] ?? null ?: null;
        }

        return $this->json(ExamSerializer::assessment($assessments->saveOrFail($row)));
    }

    /** DELETE /assessments/{id} — only while no scores exist. */
    public function delete(string $id): Response
    {
        $assessments = $this->fetchTable('EmsAssessments');
        $row = $this->tenant()->query('EmsAssessments')
            ->where(['id' => $id])
            ->first();
        if ($row !== null) {
            $this->scope()->assertClassAccess((string)$row->class_group_id);
            $this->assertNoScores($id);
            $assessments->deleteOrFail($row);
        }

        return $this->response->withStatus(204);
    }

    private function assertNoScores(string $assessmentId): void
    {
        $this->assertNoReferences(
            $assessmentId,
            ['EmsAssessmentScores' => 'assessment_id'],
            Messages::ASSESSMENT_HAS_SCORES,
        );
    }

    public function index(): Response
    {
        $examId = (string)$this->request->getQuery('examId', '');
        $classGroupId = (string)$this->request->getQuery('classGroupId', '');
        $subject = (string)$this->request->getQuery('subject', '');
        $subjectId = $this->requireSubjectId($subject);

        $rosterIds = $this->rosterIds($classGroupId);
        $rosterCount = count($rosterIds);
        $scores = $this->fetchTable('EmsAssessmentScores');
        $rows = $this->tenant()->query('EmsAssessments')
            ->where([
                'exam_id' => $examId,
                'class_group_id' => $classGroupId,
                'subject_id' => $subjectId,
            ])
            ->all()
            ->toList();

        $summaries = array_map(function (EntityInterface $a) use ($scores, $rosterIds, $rosterCount) {
            $scored = $rosterIds === []
                ? 0
                : $this->tenant()->query('EmsAssessmentScores')->where([
                    'assessment_id' => (string)$a->id,
                    'student_id IN' => $rosterIds,
                ])->count();

            return ExamSerializer::assessmentSummary($a, $rosterCount, $scored);
        }, $rows);

        usort($summaries, function (array $a, array $b) {
            $ka = $a['dueOn'] ?? $a['createdOn'];
            $kb = $b['dueOn'] ?? $b['createdOn'];

            return strcmp((string)$ka, (string)$kb) ?: strcmp((string)$a['name'], (string)$b['name']);
        });

        return $this->json(array_values($summaries));
    }

    /**
     * GET /assessments/{id}.
     */
    public function view(string $id): Response
    {
        return $this->json(ExamSerializer::assessment($this->findAssessment($id)));
    }

    /**
     * POST /assessments — examId, classGroupId, subject + AssessmentInput.
     */
    public function add(): Response
    {
        $body = $this->body();
        $examId = (string)($body['examId'] ?? '');
        $classGroupId = (string)($body['classGroupId'] ?? '');
        $subject = (string)($body['subject'] ?? '');
        $this->scope()->assertClassAccess($classGroupId);

        $examExists = $this->tenant()->query('EmsExams')
            ->where(['id' => $examId])
            ->count();
        if ($examExists === 0) {
            $this->fail(404, Messages::EXAM_NOT_FOUND);
        }
        $name = trim((string)($body['name'] ?? ''));
        if ($name === '') {
            $this->fail(422, Messages::ASSESSMENT_NAME_REQUIRED);
        }
        $maximum = $body['maximum'] ?? null;
        if (!is_numeric($maximum) || (float)$maximum < 1 || (float)$maximum > 100) {
            $this->fail(422, Messages::ASSESSMENT_MAX_RANGE);
        }

        $assessments = $this->fetchTable('EmsAssessments');
        $assessment = $assessments->newEntity([
            'school_id' => $this->viewer->schoolId,
            'exam_id' => $examId,
            'class_group_id' => $classGroupId,
            'subject' => $subject,
            'name' => $name,
            'maximum' => (int)round((float)$maximum),
            'due_on' => $body['dueOn'] ?? null ?: null,
            'status' => 'draft',
            'created_by' => $this->viewer->name,
            'created_on' => FrozenDate::today(),
        ], ['validate' => false]);
        $assessments->saveOrFail($assessment);

        $this->audit()->log(
            $this->viewer,
            'assessment.created',
            'assessment',
            (string)$assessment->id,
            sprintf(
                'Created assessment "%s" (max %d) for %s %s',
                $name,
                (int)$assessment->maximum,
                $this->classNameOf($classGroupId),
                $subject,
            ),
        );

        return $this->json(ExamSerializer::assessment($assessment), 201);
    }

    /**
     * POST /assessments/{id}/status — the lifecycle state machine.
     */
    public function setStatus(string $id): Response
    {
        $assessment = $this->findAssessment($id);
        $this->scope()->assertClassAccess((string)$assessment->class_group_id);

        $from = (string)$assessment->status;
        $to = (string)($this->body()['status'] ?? '');
        $reason = trim((string)($this->body()['reason'] ?? ''));

        if (!isset(self::TRANSITIONS[$from]) || !in_array($to, self::TRANSITIONS[$from], true)) {
            $this->fail(409, sprintf(Messages::ASSESSMENT_ILLEGAL_TRANSITION, $from, $to));
        }
        $isReopen = $from === 'closed' && $to === 'open';
        if ($isReopen && $reason === '') {
            $this->fail(422, Messages::ASSESSMENT_REOPEN_REASON);
        }

        $assessment->status = $to;
        $this->fetchTable('EmsAssessments')->saveOrFail($assessment);

        $label = $isReopen ? 'reopened' : ($to === 'open' ? 'opened' : ($to === 'closed' ? 'closed' : 'published'));
        $this->audit()->log(
            $this->viewer,
            'assessment.' . $label,
            'assessment',
            $id,
            sprintf(
                '%s assessment "%s" for %s %s',
                ucfirst($label),
                (string)$assessment->name,
                $this->classNameOf((string)$assessment->class_group_id),
                (string)$assessment->subject,
            ),
            $isReopen && $reason !== '' ? $reason : null,
        );

        return $this->json(ExamSerializer::assessment($assessment));
    }

    /**
     * GET /assessments/{id}/scoresheet — class access required on this read.
     */
    public function scoresheet(string $id): Response
    {
        $assessment = $this->findAssessment($id);
        $this->scope()->assertClassAccess((string)$assessment->class_group_id);

        $rows = [];
        foreach ($this->roster((string)$assessment->class_group_id) as $student) {
            $row = $this->tenant()->query('EmsAssessmentScores')
                ->where([
                    'assessment_id' => $id,
                    'student_id' => (string)$student->id,
                ])
                ->first();
            $entry = [
                'studentId' => (string)$student->id,
                'studentName' => trim((string)$student->first_name . ' ' . (string)$student->last_name),
                'admissionNumber' => (string)$student->admission_number,
                'score' => $row === null ? null : Wire::num($row->score),
            ];
            if ($row !== null && $row->absent_reason !== null && (string)$row->absent_reason !== '') {
                $entry['absentReason'] = (string)$row->absent_reason;
            }
            $entry['recorded'] = $row !== null;
            $rows[] = $entry;
        }

        return $this->json([
            'assessment' => ExamSerializer::assessment($assessment),
            'className' => $this->classNameOf((string)$assessment->class_group_id),
            'rows' => $rows,
        ]);
    }

    /**
     * PUT /assessments/{id}/scores — validate every entry before writing any.
     */
    public function saveScores(string $id): Response
    {
        $assessment = $this->findAssessment($id);
        $this->scope()->assertClassAccess((string)$assessment->class_group_id);
        if (!in_array((string)$assessment->status, self::SCORE_EDITABLE, true)) {
            $this->fail(409, Messages::ASSESSMENT_SCORES_LOCKED);
        }

        $data = $this->body();
        $entries = array_is_list($data) ? $data : (is_array($data['entries'] ?? null) ? $data['entries'] : []);
        $maximum = (int)$assessment->maximum;
        $enrolledIds = array_flip($this->rosterIds((string)$assessment->class_group_id));

        foreach ($entries as $entry) {
            if (!isset($enrolledIds[(string)($entry['studentId'] ?? '')])) {
                $this->fail(422, Messages::SCORE_NOT_ENROLLED);
            }
            $score = $entry['score'] ?? null;
            if ($score !== null) {
                if (!is_numeric($score) || (float)$score < 0) {
                    $this->fail(422, Messages::SCORE_NEGATIVE);
                }
                if ((float)$score > $maximum) {
                    $this->fail(422, sprintf(Messages::SCORE_ABOVE_MAX, Wire::num($score), $maximum));
                }
            }
        }

        $scores = $this->fetchTable('EmsAssessmentScores');
        $scores->getConnection()->transactional(function () use ($scores, $entries, $id): void {
            foreach ($entries as $entry) {
                $studentId = (string)($entry['studentId'] ?? '');
                $score = $entry['score'] ?? null;
                $absentReason = trim((string)($entry['absentReason'] ?? ''));
                $existing = $this->tenant()->query('EmsAssessmentScores')
                    ->where([
                        'assessment_id' => $id,
                        'student_id' => $studentId,
                    ])
                    ->first();
                if ($existing !== null) {
                    // A cleared, never-absent cell is removed so it reads as a
                    // missing (unresolved) mark rather than a recorded zero.
                    if ($score === null && $absentReason === '') {
                        $scores->deleteOrFail($existing);
                    } else {
                        $existing->score = $score;
                        $existing->absent_reason = $absentReason !== '' ? $absentReason : null;
                        $existing->entered_by = $this->viewer->name;
                        $scores->saveOrFail($existing);
                    }
                } elseif ($score !== null || $absentReason !== '') {
                    $scores->saveOrFail($scores->newEntity([
                        'school_id' => $this->viewer->schoolId,
                        'assessment_id' => $id,
                        'student_id' => $studentId,
                        'score' => $score,
                        'absent_reason' => $absentReason !== '' ? $absentReason : null,
                        'entered_by' => $this->viewer->name,
                    ], ['validate' => false]));
                }
            }
        });

        return $this->json(null, 204);
    }

    // --- helpers ----------------------------------------------------------

    private function findAssessment(string $id): EntityInterface
    {
        return $this->findOr404('EmsAssessments', $id, Messages::ASSESSMENT_NOT_FOUND);
    }

    private function classNameOf(string $classGroupId): string
    {
        $c = $this->tenant()->query('EmsClassGroups')
            ->select(['name'])
            ->where(['id' => $classGroupId])
            ->first();

        return $c === null ? '—' : (string)$c->name;
    }

    /**
     * The enrolled roster of a class (by id → name), ordered by surname.
     *
     * @return array<\Cake\Datasource\EntityInterface>
     */
    private function roster(string $classGroupId): array
    {
        $name = $this->classNameOf($classGroupId);
        if ($name === '—') {
            return [];
        }

        return $this->tenant()->query('EmsStudents')
            ->where([
                'class_group' => $name,
                'status' => 'enrolled',
            ])
            ->orderByAsc('last_name')
            ->orderByAsc('first_name')
            ->all()
            ->toList();
    }

    /**
     * @return array<int, string>
     */
    private function rosterIds(string $classGroupId): array
    {
        return array_map(fn($s) => (string)$s->id, $this->roster($classGroupId));
    }
}
