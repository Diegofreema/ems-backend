<?php
declare(strict_types=1);

namespace App\Ems;

use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorInterface;
use DateTimeImmutable;
use DateTimeZone;

/**
 * The Reporting engine (document.md §3.21). Only preapproved definitions run —
 * a definition is a named function with named filters, never free-form SQL —
 * and each carries the roles allowed to run it. A run is a JOB the worker
 * settles over real time (queued → running → ready → expired). Every CSV opens
 * with provenance lines; row-level reports (class list, reconciliation) carry a
 * CONFIDENTIAL marker. Money cells read in Naira (kobo ÷ 100) — the one surface
 * where amounts leave the system in Naira.
 */
class Reports
{
    public const RETENTION_DAYS = 7;
    // The worker's pace (matches the mock): real elapsed since queueing.
    private const RUNNING_AFTER_MS = 900;
    private const READY_AFTER_MS = 2600;

    /** type => [title, roles, rowLevel]. */
    public const DEFINITIONS = [
        'attendance_summary' => ['title' => 'Attendance summary', 'roles' => ['administrator', 'registrar'], 'rowLevel' => false],
        'admissions_conversion' => ['title' => 'Admissions conversion', 'roles' => ['administrator', 'registrar'], 'rowLevel' => false],
        'class_list' => ['title' => 'Class list', 'roles' => ['administrator', 'registrar'], 'rowLevel' => true],
        'grade_distribution' => ['title' => 'Grade distribution', 'roles' => ['administrator', 'registrar'], 'rowLevel' => false],
        'fee_ageing' => ['title' => 'Fee ageing', 'roles' => ['administrator', 'bursar'], 'rowLevel' => false],
        'collections' => ['title' => 'Collections', 'roles' => ['administrator', 'bursar'], 'rowLevel' => false],
        'reconciliation_status' => ['title' => 'Reconciliation status', 'roles' => ['administrator', 'bursar'], 'rowLevel' => true],
    ];

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

    /**
     * @var \App\Ems\Fees
     */
    private Fees $fees;

    /**
     * @var string Server's real today (YYYY-MM-DD).
     */
    private string $today;

    /**
     * @var \App\Ems\Tenant|null
     */
    private ?Tenant $tenantScope = null;

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

    public static function isKnownType(string $type): bool
    {
        return isset(self::DEFINITIONS[$type]);
    }

    /**
     * Settle a job in place against the real clock: advance queued→running→ready
     * and retire an expired file. Returns true if the entity changed.
     */
    public function settle(EntityInterface $job, int $elapsedMs): bool
    {
        $changed = false;
        $status = (string)$job->status;
        if ($status === 'queued' || $status === 'running') {
            if ($elapsedMs >= self::READY_AFTER_MS) {
                $job->status = 'ready';
                $job->ready_on = $this->today;
                $job->expires_on = $this->addDays($this->today, self::RETENTION_DAYS);
                $job->storage_path = $this->schoolId . '/reports/' . (string)$job->id . '.csv';
                $changed = true;
            } elseif ($elapsedMs >= self::RUNNING_AFTER_MS && $status !== 'running') {
                $job->status = 'running';
                $changed = true;
            }
        }
        if (
            (string)$job->status === 'ready' && $job->expires_on !== null
            && Wire::date($job->expires_on) < $this->today
        ) {
            $job->status = 'expired';
            $job->storage_path = null;
            $changed = true;
        }

        return $changed;
    }

    // --- report bodies -------------------------------------------------------

    /**
     * Build a report's header + string rows.
     *
     * @param array<string, mixed> $filters
     * @return array{header: array<int, string>, rows: array<int, array<int, string>>}
     */
    public function build(string $type, array $filters): array
    {
        switch ($type) {
            case 'attendance_summary':
                return $this->attendanceSummary($filters);
            case 'admissions_conversion':
                return $this->admissionsConversion();
            case 'class_list':
                return $this->classList($filters);
            case 'grade_distribution':
                return $this->gradeDistribution($filters);
            case 'fee_ageing':
                return $this->feeAgeing($filters);
            case 'collections':
                return $this->collections($filters);
            case 'reconciliation_status':
                return $this->reconciliationStatus();
            default:
                return ['header' => [], 'rows' => []];
        }
    }

    private function attendanceSummary(array $filters): array
    {
        $classOf = [];
        foreach ($this->students() as $s) {
            $classOf[(string)$s->id] = (string)$s->class_group;
        }
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $classGroup = $filters['classGroup'] ?? null;
        $buckets = [];
        // The window is bounded in SQL; the same PHP guards stay as a belt on the
        // boundary so the output is byte-identical to the whole-history version.
        foreach ($this->attendance($from, $to) as $r) {
            $date = Wire::date($r->date);
            if ($from && $date < $from) {
                continue;
            }
            if ($to && $date > $to) {
                continue;
            }
            $className = $classOf[(string)$r->student_id] ?? null;
            if ($className === null) {
                continue;
            }
            if ($classGroup && $classGroup !== 'all' && $className !== $classGroup) {
                continue;
            }
            $b = $buckets[$className] ?? ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
            $status = (string)$r->status;
            if (isset($b[$status])) {
                $b[$status]++;
            }
            $buckets[$className] = $b;
        }
        ksort($buckets, SORT_STRING);
        $rows = [];
        foreach ($buckets as $className => $b) {
            $total = $b['present'] + $b['absent'] + $b['late'] + $b['excused'];
            $rate = $total === 0 ? 0 : ($b['present'] + $b['late']) / $total;
            $rows[] = [(string)$className, (string)$b['present'], (string)$b['absent'], (string)$b['late'], (string)$b['excused'], Money::jsRound($rate * 100) . '%'];
        }

        return ['header' => ['Class', 'Present', 'Absent', 'Late', 'Excused', 'Attendance rate'], 'rows' => $rows];
    }

    private function admissionsConversion(): array
    {
        $counts = [];
        $total = 0;
        foreach ($this->tenant()->query('EmsAdmissionApplications')->all() as $a) {
            $counts[(string)$a->status] = ($counts[(string)$a->status] ?? 0) + 1;
            $total++;
        }
        // Descending by count; PHP 8 usort is stable, so ties keep first-seen
        // order — matching the mock's stable sort.
        $pairs = [];
        foreach ($counts as $stage => $n) {
            $pairs[] = ['stage' => (string)$stage, 'n' => $n];
        }
        usort($pairs, fn($a, $b) => $b['n'] <=> $a['n']);
        $rows = [];
        foreach ($pairs as $p) {
            $rows[] = [$p['stage'], (string)$p['n'], $total === 0 ? '0%' : Money::jsRound($p['n'] / $total * 100) . '%'];
        }

        return ['header' => ['Stage', 'Applications', 'Share'], 'rows' => $rows];
    }

    private function classList(array $filters): array
    {
        $classGroup = $filters['classGroup'] ?? null;
        $guardians = $this->guardians();
        $primaryOf = [];
        foreach ($guardians as $g) {
            if ((bool)$g->is_primary) {
                $primaryOf[(string)$g->student_id] = $g;
            }
        }
        $students = [];
        foreach ($this->students() as $s) {
            if ((string)$s->status !== 'enrolled') {
                continue;
            }
            if ($classGroup && $classGroup !== 'all' && (string)$s->class_group !== $classGroup) {
                continue;
            }
            $students[] = $s;
        }
        usort($students, fn($a, $b) => strcmp(
            trim((string)$a->last_name . ' ' . (string)$a->first_name),
            trim((string)$b->last_name . ' ' . (string)$b->first_name),
        ));
        $rows = [];
        foreach ($students as $s) {
            $primary = $primaryOf[(string)$s->id] ?? null;
            $rows[] = [
                (string)$s->admission_number,
                (string)$s->last_name . ', ' . (string)$s->first_name,
                (string)$s->class_group,
                $primary === null ? '' : trim((string)$primary->first_name . ' ' . (string)$primary->last_name),
                $primary === null ? '' : (string)$primary->phone,
            ];
        }

        return ['header' => ['Admission number', 'Student', 'Class', 'Primary guardian', 'Guardian phone'], 'rows' => $rows];
    }

    private function gradeDistribution(array $filters): array
    {
        $term = $filters['term'] ?? null;
        $exams = [];
        foreach (
            $this->tenant()->query('EmsExams')
            ->where(['status' => 'published'])->all() as $e
        ) {
            if (!$term || $term === 'all' || (string)$e->term === $term) {
                $exams[] = $e;
            }
        }
        $rows = [];
        foreach ($exams as $exam) {
            $bands = $this->grading->schemeForExam($exam)['bands'];
            $buckets = [];
            foreach (
                $this->tenant()->query('EmsExamGrades')
                ->where(['exam_id' => (string)$exam->id])->all() as $g
            ) {
                if ($g->ca === null || $g->exam === null) {
                    continue;
                }
                $letter = Grading::gradeFor((float)$g->ca + (float)$g->exam, $bands)['letter'];
                $buckets[$letter] = ($buckets[$letter] ?? 0) + 1;
            }
            ksort($buckets, SORT_STRING);
            foreach ($buckets as $letter => $n) {
                $rows[] = [(string)$exam->title . ' (' . (string)$exam->session . ')', (string)$letter, (string)$n];
            }
        }

        return ['header' => ['Examination', 'Grade', 'Scores'], 'rows' => $rows];
    }

    private function feeAgeing(array $filters): array
    {
        $term = $filters['term'] ?? null;
        $paid = $this->fees->netPaidByInvoice();
        $buckets = [];
        foreach ($this->invoices() as $i) {
            if ($term && $term !== 'all' && (string)$i->term !== $term) {
                continue;
            }
            foreach ($this->outstandingPieces($i, (int)($paid[(string)$i->id] ?? 0)) as $piece) {
                $days = Money::jsRound((strtotime($this->today . ' UTC') - strtotime($piece['dueOn'] . ' UTC')) / 86400);
                $key = $this->ageLabel($days);
                $b = $buckets[$key] ?? ['count' => 0, 'amount' => 0];
                $b['count']++;
                $b['amount'] += $piece['amount'];
                $buckets[$key] = $b;
            }
        }
        $order = ['Not yet due', '1 to 30 days', '31 to 60 days', 'Over 60 days'];
        $rows = [];
        foreach ($order as $k) {
            if (isset($buckets[$k])) {
                $rows[] = [$k, (string)$buckets[$k]['count'], $this->naira($buckets[$k]['amount'])];
            }
        }

        return ['header' => ['Age', 'Amounts due', 'Outstanding'], 'rows' => $rows];
    }

    private function collections(array $filters): array
    {
        $term = $filters['term'] ?? null;
        $paid = $this->fees->netPaidByInvoice();
        $classOf = [];
        foreach ($this->students() as $s) {
            $classOf[(string)$s->id] = (string)$s->class_group;
        }
        $byClass = [];
        foreach ($this->invoices() as $i) {
            if ((string)$i->status === 'cancelled') {
                continue;
            }
            if ($term && $term !== 'all' && (string)$i->term !== $term) {
                continue;
            }
            $className = $classOf[(string)$i->student_id] ?? 'Unassigned';
            $b = $byClass[$className] ?? ['invoiced' => 0, 'collected' => 0];
            $b['invoiced'] += (int)$i->total;
            $b['collected'] += (int)($paid[(string)$i->id] ?? 0);
            $byClass[$className] = $b;
        }
        ksort($byClass, SORT_STRING);
        $rows = [];
        foreach ($byClass as $className => $b) {
            $rate = $b['invoiced'] === 0 ? '0%' : Money::jsRound($b['collected'] / $b['invoiced'] * 100) . '%';
            $rows[] = [
                (string)$className,
                $this->naira($b['invoiced']),
                $this->naira($b['collected']),
                $this->naira($b['invoiced'] - $b['collected']),
                $rate,
            ];
        }

        return ['header' => ['Class', 'Invoiced', 'Collected', 'Outstanding', 'Collection rate'], 'rows' => $rows];
    }

    private function reconciliationStatus(): array
    {
        $students = [];
        foreach ($this->students() as $s) {
            $students[(string)$s->id] = $s;
        }
        $invoices = [];
        foreach ($this->invoices() as $i) {
            $invoices[(string)$i->id] = $i;
        }
        $payments = [];
        foreach (
            $this->tenant()->query('EmsPayments')
            ->where(['channel' => 'provider'])->all() as $p
        ) {
            $payments[] = $p;
        }
        usort($payments, fn($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));
        $rows = [];
        foreach ($payments as $p) {
            $s = $students[(string)$p->student_id] ?? null;
            $rows[] = [
                (string)($p->reference ?? ''),
                $s === null ? '' : trim((string)$s->first_name . ' ' . (string)$s->last_name),
                $invoices[(string)$p->invoice_id]->invoice_number ?? '',
                $this->naira((int)$p->amount),
                (string)$p->state,
                Wire::date($p->paid_on),
            ];
        }

        return ['header' => ['Reference', 'Student', 'Invoice', 'Amount', 'State', 'Date'], 'rows' => $rows];
    }

    /**
     * The CSV text with its provenance header (§3.21 AC-5).
     *
     * @param array{header: array<int, string>, rows: array<int, array<int, string>>} $rows
     */
    public function labelled(EntityInterface $school, EntityInterface $job, array $rows): string
    {
        $definition = self::DEFINITIONS[(string)$job->report_type];
        $lines = [
            '# ' . (string)($school->name ?? 'School') . ' — ' . $definition['title'],
            '# Requested by ' . (string)$job->requested_by . ' on ' . Wire::date($job->requested_on),
            '# Generated ' . (Wire::date($job->ready_on) ?? $this->today) . '; this copy expires ' . (Wire::date($job->expires_on) ?? ''),
        ];
        if ($definition['rowLevel']) {
            $lines[] = '# CONFIDENTIAL — names individual students. Do not forward.';
        }
        $filters = $this->decodeFilters($job->filters);
        $parts = [];
        foreach ($filters as $k => $v) {
            if ($v !== null && $v !== '' && $v !== false) {
                $parts[] = $k . '=' . $v;
            }
        }
        $lines[] = '# Filters: ' . ($parts === [] ? 'none' : implode(', ', $parts));
        $lines[] = implode(',', array_map([self::class, 'csvCell'], $rows['header']));
        foreach ($rows['rows'] as $row) {
            $lines[] = implode(',', array_map([self::class, 'csvCell'], $row));
        }

        return implode("\n", $lines);
    }

    public static function csvCell(string $value): string
    {
        if (preg_match('/[",\n]/', $value)) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }

    // --- helpers -------------------------------------------------------------

    /** @return array<int, array{dueOn:string, amount:int}> */
    private function outstandingPieces(EntityInterface $invoice, int $paid): array
    {
        if ((string)$invoice->status === 'cancelled') {
            return [];
        }
        $balance = (int)$invoice->total - $paid;
        if ($balance <= 0) {
            return [];
        }
        $instalments = $this->decodeFilters($invoice->instalments);
        if ($instalments === []) {
            return [['dueOn' => Wire::date($invoice->due_date), 'amount' => $balance]];
        }
        $out = [];
        foreach (Money::instalmentStates($instalments, $paid, $this->today) as $s) {
            if ((int)$s['balance'] > 0) {
                $out[] = ['dueOn' => (string)$s['dueOn'], 'amount' => (int)$s['balance']];
            }
        }

        return $out;
    }

    private function ageLabel(int $days): string
    {
        if ($days <= 0) {
            return 'Not yet due';
        }
        if ($days <= 30) {
            return '1 to 30 days';
        }
        if ($days <= 60) {
            return '31 to 60 days';
        }

        return 'Over 60 days';
    }

    /** kobo → Naira, formatted as JS String(kobo/100) would render it. */
    private function naira(int $kobo): string
    {
        $value = $kobo / 100;
        if ($value == floor($value)) {
            return (string)(int)$value;
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function addDays(string $iso, int $days): string
    {
        return (new DateTimeImmutable($iso . ' 00:00:00', new DateTimeZone('UTC')))
            ->modify('+' . $days . ' days')->format('Y-m-d');
    }

    /** @param mixed $value */
    private function decodeFilters(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($value) ? $value : [];
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function students(): array
    {
        return $this->tenant()->query('EmsStudents')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function guardians(): array
    {
        return $this->tenant()->query('EmsGuardians')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function invoices(): array
    {
        return $this->tenant()->query('EmsInvoices')->all()->toList();
    }

    /**
     * Attendance records, optionally bounded to a [from, to] date window pushed
     * into SQL on the (school_id, date) index — the report never loads the whole
     * register when a range is asked for.
     *
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function attendance(?string $from = null, ?string $to = null): array
    {
        $query = $this->tenant()->query('EmsAttendanceRecords');
        if ($from !== null && $from !== '') {
            $query->where(['date >=' => $from]);
        }
        if ($to !== null && $to !== '') {
            $query->where(['date <=' => $to]);
        }

        return $query->all()->toList();
    }
}
