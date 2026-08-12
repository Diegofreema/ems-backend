<?php
declare(strict_types=1);

namespace App\Ems;

use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorInterface;

/**
 * The Analytics engine (document.md §3.22) — four read-only derived dashboards.
 *
 * Stores NOTHING: every figure is computed on read from the same tables the
 * other modules mutate, so a payment recorded in Fees or a register saved in
 * Classes moves these numbers the moment the query refetches. All money uses the
 * shared net-paid rule (via App\Ems\Fees); all grade figures use each exam's
 * pinned scheme (via App\Ems\Grading). Averages and rates are emitted RAW (full
 * precision — the frontend formats them); `Wire::num` only normalises a whole
 * value to an int so the wire bytes match the mock the frontend was built on.
 *
 * Parity note: `CURRENT_TERM` is a fixed constant in the mock (not derived from
 * the calendar) — the collection rate is scoped to Third-term invoices — so it
 * is a constant here too.
 */
class Analytics
{
    private const ATTENDANCE_WINDOW = 20;
    private const ABSENCE_THRESHOLD = 3;
    private const CURRENT_TERM = 'Third';

    /** @var \Cake\ORM\Locator\LocatorInterface */
    private $locator;

    /** @var string */
    private $schoolId;

    /** @var \App\Ems\Grading */
    private $grading;

    /** @var \App\Ems\Fees */
    private $fees;

    /** @var string Server's real today (YYYY-MM-DD). */
    private $today;

    /** @var \App\Ems\Tenant|null */
    private $tenantScope;

    public function __construct(LocatorInterface $locator, string $schoolId, Grading $grading, Fees $fees, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->grading = $grading;
        $this->fees = $fees;
        $this->today = $today;
    }

    /**
     * This engine's tenant-scope choke point — reads narrowed to $this->schoolId
     * by construction. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    // --- headline overview ---------------------------------------------------
    public function overview(): array
    {
        $students = $this->students();
        $enrolled = 0;
        $applicants = 0;
        foreach ($students as $s) {
            if ((string)$s->status === 'enrolled') {
                $enrolled++;
            } elseif ((string)$s->status === 'applicant') {
                $applicants++;
            }
        }
        $activeTeachers = $this->tenant()->query('EmsTeachers')
            ->where(['status' => 'active'])
            ->count();

        // Attendance: the recent window vs the window before it.
        $window = $this->attendanceWindow(self::ATTENDANCE_WINDOW * 2);
        $attendance = $window['records'];
        $dates = $window['dates'];
        $split = max(0, count($dates) - self::ATTENDANCE_WINDOW);
        $recentDates = array_flip(array_slice($dates, $split));
        $priorDates = array_flip(array_slice($dates, 0, $split));
        $recentRate = $this->rateOf(array_filter($attendance, fn ($r) => isset($recentDates[Wire::date($r->date)])));
        $priorRate = $this->rateOf(array_filter($attendance, fn ($r) => isset($priorDates[Wire::date($r->date)])));

        // Current-term fees, mirroring the Fees module's derived balances.
        $paid = $this->fees->netPaidByInvoice();
        $invoiced = 0;
        $collected = 0;
        $overdueInvoices = 0;
        foreach ($this->invoices() as $i) {
            if ((string)$i->status === 'cancelled') {
                continue;
            }
            $net = (int)($paid[(string)$i->id] ?? 0);
            $total = (int)$i->total;
            if ((string)$i->term === self::CURRENT_TERM) {
                $invoiced += $total;
                $collected += min($net, $total);
            }
            if (Wire::date($i->due_date) < $this->today && $total - $net > 0) {
                $overdueInvoices++;
            }
        }

        $exam = $this->latestPublishedExam();
        $bands = $exam !== null
            ? $this->grading->schemeForExam($exam)['bands']
            : $this->grading->activeScheme()['bands'];

        return [
            'enrolled' => $enrolled,
            'applicants' => $applicants,
            'activeTeachers' => $activeTeachers,
            'studentTeacherRatio' => Wire::num($activeTeachers === 0 ? 0 : $enrolled / $activeTeachers),
            'classCount' => $this->tenant()->query('EmsClassGroups')->count(),
            'attendanceRate' => Wire::num($recentRate),
            'attendanceTrend' => Wire::num(count($priorDates) === 0 ? 0 : $recentRate - $priorRate),
            'collectionRate' => Wire::num($invoiced === 0 ? 0 : $collected / $invoiced),
            'outstanding' => $invoiced - $collected,
            'overdueInvoices' => $overdueInvoices,
            'latestExam' => $exam === null ? null : [
                'id' => (string)$exam->id,
                'title' => (string)$exam->title,
                'session' => (string)$exam->session,
                'term' => (string)$exam->term,
                'average' => Wire::num($this->examAverage((string)$exam->id)['average']),
            ],
            'gradeBands' => $bands,
        ];
    }

    // --- attendance insights -------------------------------------------------
    public function attendanceInsights(): array
    {
        $empty = [
            'windowDays' => self::ATTENDANCE_WINDOW,
            'overallRate' => 0,
            'days' => [],
            'byClass' => [],
            'frequentlyAbsent' => [],
        ];

        $window = $this->attendanceWindow(self::ATTENDANCE_WINDOW);
        $dates = $window['dates'];
        $records = $window['records'];
        if ($records === []) {
            return $empty;
        }

        $students = $this->studentsById();

        $days = [];
        foreach ($dates as $date) {
            $marks = array_values(array_filter($records, fn ($r) => Wire::date($r->date) === $date));
            $days[] = [
                'date' => $date,
                'rate' => Wire::num($this->rateOf($marks)),
                'present' => count(array_filter($marks, fn ($r) => $this->attended((string)$r->status))),
                'total' => count($marks),
            ];
        }

        $byClassMap = [];
        $absences = [];
        foreach ($records as $r) {
            $student = $students[(string)$r->student_id] ?? null;
            if ($student === null) {
                continue;
            }
            $cg = (string)$student->class_group;
            $g = $byClassMap[$cg] ?? ['present' => 0, 'late' => 0, 'absent' => 0, 'total' => 0];
            $g['total']++;
            $status = (string)$r->status;
            if ($status === 'present') {
                $g['present']++;
            } elseif ($status === 'late') {
                $g['late']++;
            } elseif ($status === 'absent') {
                $g['absent']++;
                $absences[(string)$r->student_id] = ($absences[(string)$r->student_id] ?? 0) + 1;
            }
            $byClassMap[$cg] = $g;
        }

        $byClass = [];
        foreach ($byClassMap as $classGroup => $g) {
            $byClass[] = [
                'classGroup' => (string)$classGroup,
                'rate' => Wire::num($g['total'] === 0 ? 0 : ($g['present'] + $g['late']) / $g['total']),
                'present' => $g['present'],
                'late' => $g['late'],
                'absent' => $g['absent'],
                'total' => $g['total'],
            ];
        }
        usort($byClass, fn ($a, $b) =>
            ($a['rate'] <=> $b['rate']) ?: strcmp((string)$a['classGroup'], (string)$b['classGroup']));

        $frequentlyAbsent = [];
        foreach ($absences as $studentId => $n) {
            if ($n < self::ABSENCE_THRESHOLD) {
                continue;
            }
            $s = $students[(string)$studentId] ?? null;
            if ($s === null) {
                continue;
            }
            $frequentlyAbsent[] = [
                'studentId' => (string)$studentId,
                'name' => $this->studentName($s),
                'classGroup' => (string)$s->class_group,
                'absences' => $n,
            ];
        }
        usort($frequentlyAbsent, fn ($a, $b) =>
            ($b['absences'] <=> $a['absences']) ?: strcmp((string)$a['name'], (string)$b['name']));

        return [
            'windowDays' => self::ATTENDANCE_WINDOW,
            'overallRate' => Wire::num($this->rateOf($records)),
            'days' => $days,
            'byClass' => $byClass,
            'frequentlyAbsent' => $frequentlyAbsent,
        ];
    }

    // --- academic performance (latest published exam) ------------------------
    public function academicPerformance(): array
    {
        $exam = $this->latestPublishedExam();
        if ($exam === null) {
            return [
                'exam' => null,
                'overallAverage' => 0,
                'gradedEntries' => 0,
                'bySubject' => [],
                'byClass' => [],
                'distribution' => [],
                'topStudents' => [],
                'gradeBands' => $this->grading->activeScheme()['bands'],
            ];
        }

        $bands = $this->grading->schemeForExam($exam)['bands'];
        $grades = [];
        foreach ($this->examGrades((string)$exam->id) as $g) {
            if ($g->ca !== null && $g->exam !== null) {
                $grades[] = $g;
            }
        }
        $students = $this->studentsById();
        $classNames = [];
        foreach ($this->tenant()->query('EmsClassGroups')->all() as $c) {
            $classNames[(string)$c->id] = (string)$c->name;
        }

        $subjectMap = [];
        $classMap = [];
        $studentMap = [];
        $sum = 0.0;
        foreach ($grades as $g) {
            $total = (float)$g->ca + (float)$g->exam;
            $sum += $total;
            $subject = (string)$g->subject;
            $s = $subjectMap[$subject] ?? ['sum' => 0.0, 'n' => 0];
            $s['sum'] += $total;
            $s['n']++;
            $subjectMap[$subject] = $s;
            $className = $classNames[(string)$g->class_group_id] ?? (string)$g->class_group_id;
            $c = $classMap[$className] ?? ['sum' => 0.0, 'n' => 0, 'students' => []];
            $c['sum'] += $total;
            $c['n']++;
            $c['students'][(string)$g->student_id] = true;
            $classMap[$className] = $c;
            $sid = (string)$g->student_id;
            $st = $studentMap[$sid] ?? ['sum' => 0.0, 'n' => 0];
            $st['sum'] += $total;
            $st['n']++;
            $studentMap[$sid] = $st;
        }

        $bySubject = [];
        foreach ($subjectMap as $subject => $s) {
            $bySubject[] = ['subject' => (string)$subject, 'average' => Wire::num($s['sum'] / $s['n']), 'entries' => $s['n']];
        }
        usort($bySubject, fn ($a, $b) => $b['average'] <=> $a['average']);

        $byClass = [];
        foreach ($classMap as $classGroup => $c) {
            $byClass[] = ['classGroup' => (string)$classGroup, 'average' => Wire::num($c['sum'] / $c['n']), 'students' => count($c['students'])];
        }
        usort($byClass, fn ($a, $b) => $b['average'] <=> $a['average']);

        $counts = [];
        foreach ($studentMap as $st) {
            $letter = Grading::gradeFor($st['sum'] / $st['n'], $bands)['letter'];
            $counts[$letter] = ($counts[$letter] ?? 0) + 1;
        }
        $studentTotal = count($studentMap);
        $distribution = [];
        foreach ($bands as $band) {
            $distribution[] = [
                'letter' => $band['letter'],
                'label' => $band['label'],
                'count' => $counts[$band['letter']] ?? 0,
                'share' => Wire::num($studentTotal === 0 ? 0 : ($counts[$band['letter']] ?? 0) / $studentTotal),
            ];
        }

        $topStudents = [];
        foreach ($studentMap as $studentId => $st) {
            $s = $students[(string)$studentId] ?? null;
            $topStudents[] = [
                'studentId' => (string)$studentId,
                'name' => $s !== null ? $this->studentName($s) : (string)$studentId,
                'classGroup' => $s !== null ? (string)$s->class_group : '—',
                'average' => Wire::num($st['sum'] / $st['n']),
            ];
        }
        usort($topStudents, fn ($a, $b) => $b['average'] <=> $a['average']);
        $topStudents = array_slice($topStudents, 0, 5);

        return [
            'exam' => [
                'id' => (string)$exam->id,
                'title' => (string)$exam->title,
                'session' => (string)$exam->session,
                'term' => (string)$exam->term,
            ],
            'overallAverage' => Wire::num($grades === [] ? 0 : $sum / count($grades)),
            'gradedEntries' => count($grades),
            'bySubject' => $bySubject,
            'byClass' => $byClass,
            'distribution' => $distribution,
            'topStudents' => $topStudents,
            'gradeBands' => $bands,
        ];
    }

    // --- enrolment trends ----------------------------------------------------
    public function enrolmentTrends(): array
    {
        $students = $this->students();
        $enrolledStudents = array_values(array_filter($students, fn ($s) => (string)$s->status === 'enrolled'));

        $capacityByLevel = [];
        foreach ($this->tenant()->query('EmsClassGroups')->all() as $c) {
            $capacityByLevel[(string)$c->level] = ($capacityByLevel[(string)$c->level] ?? 0) + (int)$c->capacity;
        }

        $levelMap = [];
        foreach ($enrolledStudents as $s) {
            $level = $this->levelOf((string)$s->class_group);
            $g = $levelMap[$level] ?? ['enrolled' => 0, 'female' => 0, 'male' => 0];
            $g['enrolled']++;
            if ((string)$s->gender === 'female') {
                $g['female']++;
            } else {
                $g['male']++;
            }
            $levelMap[$level] = $g;
        }
        $byLevel = [];
        foreach ($levelMap as $level => $g) {
            $byLevel[] = [
                'level' => (string)$level,
                'capacity' => $capacityByLevel[(string)$level] ?? 0,
                'enrolled' => $g['enrolled'],
                'female' => $g['female'],
                'male' => $g['male'],
            ];
        }
        usort($byLevel, fn ($a, $b) => strcmp((string)$a['level'], (string)$b['level']));

        $intake = [];
        foreach ($students as $s) {
            if ((string)$s->status === 'applicant') {
                continue;
            }
            $year = substr(Wire::date($s->enrolled_on) ?? '', 0, 4);
            $intake[$year] = ($intake[$year] ?? 0) + 1;
        }
        ksort($intake);
        $intakeByYear = [];
        foreach ($intake as $year => $count) {
            $intakeByYear[] = ['year' => (string)$year, 'count' => $count];
        }

        $female = 0;
        $male = 0;
        foreach ($enrolledStudents as $s) {
            if ((string)$s->gender === 'female') {
                $female++;
            } elseif ((string)$s->gender === 'male') {
                $male++;
            }
        }

        return [
            'enrolled' => count($enrolledStudents),
            'byLevel' => $byLevel,
            'intakeByYear' => $intakeByYear,
            'genderSplit' => ['female' => $female, 'male' => $male],
            'statuses' => [
                'enrolled' => count($enrolledStudents),
                'applicant' => count(array_filter($students, fn ($s) => (string)$s->status === 'applicant')),
                'graduated' => count(array_filter($students, fn ($s) => (string)$s->status === 'graduated')),
                'withdrawn' => count(array_filter($students, fn ($s) => (string)$s->status === 'withdrawn')),
            ],
        ];
    }

    // --- shared helpers ------------------------------------------------------

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function students(): array
    {
        return $this->tenant()->query('EmsStudents')->all()->toList();
    }

    /** @return array<string, \Cake\Datasource\EntityInterface> */
    private function studentsById(): array
    {
        $map = [];
        foreach ($this->students() as $s) {
            $map[(string)$s->id] = $s;
        }

        return $map;
    }

    /**
     * The attendance records for the most recent `$windowDays` distinct register
     * dates, with those dates oldest-first. Two queries against the
     * (school_id, date) index — one for the window's date range, one for its
     * records — so the unbounded register is never materialised in full.
     *
     * @return array{records: array<int, \Cake\Datasource\EntityInterface>, dates: array<int, string>}
     */
    private function attendanceWindow(int $windowDays): array
    {
        $recentDates = $this->tenant()->query('EmsAttendanceRecords')
            ->select(['date'])
            ->distinct(['date'])
            ->orderByDesc('date')
            ->limit($windowDays)
            ->all()
            ->toList();
        if ($recentDates === []) {
            return ['records' => [], 'dates' => []];
        }
        // The window spans everything on or after its oldest date; because that
        // date is the $windowDays-th most recent, `date >=` yields exactly those
        // distinct dates — the same set the old whole-history slice produced.
        $cutoff = end($recentDates)->date;
        $records = $this->tenant()->query('EmsAttendanceRecords')
            ->where(['date >=' => $cutoff])
            ->all()
            ->toList();
        // Key on the ISO date (Wire::date), which sorts chronologically as a
        // string — the raw (string)$row->date cast is a locale M/D/YY that does
        // NOT, which would scramble the recent/prior split the callers derive.
        $dates = [];
        foreach ($recentDates as $row) {
            $dates[Wire::date($row->date)] = true;
        }
        $dates = array_keys($dates);
        sort($dates, SORT_STRING);

        return ['records' => $records, 'dates' => $dates];
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function invoices(): array
    {
        return $this->tenant()->query('EmsInvoices')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function examGrades(string $examId): array
    {
        return $this->tenant()->query('EmsExamGrades')
            ->where(['exam_id' => $examId])
            ->orderByAsc('id')->all()->toList();
    }

    private function attended(string $status): bool
    {
        return $status === 'present' || $status === 'late';
    }

    /**
     * @param array<int, \Cake\Datasource\EntityInterface> $records
     */
    private function rateOf(array $records): float
    {
        $n = count($records);
        if ($n === 0) {
            return 0.0;
        }
        $in = 0;
        foreach ($records as $r) {
            if ($this->attended((string)$r->status)) {
                $in++;
            }
        }

        return $in / $n;
    }

    private function levelOf(string $classGroup): string
    {
        return trim(substr($classGroup, 0, -1));
    }

    private function studentName(EntityInterface $s): string
    {
        return trim((string)$s->first_name . ' ' . (string)$s->last_name);
    }

    private function latestPublishedExam(): ?EntityInterface
    {
        $exams = $this->tenant()->query('EmsExams')
            ->where(['status' => 'published'])
            ->all()->toList();
        usort($exams, fn ($a, $b) => strcmp((string)$b->end_date, (string)$a->end_date));

        return $exams[0] ?? null;
    }

    /** @return array{average: float, entries: int} */
    private function examAverage(string $examId): array
    {
        $sum = 0.0;
        $entries = 0;
        foreach ($this->examGrades($examId) as $g) {
            if ($g->ca !== null && $g->exam !== null) {
                $sum += (float)$g->ca + (float)$g->exam;
                $entries++;
            }
        }

        return ['average' => $entries === 0 ? 0.0 : $sum / $entries, 'entries' => $entries];
    }
}
