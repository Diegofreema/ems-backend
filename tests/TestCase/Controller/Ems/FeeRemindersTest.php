<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Core\Configure;
use Cake\Http\TestSuite\HttpClientTrait;
use Cake\Utility\Text;

/**
 * Fee reminders (document.md §3.7). A bursar sends overdue or due-soon nudges to
 * owing families — an in-app portal row and an e-mail to the primary guardian,
 * cloning the AbsenceAlerts two-phase pattern. A 7-day per-instalment cooldown
 * stops a repeat run re-nagging the same family; a family with no e-mail on file
 * is recorded suppressed, never lost. The Resend HTTP call is mocked — a test
 * must never reach the real provider.
 */
final class FeeRemindersTest extends EmsIntegrationTestCase
{
    use HttpClientTrait;

    protected const CLEANUP_TABLES = [
        'ems_portal_notifications',
        'ems_message_recipients',
        'ems_notifications',
        'ems_guardians',
        'ems_students',
        'ems_users',
        'ems_schools',
    ];

    private string $bursarId;

    protected function setUp(): void
    {
        parent::setUp();
        Configure::write('Ems.resendApiKey', 'test-resend-key');
        Configure::write('Ems.emailFrom', 'EMS <noreply@test.school>');
        Configure::write('Ems.frontendBaseUrl', 'http://localhost:5173');
        $this->mockClientPost(
            'https://api.resend.com/emails',
            $this->newClientResponse(200, ['Content-Type: application/json'], '{"id":"message-1"}'),
        );
        $this->bursarId = Text::uuid();
    }

    public function testOverdueReminderNotifiesAndThenRespectsTheCooldown(): void
    {
        $studentId = $this->seedStudent('Ola', 'Owoye');
        $guardianId = $this->seedGuardian($studentId, 'grace@family.test');
        $parentUserId = $this->seedParentUser([$studentId]);
        $invoiceId = $this->seedInvoice($studentId, '2020-01-01', 100000);

        // Preview first — no write.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'rem-preview');
        $this->post($this->schoolPath('/fee-reminders/preview'), ['kind' => 'overdue']);
        $this->assertResponseOk();
        $this->assertSame(1, $this->responseJson()['remindableCount']);
        $this->assertSame(0, $this->rowCount('ems_fee_reminders', ['invoice_id' => $invoiceId]));

        // Send: marker, portal row, mocked e-mail, send-log header.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'rem-send-1');
        $this->post($this->schoolPath('/fee-reminders'), ['kind' => 'overdue']);
        $this->assertResponseOk();
        $this->assertSame(1, $this->responseJson()['remindedCount']);
        $this->assertSame(1, $this->responseJson()['emailed']);
        $this->assertSame(1, $this->rowCount('ems_fee_reminders', ['invoice_id' => $invoiceId, 'kind' => 'overdue']));
        $this->assertSame(1, $this->rowCount('ems_portal_notifications', ['user_id' => $parentUserId, 'kind' => 'fee_reminder']));
        $this->assertSame(1, $this->rowCount('ems_message_recipients', ['person_id' => $guardianId, 'status' => 'sent']));
        $this->assertSame(1, $this->rowCount('ems_notifications', ['kind' => 'fee_reminder']));

        // A second run inside the cooldown suppresses the same instalment.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'rem-send-2');
        $this->post($this->schoolPath('/fee-reminders'), ['kind' => 'overdue']);
        $this->assertResponseOk();
        $this->assertSame(0, $this->responseJson()['remindedCount']);
        $this->assertSame(1, $this->responseJson()['suppressedCount']);
        $this->assertSame(1, $this->rowCount('ems_fee_reminders', ['invoice_id' => $invoiceId]));
    }

    public function testDueSoonRemindsOnlyInstalmentsInsideTheLeadWindow(): void
    {
        $soon = $this->seedStudent('Uche', 'Udo');
        $this->seedGuardian($soon, 'uche@family.test');
        $this->seedParentUser([$soon]);
        $soonInvoice = $this->seedInvoice($soon, date('Y-m-d', strtotime('+3 days')), 50000);

        // A far-future instalment is not "due soon".
        $far = $this->seedStudent('Zara', 'Zik');
        $this->seedGuardian($far, 'zara@family.test');
        $this->seedInvoice($far, date('Y-m-d', strtotime('+40 days')), 50000);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'due-soon');
        $this->post($this->schoolPath('/fee-reminders'), ['kind' => 'due_soon']);
        $this->assertResponseOk();
        $this->assertSame(1, $this->responseJson()['remindedCount']);
        $this->assertSame(1, $this->rowCount('ems_fee_reminders', ['invoice_id' => $soonInvoice, 'kind' => 'due_soon']));
    }

    public function testAFamilyWithoutAnEmailIsSuppressedNotLost(): void
    {
        $studentId = $this->seedStudent('Nkem', 'Nnaji');
        $this->seedGuardian($studentId, '');
        $invoiceId = $this->seedInvoice($studentId, '2020-01-01', 100000);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'no-email');
        $this->post($this->schoolPath('/fee-reminders'), ['kind' => 'overdue']);
        $this->assertResponseOk();
        // The instalment is still marked reminded (bell delivered), but the
        // e-mail is recorded suppressed with a reason.
        $this->assertSame(1, $this->rowCount('ems_fee_reminders', ['invoice_id' => $invoiceId]));
        $this->assertSame(1, $this->rowCount('ems_message_recipients', ['status' => 'suppressed']));
        $this->assertSame(0, $this->responseJson()['emailed']);
    }

    public function testOnlyABursarSendsAndTheKindIsValidated(): void
    {
        // An administrator can preview but not send.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/fee-reminders'), ['kind' => 'overdue']);
        $this->assertResponseCode(403);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'bad-kind');
        $this->post($this->schoolPath('/fee-reminders'), ['kind' => 'nudge']);
        $this->assertResponseCode(422);
    }

    // --- helpers -------------------------------------------------------------

    private function seedStudent(string $first, string $last): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id, 'school_id' => $this->schoolId, 'admission_number' => 'ADM-' . substr($id, 0, 6),
            'first_name' => $first, 'last_name' => $last, 'date_of_birth' => '2014-05-10',
            'gender' => 'female', 'class_group' => 'JSS 1A', 'status' => 'enrolled', 'enrolled_on' => '2025-09-01',
        ]);

        return $id;
    }

    private function seedGuardian(string $studentId, string $email): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_guardians', [
            'id' => $id, 'school_id' => $this->schoolId, 'student_id' => $studentId,
            'first_name' => 'Guardian', 'last_name' => 'One', 'relationship' => 'mother',
            'phone' => '08040000001', 'email' => $email, 'is_primary' => 1,
        ]);

        return $id;
    }

    /** @param array<int,string> $studentIds */
    private function seedParentUser(array $studentIds): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $id, 'school_id' => $this->schoolId, 'name' => 'Parent One',
            'email' => 'p-' . substr($id, 0, 8) . '@test.school', 'role' => 'parent', 'status' => 'active',
            'link_kind' => 'parent', 'link_student_ids' => json_encode($studentIds), 'added_on' => $this->now(),
        ]);

        return $id;
    }

    private function seedInvoice(string $studentId, string $dueOn, int $total): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_invoices', [
            'id' => $id, 'school_id' => $this->schoolId, 'invoice_number' => 'TES/INV/' . substr($id, 0, 4),
            'student_id' => $studentId, 'student_name' => 'Owing Family', 'class_group' => 'JSS 1A',
            'session' => '2025/2026', 'term' => 'First', 'issued_on' => '2025-09-01', 'due_date' => $dueOn,
            'line_items' => json_encode([['name' => 'Tuition', 'amount' => $total, 'kind' => 'charge']]),
            'total' => $total, 'status' => 'issued',
            'instalments' => json_encode([['number' => 1, 'label' => 'First', 'dueOn' => $dueOn, 'amount' => $total]]),
        ]);

        return $id;
    }

    private function finance(string $role, string $id, string $name, string $key): void
    {
        $this->ensureUser($role, $id, $name);
        $this->configRequest(['headers' => [
            'Authorization' => 'Bearer ' . $this->token($role, $id, $name),
            'Accept' => 'application/json',
            'Idempotency-Key' => $key,
        ]]);
    }
}
