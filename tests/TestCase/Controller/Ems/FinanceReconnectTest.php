<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

/**
 * The reconnected finance flows (document.md §3.7): award grant/end through
 * the two-person request/decision pattern, invoice change requests replacing
 * direct cancel/reschedule, and the batch read endpoints that feed the desk's
 * pickers. Companion to OfflinePaymentsTest, which owns claims/adjustments.
 */
final class FinanceReconnectTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_finance_idempotency', 'ems_finance_integrity_locks', 'ems_audit_events',
        'ems_receipts', 'ems_finance_ledger_events', 'ems_finance_decisions',
        'ems_payment_submissions', 'ems_cash_batches', 'ems_bank_statement_rows',
        'ems_bank_statement_batches', 'ems_invoice_events', 'ems_invoice_change_requests',
        'ems_fee_award_end_requests', 'ems_fee_awards', 'ems_fee_plan_versions',
        'ems_payments', 'ems_invoices', 'ems_sequences', 'ems_students', 'ems_users', 'ems_schools',
    ];

    private string $studentId;
    private string $bursarId;
    private string $secondAdminId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $this->studentId, 'school_id' => $this->schoolId, 'admission_number' => 'EMS/001',
            'first_name' => 'Ayo', 'last_name' => 'Student', 'date_of_birth' => '2015-01-01',
            'gender' => 'male', 'class_group' => 'JSS 1A', 'status' => 'enrolled', 'enrolled_on' => date('Y-m-d'),
        ]);
        $this->bursarId = Text::uuid();
        $this->secondAdminId = Text::uuid();
    }

    // --- awards -------------------------------------------------------------

    public function testDraftedAwardPricesNothingUntilApproved(): void
    {
        $planId = $this->approvedPlan();

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'award-1');
        $this->post($this->schoolPath('/fee-awards'), [
            'name' => 'Founders Scholarship', 'kind' => 'scholarship', 'basis' => 'percentage',
            'value' => 50, 'scope' => 'student', 'studentId' => $this->studentId,
            'session' => '2025/2026', 'term' => 'First',
        ]);
        $this->assertResponseCode(201);
        $award = $this->responseJson();
        $this->assertSame('pending', $award['status']);

        // Pending: the preview prices the full bill and the family ledger
        // does not mention the draft.
        $this->authAsAdmin();
        $this->post($this->schoolPath('/invoices/preview'), ['studentId' => $this->studentId, 'feePlanVersionId' => $planId]);
        $this->assertResponseOk();
        $this->assertSame(100000, $this->responseJson()['total']);
        $this->assertSame([], $this->responseJson()['applied']);
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $this->studentId . '/ledger'));
        $this->assertSame([], $this->responseJson()['awards']);

        // A different administrator approves; now it prices and is visible.
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'award-approve-1');
        $this->post($this->schoolPath('/fee-awards/' . $award['id'] . '/decision'), ['decision' => 'approved', 'reason' => 'Board approved.']);
        $this->assertResponseOk();
        $this->assertSame('active', $this->responseJson()['status']);
        $this->authAsAdmin();
        $this->post($this->schoolPath('/invoices/preview'), ['studentId' => $this->studentId, 'feePlanVersionId' => $planId]);
        $this->assertSame(50000, $this->responseJson()['total']);
        $this->assertCount(1, $this->responseJson()['applied']);
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $this->studentId . '/ledger'));
        $this->assertCount(1, $this->responseJson()['awards']);
    }

    public function testAwardEndingNeedsAnIndependentDecision(): void
    {
        $awardId = Text::uuid();
        $this->insertRow('ems_fee_awards', [
            'id' => $awardId, 'school_id' => $this->schoolId, 'name' => 'Sibling Discount',
            'kind' => 'discount', 'basis' => 'percentage', 'value' => 10, 'applies_to_item' => 'all',
            'scope' => 'level', 'level' => 'JSS 1', 'session' => '2025/2026', 'term' => 'all',
            'status' => 'active', 'awarded_by' => 'Ada Admin', 'awarded_on' => '2025-08-01',
        ]);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'end-1');
        $this->post($this->schoolPath('/fee-awards/' . $awardId . '/end'), ['reason' => 'Policy withdrawn.']);
        $this->assertResponseCode(201);
        $requestId = $this->responseJson()['id'];
        $this->assertSame('pending', $this->responseJson()['status']);
        $this->assertSame(
            'active',
            $this->db->execute('SELECT status FROM ems_fee_awards WHERE id=?', [$awardId])->fetch('assoc')['status'],
            'the award stays active until an administrator agrees',
        );

        // Only one open end request at a time.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'end-2');
        $this->post($this->schoolPath('/fee-awards/' . $awardId . '/end'), ['reason' => 'Again.']);
        $this->assertResponseCode(409);

        // A rejection re-opens the road; an approval ends the award with the
        // requester's reason.
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'end-reject');
        $this->post($this->schoolPath('/fee-award-end-requests/' . $requestId . '/decision'), ['decision' => 'rejected', 'reason' => 'Keep it this session.']);
        $this->assertResponseOk();
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'end-3');
        $this->post($this->schoolPath('/fee-awards/' . $awardId . '/end'), ['reason' => 'Withdrawn for next term.']);
        $this->assertResponseCode(201);
        $second = $this->responseJson()['id'];
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'end-approve');
        $this->post($this->schoolPath('/fee-award-end-requests/' . $second . '/decision'), ['decision' => 'approved', 'reason' => 'Agreed.']);
        $this->assertResponseOk();
        $row = $this->db->execute('SELECT status, ended_reason FROM ems_fee_awards WHERE id=?', [$awardId])->fetch('assoc');
        $this->assertSame('ended', $row['status']);
        $this->assertSame('Withdrawn for next term.', $row['ended_reason']);
    }

    // --- invoice change requests --------------------------------------------

    public function testCancelRequestNeedsACleanInvoiceAndAnIndependentDecision(): void
    {
        $invoiceId = $this->seedInvoiceRow();

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'cancel-1');
        $this->post($this->schoolPath('/invoices/' . $invoiceId . '/change-requests'), ['kind' => 'cancel', 'reason' => 'Issued to the wrong student.']);
        $this->assertResponseCode(201);
        $requestId = $this->responseJson()['id'];
        $this->assertSame('pending', $this->responseJson()['status']);

        // One open request per invoice.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'cancel-2');
        $this->post($this->schoolPath('/invoices/' . $invoiceId . '/change-requests'), ['kind' => 'cancel', 'reason' => 'Again.']);
        $this->assertResponseCode(409);

        // The requester cannot decide (bursar lacks the decision capability).
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'cancel-own');
        $this->post($this->schoolPath('/invoice-change-requests/' . $requestId . '/decision'), ['decision' => 'approved', 'reason' => 'Me.']);
        $this->assertResponseCode(403);

        $this->finance('administrator', $this->adminId, 'Ada Admin', 'cancel-approve');
        $this->post($this->schoolPath('/invoice-change-requests/' . $requestId . '/decision'), ['decision' => 'approved', 'reason' => 'Confirmed with the office.']);
        $this->assertResponseOk();
        $row = $this->db->execute('SELECT status, cancellation_reason FROM ems_invoices WHERE id=?', [$invoiceId])->fetch('assoc');
        $this->assertSame('cancelled', $row['status']);
        $this->assertSame('Issued to the wrong student.', $row['cancellation_reason']);
        $this->assertSame(1, $this->rowCount('ems_invoice_events', ['invoice_id' => $invoiceId, 'kind' => 'cancelled']));

        // The queue lists it decided.
        $this->authAsAdmin();
        $this->get($this->schoolPath('/invoice-change-requests'));
        $this->assertResponseOk();
        $this->assertSame('approved', $this->responseJson()['items'][0]['status']);
    }

    public function testCancelRequestIsRefusedWhileMoneyIsPosted(): void
    {
        $invoiceId = $this->seedInvoiceRow();
        $paymentId = Text::uuid();
        $this->insertRow('ems_payments', [
            'id' => $paymentId, 'school_id' => $this->schoolId, 'invoice_id' => $invoiceId,
            'student_id' => $this->studentId, 'receipt_number' => 'TES/RCP/00001', 'amount' => 40000,
            'method' => 'cash', 'paid_on' => date('Y-m-d'), 'state' => 'completed',
        ]);
        $this->db->insert('ems_finance_ledger_events', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'invoice_id' => $invoiceId,
            'student_id' => $this->studentId, 'payment_id' => $paymentId, 'event_type' => 'payment',
            'amount' => 40000, 'provenance' => 'test', 'key_id' => 'test-key-1',
            'previous_hash' => str_repeat('0', 64), 'event_hash' => hash('sha256', Text::uuid()),
            'occurred_at' => $this->now(),
        ]);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'cancel-paid');
        $this->post($this->schoolPath('/invoices/' . $invoiceId . '/change-requests'), ['kind' => 'cancel', 'reason' => 'Mistake.']);
        $this->assertResponseCode(422);
        $this->assertStringContainsString('payments recorded', $this->responseJson()['message']);
    }

    public function testRescheduleRequestValidatesAndAppliesOnApproval(): void
    {
        $invoiceId = $this->seedInvoiceRow();

        // A schedule that does not add up is refused at request time.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'resched-bad');
        $this->post($this->schoolPath('/invoices/' . $invoiceId . '/change-requests'), [
            'kind' => 'reschedule', 'reason' => 'Family hardship.', 'agreedWith' => 'Pat Parent',
            'instalments' => [['label' => 'First', 'dueOn' => '2026-09-01', 'amount' => 10000]],
        ]);
        $this->assertResponseCode(422);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'resched-1');
        $this->post($this->schoolPath('/invoices/' . $invoiceId . '/change-requests'), [
            'kind' => 'reschedule', 'reason' => 'Family hardship.', 'agreedWith' => 'Pat Parent',
            'instalments' => [
                ['label' => 'First', 'dueOn' => '2026-09-01', 'amount' => 60000],
                ['label' => 'Second', 'dueOn' => '2026-11-01', 'amount' => 40000],
            ],
        ]);
        $this->assertResponseCode(201);
        $requestId = $this->responseJson()['id'];

        $this->finance('administrator', $this->adminId, 'Ada Admin', 'resched-approve');
        $this->post($this->schoolPath('/invoice-change-requests/' . $requestId . '/decision'), ['decision' => 'approved', 'reason' => 'Agreed with the family.']);
        $this->assertResponseOk();
        $row = $this->db->execute('SELECT due_date, instalments, schedule_revisions FROM ems_invoices WHERE id=?', [$invoiceId])->fetch('assoc');
        $this->assertSame('2026-11-01', substr((string)$row['due_date'], 0, 10));
        $instalments = json_decode((string)$row['instalments'], true);
        $this->assertCount(2, $instalments);
        $revisions = json_decode((string)$row['schedule_revisions'], true);
        $this->assertCount(1, $revisions, 'the schedule it replaced is kept as history');
        $this->assertSame('Pat Parent', $revisions[0]['agreedWith']);
        $this->assertSame('Full payment', $revisions[0]['previous'][0]['label']);
        $this->assertSame(1, $this->rowCount('ems_invoice_events', ['invoice_id' => $invoiceId, 'kind' => 'rescheduled']));
    }

    // --- batch reads ----------------------------------------------------------

    public function testBatchIndexesFeedTheDeskPickers(): void
    {
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-open');
        $this->post($this->schoolPath('/cash-batches'), ['batchNumber' => 'CASH-100', 'collectionDate' => date('Y-m-d')]);
        $this->assertResponseCode(201);
        $batchId = $this->responseJson()['id'];
        $invoiceId = $this->seedInvoiceRow();
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-claim');
        $this->post($this->schoolPath('/invoices/' . $invoiceId . '/payment-submissions'), [
            'amount' => 25000, 'method' => 'cash', 'receivedOn' => date('Y-m-d'),
            'payerName' => 'Pat Parent', 'payerRelationship' => 'parent',
            'cashAcknowledgement' => 'ACK-100', 'cashBatchId' => $batchId,
        ]);
        $this->assertResponseCode(201);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/cash-batches'));
        $this->assertResponseOk();
        $batch = $this->responseJson()['items'][0];
        $this->assertSame('CASH-100', $batch['batchNumber']);
        $this->assertSame('open', $batch['status']);
        $this->assertSame(25000, $batch['expectedAmount']);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'stmt-import');
        $this->post($this->schoolPath('/statement-batches'), [
            'sourceName' => 'GTB August',
            'csv' => "date,reference,description,amount,direction\n2026-08-01,TRF-1,Transfer,40000,credit",
        ]);
        $this->assertResponseCode(201);
        $this->authAsAdmin();
        $this->get($this->schoolPath('/statement-batches'));
        $this->assertResponseOk();
        $imported = $this->responseJson()['items'][0];
        $this->assertSame('GTB August', $imported['sourceName']);
        $this->assertSame(1, $imported['rowCount']);
    }

    // --- helpers --------------------------------------------------------------

    /** An approved 100000-kobo Tuition plan for JSS 1, First term. */
    private function approvedPlan(): string
    {
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'plan-1');
        $this->post($this->schoolPath('/fee-plan-versions'), [
            'session' => '2025/2026', 'term' => 'First', 'level' => 'JSS 1',
            'items' => [['name' => 'Tuition', 'amount' => 100000]],
        ]);
        $this->assertResponseCode(201);
        $planId = $this->responseJson()['id'];
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'plan-approve');
        $this->post($this->schoolPath('/fee-plan-versions/' . $planId . '/decision'), ['decision' => 'approved', 'reason' => 'Fee plan reviewed.']);
        $this->assertResponseOk();

        return $planId;
    }

    private function seedInvoiceRow(): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_invoices', [
            'id' => $id, 'school_id' => $this->schoolId, 'invoice_number' => 'TES/INV/' . substr($id, 0, 4),
            'student_id' => $this->studentId, 'student_name' => 'Ayo Student', 'class_group' => 'JSS 1A',
            'session' => '2025/2026', 'term' => 'First', 'issued_on' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'line_items' => json_encode([['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge']]),
            'total' => 100000, 'status' => 'issued',
        ]);

        return $id;
    }

    /** Authenticate with a fresh Idempotency-Key for a finance write. */
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
