<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Datasource\EntityInterface;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorInterface;
use Throwable;

/**
 * The Communication engine (document.md §3.20). One place resolves an audience,
 * checks consent, masks an address and asks the "provider", so the composer's
 * preview and the actual send can never disagree — the preview IS the resolution
 * run without dispatching. Alerts are DERIVED here from the fees, attendance and
 * exams tables, so a reminder can never disagree with the data it warns about.
 *
 * Consent rule (the heart of it): `transactional` always sends; `school_news`
 * needs an explicit enabled preference — a missing record means NO. Silence is
 * never a yes.
 */
class Comms
{
    public const MAX_DELIVERY_ATTEMPTS = 3;
    private const ABSENCE_WINDOW_DAYS = 14;
    private const ABSENCE_THRESHOLD = 3;

    private const FAILURE_REASONS = [
        'Mailbox full',
        'Address does not exist',
        'Number unreachable',
        'Provider rejected the message',
    ];

    /** Failures first — the rows that need a person are worth reading. */
    public const STATUS_ORDER = ['failed' => 0, 'queued' => 1, 'sent' => 2, 'suppressed' => 3];

    /** The reminder content the "gateway" sends per alert kind. */
    public const ALERT_MESSAGES = [
        'fee_overdue' => [
            'kind' => 'fee_reminder',
            'subject' => 'Outstanding fees on your ward’s account',
            'body' => 'Dear parent, our records show an overdue fee balance on your ward’s account. Kindly settle it, or speak to the bursary about a payment plan.',
        ],
        'attendance' => [
            'kind' => 'attendance_alert',
            'subject' => 'Attendance concern',
            'body' => 'Dear parent, your ward has recorded repeated absences in the last two weeks. Please contact the form teacher to discuss.',
        ],
        'result_published' => [
            'kind' => 'result_alert',
            'subject' => 'Results are now published',
            'body' => 'Dear parent, your ward’s latest results are now published in the portal, and report cards are ready. Please review them with your ward.',
        ],
    ];

    /** @var \Cake\ORM\Locator\LocatorInterface */
    private $locator;

    /** @var string */
    private $schoolId;

    /** @var string */
    private $today;

    /** @var \App\Ems\Tenant|null */
    private $tenantScope;

    public function __construct(LocatorInterface $locator, string $schoolId, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
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

    /** teacher→teachers, parent→parents, student→students, staff→'all'. */
    public function feedAudienceForRole(string $role): string
    {
        switch ($role) {
            case 'teacher':
                return 'teachers';
            case 'parent':
                return 'parents';
            case 'student':
                return 'students';
            default:
                return 'all';
        }
    }

    public function audienceLabel(string $audience): string
    {
        switch ($audience) {
            case 'all':
                return 'All audiences';
            case 'everyone':
                return 'Everyone';
            case 'teachers':
                return 'Teachers';
            case 'parents':
                return 'Parents';
            case 'students':
                return 'Students';
            default:
                return $audience;
        }
    }

    public function maskAddress(string $address): string
    {
        if (strpos($address, '@') !== false) {
            [$name, $domain] = explode('@', $address, 2);
            return substr($name, 0, 2) . str_repeat('•', max(strlen($name) - 2, 1)) . '@' . $domain;
        }

        return substr($address, 0, 5) . str_repeat('•', max(strlen($address) - 8, 1)) . substr($address, -3);
    }

    /**
     * The people an audience resolves to on THIS channel, in a stable order.
     * Non-teacher audiences resolve to the PRIMARY guardian of each enrolled
     * student, so a send to students and to parents addresses the same inboxes
     * ('everyone' counts each household once). teachers/everyone add active
     * teachers.
     *
     * @return array<int, array{personId:string,personName:string,personKind:string,aboutStudentName:?string,address:string}>
     */
    public function resolveAudience(string $audience, string $channel): array
    {
        $members = [];
        if ($audience !== 'teachers') {
            $enrolled = [];
            foreach ($this->students() as $s) {
                if ((string)$s->status === 'enrolled') {
                    $enrolled[(string)$s->id] = $s;
                }
            }
            $members = $this->guardianMembers($enrolled, $channel);
        }
        if ($audience === 'teachers' || $audience === 'everyone') {
            foreach ($this->teachers() as $t) {
                if ((string)$t->status !== 'active') {
                    continue;
                }
                $members[] = [
                    'personId' => (string)$t->id,
                    'personName' => trim((string)$t->first_name . ' ' . (string)$t->last_name),
                    'personKind' => 'teacher',
                    'aboutStudentName' => null,
                    'address' => $channel === 'email' ? (string)$t->email : (string)$t->phone,
                ];
            }
        }

        return $members;
    }

    /** The primary guardians affected by one live transactional alert. */
    public function resolveAlertAudience(string $kind, string $channel): array
    {
        switch ($kind) {
            case 'fee_overdue':
                $studentIds = $this->overdueSummary()['studentIds'];
                break;
            case 'attendance':
                $studentIds = $this->frequentAbsenceStudentIds();
                break;
            case 'result_published':
                $studentIds = $this->latestPublishedExam() === null ? [] : $this->enrolledStudentIds();
                break;
            default:
                $studentIds = [];
        }

        $students = [];
        foreach ($this->students() as $student) {
            if (in_array((string)$student->id, $studentIds, true)) {
                $students[(string)$student->id] = $student;
            }
        }
        return $this->guardianMembers($students, $channel);
    }

    /**
     * Whether this person has agreed to hear from the school for this purpose.
     *
     * @return array{allowed:bool,reason:?string}
     */
    public function consentFor(string $personId, string $channel, string $purpose): array
    {
        if ($purpose === 'transactional') {
            return ['allowed' => true, 'reason' => null];
        }
        $pref = $this->tenant()->query('EmsContactPreferences')
            ->where([
                'person_id IN' => $this->guardianAliasIds($personId, $channel),
                'channel' => $channel,
                'purpose' => $purpose,
            ])
            ->orderByDesc('recorded_on')
            ->orderByDesc('modified')
            ->first();
        if ($pref === null) {
            return ['allowed' => false, 'reason' => 'No consent recorded for school news'];
        }
        if (!(bool)$pref->enabled) {
            return ['allowed' => false, 'reason' => $pref->withdrawn_on !== null ? 'Consent withdrawn' : 'Opted out'];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Deliver e-mail through the configured provider; keep SMS simulated until a provider exists.
     *
     * @return array{ok:bool,reason:?string,ref:?string}
     */
    public function attemptDelivery(
        string $recipientId,
        int $attempt,
        string $channel,
        string $address,
        string $subject,
        string $body,
    ): array {
        if ($channel === 'email') {
            try {
                Resend::deliver($address, $subject, $body);

                return [
                    'ok' => true,
                    'reason' => null,
                    'ref' => 'EMAIL-' . strtoupper(substr($recipientId, 0, 8)),
                ];
            } catch (Throwable $e) {
                Log::error(sprintf(
                    'EMS email delivery failed for recipient %s on attempt %d: %s',
                    $recipientId,
                    $attempt,
                    $e->getMessage(),
                ));

                return ['ok' => false, 'reason' => 'Email provider rejected the message', 'ref' => null];
            }
        }

        $key = $recipientId . '#' . $attempt;
        $h = 0;
        $len = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            $h = ($h * 31 + ord($key[$i])) & 0xFFFFFFFF;
        }
        if ($h % 9 === 0) {
            return ['ok' => false, 'reason' => self::FAILURE_REASONS[$h % 4], 'ref' => null];
        }

        return ['ok' => true, 'reason' => null, 'ref' => 'MSG-' . substr(strtoupper($this->base36($h)), 0, 8)];
    }

    /** @param array<int, \Cake\Datasource\EntityInterface> $rows */
    public function deliveryStatusOf(array $rows): string
    {
        if ($rows === []) {
            return 'not_sent';
        }
        foreach ($rows as $r) {
            if ((string)$r->status === 'queued') {
                return 'queued';
            }
        }
        foreach ($rows as $r) {
            if ((string)$r->status === 'failed') {
                return 'partly_failed';
            }
        }

        return 'sent';
    }

    /**
     * The contact record a signed-in account IS, for its own preferences screen.
     *
     * @return array{personId:string,personName:string}|null
     */
    public function contactIdentityFor(string $userId, string $role, string $userName): ?array
    {
        $user = null;
        if ($userId !== '') {
            $user = $this->tenant()->query('EmsUsers')
                ->where(['id' => $userId, 'status' => 'active'])->first();
        }
        if ($user === null || $user->link_kind === null) {
            return null;
        }
        if ((string)$user->link_kind === 'teacher') {
            $t = $this->tenant()->query('EmsTeachers')
                ->where(['id' => (string)$user->link_teacher_id])->first();

            return $t === null ? null : ['personId' => (string)$t->id, 'personName' => trim((string)$t->first_name . ' ' . (string)$t->last_name)];
        }
        $wards = [];
        if ((string)$user->link_kind === 'parent') {
            $ids = $user->link_student_ids;
            if (is_string($ids)) {
                $ids = json_decode($ids, true);
            }
            $wards = is_array($ids) ? array_map('strval', $ids) : [];
        } elseif ($user->link_student_id !== null) {
            $wards = [(string)$user->link_student_id];
        }
        if ($wards === []) {
            return null;
        }
        $mine = [];
        foreach ($this->guardians() as $g) {
            if ((bool)$g->is_primary && in_array((string)$g->student_id, $wards, true)) {
                $mine[] = $g;
            }
        }
        if ($mine === []) {
            return null;
        }
        $chosen = null;
        foreach ($mine as $g) {
            if (trim((string)$g->first_name . ' ' . (string)$g->last_name) === (string)$user->name) {
                $chosen = $g;
                break;
            }
        }
        $chosen = $chosen ?? $mine[0];

        return ['personId' => (string)$chosen->id, 'personName' => trim((string)$chosen->first_name . ' ' . (string)$chosen->last_name)];
    }

    /**
     * One recipient per household contact on the chosen channel. Guardian rows
     * are student relationships, so the same household can appear more than
     * once when siblings attend the school.
     *
     * @param array<string,EntityInterface> $students
     * @return array<int,array{personId:string,personName:string,personKind:string,aboutStudentName:?string,address:string}>
     */
    private function guardianMembers(array $students, string $channel): array
    {
        $members = [];
        $byHousehold = [];
        foreach ($this->guardians() as $guardian) {
            $student = $students[(string)$guardian->student_id] ?? null;
            if (!(bool)$guardian->is_primary || $student === null) {
                continue;
            }
            $key = $this->guardianHouseholdKey($guardian, $channel);
            $studentName = trim((string)$student->first_name . ' ' . (string)$student->last_name);
            if (isset($byHousehold[$key])) {
                $index = $byHousehold[$key];
                $members[$index]['aboutStudentName'] = mb_substr(
                    (string)$members[$index]['aboutStudentName'] . ', ' . $studentName,
                    0,
                    190
                );
                continue;
            }
            $byHousehold[$key] = count($members);
            $members[] = [
                'personId' => (string)$guardian->id,
                'personName' => trim((string)$guardian->first_name . ' ' . (string)$guardian->last_name),
                'personKind' => 'guardian',
                'aboutStudentName' => $studentName,
                'address' => $channel === 'email' ? (string)$guardian->email : (string)$guardian->phone,
            ];
        }

        return $members;
    }

    /** @return array<int,string> */
    private function guardianAliasIds(string $personId, string $channel): array
    {
        $subject = $this->tenant()->query('EmsGuardians')->where(['id' => $personId])->first();
        if ($subject === null) {
            return [$personId];
        }
        $key = $this->guardianHouseholdKey($subject, $channel);
        $ids = [];
        foreach ($this->guardians() as $guardian) {
            if ((bool)$guardian->is_primary && $this->guardianHouseholdKey($guardian, $channel) === $key) {
                $ids[] = (string)$guardian->id;
            }
        }

        return $ids === [] ? [$personId] : $ids;
    }

    private function guardianHouseholdKey(EntityInterface $guardian, string $channel): string
    {
        $email = strtolower(trim((string)$guardian->email));
        $phone = preg_replace('/\D+/', '', (string)$guardian->phone) ?? '';
        $address = $channel === 'email' ? $email : $phone;
        if ($address !== '') {
            return $channel . ':' . $address;
        }

        return 'contact:' . $email . ':' . $phone . ':'
            . strtolower(trim((string)$guardian->first_name . ' ' . (string)$guardian->last_name));
    }

    // --- derived alerts ------------------------------------------------------

    /** @return array<int, array> */
    public function computeAlerts(): array
    {
        $alerts = [];
        foreach ([$this->feeOverdueAlert(), $this->attendanceAlert(), $this->resultAlert()] as $a) {
            if ($a !== null) {
                $alerts[] = $a;
            }
        }

        return $alerts;
    }

    private function feeOverdueAlert(): ?array
    {
        $summary = $this->overdueSummary();
        $count = $summary['count'];
        if ($count === 0) {
            return null;
        }

        return [
            'id' => 'alert_fee_overdue',
            'kind' => 'fee_overdue',
            'severity' => 'destructive',
            'title' => sprintf('%d invoice%s overdue', $count, $count === 1 ? '' : 's'),
            'detail' => sprintf('%s is outstanding past its due date. A reminder goes to each affected family.', Money::formatCurrency($summary['outstanding'])),
            'count' => $count,
            'audienceLabel' => 'Parents with overdue invoices',
        ];
    }

    private function attendanceAlert(): ?array
    {
        $flagged = count($this->frequentAbsenceStudentIds());
        if ($flagged === 0) {
            return null;
        }

        return [
            'id' => 'alert_attendance',
            'kind' => 'attendance',
            'severity' => 'warning',
            'title' => sprintf('%d student%s frequently absent', $flagged, $flagged === 1 ? '' : 's'),
            'detail' => '3+ absences in the last two weeks. An alert asks each family to contact the form teacher.',
            'count' => $flagged,
            'audienceLabel' => 'Parents of frequently absent students',
        ];
    }

    private function resultAlert(): ?array
    {
        $latest = $this->latestPublishedExam();
        if ($latest === null) {
            return null;
        }
        $families = count($this->enrolledStudentIds());

        return [
            'id' => 'alert_result_published',
            'kind' => 'result_published',
            'severity' => 'info',
            'title' => sprintf('%s term %s results are published', (string)$latest->term, (string)$latest->session),
            'detail' => sprintf('Report cards for the %s are live in the portal. Tell families where to find them.', strtolower((string)$latest->title)),
            'count' => $families,
            'audienceLabel' => 'All parents',
        ];
    }

    /** @return array{count:int,outstanding:int,studentIds:array<int,string>} */
    private function overdueSummary(): array
    {
        // Only completed payments count here (the mock does NOT net refunds).
        $paid = [];
        foreach ($this->tenant()->query('EmsPayments')->where(['state' => 'completed'])->all() as $payment) {
            $paid[(string)$payment->invoice_id] = ($paid[(string)$payment->invoice_id] ?? 0) + (int)$payment->amount;
        }
        $count = 0;
        $outstanding = 0;
        $studentIds = [];
        foreach ($this->invoices() as $invoice) {
            $net = (int)($paid[(string)$invoice->id] ?? 0);
            $balance = (int)$invoice->total - $net;
            if ((string)$invoice->status !== 'cancelled'
                && Serializer\Wire::date($invoice->due_date) < $this->today
                && $balance > 0) {
                $count++;
                $outstanding += $balance;
                $studentIds[(string)$invoice->student_id] = true;
            }
        }

        return ['count' => $count, 'outstanding' => $outstanding, 'studentIds' => array_keys($studentIds)];
    }

    /** @return array<int,string> */
    private function frequentAbsenceStudentIds(): array
    {
        $since = $this->isoAddDays($this->today, -self::ABSENCE_WINDOW_DAYS);
        $absences = [];
        foreach ($this->attendance() as $record) {
            if ((string)$record->status === 'absent' && Serializer\Wire::date($record->date) >= $since) {
                $absences[(string)$record->student_id] = ($absences[(string)$record->student_id] ?? 0) + 1;
            }
        }

        return array_keys(array_filter($absences, fn ($count) => $count >= self::ABSENCE_THRESHOLD));
    }

    /** @return array<int,string> */
    private function enrolledStudentIds(): array
    {
        $ids = [];
        foreach ($this->students() as $student) {
            if ((string)$student->status === 'enrolled') {
                $ids[] = (string)$student->id;
            }
        }

        return $ids;
    }

    private function latestPublishedExam(): ?EntityInterface
    {
        $exams = $this->tenant()->query('EmsExams')
            ->where(['status' => 'published'])->all()->toList();
        usort($exams, fn ($a, $b) => strcmp((string)$b->end_date, (string)$a->end_date));

        return $exams[0] ?? null;
    }

    // --- data + small helpers ------------------------------------------------

    private function base36(int $n): string
    {
        if ($n === 0) {
            return '0';
        }
        $digits = '0123456789abcdefghijklmnopqrstuvwxyz';
        $s = '';
        while ($n > 0) {
            $s = $digits[$n % 36] . $s;
            $n = intdiv($n, 36);
        }

        return $s;
    }

    private function isoAddDays(string $iso, int $days): string
    {
        return (new \DateTimeImmutable($iso . ' 00:00:00', new \DateTimeZone('UTC')))
            ->modify(($days >= 0 ? '+' : '') . $days . ' days')
            ->format('Y-m-d');
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function students(): array
    {
        return $this->tenant()->query('EmsStudents')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function guardians(): array
    {
        return $this->tenant()->query('EmsGuardians')
            ->orderByAsc('created')->orderByAsc('id')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function teachers(): array
    {
        return $this->tenant()->query('EmsTeachers')
            ->orderByAsc('created')->orderByAsc('id')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function invoices(): array
    {
        return $this->tenant()->query('EmsInvoices')->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function attendance(): array
    {
        return $this->tenant()->query('EmsAttendanceRecords')->all()->toList();
    }
}
