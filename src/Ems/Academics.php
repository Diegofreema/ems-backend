<?php
declare(strict_types=1);

namespace App\Ems;

use App\Ems\Serializer\ClassSerializer;
use App\Ems\Serializer\ExamSerializer;
use App\Ems\Serializer\SettingsSerializer;
use App\Ems\Serializer\StudentSerializer;
use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorInterface;
use stdClass;

/**
 * The academics computation engine (document.md §3.1/§3.2/§3.5) — a faithful
 * port of the frontend's exams/assessments/transcripts services so the grade
 * sheet, broadsheet, report card and transcript can never disagree.
 *
 * Every server-computed read model is built here (never in the controller and
 * never in the frontend). The two arithmetic invariants the parity check leans
 * on live in one place:
 *   - averages / derived CA use Math.round on a scaled value — replicated as
 *     floor(v + 0.5) so PHP's round() pre-rounding can never diverge from JS;
 *   - a total exists only when BOTH ca and exam are present (no partial totals).
 */
class Academics
{
    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var string
     */
    private string $schoolId;

    /**
     * @var \App\Ems\Grading
     */
    private Grading $grading;

    /** @var array<int, int> Term ordinal for transcript sorting. */
    private const TERM_ORDER = ['First' => 1, 'Second' => 2, 'Third' => 3];

    /** @var array<int, string> Assessment statuses that feed the derived CA. */
    private const CONTRIBUTING = ['open', 'closed', 'published'];

    /**
     * @var \App\Ems\Tenant|null
     */
    private ?Tenant $tenantScope = null;

    public function __construct(LocatorInterface $locator, string $schoolId, Grading $grading)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->grading = $grading;
    }

    /**
     * This engine's tenant-scope choke point — reads narrowed to $this->schoolId
     * by construction. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    // --- arithmetic primitives ---------------------------------------------

    /** JS Math.round for the non-negative values academics deals in. */
    private static function jsRound(float $v): float
    {
        return floor($v + 0.5);
    }

    /** A decimal-or-null DB value as a float or null. */
    private static function n(mixed $v): ?float
    {
        return $v === null ? null : (float)$v;
    }

    /** Mean of the non-null totals, 1 dp; null when nothing counted. */
    private static function averageOfTotals(array $totals): ?float
    {
        $sum = 0.0;
        $counted = 0;
        foreach ($totals as $t) {
            if ($t !== null) {
                $sum += $t;
                $counted++;
            }
        }

        return $counted === 0 ? null : self::jsRound($sum / $counted * 10) / 10;
    }

    /** ca + exam, but only when both are present. */
    private static function totalWith(?float $ca, ?float $paper): ?float
    {
        return $ca !== null && $paper !== null ? $ca + $paper : null;
    }

    // --- CA resolution (§3.1 / §3.2) ---------------------------------------

    /**
     * Whether an offering has ANY assessment (drafts included) — the switch
     * that flips the CA from an entered figure to a derived roll-up.
     */
    public function offeringHasAssessments(string $examId, string $classGroupId, string $subjectId): bool
    {
        return $this->tenant()->query('EmsAssessments')
            ->where([
                'exam_id' => $examId,
                'class_group_id' => $classGroupId,
                'subject_id' => $subjectId,
            ])
            ->count() > 0;
    }

    /** The status-contributing assessments of one offering (§3.2), ordered stably. */
    private function contributingAssessments(string $examId, string $classGroupId, string $subjectId): array
    {
        return $this->tenant()->query('EmsAssessments')
            ->where([
                'exam_id' => $examId,
                'class_group_id' => $classGroupId,
                'subject_id' => $subjectId,
                'status IN' => self::CONTRIBUTING,
            ])
            ->all()
            ->toList();
    }

    /**
     * The derived-CA arithmetic (§3.2), independent of WHERE the assessment rows
     * and scores are sourced — so the single-offering read and the batched
     * whole-class read can never diverge. `$scoreFor($assessmentId)` returns that
     * student's score row for an assessment: null = no mark yet (excluded from the
     * ratio, never zeroed); a row whose `->score` is null = an excused absence.
     *
     * @param array<\Cake\Datasource\EntityInterface> $assessments
     * @param callable(string):?\Cake\Datasource\EntityInterface $scoreFor
     * @return array{ca:?int, earned:float, possible:float, scoredCount:int, missingCount:int, contributingCount:int, complete:bool}
     */
    private static function caFrom(array $assessments, callable $scoreFor, int $caMax): array
    {
        $earned = 0.0;
        $possible = 0.0;
        $scoredCount = 0;
        $missingCount = 0;
        foreach ($assessments as $a) {
            $row = $scoreFor((string)$a->id);
            if ($row === null) {
                $missingCount++; // no mark yet — unresolved, not zero
                continue;
            }
            if ($row->score === null) {
                continue; // explicit absence — excused
            }
            $earned += (float)$row->score;
            $possible += (float)$a->maximum;
            $scoredCount++;
        }
        $ca = $possible == 0.0 ? null : (int)self::jsRound($earned / $possible * $caMax);

        return [
            'ca' => $ca,
            'earned' => $earned,
            'possible' => $possible,
            'scoredCount' => $scoredCount,
            'missingCount' => $missingCount,
            'contributingCount' => count($assessments),
            'complete' => count($assessments) > 0 && $missingCount === 0,
        ];
    }

    /**
     * The derived CA for one student in one offering (§3.2 algorithm), scaled to
     * caMax and whole-number rounded. Missing marks are excluded from the ratio
     * (never zeroed) and surfaced; an explicit absence counts toward neither side.
     *
     * @return array{ca:?int, earned:float, possible:float, scoredCount:int, missingCount:int, contributingCount:int, complete:bool}
     */
    public function derivedCa(string $examId, string $classGroupId, string $subjectId, string $studentId, int $caMax): array
    {
        return self::caFrom(
            $this->contributingAssessments($examId, $classGroupId, $subjectId),
            function (string $assessmentId) use ($studentId) {
                return $this->tenant()->query('EmsAssessmentScores')
                    ->where([
                        'assessment_id' => $assessmentId,
                        'student_id' => $studentId,
                    ])
                    ->first();
            },
            $caMax,
        );
    }

    /**
     * The per-request CA inputs for a whole class+exam, materialised in TWO
     * queries (all assessments for the subjects, then all their scores) instead
     * of the per-student-per-assessment N+1. Feeds {@see resolveCaFrom}, which is
     * pure — no query runs once this is built.
     *
     * @param array<int, string> $subjectIds
     * @return array{has:array<string,bool>, contributing:array<string,array<\Cake\Datasource\EntityInterface>>, scores:array<string,\Cake\Datasource\EntityInterface>}
     */
    private function buildCaContext(string $examId, string $classGroupId, array $subjectIds): array
    {
        $has = [];
        $contributing = [];
        $scores = [];
        $subjectIds = array_values(array_unique($subjectIds));
        if ($subjectIds === []) {
            return ['has' => $has, 'contributing' => $contributing, 'scores' => $scores];
        }

        // One query for every assessment in these offerings (ANY status, so the
        // "has assessments" derive-switch sees drafts too, matching
        // offeringHasAssessments); the CA sum uses only the contributing ones.
        $assessmentIds = [];
        foreach (
            $this->tenant()->query('EmsAssessments')
                ->where([
                    'exam_id' => $examId,
                    'class_group_id' => $classGroupId,
                    'subject_id IN' => $subjectIds,
                ]) as $a
        ) {
            $sid = (string)$a->subject_id;
            $has[$sid] = true;
            if (in_array((string)$a->status, self::CONTRIBUTING, true)) {
                $contributing[$sid][] = $a;
                $assessmentIds[] = (string)$a->id;
            }
        }

        // One query for every score across all those assessments, keyed for O(1)
        // (assessment, student) lookup.
        if ($assessmentIds !== []) {
            foreach (
                $this->tenant()->query('EmsAssessmentScores')
                    ->where([
                        'assessment_id IN' => array_values(array_unique($assessmentIds)),
                    ]) as $row
            ) {
                $scores[(string)$row->assessment_id . '::' . (string)$row->student_id] = $row;
            }
        }

        return ['has' => $has, 'contributing' => $contributing, 'scores' => $scores];
    }

    /**
     * {@see resolveCa}, served from a prebuilt {@see buildCaContext} — the same
     * derived-vs-entered decision and the same arithmetic, but with zero queries.
     *
     * @param array{has:array<string,bool>, contributing:array<string,array<\Cake\Datasource\EntityInterface>>, scores:array<string,\Cake\Datasource\EntityInterface>} $ctx
     * @return array{ca:?float, fromAssessments:bool, missing:int}
     */
    private function resolveCaFrom(array $ctx, EntityInterface $exam, string $subjectId, string $studentId, ?EntityInterface $grade): array
    {
        if (!empty($ctx['has'][$subjectId])) {
            $d = self::caFrom(
                $ctx['contributing'][$subjectId] ?? [],
                function (string $assessmentId) use ($ctx, $studentId) {
                    return $ctx['scores'][$assessmentId . '::' . $studentId] ?? null;
                },
                (int)$exam->ca_max,
            );

            return ['ca' => $d['ca'] === null ? null : (float)$d['ca'], 'fromAssessments' => true, 'missing' => $d['missingCount']];
        }

        return ['ca' => $grade === null ? null : self::n($grade->ca), 'fromAssessments' => false, 'missing' => 0];
    }

    // --- roster & class computation ----------------------------------------

    /**
     * Enrolled students of a class, matched by class id (with a name fallback
     * for students not yet linked to an id), ordered by surname then first name
     * (the backend's roster convention, §3.12). Keyed by id so two arms that
     * share a name never merge into one grade roster.
     *
     * @return array<\Cake\Datasource\EntityInterface>
     */
    private function rosterFor(EntityInterface $classGroup): array
    {
        return $this->tenant()->query('EmsStudents')
            ->where([
                'OR' => [
                    'class_group_id' => (string)$classGroup->id,
                    ['class_group_id IS' => null, 'class_group' => (string)$classGroup->name],
                ],
                'status' => 'enrolled',
            ])
            ->orderByAsc('last_name')
            ->orderByAsc('first_name')
            ->all()
            ->toList();
    }

    /**
     * One exam+subject's grade rows for a roster, keyed by student_id — one query
     * instead of a lookup per student.
     *
     * @param array<\Cake\Datasource\EntityInterface> $roster
     * @return array<string,\Cake\Datasource\EntityInterface>
     */
    private function gradesByStudent(string $examId, string $subjectId, array $roster): array
    {
        $studentIds = array_map(fn($s) => (string)$s->id, $roster);
        if ($studentIds === []) {
            return [];
        }

        $byStudent = [];
        foreach (
            $this->tenant()->query('EmsExamGrades')
                ->where([
                    'exam_id' => $examId,
                    'subject_id' => $subjectId,
                    'student_id IN' => $studentIds,
                ]) as $g
        ) {
            $byStudent[(string)$g->student_id] = $g;
        }

        return $byStudent;
    }

    /**
     * Every enrolled student in a class, their per-subject totals, class average
     * and dense-rank position (ties shared). Shared by broadsheet + report card.
     *
     * @return array{subjects:array<int,string>, rows:array<int, array{student:\Cake\Datasource\EntityInterface, totals:array<string,?float>, average:?float, position:?int}>}
     */
    private function computeClassResults(EntityInterface $exam, EntityInterface $classGroup): array
    {
        $grades = $this->tenant()->query('EmsExamGrades')
            ->where([
                'exam_id' => (string)$exam->id,
                'class_group_id' => (string)$classGroup->id,
            ])
            ->all()
            ->toList();

        $subjects = [];
        $subjectIdByName = [];
        $byStudentSubject = [];
        foreach ($grades as $g) {
            $subjects[(string)$g->subject] = true;
            $subjectIdByName[(string)$g->subject] = (string)$g->subject_id;
            $byStudentSubject[(string)$g->student_id . '::' . (string)$g->subject] = $g;
        }
        $subjects = array_keys($subjects);
        usort($subjects, 'strcmp');

        // Two queries feed every (student × subject) CA below — no per-cell N+1.
        $ctx = $this->buildCaContext((string)$exam->id, (string)$classGroup->id, array_values($subjectIdByName));

        $rows = [];
        foreach ($this->rosterFor($classGroup) as $student) {
            $totals = [];
            $sum = 0.0;
            $counted = 0;
            foreach ($subjects as $subject) {
                $grade = $byStudentSubject[(string)$student->id . '::' . $subject] ?? null;
                $ca = $this->resolveCaFrom($ctx, $exam, $subjectIdByName[$subject], (string)$student->id, $grade)['ca'];
                $total = self::totalWith($ca, $grade === null ? null : self::n($grade->exam));
                $totals[$subject] = $total;
                if ($total !== null) {
                    $sum += $total;
                    $counted++;
                }
            }
            $average = $counted === 0 ? null : self::jsRound($sum / $counted * 10) / 10;
            $rows[] = ['student' => $student, 'totals' => $totals, 'average' => $average, 'position' => null];
        }

        // Dense rank by average, highest first; equal averages share a position.
        $rankedIdx = [];
        foreach ($rows as $i => $row) {
            if ($row['average'] !== null) {
                $rankedIdx[] = $i;
            }
        }
        usort($rankedIdx, fn($a, $b) => $rows[$b]['average'] <=> $rows[$a]['average']);
        $lastAverage = null;
        $lastPosition = 0;
        foreach ($rankedIdx as $index => $i) {
            if ($lastAverage !== null && $rows[$i]['average'] === $lastAverage) {
                $rows[$i]['position'] = $lastPosition;
            } else {
                $rows[$i]['position'] = $index + 1;
                $lastPosition = $index + 1;
                $lastAverage = $rows[$i]['average'];
            }
        }

        return ['subjects' => $subjects, 'rows' => $rows];
    }

    private function summarizeAttendance(string $studentId): array
    {
        $records = $this->tenant()->query('EmsAttendanceRecords')
            ->where(['student_id' => $studentId])
            ->all()
            ->toList();

        return StudentSerializer::attendanceSummary($records);
    }

    // --- read models -------------------------------------------------------

    /**
     * The grade-entry sheet for one class + subject (§3.1).
     */
    public function gradesheet(EntityInterface $exam, EntityInterface $classGroup, string $subject): array
    {
        $subjectId = SubjectCatalog::requireId($this->schoolId, $subject);
        $bands = $this->grading->schemeForExam($exam)['bands'];
        $ctx = $this->buildCaContext((string)$exam->id, (string)$classGroup->id, [$subjectId]);
        $caFromAssessments = !empty($ctx['has'][$subjectId]);

        $roster = $this->rosterFor($classGroup);
        $gradeByStudent = $this->gradesByStudent((string)$exam->id, $subjectId, $roster);

        $rows = [];
        foreach ($roster as $student) {
            $grade = $gradeByStudent[(string)$student->id] ?? null;
            $resolved = $this->resolveCaFrom($ctx, $exam, $subjectId, (string)$student->id, $grade);
            $paper = $grade === null ? null : self::n($grade->exam);
            $total = self::totalWith($resolved['ca'], $paper);
            $row = [
                'student' => StudentSerializer::one($student),
                'ca' => Wire::num($resolved['ca']),
                'exam' => Wire::num($paper),
                'total' => Wire::num($total),
                'grade' => $total === null ? null : Grading::gradeFor($total, $bands),
            ];
            if ($resolved['fromAssessments']) {
                $row['caMissing'] = $resolved['missing'];
            }
            $rows[] = $row;
        }

        return [
            'exam' => ExamSerializer::exam($exam),
            'classGroup' => ClassSerializer::group($classGroup),
            'subject' => $subject,
            'rows' => $rows,
            'gradeBands' => $bands,
            'caFromAssessments' => $caFromAssessments,
        ];
    }

    /**
     * The computed results broadsheet for a class (§3.1).
     */
    public function broadsheet(EntityInterface $exam, EntityInterface $classGroup): array
    {
        $bands = $this->grading->schemeForExam($exam)['bands'];
        $computed = $this->computeClassResults($exam, $classGroup);
        $subjects = $computed['subjects'];

        $rows = [];
        foreach ($computed['rows'] as $r) {
            $cells = [];
            foreach ($subjects as $subject) {
                $total = $r['totals'][$subject] ?? null;
                $cells[$subject] = [
                    'total' => Wire::num($total),
                    'grade' => $total === null ? null : Grading::gradeFor($total, $bands),
                ];
            }
            $rows[] = [
                'student' => StudentSerializer::one($r['student']),
                'cells' => $cells === [] ? new stdClass() : $cells,
                'average' => Wire::num($r['average']),
                'position' => $r['position'],
            ];
        }

        return [
            'exam' => ExamSerializer::exam($exam),
            'classGroup' => ClassSerializer::group($classGroup),
            'subjects' => $subjects,
            'rows' => $rows,
        ];
    }

    /**
     * A single student's printable report card for an exam (§3.1). Graded on the
     * scheme pinned when the exam was released (published exams only).
     */
    public function reportCard(EntityInterface $school, EntityInterface $exam, EntityInterface $student): array
    {
        $scheme = $this->grading->schemeForExam($exam);
        $bands = $scheme['bands'];
        $classGroupId = (string)($student->class_group_id ?? '');
        $classGroup = $classGroupId !== ''
            ? $this->tenant()->query('EmsClassGroups')
                ->where(['id' => $classGroupId])
                ->first()
            : $this->tenant()->query('EmsClassGroups')
                ->where(['name' => (string)$student->class_group])
                ->orderByAsc('created')
                ->first();

        $subjects = [];
        $average = null;
        $position = null;
        $classSize = 0;

        if ($classGroup !== null) {
            $computed = $this->computeClassResults($exam, $classGroup);
            $classSize = count($computed['rows']);
            foreach ($computed['rows'] as $r) {
                if ((string)$r['student']->id === (string)$student->id) {
                    $average = $r['average'];
                    $position = $r['position'];
                    break;
                }
            }

            // Per-subject class averages.
            $classAverages = [];
            foreach ($computed['subjects'] as $subject) {
                $sum = 0.0;
                $counted = 0;
                foreach ($computed['rows'] as $row) {
                    $total = $row['totals'][$subject] ?? null;
                    if ($total !== null) {
                        $sum += $total;
                        $counted++;
                    }
                }
                if ($counted > 0) {
                    $classAverages[$subject] = self::jsRound($sum / $counted * 10) / 10;
                }
            }

            // This student's grades for the whole exam in one query, plus one CA
            // context covering every reported subject — no per-subject N+1.
            $subjectIdByName = [];
            foreach ($computed['subjects'] as $subject) {
                $subjectIdByName[$subject] = SubjectCatalog::requireId($this->schoolId, $subject);
            }
            $ctx = $this->buildCaContext((string)$exam->id, (string)$classGroup->id, array_values($subjectIdByName));
            $gradeBySubjectId = [];
            foreach (
                $this->tenant()->query('EmsExamGrades')
                    ->where([
                        'exam_id' => (string)$exam->id,
                        'student_id' => (string)$student->id,
                    ]) as $g
            ) {
                $gradeBySubjectId[(string)$g->subject_id] = $g;
            }

            foreach ($computed['subjects'] as $subject) {
                $subjectId = $subjectIdByName[$subject];
                $grade = $gradeBySubjectId[$subjectId] ?? null;
                $resolved = $this->resolveCaFrom($ctx, $exam, $subjectId, (string)$student->id, $grade);
                $paper = $grade === null ? null : self::n($grade->exam);
                $total = self::totalWith($resolved['ca'], $paper);
                $band = $total === null ? null : Grading::gradeFor($total, $bands);
                $subjectRow = [
                    'subject' => $subject,
                    'ca' => Wire::num($resolved['ca']),
                    'exam' => Wire::num($paper),
                    'total' => Wire::num($total),
                    'grade' => $band,
                    'classAverage' => Wire::num($classAverages[$subject] ?? null),
                    'remark' => $band !== null ? $band['label'] : 'Not graded',
                ];
                if ($resolved['fromAssessments'] && $resolved['missing'] > 0) {
                    $subjectRow['caProvisional'] = true;
                }
                $subjects[] = $subjectRow;
            }
        }

        return [
            'school' => SettingsSerializer::school($school),
            'exam' => ExamSerializer::exam($exam),
            'student' => StudentSerializer::one($student),
            'classGroup' => $classGroup === null ? null : ClassSerializer::group($classGroup),
            'subjects' => $subjects,
            'average' => Wire::num($average),
            'position' => $position,
            'classSize' => $classSize,
            'attendance' => $this->summarizeAttendance((string)$student->id),
            'gradeBands' => $bands,
        ];
    }

    /**
     * Completeness before publication: expected vs entered scores (§3.1).
     */
    public function releasePreview(string $examId): array
    {
        $schedules = $this->tenant()->query('EmsExamSchedules')
            ->where(['exam_id' => $examId])
            ->all()
            ->toList();
        $subjectsByLevel = [];
        foreach ($schedules as $s) {
            $subjectsByLevel[(string)$s->level][(string)$s->subject] = true;
        }

        $levelByClassName = [];
        foreach (
            $this->tenant()->query('EmsClassGroups') as $c
        ) {
            $levelByClassName[(string)$c->name] = (string)$c->level;
        }

        $expected = 0;
        foreach (
            $this->tenant()->query('EmsStudents')
                ->where(['status' => 'enrolled']) as $student
        ) {
            $level = $levelByClassName[(string)$student->class_group] ?? null;
            if ($level === null) {
                continue;
            }
            $expected += isset($subjectsByLevel[$level]) ? count($subjectsByLevel[$level]) : 0;
        }

        $entered = $this->tenant()->query('EmsExamGrades')
            ->where([
                'exam_id' => $examId,
                'ca IS NOT' => null,
                'exam IS NOT' => null,
            ])
            ->count();
        $prior = $this->tenant()->query('EmsResultReleases')
            ->where(['exam_id' => $examId])
            ->count();

        return [
            'expectedScores' => $expected,
            'enteredScores' => $entered,
            'missingScores' => max(0, $expected - $entered),
            'nextVersion' => $prior + 1,
        ];
    }

    // --- transcript (§3.5) -------------------------------------------------

    /** A short transcript remark from an average, mirroring the report card tone. */
    private static function remarkFor(?float $average): string
    {
        if ($average === null) {
            return 'Not graded';
        }
        if ($average >= 70) {
            return 'Excellent';
        }
        if ($average >= 60) {
            return 'Very good';
        }
        if ($average >= 50) {
            return 'Credit';
        }
        if ($average >= 45) {
            return 'Pass';
        }
        if ($average >= 40) {
            return 'Fair';
        }

        return 'Below average';
    }

    /**
     * One released examination as a transcript term for a student, ranked within
     * the cohort that actually sat it (read from the grades' own classGroupId).
     * Null when the student sat nothing in this exam.
     */
    private function releasedTerm(EntityInterface $exam, string $studentId, array $mine): ?array
    {
        if ($mine === []) {
            return null;
        }
        $classGroupId = (string)$mine[0]->class_group_id;
        $scheme = $this->grading->schemeForExam($exam);
        $bands = $scheme['bands'];

        usort($mine, fn($a, $b) => strcmp((string)$a->subject, (string)$b->subject));
        $subjects = [];
        $myTotals = [];
        foreach ($mine as $g) {
            $ca = self::n($g->ca);
            $paper = self::n($g->exam);
            $total = self::totalWith($ca, $paper);
            $myTotals[] = $total;
            $subjects[] = [
                'subject' => (string)$g->subject,
                'ca' => Wire::num($ca),
                'exam' => Wire::num($paper),
                'total' => Wire::num($total),
                'grade' => $total === null ? null : Grading::gradeFor($total, $bands),
            ];
        }
        $myAverage = self::averageOfTotals($myTotals);

        // The cohort that sat this exam in the student's class — read directly by
        // class, not by scanning every class's grades for the exam. Dense-rank by
        // average.
        $totalsByStudent = [];
        foreach (
            $this->tenant()->query('EmsExamGrades')
                ->where([
                    'exam_id' => (string)$exam->id,
                    'class_group_id' => $classGroupId,
                ]) as $g
        ) {
            $totalsByStudent[(string)$g->student_id][] = self::totalWith(self::n($g->ca), self::n($g->exam));
        }
        $averages = [];
        foreach ($totalsByStudent as $sid => $totals) {
            $averages[] = ['sid' => (string)$sid, 'avg' => self::averageOfTotals($totals)];
        }
        $ranked = array_values(array_filter($averages, fn($a) => $a['avg'] !== null));
        usort($ranked, fn($a, $b) => $b['avg'] <=> $a['avg']);
        $position = null;
        $lastAvg = null;
        $lastPos = 0;
        foreach ($ranked as $index => $row) {
            $pos = $lastAvg !== null && $row['avg'] === $lastAvg ? $lastPos : $index + 1;
            $lastPos = $pos;
            $lastAvg = $row['avg'];
            if ($row['sid'] === $studentId) {
                $position = $pos;
            }
        }

        $classRow = $this->tenant()->query('EmsClassGroups')
            ->where(['id' => $classGroupId])
            ->first();
        $className = $classRow !== null ? (string)$classRow->name : (string)$exam->session;

        return [
            'key' => (string)$exam->session . '::' . (string)$exam->term,
            'session' => (string)$exam->session,
            'term' => (string)$exam->term,
            'classGroup' => $className,
            'average' => Wire::num($myAverage),
            'position' => $position,
            'classSize' => count($averages),
            'remark' => self::remarkFor($myAverage),
            'source' => 'released',
            'schemeVersion' => (int)$scheme['version'],
            'subjects' => $subjects,
        ];
    }

    /**
     * A student's cumulative transcript across every session attended (§3.5).
     */
    public function transcript(EntityInterface $school, EntityInterface $student): array
    {
        $studentId = (string)$student->id;
        $byKey = [];

        // 1. Released examinations only — grading/scheduled/draft never appear.
        //    The student's grades across every published exam come back in ONE
        //    query, keyed by exam, so an exam the student never sat costs nothing
        //    (rather than loading its whole grade sheet just to find that out).
        $examById = [];
        foreach (
            $this->tenant()->query('EmsExams')
                ->where(['status' => 'published']) as $exam
        ) {
            $examById[(string)$exam->id] = $exam;
        }
        if ($examById !== []) {
            $myGradesByExam = [];
            foreach (
                $this->tenant()->query('EmsExamGrades')
                    ->where([
                        'student_id' => $studentId,
                        'exam_id IN' => array_keys($examById),
                    ])
                    ->orderByAsc('id') as $g
            ) {
                $myGradesByExam[(string)$g->exam_id][] = $g;
            }
            foreach ($myGradesByExam as $examId => $mine) {
                $term = $this->releasedTerm($examById[$examId], $studentId, $mine);
                if ($term !== null) {
                    $byKey[$term['key']] = $term;
                }
            }
        }

        // 2. Approved academic history — a released term wins the same key.
        foreach (
            $this->tenant()->query('EmsAcademicTermRecords')
                ->where(['student_id' => $studentId]) as $record
        ) {
            $key = (string)$record->session . '::' . (string)$record->term;
            if (isset($byKey[$key])) {
                continue;
            }
            $byKey[$key] = [
                'key' => $key,
                'session' => (string)$record->session,
                'term' => (string)$record->term,
                'classGroup' => (string)$record->class_group,
                'average' => Wire::num(self::n($record->average)),
                'position' => (int)$record->position,
                'classSize' => (int)$record->class_size,
                'remark' => (string)$record->remark,
                'source' => 'history',
                'schemeVersion' => null,
                'subjects' => [],
            ];
        }

        $terms = array_values($byKey);
        $enrolments = $this->tenant()->query('EmsEnrolments')
            ->where(['student_id' => $studentId])
            ->all()
            ->toList();

        // Sessions span enrolment history AND every session with a term.
        $sessionNames = [];
        foreach ($enrolments as $e) {
            $sessionNames[(string)$e->session] = true;
        }
        foreach ($terms as $t) {
            $sessionNames[$t['session']] = true;
        }
        $sessionNames = array_keys($sessionNames);
        usort($sessionNames, 'strcmp');

        $sessions = [];
        foreach ($sessionNames as $session) {
            $sessionTerms = array_values(array_filter($terms, fn($t) => $t['session'] === $session));
            usort($sessionTerms, fn($a, $b) => (self::TERM_ORDER[$a['term']] ?? 9) <=> (self::TERM_ORDER[$b['term']] ?? 9));
            $placement = null;
            foreach ($enrolments as $e) {
                if ((string)$e->session === $session) {
                    $placement = $e;
                    break;
                }
            }
            $classGroup = $placement !== null
                ? (string)$placement->class_group
                : ($sessionTerms !== [] ? $sessionTerms[0]['classGroup'] : null);
            $sessions[] = [
                'session' => $session,
                'classGroup' => $classGroup,
                'terms' => $sessionTerms,
                'average' => Wire::num(self::averageOfTotals(array_map(fn($t) => $t['average'], $sessionTerms))),
            ];
        }

        $countedAverages = [];
        foreach ($terms as $t) {
            if ($t['average'] !== null) {
                $countedAverages[] = (float)$t['average'];
            }
        }
        $cumulativeAverage = $countedAverages === []
            ? null
            : self::jsRound(array_sum($countedAverages) / count($countedAverages) * 10) / 10;

        return [
            'school' => SettingsSerializer::school($school),
            'student' => StudentSerializer::one($student),
            'sessions' => $sessions,
            'cumulativeAverage' => Wire::num($cumulativeAverage),
            'termsCounted' => count($countedAverages),
            'gradeBands' => $this->grading->activeScheme()['bands'],
            'generatedOn' => FrozenDate::today()->format('Y-m-d'),
        ];
    }
}
