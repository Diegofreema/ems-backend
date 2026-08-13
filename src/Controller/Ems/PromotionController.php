<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Money;
use App\Ems\Serializer\Wire;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * End-of-year promotion (document.md §3.16). Reads each student's released
 * results for the closing session and the school's pass mark, proposes a
 * decision, and — on commit — writes next session's placements. The closing
 * session's placement is kept as history (moving up appends, never overwrites);
 * `Student.classGroup` (the live placement) is deliberately NOT changed, because
 * today is still inside the closing session — it becomes current only when the
 * session actually rolls over, a separate later step. Idempotent: promotion
 * runs once per session.
 */
class PromotionController extends AppController
{
    private const LEVELS = ['JSS 1', 'JSS 2', 'JSS 3', 'SSS 1', 'SSS 2', 'SSS 3'];
    private const DEFAULT_PASS_MARK = 40;

    /** GET /promotion/preview — next session's placements for every enrolled student. */
    public function preview(): Response
    {
        $passMark = $this->request->getQuery('passMark') !== null
            ? (int)$this->request->getQuery('passMark') : self::DEFAULT_PASS_MARK;
        $closing = $this->closingSession();
        if ($closing === null) {
            $this->fail(422, Messages::PROMOTION_NO_SESSION);
        }
        $toSession = $this->nextSessionName($closing['name']);
        $enrolments = $this->enrolments();
        $alreadyPromoted = false;
        $inClosing = [];
        foreach ($enrolments as $e) {
            if ((string)$e->session === $toSession) {
                $alreadyPromoted = true;
            }
            if ((string)$e->session === $closing['name']) {
                $inClosing[(string)$e->student_id] = true;
            }
        }

        $roster = array_values(array_filter(
            $this->students(),
            fn($s) => (string)$s->status === 'enrolled' && isset($inClosing[(string)$s->id]),
        ));
        $averages = $this->releasedAverages($closing['name'], array_map(fn($s) => (string)$s->id, $roster));

        $rows = [];
        foreach ($roster as $s) {
            $level = $this->levelOf((string)$s->class_group);
            $average = $averages[(string)$s->id] ?? null;
            $suggested = $this->suggestDecision($level, $average, $passMark);
            $nextClass = $suggested === 'promote'
                ? $this->nextClassName((string)$s->class_group)
                : ($suggested === 'repeat' ? (string)$s->class_group : null);
            $rows[] = [
                'studentId' => (string)$s->id,
                'studentName' => trim((string)$s->first_name . ' ' . (string)$s->last_name),
                'admissionNumber' => (string)$s->admission_number,
                'currentClass' => (string)$s->class_group,
                'currentLevel' => $level,
                'average' => $average,
                'hasResult' => $average !== null,
                'suggested' => $suggested,
                'nextClass' => $nextClass,
            ];
        }
        usort($rows, fn($a, $b) => strcmp((string)$a['currentClass'], (string)$b['currentClass'])
            ?: strcmp((string)$a['studentName'], (string)$b['studentName']));

        return $this->json([
            'fromSession' => $closing['name'],
            'toSession' => $toSession,
            'passMark' => $passMark,
            'alreadyPromoted' => $alreadyPromoted,
            'rows' => $rows,
        ]);
    }

    /** POST /promotion/commit — write next session's placements. Runs once per session. */
    public function commit(): Response
    {
        $body = $this->body();
        $fromSession = (string)($body['fromSession'] ?? '');
        $toSession = (string)($body['toSession'] ?? '');
        $decisions = is_array($body['decisions'] ?? null) ? $body['decisions'] : [];

        $enrolmentsTable = $this->fetchTable('EmsEnrolments');
        $exists = $this->tenant()->query('EmsEnrolments')
            ->where(['session' => $toSession])->count();
        if ($exists > 0) {
            $this->fail(409, sprintf('Students have already been promoted to %s. Promotion runs once per session.', $toSession));
        }
        $toStart = explode('/', $toSession)[0] . '-09-01';
        $students = $this->fetchTable('EmsStudents');
        $result = ['toSession' => $toSession, 'promoted' => 0, 'repeated' => 0, 'graduated' => 0, 'withdrawn' => 0];

        // Everything the loop reads is resolved up front in three queries rather
        // than three per decision: the students, their closing-session
        // enrolments, and — through releasedAverages — the exam list and grades.
        $studentIds = array_values(array_unique(array_filter(
            array_map(fn($d) => (string)($d['studentId'] ?? ''), $decisions),
        )));
        $studentById = $this->tenant()->query('EmsStudents')
            ->where(['id IN' => $studentIds ?: ['']])
            ->all()->indexBy('id')->toArray();
        $currentByStudent = $this->tenant()->query('EmsEnrolments')
            ->where([
                'student_id IN' => $studentIds ?: [''],
                'session' => $fromSession,
                'status' => 'active',
            ])->all()->indexBy('student_id')->toArray();
        $averageByStudent = $this->releasedAverages($fromSession, $studentIds);

        $enrolmentsTable->getConnection()->transactional(function () use (
            $decisions,
            $enrolmentsTable,
            $students,
            $studentById,
            $currentByStudent,
            $averageByStudent,
            $toSession,
            $toStart,
            &$result,
        ): void {
            $done = [];
            foreach ($decisions as $d) {
                $studentId = (string)($d['studentId'] ?? '');
                $decision = (string)($d['decision'] ?? '');
                // A student appears at most once: with entities preloaded we skip a
                // duplicate id explicitly (the per-decision refetch this replaced saw
                // the now-completed enrolment and skipped it for free).
                if ($studentId === '' || isset($done[$studentId])) {
                    continue;
                }
                $student = $studentById[$studentId] ?? null;
                if ($student === null || (string)$student->status !== 'enrolled') {
                    continue;
                }
                $current = $currentByStudent[$studentId] ?? null;
                if ($current === null) {
                    continue;
                }
                $done[$studentId] = true;

                $promotedClass = $decision === 'promote' ? $this->nextClassName((string)$student->class_group) : null;
                $effective = $decision === 'promote' && $promotedClass === null ? 'graduate' : $decision;
                $outcome = [
                'promote' => 'promoted', 'repeat' => 'repeated',
                'graduate' => 'graduated', 'withdraw' => 'withdrawn',
                ][$effective] ?? 'withdrawn';

                $current->status = 'completed';
                $current->ended_on = FrozenDate::today();
                $current->outcome = $outcome;
                $current->average = $averageByStudent[$studentId] ?? null;
                $currentClass = (string)$current->class_group;

                if ($effective === 'promote' || $effective === 'repeat') {
                    $nextClass = $effective === 'promote' ? $promotedClass : (string)$student->class_group;
                    $current->promoted_to = $nextClass;
                    $enrolmentsTable->saveOrFail($current);
                    $enrolmentsTable->saveOrFail($enrolmentsTable->newEntity([
                    'school_id' => $this->viewer->schoolId,
                    'student_id' => $studentId,
                    'session' => $toSession,
                    'class_group' => $nextClass,
                    'level' => $this->levelOf($nextClass),
                    'started_on' => $toStart,
                    'status' => 'active',
                    ], ['validate' => false]));
                    if ($effective === 'promote') {
                        $result['promoted']++;
                    } else {
                        $result['repeated']++;
                    }
                } else {
                    $enrolmentsTable->saveOrFail($current);
                    $student->status = $effective === 'graduate' ? 'graduated' : 'withdrawn';
                    $students->saveOrFail($student);
                    if ($effective === 'graduate') {
                        $result['graduated']++;
                    } else {
                        $result['withdrawn']++;
                    }
                }

                $name = trim((string)$student->first_name . ' ' . (string)$student->last_name);
                $summary = [
                'promote' => sprintf('Promoted %s from %s to %s for %s', $name, $currentClass, (string)$current->promoted_to, $toSession),
                'repeat' => sprintf('Held %s back to repeat %s in %s', $name, $currentClass, $toSession),
                'graduate' => sprintf('Graduated %s from %s', $name, $currentClass),
                'withdraw' => sprintf('Withdrew %s from %s', $name, $currentClass),
                ][$effective] ?? '';
                $this->audit()->log($this->viewer, 'student.' . $outcome, 'student', $studentId, $summary);
            }
        });

        return $this->json($result);
    }

    // --- rules ---------------------------------------------------------------

    private function levelOf(string $classGroup): string
    {
        return trim(substr($classGroup, 0, -1));
    }

    private function nextClassName(string $classGroup): ?string
    {
        $level = $this->levelOf($classGroup);
        $i = array_search($level, self::LEVELS, true);
        if ($i === false || $i >= count(self::LEVELS) - 1) {
            return null;
        }

        return self::LEVELS[$i + 1] . substr($classGroup, strlen($level));
    }

    private function isFinalLevel(string $level): bool
    {
        return $level === self::LEVELS[count(self::LEVELS) - 1];
    }

    private function nextSessionName(string $name): string
    {
        $parts = explode('/', $name);
        $a = (int)($parts[0] ?? 0);
        $b = (int)($parts[1] ?? 0);

        return ($a + 1) . '/' . ($b + 1);
    }

    /** @return array{name:string, endsOn:string}|null */
    private function closingSession(): ?array
    {
        $sessions = $this->tenant()->query('EmsAcademicSessions')
            ->all()->toList();
        $open = array_values(array_filter($sessions, fn($s) => (string)$s->status === 'open'));
        $pool = $open !== [] ? $open : $sessions;
        usort($pool, fn($a, $b) => strcmp((string)$b->starts_on, (string)$a->starts_on));
        $pick = $pool[0] ?? null;

        return $pick === null ? null : ['name' => (string)$pick->name, 'endsOn' => Wire::date($pick->ends_on)];
    }

    private function releasedAverage(string $studentId, string $session): ?float
    {
        return $this->releasedAverages($session, [$studentId])[$studentId] ?? null;
    }

    /**
     * Every given student's released average for one session, keyed by student
     * id (null where a student has no released marks). This is the whole rule —
     * the average of (ca + exam) over each student's most recent *published*
     * exam that carries released marks — but batched: the published-exam list is
     * read once and every student's grades come back in a single
     * `exam_id IN (…) AND student_id IN (…)` scan, so it stays two queries no
     * matter how large the roster is (promotion runs the whole school at once).
     *
     * @param array<int, string> $studentIds
     * @return array<string, float|null>
     */
    private function releasedAverages(string $session, array $studentIds): array
    {
        $averages = array_fill_keys($studentIds, null);
        if ($studentIds === []) {
            return $averages;
        }

        $exams = $this->tenant()->query('EmsExams')
            ->where(['session' => $session, 'status' => 'published'])
            ->all()->toList();
        if ($exams === []) {
            return $averages;
        }
        // Most recent exam first; a stable sort keeps the original order for
        // equal end dates, matching the per-student scan this replaced.
        usort($exams, fn($a, $b) => strcmp((string)$b->end_date, (string)$a->end_date));
        $rankOf = [];
        foreach ($exams as $i => $exam) {
            $rankOf[(string)$exam->id] = $i;
        }

        $grades = $this->tenant()->query('EmsExamGrades')->where([
            'exam_id IN' => array_keys($rankOf),
            'student_id IN' => $studentIds,
            'ca IS NOT' => null,
            'exam IS NOT' => null,
        ])->all()->toList();

        // Per student, gather sum/count per exam rank, then pick the smallest
        // rank present — the most recent exam that actually has marks.
        $byStudent = [];
        foreach ($grades as $g) {
            $sid = (string)$g->student_id;
            $rank = $rankOf[(string)$g->exam_id];
            $slot = &$byStudent[$sid][$rank];
            $slot['sum'] = ($slot['sum'] ?? 0.0) + (float)$g->ca + (float)$g->exam;
            $slot['count'] = ($slot['count'] ?? 0) + 1;
            unset($slot);
        }
        foreach ($byStudent as $sid => $ranks) {
            ksort($ranks);
            $best = reset($ranks);
            $averages[$sid] = Money::jsRound($best['sum'] / $best['count'] * 10) / 10;
        }

        return $averages;
    }

    private function suggestDecision(string $level, ?float $average, int $passMark): string
    {
        $passed = $average === null ? true : $average >= $passMark;
        if (!$passed) {
            return 'repeat';
        }

        return $this->isFinalLevel($level) ? 'graduate' : 'promote';
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function students(): array
    {
        return $this->tenant()->query('EmsStudents')
            ->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function enrolments(): array
    {
        return $this->tenant()->query('EmsEnrolments')
            ->all()->toList();
    }
}
