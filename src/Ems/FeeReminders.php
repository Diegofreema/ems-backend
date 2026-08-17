<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorInterface;
use Cake\Utility\Text;
use DateTimeImmutable;

/**
 * Fee reminders (document.md §3.7). The proper replacement for the blunt
 * fee_overdue broadcast: a bursar sends overdue or due-soon nudges to owing
 * families, per instalment, with a cooldown so nobody is re-nagged daily. Cloned
 * from App\Ems\AbsenceAlerts — in-app portal row AND e-mail to the primary
 * guardian, two-phase:
 *
 *  - claim() writes the once-per-window marker (ems_fee_reminders) and the
 *    guardians' portal inbox rows, inside a transaction.
 *  - deliver() runs after the commit and e-mails each guardian through the
 *    Comms delivery path (send log + masked per-recipient trail), so a slow
 *    provider never breaks the send.
 *
 * The reminder unit is one instalment of one invoice (number 0 for a lump-sum
 * invoice with no schedule). Overdue takes the OLDEST overdue instalment per
 * invoice; due-soon takes the next-due instalment falling within the lead
 * window. Balances and statuses come from App\Ems\Fees, the one money authority.
 */
class FeeReminders
{
    public const KIND = 'fee_reminder';
    public const OVERDUE = 'overdue';
    public const DUE_SOON = 'due_soon';

    /** Days a reminded instalment is suppressed from re-reminding. */
    public const COOLDOWN_DAYS = 7;

    /** How far ahead a "due soon" instalment may fall to qualify. */
    public const DUE_SOON_LEAD_DAYS = 7;

    private LocatorInterface $locator;
    private string $schoolId;
    private string $today;
    private Fees $fees;
    private ?Tenant $tenantScope = null;

    public function __construct(LocatorInterface $locator, string $schoolId, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->today = $today;
        $this->fees = new Fees($locator, $schoolId, $today);
    }

    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    public static function isKind(string $kind): bool
    {
        return in_array($kind, [self::OVERDUE, self::DUE_SOON], true);
    }

    /**
     * The dry run: who would be reminded now, and how many are held back by the
     * cooldown. No write.
     *
     * @return array<string, mixed>
     */
    public function preview(string $kind): array
    {
        $all = $this->collectUnits($kind);
        $suppressed = $this->suppressedKeys($kind);
        $targets = array_values(array_filter(
            $all,
            static fn($u) => !isset($suppressed[$u['invoiceId'] . '|' . $u['instalmentNumber']]),
        ));
        $students = [];
        foreach ($targets as $t) {
            $students[$t['studentId']] = true;
        }

        return [
            'kind' => $kind,
            'targets' => $targets,
            'remindableCount' => count($targets),
            'suppressedCount' => count($all) - count($targets),
            'recipientCount' => count($students),
        ];
    }

    /**
     * Send the reminders: claim the markers and portal rows in a transaction,
     * then e-mail the guardians. Returns a summary for the caller.
     *
     * @return array<string, mixed>
     */
    public function send(string $kind, Comms $comms, string $frontendBaseUrl, string $sentBy): array
    {
        $all = $this->collectUnits($kind);
        $suppressed = $this->suppressedKeys($kind);
        $targets = array_values(array_filter(
            $all,
            static fn($u) => !isset($suppressed[$u['invoiceId'] . '|' . $u['instalmentNumber']]),
        ));
        $suppressedCount = count($all) - count($targets);
        if ($targets === []) {
            return ['kind' => $kind, 'remindedCount' => 0, 'suppressedCount' => $suppressedCount, 'emailed' => 0, 'recipientCount' => 0];
        }

        $connection = $this->locator->get('EmsFeeReminders')->getConnection();
        $connection->transactional(function () use ($targets, $kind, $sentBy): void {
            $this->claim($targets, $kind, $sentBy);
        });
        $emailed = $this->deliver($targets, $kind, $comms, $frontendBaseUrl, $sentBy);
        $students = [];
        foreach ($targets as $t) {
            $students[$t['studentId']] = true;
        }

        return [
            'kind' => $kind,
            'remindedCount' => count($targets),
            'suppressedCount' => $suppressedCount,
            'emailed' => $emailed,
            'recipientCount' => count($students),
        ];
    }

    // --- phases --------------------------------------------------------------

    /**
     * Write the cooldown marker per unit and a portal inbox row per parent user.
     * Call inside a transaction.
     *
     * @param array<int, array<string, mixed>> $units
     */
    private function claim(array $units, string $kind, string $sentBy): void
    {
        $markers = $this->locator->get('EmsFeeReminders');
        $inbox = $this->locator->get('EmsPortalNotifications');
        $parents = $this->parentUsersByStudent();
        foreach ($units as $u) {
            $markers->saveOrFail($markers->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->schoolId,
                'invoice_id' => $u['invoiceId'],
                'student_id' => $u['studentId'],
                'instalment_number' => (int)$u['instalmentNumber'],
                'kind' => $kind,
                'reminded_on' => $this->today,
                'sent_by' => $sentBy,
                'created' => FrozenTime::now('UTC'),
            ], ['validate' => false]));

            foreach ($parents[$u['studentId']] ?? [] as $userId) {
                $inbox->saveOrFail($inbox->newEntity([
                    'school_id' => $this->schoolId,
                    'user_id' => $userId,
                    'kind' => self::KIND,
                    'title' => $this->title($u, $kind),
                    'body' => $this->portalBody($u, $kind),
                    'student_id' => $u['studentId'],
                    'date' => $u['dueOn'],
                    'read_at' => null,
                ], ['validate' => false]));
            }
        }
    }

    /**
     * E-mail each unit's primary guardian and record the send log. Call after
     * the claim transaction commits. Returns how many e-mails were sent.
     *
     * @param array<int, array<string, mixed>> $units
     */
    private function deliver(array $units, string $kind, Comms $comms, string $frontendBaseUrl, string $sentBy): int
    {
        $studentIds = array_values(array_unique(array_map(static fn($u) => (string)$u['studentId'], $units)));
        $guardianByStudent = [];
        foreach (
            $this->tenant()->query('EmsGuardians')
                ->where(['student_id IN' => $studentIds, 'is_primary' => true])
                ->all() as $guardian
        ) {
            $guardianByStudent[(string)$guardian->student_id] ??= $guardian;
        }

        $notifications = $this->locator->get('EmsNotifications');
        $recipients = $this->locator->get('EmsMessageRecipients');
        $notification = $notifications->newEntity([
            'school_id' => $this->schoolId,
            'channel' => 'email',
            'kind' => self::KIND,
            'subject' => $kind === self::OVERDUE ? 'Overdue fee reminder' : 'Upcoming fee reminder',
            'body' => $kind === self::OVERDUE
                ? 'A fee instalment is overdue.'
                : 'A fee instalment is due soon.',
            'audience_label' => sprintf('Owing families · %s · %s', $kind === self::OVERDUE ? 'overdue' : 'due soon', $this->today),
            'recipient_count' => 0,
            'sent_on' => $this->today,
            'sent_by' => $sentBy,
        ], ['validate' => false]);
        $notifications->saveOrFail($notification);

        $sent = 0;
        foreach ($units as $u) {
            $guardian = $guardianByStudent[$u['studentId']] ?? null;
            $address = $guardian === null ? '' : trim((string)$guardian->email);
            $recipientId = Text::uuid();
            $row = [
                'school_id' => $this->schoolId,
                'announcement_id' => null,
                'notification_id' => (string)$notification->id,
                'person_id' => $guardian === null ? '' : (string)$guardian->id,
                'person_name' => $guardian === null ? '' : trim((string)$guardian->first_name . ' ' . (string)$guardian->last_name),
                'person_kind' => 'guardian',
                'about_student_name' => (string)$u['studentName'],
                'channel' => 'email',
                'address' => $address === '' ? '' : $comms->maskAddress($address),
                'status' => 'suppressed',
                'attempts' => 0,
                'updated_on' => $this->today,
            ];
            if ($address === '') {
                $row['suppressed_reason'] = 'No email address on file';
            } else {
                $outcome = $comms->attemptDelivery(
                    $recipientId,
                    1,
                    'email',
                    $address,
                    $kind === self::OVERDUE ? 'Overdue fee reminder' : 'Upcoming fee reminder',
                    $this->emailBody($u, $kind, $frontendBaseUrl),
                    (string)$row['person_name'],
                    (string)$u['studentName'],
                );
                $row['status'] = $outcome['ok'] ? 'sent' : 'failed';
                $row['attempts'] = 1;
                $row['provider_ref'] = $outcome['ref'];
                $row['failure_reason'] = $outcome['reason'];
                $sent += $outcome['ok'] ? 1 : 0;
            }
            $recipient = $recipients->newEntity($row, ['validate' => false]);
            $recipient->id = $recipientId;
            $recipients->saveOrFail($recipient);
        }

        $notification->recipient_count = $sent;
        $notifications->saveOrFail($notification);

        return $sent;
    }

    // --- unit collection -----------------------------------------------------

    /**
     * Every owing reminder unit for the kind, before cooldown suppression. One
     * unit per invoice: the oldest overdue instalment, or the next-due
     * instalment inside the lead window.
     *
     * @return array<int, array<string, mixed>>
     */
    private function collectUnits(string $kind): array
    {
        $windowEnd = (new DateTimeImmutable($this->today))
            ->modify('+' . self::DUE_SOON_LEAD_DAYS . ' days')
            ->format('Y-m-d');
        $units = [];
        foreach (
            $this->tenant()->query('EmsInvoices')
                ->where(['status !=' => 'cancelled'])
                ->all() as $invoice
        ) {
            $wire = $this->fees->enrich($invoice);
            if ((int)$wire['balance'] <= 0) {
                continue;
            }
            $schedule = $wire['schedule'] ?? [];
            $unit = $kind === self::OVERDUE
                ? $this->overdueUnit($wire, $schedule)
                : $this->dueSoonUnit($wire, $schedule, $windowEnd);
            if ($unit !== null) {
                $units[] = $unit + [
                    'invoiceId' => (string)$wire['id'],
                    'studentId' => (string)$wire['studentId'],
                    'studentName' => (string)$wire['studentName'],
                    'invoiceNumber' => (string)$wire['invoiceNumber'],
                    'classGroup' => (string)($wire['classGroup'] ?? ''),
                ];
            }
        }

        return $units;
    }

    /**
     * @param array<string, mixed> $wire
     * @param array<int, array<string, mixed>> $schedule
     * @return array<string, mixed>|null
     */
    private function overdueUnit(array $wire, array $schedule): ?array
    {
        if (($wire['paymentStatus'] ?? '') !== 'overdue') {
            return null;
        }
        foreach ($schedule as $s) {
            if (($s['status'] ?? '') === 'overdue') {
                return ['instalmentNumber' => (int)$s['number'], 'dueOn' => (string)$s['dueOn'], 'amount' => (int)$s['balance']];
            }
        }
        // Lump-sum invoice (no schedule) past its due date.
        return ['instalmentNumber' => 0, 'dueOn' => (string)$wire['dueDate'], 'amount' => (int)$wire['balance']];
    }

    /**
     * @param array<string, mixed> $wire
     * @param array<int, array<string, mixed>> $schedule
     * @return array<string, mixed>|null
     */
    private function dueSoonUnit(array $wire, array $schedule, string $windowEnd): ?array
    {
        if ($schedule === []) {
            $dueOn = (string)$wire['dueDate'];
            if ($dueOn >= $this->today && $dueOn <= $windowEnd) {
                return ['instalmentNumber' => 0, 'dueOn' => $dueOn, 'amount' => (int)$wire['balance']];
            }

            return null;
        }
        $next = Money::nextDueInstalment($schedule);
        if ($next === null || ($next['status'] ?? '') === 'overdue') {
            return null;
        }
        $dueOn = (string)$next['dueOn'];
        if ($dueOn >= $this->today && $dueOn <= $windowEnd) {
            return ['instalmentNumber' => (int)$next['number'], 'dueOn' => $dueOn, 'amount' => (int)$next['balance']];
        }

        return null;
    }

    /**
     * The (invoice|instalment) keys reminded within the cooldown window for this
     * kind, so the next run skips them.
     *
     * @return array<string, bool>
     */
    private function suppressedKeys(string $kind): array
    {
        $cutoff = (new DateTimeImmutable($this->today))
            ->modify('-' . self::COOLDOWN_DAYS . ' days')
            ->format('Y-m-d');
        $keys = [];
        foreach (
            $this->tenant()->query('EmsFeeReminders')
                ->where(['kind' => $kind, 'reminded_on >=' => $cutoff])
                ->all() as $m
        ) {
            $keys[(string)$m->invoice_id . '|' . (int)$m->instalment_number] = true;
        }

        return $keys;
    }

    // --- copy & recipients ---------------------------------------------------

    /**
     * @param array<string, mixed> $u
     */
    private function title(array $u, string $kind): string
    {
        return $kind === self::OVERDUE
            ? sprintf('Overdue fees for %s', $u['studentName'])
            : sprintf('Fees due soon for %s', $u['studentName']);
    }

    /**
     * @param array<string, mixed> $u
     */
    private function portalBody(array $u, string $kind): string
    {
        return sprintf(
            $kind === self::OVERDUE
                ? '%s on invoice %s was due on %s and is outstanding. Open the fees page to settle it.'
                : '%s on invoice %s is due on %s. Open the fees page to pay ahead of time.',
            Money::formatCurrency((int)$u['amount']),
            $u['invoiceNumber'],
            $u['dueOn'],
        );
    }

    /**
     * @param array<string, mixed> $u
     */
    private function emailBody(array $u, string $kind, string $frontendBaseUrl): string
    {
        return sprintf(
            $kind === self::OVERDUE
                ? '%s has an outstanding fee of %s on invoice %s, which was due on %s. '
                    . 'Please sign in to the school portal to settle it or view the statement: %s'
                : '%s has a fee instalment of %s on invoice %s due on %s. '
                    . 'Please sign in to the school portal to pay or view the statement: %s',
            $u['studentName'],
            Money::formatCurrency((int)$u['amount']),
            $u['invoiceNumber'],
            $u['dueOn'],
            $frontendBaseUrl,
        );
    }

    /**
     * Which portal accounts hear about which student: active parent users mapped
     * from their linked ward ids (mirrors AbsenceAlerts).
     *
     * @return array<string, array<int, string>>
     */
    private function parentUsersByStudent(): array
    {
        $map = [];
        foreach (
            $this->tenant()->query('EmsUsers')
                ->where(['role' => 'parent', 'status' => 'active'])
                ->all() as $user
        ) {
            $ids = $user->link_student_ids;
            if (is_string($ids)) {
                $ids = json_decode($ids, true);
            }
            if (!is_array($ids)) {
                continue;
            }
            foreach ($ids as $studentId) {
                $map[(string)$studentId][] = (string)$user->id;
            }
        }

        return $map;
    }
}
