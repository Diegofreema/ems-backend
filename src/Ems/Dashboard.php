<?php
declare(strict_types=1);

namespace App\Ems;

use App\Ems\Serializer\CommsSerializer;
use App\Ems\Serializer\ExamSerializer;
use App\Ems\Serializer\Wire;
use Cake\ORM\Locator\LocatorInterface;

/**
 * The staff dashboard engine — one read that assembles the school's day at a
 * glance (people, today's registers, current-term fees, the admissions
 * pipeline, upcoming sittings, latest announcements). Like Analytics it stores
 * NOTHING: every figure is computed on read from the tables the modules
 * mutate. Unlike Analytics (whose CURRENT_TERM is a mock-parity constant) the
 * term here is derived from the academic calendar, falling back to all terms
 * when no term brackets today.
 *
 * Counting stays in SQL (GROUP BY status / date) so the dashboard never
 * materialises the whole register or student body to render six numbers.
 */
class Dashboard
{
    private const TREND_DAYS = 10;
    private const UPCOMING_SITTINGS = 5;
    private const ANNOUNCEMENTS = 3;

    /** @var \Cake\ORM\Locator\LocatorInterface */
    private $locator;

    /** @var string */
    private $schoolId;

    /** @var \App\Ems\Fees */
    private $fees;

    /** @var string Server's real today (YYYY-MM-DD). */
    private $today;

    /** @var \App\Ems\Tenant|null */
    private $tenantScope;

    public function __construct(LocatorInterface $locator, string $schoolId, Fees $fees, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->fees = $fees;
        $this->today = $today;
    }

    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    /**
     * GET /dashboard — the whole payload.
     */
    public function overview(): array
    {
        $term = $this->currentTerm();

        return [
            'today' => $this->today,
            'term' => $term,
            'people' => $this->people(),
            'attendanceToday' => $this->attendanceToday(),
            'fees' => $this->feesSummary($term === null ? null : (string)$term['name']),
            'admissions' => $this->admissions(),
            'upcomingSittings' => $this->upcomingSittings(),
            'announcements' => $this->announcements(),
            'attendanceTrend' => $this->attendanceTrend(),
            'openIncidents' => $this->tenant()->query('EmsIncidents')
                ->where(['status !=' => 'closed'])
                ->count(),
        ];
    }

    /**
     * The term whose dates bracket today across open sessions; else the most
     * recently started term (the between-terms gap belongs to the term just
     * ended); else the first upcoming; else null (calendar not configured).
     * Shape: {session, name, startsOn, endsOn, status}.
     */
    public function currentTerm(): ?array
    {
        $sessions = $this->tenant()->query('EmsAcademicSessions')
            ->where(['status' => 'open'])
            ->all()->toList();
        if ($sessions === []) {
            return null;
        }
        $nameBySession = [];
        foreach ($sessions as $s) {
            $nameBySession[(string)$s->id] = (string)$s->name;
        }
        $terms = $this->tenant()->query('EmsAcademicTerms')
            ->where(['session_id IN' => array_keys($nameBySession)])
            ->all()->toList();
        if ($terms === []) {
            return null;
        }
        usort($terms, fn ($a, $b) => strcmp(Wire::date($a->starts_on), Wire::date($b->starts_on)));

        $current = null;
        foreach ($terms as $t) {
            $starts = Wire::date($t->starts_on);
            $ends = Wire::date($t->ends_on);
            if ($starts <= $this->today && $this->today <= $ends) {
                $current = $t;
                break;
            }
            if ($starts <= $this->today) {
                $current = $t; // latest started so far; overwritten by a later match
            }
        }
        $current = $current ?? $terms[0];

        return [
            'session' => $nameBySession[(string)$current->session_id] ?? '',
            'name' => (string)$current->name,
            'startsOn' => Wire::date($current->starts_on),
            'endsOn' => Wire::date($current->ends_on),
            'status' => (string)$current->status,
        ];
    }

    private function people(): array
    {
        $byStatus = ['enrolled' => 0, 'applicant' => 0, 'graduated' => 0, 'withdrawn' => 0];
        $rows = $this->tenant()->query('EmsStudents')
            ->select(['status', 'n' => 'COUNT(*)'])
            ->groupBy(['status'])
            ->all();
        foreach ($rows as $row) {
            if (array_key_exists((string)$row->status, $byStatus)) {
                $byStatus[(string)$row->status] = (int)$row->n;
            }
        }
        $activeTeachers = $this->tenant()->query('EmsTeachers')
            ->where(['status' => 'active'])
            ->count();

        return [
            'enrolled' => $byStatus['enrolled'],
            'applicants' => $byStatus['applicant'],
            'graduated' => $byStatus['graduated'],
            'withdrawn' => $byStatus['withdrawn'],
            'activeTeachers' => $activeTeachers,
            'classCount' => $this->tenant()->query('EmsClassGroups')->count(),
            'studentTeacherRatio' => Wire::num(
                $activeTeachers === 0 ? 0 : $byStatus['enrolled'] / $activeTeachers
            ),
        ];
    }

    /**
     * Today's registers: marks by status plus how many class registers have
     * been submitted (ems_attendance_sessions is the submission row).
     */
    private function attendanceToday(): array
    {
        $counts = ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0];
        $rows = $this->tenant()->query('EmsAttendanceRecords')
            ->select(['status', 'n' => 'COUNT(*)'])
            ->where(['date' => $this->today])
            ->groupBy(['status'])
            ->all();
        foreach ($rows as $row) {
            if (array_key_exists((string)$row->status, $counts)) {
                $counts[(string)$row->status] = (int)$row->n;
            }
        }
        $total = array_sum($counts);

        return [
            'present' => $counts['present'],
            'late' => $counts['late'],
            'absent' => $counts['absent'],
            'excused' => $counts['excused'],
            'total' => $total,
            'rate' => Wire::num($total === 0 ? 0 : ($counts['present'] + $counts['late']) / $total),
            'registersSubmitted' => $this->tenant()->query('EmsAttendanceSessions')
                ->where(['date' => $this->today])
                ->count(),
        ];
    }

    /**
     * Fees position, mirroring the Fees module's derived balances. Invoiced /
     * collected are scoped to the current term when the calendar names one;
     * overdue counts across every open invoice regardless of term.
     */
    private function feesSummary(?string $termName): array
    {
        $paid = $this->fees->netPaidByInvoice();
        $invoiced = 0;
        $collected = 0;
        $overdueInvoices = 0;
        $rows = $this->tenant()->query('EmsInvoices')
            ->select(['id', 'term', 'total', 'status', 'due_date'])
            ->where(['status !=' => 'cancelled'])
            ->all();
        foreach ($rows as $i) {
            $net = (int)($paid[(string)$i->id] ?? 0);
            $total = (int)$i->total;
            if ($termName === null || (string)$i->term === $termName) {
                $invoiced += $total;
                $collected += min($net, $total);
            }
            if (Wire::date($i->due_date) < $this->today && $total - $net > 0) {
                $overdueInvoices++;
            }
        }

        return [
            'term' => $termName ?? 'all',
            'invoiced' => $invoiced,
            'collected' => $collected,
            'outstanding' => $invoiced - $collected,
            'collectionRate' => Wire::num($invoiced === 0 ? 0 : $collected / $invoiced),
            'overdueInvoices' => $overdueInvoices,
        ];
    }

    private function admissions(): array
    {
        $pipeline = [
            'submitted' => 0, 'under_review' => 0, 'waitlisted' => 0, 'offered' => 0,
            'accepted' => 0, 'declined' => 0, 'withdrawn' => 0, 'expired' => 0, 'enrolled' => 0,
        ];
        $rows = $this->tenant()->query('EmsAdmissionApplications')
            ->select(['status', 'n' => 'COUNT(*)'])
            ->groupBy(['status'])
            ->all();
        foreach ($rows as $row) {
            if (array_key_exists((string)$row->status, $pipeline)) {
                $pipeline[(string)$row->status] = (int)$row->n;
            }
        }

        return [
            'openCycles' => $this->tenant()->query('EmsAdmissionCycles')
                ->where(['status' => 'open'])
                ->count(),
            // Applications still owed a decision by the office.
            'pendingReview' => $pipeline['submitted'] + $pipeline['under_review'] + $pipeline['waitlisted'],
            'pipeline' => $pipeline,
        ];
    }

    /** The next sittings across every level, soonest first. */
    private function upcomingSittings(): array
    {
        $schedules = $this->tenant()->query('EmsExamSchedules')
            ->where(['date >=' => $this->today])
            ->all()->toList();
        usort($schedules, fn ($a, $b) =>
            strcmp(Wire::date($a->date), Wire::date($b->date))
                ?: strcmp((string)$a->start_time, (string)$b->start_time));
        $schedules = array_slice($schedules, 0, self::UPCOMING_SITTINGS);
        if ($schedules === []) {
            return [];
        }

        $examIds = array_values(array_unique(array_map(fn ($s) => (string)$s->exam_id, $schedules)));
        $titleById = [];
        foreach ($this->tenant()->query('EmsExams')
            ->select(['id', 'title'])
            ->where(['id IN' => $examIds])
            ->all() as $e) {
            $titleById[(string)$e->id] = (string)$e->title;
        }

        return array_map(
            fn ($s) => ExamSerializer::schedule($s)
                + ['examTitle' => $titleById[(string)$s->exam_id] ?? 'Examination'],
            $schedules
        );
    }

    /** Latest published announcements, pinned first. */
    private function announcements(): array
    {
        $rows = $this->tenant()->query('EmsAnnouncements')
            ->where(['status' => 'published'])
            ->all()->toList();
        usort($rows, function ($a, $b) {
            if ((bool)$a->pinned !== (bool)$b->pinned) {
                return (bool)$a->pinned ? -1 : 1;
            }

            return strcmp(
                Wire::date($b->published_on) ?? '',
                Wire::date($a->published_on) ?? ''
            );
        });

        return array_map(
            [CommsSerializer::class, 'announcement'],
            array_slice($rows, 0, self::ANNOUNCEMENTS)
        );
    }

    /**
     * Attendance rate for the last TREND_DAYS register dates, oldest first —
     * one GROUP BY over the (school_id, date) index, never the raw register.
     */
    private function attendanceTrend(): array
    {
        $recentDates = $this->tenant()->query('EmsAttendanceRecords')
            ->select(['date'])
            ->distinct(['date'])
            ->orderByDesc('date')
            ->limit(self::TREND_DAYS)
            ->all()->toList();
        if ($recentDates === []) {
            return [];
        }
        $cutoff = end($recentDates)->date;
        $byDay = [];
        $rows = $this->tenant()->query('EmsAttendanceRecords')
            ->select(['date', 'status', 'n' => 'COUNT(*)'])
            ->where(['date >=' => $cutoff])
            ->groupBy(['date', 'status'])
            ->all();
        foreach ($rows as $row) {
            $date = Wire::date($row->date);
            $d = $byDay[$date] ?? ['attended' => 0, 'total' => 0];
            $status = (string)$row->status;
            $n = (int)$row->n;
            $d['total'] += $n;
            if ($status === 'present' || $status === 'late') {
                $d['attended'] += $n;
            }
            $byDay[$date] = $d;
        }
        ksort($byDay, SORT_STRING);

        $out = [];
        foreach ($byDay as $date => $d) {
            $out[] = [
                'date' => (string)$date,
                'rate' => Wire::num($d['total'] === 0 ? 0 : $d['attended'] / $d['total']),
                'present' => $d['attended'],
                'total' => $d['total'],
            ];
        }

        return $out;
    }
}
