<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\ORM\Locator\LocatorInterface;
use Cake\Utility\Text;

/**
 * Automatic guardian absence alerts (document.md §3.12). When a register save
 * makes a student NEWLY absent for a date, the family hears about it the same
 * moment: an e-mail to the primary guardian and an in-app row in the portal
 * inbox. Two-phase by design:
 *
 *  - claim() runs INSIDE the register transaction. It takes the once-only
 *    marker per (student, date) and writes the portal inbox rows, so the
 *    marker and the register commit or roll back together, and a correction
 *    that re-flips a student to absent on the same date stays silent.
 *  - deliver() runs AFTER the commit. E-mail rides the Comms delivery path
 *    (send log + per-recipient trail, masked addresses, failures recorded as
 *    retryable rows) so a slow or down provider can never break the register.
 *
 * The e-mail deliberately carries the fact, not the teacher's register note —
 * notes are written for the office and can hold sensitive detail; families
 * read them in the portal.
 */
class AbsenceAlerts
{
    public const KIND = 'absence_alert';

    private LocatorInterface $locator;
    private string $schoolId;
    private ?Tenant $tenantScope = null;

    public function __construct(LocatorInterface $locator, string $schoolId)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
    }

    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    /**
     * Claim the once-only alert marker for these newly-absent students and
     * write their guardians' portal inbox rows. Call inside the register
     * transaction. Returns the claimed students (already-alerted ones drop
     * out), keyed by id, for the post-commit deliver() phase.
     *
     * @param array<int,string> $studentIds
     * @return array<string,\Cake\Datasource\EntityInterface>
     */
    public function claim(array $studentIds, string $date): array
    {
        if ($studentIds === []) {
            return [];
        }

        $already = [];
        foreach (
            $this->tenant()->query('EmsAbsenceAlerts')
                ->where(['student_id IN' => $studentIds, 'date' => $date])
                ->all() as $row
        ) {
            $already[(string)$row->student_id] = true;
        }
        $fresh = array_values(array_filter($studentIds, fn($id) => !isset($already[$id])));
        if ($fresh === []) {
            return [];
        }

        $students = [];
        foreach (
            $this->tenant()->query('EmsStudents')
                ->where(['id IN' => $fresh])
                ->all() as $student
        ) {
            $students[(string)$student->id] = $student;
        }

        $markers = $this->locator->get('EmsAbsenceAlerts');
        $inbox = $this->locator->get('EmsPortalNotifications');
        $parents = $this->parentUsersByStudent();
        foreach ($students as $studentId => $student) {
            $markers->saveOrFail($markers->newEntity([
                'school_id' => $this->schoolId,
                'student_id' => $studentId,
                'date' => $date,
            ], ['validate' => false]));

            $name = trim((string)$student->first_name . ' ' . (string)$student->last_name);
            foreach ($parents[$studentId] ?? [] as $userId) {
                $inbox->saveOrFail($inbox->newEntity([
                    'school_id' => $this->schoolId,
                    'user_id' => $userId,
                    'kind' => self::KIND,
                    'title' => sprintf('%s was marked absent', $name),
                    'body' => sprintf(
                        '%s was marked absent on the %s register. See the attendance page for details.',
                        $name,
                        $date,
                    ),
                    'student_id' => $studentId,
                    'date' => $date,
                    'read_at' => null,
                ], ['validate' => false]));
            }
        }

        return $students;
    }

    /**
     * E-mail each claimed student's primary guardian and record the send in the
     * staff send log. Call after the register transaction has committed.
     *
     * @param array<string,\Cake\Datasource\EntityInterface> $students claimed by claim()
     */
    public function deliver(
        array $students,
        string $date,
        string $className,
        Comms $comms,
        string $frontendBaseUrl,
        string $sentBy,
        string $today,
    ): void {
        if ($students === []) {
            return;
        }

        $guardianByStudent = [];
        foreach (
            $this->tenant()->query('EmsGuardians')
                ->where(['student_id IN' => array_keys($students), 'is_primary' => true])
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
            'subject' => 'Absence notice',
            'body' => 'Your ward was marked absent on today’s class register.',
            'audience_label' => sprintf('Guardians of absent students · %s · %s', $className, $date),
            'recipient_count' => 0,
            'sent_on' => $today,
            'sent_by' => $sentBy,
        ], ['validate' => false]);
        $notifications->saveOrFail($notification);

        $sent = 0;
        foreach ($students as $studentId => $student) {
            $studentName = trim((string)$student->first_name . ' ' . (string)$student->last_name);
            $guardian = $guardianByStudent[$studentId] ?? null;
            $address = $guardian === null ? '' : trim((string)$guardian->email);
            $recipientId = Text::uuid();
            $row = [
                'school_id' => $this->schoolId,
                'announcement_id' => null,
                'notification_id' => (string)$notification->id,
                'person_id' => $guardian === null ? '' : (string)$guardian->id,
                'person_name' => $guardian === null
                    ? (string)$student->guardian_name
                    : trim((string)$guardian->first_name . ' ' . (string)$guardian->last_name),
                'person_kind' => 'guardian',
                'about_student_name' => $studentName,
                'channel' => 'email',
                'address' => $address === '' ? '' : $comms->maskAddress($address),
                'status' => 'suppressed',
                'attempts' => 0,
                'updated_on' => $today,
            ];
            if ($address === '') {
                $row['suppressed_reason'] = 'No email address on file';
            } else {
                $outcome = $comms->attemptDelivery(
                    $recipientId,
                    1,
                    'email',
                    $address,
                    'Absence notice',
                    sprintf(
                        '%s was marked absent from school on %s (%s). If this is unexpected, '
                        . 'please contact the school office. Sign in to the portal for the full '
                        . 'attendance record: %s',
                        $studentName,
                        $date,
                        $className,
                        $frontendBaseUrl,
                    ),
                    (string)$row['person_name'],
                    $studentName,
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
    }

    /**
     * Which portal accounts hear about which student: active parent users,
     * mapped from their linked ward ids.
     *
     * @return array<string,array<int,string>> studentId => [userId, ...]
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
