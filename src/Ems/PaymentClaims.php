<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\ORM\Locator\LocatorInterface;
use Cake\Utility\Text;

/**
 * Family payment declarations (document.md §3.19). A linked guardian declares an
 * offline payment against a ward's invoice through the portal; it enters the
 * shared ems_payment_submissions queue (provenance 'parent') and an administrator
 * verifies it exactly as a bursar-entered claim.
 *
 * This service owns the one piece unique to the family origin: telling the
 * declaring guardian what the reviewer decided. Cloned from App\Ems\FeeReminders
 * — an in-app portal row AND an e-mail to the guardian — but a single recipient,
 * so it runs straight through after the decision transaction commits (a slow
 * mail provider never blocks the decision itself).
 */
class PaymentClaims
{
    public const KIND = 'payment_claim';

    private LocatorInterface $locator;
    private string $schoolId;
    private string $today;
    private ?Tenant $tenantScope = null;

    public function __construct(LocatorInterface $locator, string $schoolId, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->today = $today;
    }

    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    /**
     * Notify the declaring guardian of an approve/reject. Writes the portal bell
     * row and e-mails the guardian's account address. Returns 1 if e-mailed.
     *
     * @param array<string, mixed> $notice userId, studentId, invoiceId, amount, decision, reason
     */
    public function notifyDecision(array $notice, Comms $comms, string $frontendBaseUrl): int
    {
        $userId = (string)($notice['userId'] ?? '');
        $decision = (string)($notice['decision'] ?? '');
        if ($userId === '' || !in_array($decision, ['approved', 'rejected'], true)) {
            return 0;
        }
        $amount = (int)($notice['amount'] ?? 0);
        $reason = trim((string)($notice['reason'] ?? ''));
        $studentId = (string)($notice['studentId'] ?? '');

        $invoice = $this->tenant()->query('EmsInvoices')->where(['id' => (string)($notice['invoiceId'] ?? '')])->first();
        $invoiceNumber = $invoice === null ? '' : (string)$invoice->invoice_number;
        $student = $this->tenant()->query('EmsStudents')->where(['id' => $studentId])->first();
        $studentName = $student === null ? 'your ward' : trim((string)$student->first_name . ' ' . (string)$student->last_name);
        $user = $this->tenant()->query('EmsUsers')->where(['id' => $userId])->first();
        $address = $user === null ? '' : trim((string)$user->email);

        $this->writeBell($userId, $studentId, $decision, $amount, $invoiceNumber, $reason);

        return $this->email($comms, $userId, $address, $decision, $amount, $invoiceNumber, $studentName, $reason, $frontendBaseUrl);
    }

    private function writeBell(string $userId, string $studentId, string $decision, int $amount, string $invoiceNumber, string $reason): void
    {
        $inbox = $this->locator->get('EmsPortalNotifications');
        $inbox->saveOrFail($inbox->newEntity([
            'school_id' => $this->schoolId,
            'user_id' => $userId,
            'kind' => self::KIND,
            'title' => $decision === 'approved'
                ? 'Payment confirmed'
                : 'Payment declaration needs another look',
            'body' => $this->portalBody($decision, $amount, $invoiceNumber, $reason),
            'student_id' => $studentId,
            'date' => $this->today,
            'read_at' => null,
        ], ['validate' => false]));
    }

    private function email(
        Comms $comms,
        string $personId,
        string $address,
        string $decision,
        int $amount,
        string $invoiceNumber,
        string $studentName,
        string $reason,
        string $frontendBaseUrl,
    ): int {
        $notifications = $this->locator->get('EmsNotifications');
        $recipients = $this->locator->get('EmsMessageRecipients');
        $subject = $decision === 'approved'
            ? 'Your fee payment has been confirmed'
            : 'Your fee payment declaration was not approved';
        $notification = $notifications->newEntity([
            'school_id' => $this->schoolId,
            'channel' => 'email',
            'kind' => self::KIND,
            'subject' => $subject,
            'body' => $this->portalBody($decision, $amount, $invoiceNumber, $reason),
            'audience_label' => sprintf('Declaring guardian · %s · %s', $decision, $this->today),
            'recipient_count' => 0,
            'sent_on' => $this->today,
            'sent_by' => 'System',
        ], ['validate' => false]);
        $notifications->saveOrFail($notification);

        $recipientId = Text::uuid();
        $row = [
            'school_id' => $this->schoolId,
            'announcement_id' => null,
            'notification_id' => (string)$notification->id,
            'person_id' => $personId,
            'person_name' => $studentName,
            'person_kind' => 'guardian',
            'about_student_name' => $studentName,
            'channel' => 'email',
            'address' => $address === '' ? '' : $comms->maskAddress($address),
            'status' => 'suppressed',
            'attempts' => 0,
            'updated_on' => $this->today,
        ];
        $sent = 0;
        if ($address === '') {
            $row['suppressed_reason'] = 'No email address on file';
        } else {
            $outcome = $comms->attemptDelivery(
                $recipientId,
                1,
                'email',
                $address,
                $subject,
                $this->emailBody($decision, $amount, $invoiceNumber, $studentName, $reason, $frontendBaseUrl),
                $studentName,
                $studentName,
            );
            $row['status'] = $outcome['ok'] ? 'sent' : 'failed';
            $row['attempts'] = 1;
            $row['provider_ref'] = $outcome['ref'];
            $row['failure_reason'] = $outcome['reason'];
            $sent = $outcome['ok'] ? 1 : 0;
        }
        $recipient = $recipients->newEntity($row, ['validate' => false]);
        $recipient->id = $recipientId;
        $recipients->saveOrFail($recipient);

        $notification->recipient_count = $sent;
        $notifications->saveOrFail($notification);

        return $sent;
    }

    private function portalBody(string $decision, int $amount, string $invoiceNumber, string $reason): string
    {
        if ($decision === 'approved') {
            return sprintf(
                'Your declared payment of %s on invoice %s has been verified and receipted. Thank you.',
                Money::formatCurrency($amount),
                $invoiceNumber,
            );
        }

        return sprintf(
            'Your declared payment of %s on invoice %s was not approved: %s You can submit a corrected declaration.',
            Money::formatCurrency($amount),
            $invoiceNumber,
            $reason,
        );
    }

    private function emailBody(
        string $decision,
        int $amount,
        string $invoiceNumber,
        string $studentName,
        string $reason,
        string $frontendBaseUrl,
    ): string {
        if ($decision === 'approved') {
            return sprintf(
                'The declared payment of %s for %s on invoice %s has been verified and a receipt issued. '
                    . 'Sign in to the school portal to view it: %s',
                Money::formatCurrency($amount),
                $studentName,
                $invoiceNumber,
                $frontendBaseUrl,
            );
        }

        return sprintf(
            'The declared payment of %s for %s on invoice %s was not approved. Reason: %s '
                . 'You can submit a corrected declaration from the school portal: %s',
            Money::formatCurrency($amount),
            $studentName,
            $invoiceNumber,
            $reason,
            $frontendBaseUrl,
        );
    }
}
