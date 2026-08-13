<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Grading;
use App\Ems\Messages;
use App\Ems\Serializer\ClassSerializer;
use App\Ems\Serializer\CommsSerializer;
use App\Ems\Serializer\ExamSerializer;
use App\Ems\Serializer\SettingsSerializer;
use App\Ems\Serializer\StudentSerializer;
use App\Ems\Serializer\TeacherSerializer;
use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;

/**
 * The parent/student portal (document.md §3.19). Read-only endpoints:
 * `identity` resolves the signed-in account to its linked person (teacher /
 * wards / student), `wardOverview` derives one ward's cross-module position
 * (attendance, fees, latest result, upcoming sittings) from the same rules the
 * Classes, Fees and Academics modules use — never a stored figure — and
 * `dashboard` assembles the family home in one read: the current term, the
 * family's announcement feed, and every ward's overview.
 */
class PortalController extends AppController
{
    private const ATTENDANCE_WINDOW = 20;

    /**
     * GET /portal/identity — never errors. Resolves the token's user to its
     * PersonLink; falls back to the role's first active account (dev switcher).
     */
    public function identity(): Response
    {
        $user = null;
        if ($this->viewer->userId !== '') {
            $user = $this->tenant()->query('EmsUsers')
                ->where(['id' => $this->viewer->userId, 'status' => 'active'])
                ->first();
        }
        if ($user === null) {
            $user = $this->tenant()->query('EmsUsers')
                ->where(['role' => $this->viewer->role, 'status' => 'active'])
                ->first();
        }

        $identity = [
            'user' => $user === null ? null : SettingsSerializer::user($user),
            'teacher' => null,
            'wards' => [],
            'student' => null,
        ];
        $link = $user === null ? null : SettingsSerializer::link($user);
        if ($link === null) {
            return $this->json($identity);
        }

        switch ($link['kind']) {
            case 'teacher':
                $teacher = $this->tenant()->query('EmsTeachers')
                    ->where(['id' => $link['teacherId']])
                    ->first();
                $identity['teacher'] = $teacher === null ? null : TeacherSerializer::one($teacher);
                break;
            case 'parent':
                if ($link['studentIds'] !== []) {
                    $wards = $this->tenant()->query('EmsStudents')
                        ->where(['id IN' => $link['studentIds']])
                        ->all()->toList();
                    // Preserve the link's ward order.
                    $byId = [];
                    foreach ($wards as $w) {
                        $byId[(string)$w->id] = $w;
                    }
                    foreach ($link['studentIds'] as $sid) {
                        if (isset($byId[$sid])) {
                            $identity['wards'][] = StudentSerializer::one($byId[$sid]);
                        }
                    }
                }
                break;
            case 'student':
                $student = $this->tenant()->query('EmsStudents')
                    ->where(['id' => $link['studentId']])
                    ->first();
                $identity['student'] = $student === null ? null : StudentSerializer::one($student);
                break;
        }

        return $this->json($identity);
    }

    /**
     * GET /portal/notifications — the signed-in account's in-app inbox (§3.19):
     * latest 50 rows plus the TOTAL unread count (not just unread-in-page).
     * Accounts with no inbox rows — every staff role today — get an empty list.
     */
    public function notifications(): Response
    {
        $userId = $this->viewer->userId;
        if ($userId === '') {
            return $this->json(['items' => [], 'unread' => 0]);
        }
        $rows = $this->tenant()->query('EmsPortalNotifications')
            ->where(['user_id' => $userId])
            ->orderByDesc('created')
            ->limit(50)
            ->all()->toList();
        $unread = $this->tenant()->query('EmsPortalNotifications')
            ->where(['user_id' => $userId, 'read_at IS' => null])
            ->count();

        return $this->json([
            'items' => array_map([CommsSerializer::class, 'portalNotification'], $rows),
            'unread' => $unread,
        ]);
    }

    /** POST /portal/notifications/read — opening the panel marks all read. */
    public function markNotificationsRead(): Response
    {
        $userId = $this->viewer->userId;
        if ($userId !== '') {
            $this->fetchTable('EmsPortalNotifications')->updateAll(
                ['read_at' => FrozenTime::now('UTC')],
                ['school_id' => $this->viewer->schoolId, 'user_id' => $userId, 'read_at IS' => null],
            );
        }

        return $this->json(null, 204);
    }

    /**
     * GET /portal/wards/{studentId} — one ward's live overview. Student access
     * (403 family message); 404 if the student is gone.
     */
    public function wardOverview(string $studentId): Response
    {
        $this->scope()->assertStudentAccess($studentId);
        $student = $this->tenant()->query('EmsStudents')
            ->where(['id' => $studentId])
            ->first();
        if ($student === null) {
            $this->fail(404, Messages::PORTAL_STUDENT_NOT_FOUND);
        }

        return $this->json($this->wardData($student));
    }

    /**
     * GET /portal/dashboard — the family home in one read. Family roles only;
     * staff have their own dashboard.
     */
    public function dashboard(): Response
    {
        $this->requireRole(['parent', 'student'], Messages::ACTION_FORBIDDEN);

        // The viewer's own reach — the parent's linked wards in link order, or
        // the student themself. Scope never returns null for a family role.
        $ids = $this->scope()->studentIds() ?? [];
        $students = [];
        if ($ids !== []) {
            $byId = [];
            foreach (
                $this->tenant()->query('EmsStudents')
                ->where(['id IN' => $ids])
                ->all() as $s
            ) {
                $byId[(string)$s->id] = $s;
            }
            foreach ($ids as $sid) {
                if (isset($byId[$sid])) {
                    $students[] = $byId[$sid];
                }
            }
        }

        return $this->json([
            'term' => $this->dashboardEngine()->currentTerm(),
            'announcements' => $this->latestAnnouncements(),
            'wards' => array_map(fn($s) => $this->wardData($s), $students),
        ]);
    }

    /**
     * One ward's live overview — the shared body of `wardOverview` and each
     * `dashboard` ward entry.
     */
    private function wardData(EntityInterface $student): array
    {
        $studentId = (string)$student->id;
        $today = FrozenDate::today()->format('Y-m-d');

        // Attendance over the school's recent register window. The window's date
        // range and this ward's marks within it are both read in SQL on the
        // (school_id, date) / (school_id, student_id, date) indexes — the whole
        // register is never loaded to render one ward.
        $windowDates = $this->tenant()->query('EmsAttendanceRecords')
            ->select(['date'])
            ->distinct(['date'])
            ->orderByDesc('date')
            ->limit(self::ATTENDANCE_WINDOW)
            ->all()
            ->toList();
        $marks = [];
        if ($windowDates !== []) {
            // Everything on or after the window's oldest date is exactly its N
            // most recent register dates (the same set the old array_slice took).
            $cutoff = end($windowDates)->date;
            $marks = $this->tenant()->query('EmsAttendanceRecords')
                ->where([
                    'student_id' => $studentId,
                    'date >=' => $cutoff,
                ])
                ->all()
                ->toList();
        }
        $counts = ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0];
        foreach ($marks as $r) {
            $status = (string)$r->status;
            if (array_key_exists($status, $counts)) {
                $counts[$status]++;
            }
        }
        $attended = $counts['present'] + $counts['late'];
        $attendance = [
            'rate' => Wire::num(count($marks) === 0 ? 0 : $attended / count($marks)),
            'absences' => $counts['absent'],
            'windowDays' => self::ATTENDANCE_WINDOW,
            'present' => $counts['present'],
            'late' => $counts['late'],
            'absent' => $counts['absent'],
            'excused' => $counts['excused'],
            'total' => count($marks),
        ];

        // Fees, mirroring the Fees module's derived balances. `nextDueDate` is
        // the earliest due date still carrying a balance.
        $netPaid = $this->feesEngine()->netPaidByInvoice();
        $invoices = $this->tenant()->query('EmsInvoices')
            ->where(['student_id' => $studentId])
            ->all()->toList();
        $balance = 0;
        $overdueCount = 0;
        $nextDueDate = null;
        foreach ($invoices as $i) {
            if ((string)$i->status === 'cancelled') {
                continue;
            }
            $paid = (int)($netPaid[(string)$i->id] ?? 0);
            $balance += (int)$i->total - $paid;
            if ((int)$i->total - $paid > 0) {
                $due = Wire::date($i->due_date);
                if ($due < $today) {
                    $overdueCount++;
                }
                if ($nextDueDate === null || $due < $nextDueDate) {
                    $nextDueDate = $due;
                }
            }
        }

        // The latest published examination this ward sat.
        $exams = $this->tenant()->query('EmsExams')
            ->where(['status' => 'published'])
            ->all()->toList();
        usort($exams, fn($a, $b) => strcmp((string)$b->end_date, (string)$a->end_date));
        $latestResult = null;
        foreach ($exams as $exam) {
            $rows = $this->tenant()->query('EmsExamGrades')
                ->where([
                    'exam_id' => (string)$exam->id,
                    'student_id' => $studentId,
                    'ca IS NOT' => null,
                    'exam IS NOT' => null,
                ])->all()->toList();
            if ($rows === []) {
                continue;
            }
            $sum = 0.0;
            foreach ($rows as $g) {
                $sum += (float)$g->ca + (float)$g->exam;
            }
            $average = $sum / count($rows);
            $bands = $this->grading()->schemeForExam($exam)['bands'];
            $latestResult = [
                'examId' => (string)$exam->id,
                'examTitle' => (string)$exam->title,
                'session' => (string)$exam->session,
                'average' => Wire::num($average),
                'subjects' => count($rows),
                'grade' => Grading::gradeFor($average, $bands)['letter'],
            ];
            break;
        }

        // Upcoming sittings at the ward's level, soonest first.
        $level = trim(substr((string)$student->class_group, 0, -1));
        $titleById = [];
        foreach (
            $this->tenant()->query('EmsExams')
            ->all() as $e
        ) {
            $titleById[(string)$e->id] = (string)$e->title;
        }
        $schedules = $this->tenant()->query('EmsExamSchedules')
            ->where(['level' => $level, 'date >=' => $today])
            ->all()->toList();
        usort($schedules, fn($a, $b) => strcmp((string)$a->date, (string)$b->date) ?: strcmp((string)$a->start_time, (string)$b->start_time));
        $upcomingSittings = [];
        foreach (array_slice($schedules, 0, 5) as $s) {
            $upcomingSittings[] = ExamSerializer::schedule($s)
                + ['examTitle' => $titleById[(string)$s->exam_id] ?? 'Examination'];
        }

        // The ward's class group (matched by name — class_group is a name, not
        // an FK) and its timetable for today's weekday.
        $classGroup = $this->tenant()->query('EmsClassGroups')
            ->where(['name' => (string)$student->class_group])
            ->first();
        $todayPeriods = [];
        if ($classGroup !== null) {
            $slots = $this->tenant()->query('EmsTimetableSlots')
                ->where([
                    'class_group_id' => (string)$classGroup->id,
                    'day' => FrozenDate::today()->format('D'),
                ])
                ->orderByAsc('period')
                ->all();
            foreach ($slots as $slot) {
                $period = (int)$slot->period;
                $times = ClassSerializer::PERIODS[$period] ?? ['', ''];
                $todayPeriods[] = [
                    'period' => $period,
                    'start' => $times[0],
                    'end' => $times[1],
                    'subject' => (string)$slot->subject,
                ];
            }
        }

        return [
            'student' => StudentSerializer::one($student),
            'classGroupId' => $classGroup === null ? null : (string)$classGroup->id,
            'attendance' => $attendance,
            'fees' => [
                'balance' => $balance,
                'overdueCount' => $overdueCount,
                'nextDueDate' => $nextDueDate,
            ],
            'latestResult' => $latestResult,
            'upcomingSittings' => $upcomingSittings,
            'todayPeriods' => $todayPeriods,
        ];
    }

    /**
     * The viewer's announcement feed, pinned first then newest, capped to the
     * dashboard's few — the same audience rule as Communication::feed.
     */
    private function latestAnnouncements(): array
    {
        $audience = $this->comms()->feedAudienceForRole($this->viewer->role);
        $rows = [];
        foreach (
            $this->tenant()->query('EmsAnnouncements')
            ->where(['status' => 'published'])
            ->all() as $a
        ) {
            if ($audience === 'all' || (string)$a->audience === 'everyone' || (string)$a->audience === $audience) {
                $rows[] = $a;
            }
        }
        usort($rows, function ($a, $b) {
            if ((bool)$a->pinned !== (bool)$b->pinned) {
                return (bool)$a->pinned ? -1 : 1;
            }

            return strcmp(
                Wire::date($b->published_on) ?? '',
                Wire::date($a->published_on) ?? '',
            );
        });

        return array_map([CommsSerializer::class, 'announcement'], array_slice($rows, 0, 3));
    }
}
