<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Utility\Text;

final class OfflinePaymentsTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = ['ems_finance_idempotency','ems_finance_integrity_locks','ems_audit_events','ems_receipts','ems_finance_ledger_events','ems_finance_decisions','ems_payment_submissions','ems_cash_batches','ems_payments','ems_invoices','ems_sequences','ems_students','ems_users','ems_schools'];
    private string $studentId;
    private string $invoiceId;
    private string $bursarId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->studentId = Text::uuid();
        $this->insertRow('ems_students', ['id' => $this->studentId,'school_id' => $this->schoolId,'admission_number' => 'EMS/001','first_name' => 'Ayo','last_name' => 'Student','date_of_birth' => '2015-01-01','gender' => 'male','class_group' => 'JSS 1A','status' => 'enrolled','enrolled_on' => date('Y-m-d')]);
        $this->invoiceId = Text::uuid();
        $this->insertRow('ems_invoices', ['id' => $this->invoiceId,'school_id' => $this->schoolId,'invoice_number' => 'TES/INV/0001','student_id' => $this->studentId,'student_name' => 'Ayo Student','class_group' => 'JSS 1A','session' => '2025/2026','term' => 'First','issued_on' => date('Y-m-d'),'due_date' => date('Y-m-d', strtotime('+5 days')),'line_items' => json_encode([['name' => 'Tuition','amount' => 100000,'kind' => 'charge']]),'total' => 100000,'status' => 'issued']);
        $this->bursarId = Text::uuid();
    }

    public function testCashSubmissionApprovalPostsLedgerAndStableReceipt(): void
    {
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'open-1');
        $this->post($this->schoolPath('/cash-batches'), ['batchNumber' => 'CASH-001','collectionDate' => date('Y-m-d')]);
        $this->assertResponseCode(201);
        $batchId = $this->responseJson()['id'];
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'submit-1');
        $this->post($this->schoolPath('/invoices/' . $this->invoiceId . '/payment-submissions'), ['amount' => 40000,'method' => 'cash','receivedOn' => date('Y-m-d'),'payerName' => 'Pat Parent','payerRelationship' => 'parent','cashAcknowledgement' => 'ACK-0001','cashBatchId' => $batchId]);
        $this->assertResponseCode(201);
        $this->assertSame('pending', $this->responseJson()['status']);
        $this->assertSame(0, $this->rowCount('ems_payments', ['invoice_id' => $this->invoiceId]));
        $this->authFinance('administrator', $this->adminId, 'Ada Admin', 'close-1');
        $this->post($this->schoolPath('/cash-batches/' . $batchId . '/close'), ['countedAmount' => 40000]);
        $this->assertTrue($this->_response->getStatusCode() >= 200 && $this->_response->getStatusCode() < 300, json_encode($this->responseJson()));
        $this->assertSame(1, $this->responseJson()['approvedCount']);
        $this->assertSame(1, $this->rowCount('ems_finance_ledger_events', ['invoice_id' => $this->invoiceId,'amount' => 40000]));
        $this->assertSame(1, $this->rowCount('ems_receipts', ['invoice_id' => $this->invoiceId,'balance_after' => 60000]));
        $payment = $this->db->execute('SELECT id FROM ems_payments WHERE invoice_id=?', [$this->invoiceId])->fetch('assoc');
        $this->authAsAdmin();
        $this->get($this->schoolPath('/payments/' . $payment['id'] . '/receipt'));
        $this->assertResponseOk();
        $this->assertSame(60000, $this->responseJson()['balanceAfter']);
    }

    public function testIdempotencyAndSeparationOfDuties(): void
    {
        $batchId = $this->seedOpenBatch();
        $body = ['amount' => 30000,'method' => 'cash','receivedOn' => date('Y-m-d'),'payerName' => 'Pat Parent','payerRelationship' => 'parent','cashAcknowledgement' => 'ACK-2','cashBatchId' => $batchId];
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'same-key');
        $this->post($this->schoolPath('/invoices/' . $this->invoiceId . '/payment-submissions'), $body);
        $this->assertResponseCode(201);
        $id = $this->responseJson()['id'];
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'same-key');
        $this->post($this->schoolPath('/invoices/' . $this->invoiceId . '/payment-submissions'), $body);
        $this->assertResponseCode(201);
        $this->assertSame($id, $this->responseJson()['id']);
        $this->assertSame(1, $this->rowCount('ems_payment_submissions', ['invoice_id' => $this->invoiceId]));
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'own-decision');
        $this->post($this->schoolPath('/payment-submissions/' . $id . '/decision'), ['decision' => 'approved','reason' => 'Looks right']);
        $this->assertResponseCode(403);
    }

    public function testPendingClaimIsVisibleButDoesNotChangeStudentBalanceStatus(): void
    {
        $batchId = $this->seedOpenBatch();
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'pending-status');
        $this->post($this->schoolPath('/invoices/' . $this->invoiceId . '/payment-submissions'), ['amount' => 20000,'method' => 'cash','receivedOn' => date('Y-m-d'),'payerName' => 'Pat Parent','payerRelationship' => 'parent','cashAcknowledgement' => 'ACK-STATUS','cashBatchId' => $batchId]);
        $this->assertResponseCode(201);
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $this->studentId));
        $this->assertResponseOk();
        $this->assertSame('pending_verification', $this->responseJson()['financeStatus']);
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $this->studentId . '/ledger'));
        $this->assertResponseOk();
        $this->assertSame(0, $this->responseJson()['totalPaid']);
        $this->assertSame(100000, $this->responseJson()['totalBalance']);
    }

    public function testIntegrityFaultLocksFinanceWritesButNotReads(): void
    {
        $this->db->insert('ems_finance_integrity_locks', ['id' => Text::uuid(),'school_id' => $this->schoolId,'reason' => 'Audit chain mismatch','detected_at' => $this->now()]);
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'locked-write');
        $this->post($this->schoolPath('/cash-batches'), ['batchNumber' => 'LOCKED','collectionDate' => date('Y-m-d')]);
        $this->assertResponseCode(423);
        $this->authAsAdmin();
        $this->get($this->schoolPath('/students/' . $this->studentId));
        $this->assertResponseOk();
    }

    public function testDirectPostingAndReversalAreGone(): void
    {
        $this->authAsAdmin();
        $this->post($this->schoolPath('/invoices/' . $this->invoiceId . '/payments'), ['amount' => 1]);
        $this->assertResponseCode(410);
        $this->authAsAdmin();
        $this->post($this->schoolPath('/payments/' . Text::uuid() . '/reverse'), ['reason' => 'No']);
        $this->assertResponseCode(410);
    }

    public function testRefundPayoutNeedsASecondAdministrator(): void
    {
        $this->ensureUser('bursar', $this->bursarId, 'Bola Bursar');
        $paymentId = Text::uuid();
        $requestId = Text::uuid();
        $this->db->insert('ems_payments', [
            'id' => $paymentId, 'school_id' => $this->schoolId,
            'invoice_id' => $this->invoiceId, 'student_id' => $this->studentId,
            'receipt_number' => 'TES/RCP/PAYOUT', 'amount' => 10000, 'method' => 'bank_transfer',
            'paid_on' => date('Y-m-d'), 'state' => 'completed', 'provenance' => 'test',
        ]);
        $this->db->insert('ems_finance_adjustment_requests', [
            'id' => $requestId, 'school_id' => $this->schoolId, 'payment_id' => $paymentId,
            'invoice_id' => $this->invoiceId, 'student_id' => $this->studentId,
            'kind' => 'refund', 'amount' => 10000, 'reason' => 'Overpayment',
            'requested_by_user_id' => $this->bursarId, 'requested_by_name' => 'Bola Bursar',
            'created' => $this->now(),
        ]);
        $this->db->insert('ems_finance_decisions', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'request_type' => 'adjustment',
            'request_id' => $requestId, 'decision' => 'approved', 'reason' => 'Approved',
            'requested_by_user_id' => $this->bursarId, 'decided_by_user_id' => $this->adminId,
            'decided_by_name' => 'Ada Admin', 'decided_at' => $this->now(),
        ]);

        $this->authFinance('administrator', $this->adminId, 'Ada Admin', 'same-admin-payout');
        $this->post($this->schoolPath('/finance-adjustments/' . $requestId . '/payout-confirmation'), []);

        $this->assertResponseCode(403);
        $this->assertSame('A different administrator must confirm the refund payout.', $this->responseJson()['message']);
    }

    public function testMatchedTransferEvidenceCanBeReviewedAndApproved(): void
    {
        $this->ensureUser('bursar', $this->bursarId, 'Bola Bursar');
        $batchId = Text::uuid();
        $rowId = Text::uuid();
        $submissionId = Text::uuid();
        $evidenceId = Text::uuid();
        $path = $this->schoolId . '/finance/payment_submission/' . $submissionId . '/' . $evidenceId;
        $pdf = "%PDF-1.4\nClean bank receipt";
        $this->db->insert('ems_bank_statement_batches', [
            'id' => $batchId,
            'school_id' => $this->schoolId,
            'source_name' => 'Test bank',
            'file_hash' => hash('sha256', 'test-bank-transfer'),
            'imported_by_user_id' => $this->bursarId,
            'created' => $this->now(),
        ]);
        $this->db->insert('ems_bank_statement_rows', [
            'id' => $rowId,
            'school_id' => $this->schoolId,
            'batch_id' => $batchId,
            'occurred_on' => date('Y-m-d'),
            'reference' => 'TRANSFER-001',
            'description' => 'School fees transfer',
            'amount' => 25000,
            'direction' => 'credit',
            'row_hash' => hash('sha256', 'test-bank-transfer-row'),
        ]);
        $this->db->insert('ems_payment_submissions', [
            'id' => $submissionId,
            'school_id' => $this->schoolId,
            'invoice_id' => $this->invoiceId,
            'student_id' => $this->studentId,
            'amount' => 25000,
            'method' => 'bank_transfer',
            'normalized_reference' => 'TRANSFER-001',
            'payer_name' => 'Pat Parent',
            'payer_relationship' => 'parent',
            'received_on' => date('Y-m-d'),
            'statement_row_id' => $rowId,
            'recorded_by_user_id' => $this->bursarId,
            'recorded_by_name' => 'Bola Bursar',
            'provenance' => 'offline',
            'created' => $this->now(),
        ]);
        $this->db->insert('ems_document_objects', [
            'id' => Text::uuid(),
            'storage_path' => $path,
            'content_type' => 'application/pdf',
            'size_bytes' => strlen($pdf),
            'body' => $pdf,
            'created' => $this->now(),
            'modified' => $this->now(),
        ]);
        $this->db->insert('ems_finance_evidence', [
            'id' => $evidenceId,
            'school_id' => $this->schoolId,
            'owner_type' => 'payment_submission',
            'owner_id' => $submissionId,
            'storage_path' => $path,
            'filename' => 'bank-receipt.pdf',
            'content_hash' => hash('sha256', $pdf),
            'media_type' => 'application/pdf',
            'size_bytes' => strlen($pdf),
            'scan_status' => 'clean',
            'created_by_user_id' => $this->bursarId,
            'created' => $this->now(),
        ]);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/payment-submissions'));
        $this->assertResponseOk();
        $item = $this->responseJson()['items'][0];
        $this->assertSame($rowId, $item['statementRowId']);
        $this->assertSame('clean', $item['evidence']['scanStatus']);

        $this->authAsAdmin();
        $this->get($this->schoolPath('/payment-submissions/' . $submissionId . '/evidence'));
        $this->assertResponseOk();
        $this->assertSame('application/pdf', $this->_response->getHeaderLine('Content-Type'));
        $this->assertSame($pdf, (string)$this->_response->getBody());

        $this->authFinance('parent', Text::uuid(), 'Other Parent', 'not-used');
        $this->get($this->schoolPath('/payment-submissions/' . $submissionId . '/evidence'));
        $this->assertResponseCode(403);

        $this->authFinance('administrator', $this->adminId, 'Ada Admin', 'approve-transfer');
        $this->post($this->schoolPath('/payment-submissions/' . $submissionId . '/decision'), [
            'decision' => 'approved',
            'reason' => 'Evidence and statement row match.',
        ]);
        $this->assertResponseOk();
        $this->assertSame(1, $this->rowCount('ems_finance_ledger_events', [
            'invoice_id' => $this->invoiceId,
            'amount' => 25000,
        ]));
    }

    private function seedOpenBatch(): string
    {
        $this->authFinance('bursar', $this->bursarId, 'Bola Bursar', 'batch-' . Text::uuid());
        $this->post($this->schoolPath('/cash-batches'), ['batchNumber' => 'CASH-' . substr(Text::uuid(), 0, 6),'collectionDate' => date('Y-m-d')]);
        $this->assertResponseCode(201);

        return (string)$this->responseJson()['id'];
    }

    private function authFinance(string $role, string $id, string $name, string $key): void
    {
        $this->ensureUser($role, $id, $name);
        $this->configRequest(['headers' => ['Authorization' => 'Bearer ' . $this->token($role, $id, $name),'Accept' => 'application/json','Idempotency-Key' => $key]]);
    }
}
