<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use Cake\Core\Configure;
use Cake\Utility\Text;

/**
 * The family payment settlement path (document.md §3.19). A linked guardian
 * declares an offline payment against a ward's invoice; it enters the shared
 * ems_payment_submissions queue (provenance 'parent') and an administrator
 * verifies it through the unchanged approval path, matching the credit statement
 * row at decision time (the parent can't see the bank feed, and the claim row is
 * immutable). Companion to OfflinePaymentsTest, which owns bursar-entered claims.
 */
final class PortalPaymentClaimsTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_portal_notifications', 'ems_message_recipients', 'ems_notifications',
        'ems_finance_idempotency', 'ems_finance_integrity_locks', 'ems_audit_events',
        'ems_receipts', 'ems_finance_ledger_events', 'ems_finance_decisions',
        'ems_finance_evidence', 'ems_document_objects', 'ems_payment_submissions',
        'ems_bank_statement_rows', 'ems_bank_statement_batches',
        'ems_payments', 'ems_invoices', 'ems_sequences', 'ems_students', 'ems_users', 'ems_schools',
    ];

    private string $studentId;
    private string $invoiceId;
    private string $parentId;

    protected function setUp(): void
    {
        parent::setUp();
        // No real mail: an empty Resend key makes delivery suppress in-process
        // (Resend::deliver throws → attemptDelivery records a failed send), so a
        // decision notice never reaches api.resend.com from the test suite.
        Configure::write('Ems.resendApiKey', '');
        Configure::write('Ems.emailFrom', '');
        $this->studentId = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $this->studentId, 'school_id' => $this->schoolId, 'admission_number' => 'EMS/001',
            'first_name' => 'Ada', 'last_name' => 'Junior', 'date_of_birth' => '2015-01-01',
            'gender' => 'female', 'class_group' => 'JSS 1A', 'status' => 'enrolled', 'enrolled_on' => date('Y-m-d'),
        ]);
        $this->invoiceId = $this->seedInvoice();
        // A linked guardian portal account: link_student_ids drives Scope for the
        // parent role, and the account email is where a decision notice lands.
        $this->parentId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $this->parentId, 'school_id' => $this->schoolId, 'name' => 'Pat Parent',
            'email' => 'pat@family.test', 'role' => 'parent', 'status' => 'active',
            'added_on' => $this->now(), 'link_student_ids' => json_encode([$this->studentId]),
        ]);
    }

    // --- declaring -----------------------------------------------------------

    public function testGuardianDeclarationEntersTheSharedQueue(): void
    {
        $this->parent('claim-1');
        $this->post($this->wardPath('/payment-claims'), $this->transfer(40000, 'PAYSTACK-77'));
        $this->assertResponseCode(201);
        $claim = $this->responseJson();
        $this->assertSame('parent', $claim['provenance']);
        $this->assertSame(40000, $claim['amount']);

        // It shows in the family's own claim list, pending.
        $this->parentGet();
        $this->get($this->wardPath('/payment-claims'));
        $this->assertResponseOk();
        $this->assertSame(1, $this->responseJson()['total']);
        $this->assertSame('pending', $this->responseJson()['items'][0]['status']);

        // And in the staff verification queue, labelled by origin.
        $this->authAsAdmin();
        $this->get($this->schoolPath('/payment-submissions'));
        $this->assertResponseOk();
        $this->assertSame('parent', $this->responseJson()['items'][0]['provenance']);
    }

    public function testInvalidDeclarationsAreRefused(): void
    {
        // Cash is never offered to a family — it needs an open bursary cash batch.
        $this->parent('bad-cash');
        $this->post($this->wardPath('/payment-claims'), [
            'invoiceId' => $this->invoiceId, 'method' => 'cash', 'amount' => 10000, 'receivedOn' => date('Y-m-d'),
        ]);
        $this->assertResponseCode(422);

        // More than the invoice balance.
        $this->parent('over');
        $this->post($this->wardPath('/payment-claims'), $this->transfer(500000, 'TOO-BIG'));
        $this->assertResponseCode(422);

        // A transfer with no evidence.
        $this->parent('no-evidence');
        $body = $this->transfer(20000, 'NOPROOF');
        unset($body['evidence']);
        $this->post($this->wardPath('/payment-claims'), $body);
        $this->assertResponseCode(422);
    }

    public function testOnlyALinkedGuardianCanDeclare(): void
    {
        $strangerId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $strangerId, 'school_id' => $this->schoolId, 'name' => 'Otis Other',
            'email' => 'otis@other.test', 'role' => 'parent', 'status' => 'active',
            'added_on' => $this->now(), 'link_student_ids' => json_encode([]),
        ]);
        $this->configRequest(['headers' => [
            'Authorization' => 'Bearer ' . $this->token('parent', $strangerId, 'Otis Other'),
            'Accept' => 'application/json', 'Idempotency-Key' => 'stranger',
        ]]);
        $this->post($this->wardPath('/payment-claims'), $this->transfer(10000, 'STRANGER'));
        $this->assertResponseCode(403);
    }

    // --- one open per invoice + resubmit after rejection ---------------------

    public function testOneOpenDeclarationThenResubmitAfterRejection(): void
    {
        $this->parent('open-1');
        $this->post($this->wardPath('/payment-claims'), $this->transfer(40000, 'FIRST'));
        $this->assertResponseCode(201);
        $submissionId = $this->responseJson()['id'];

        // A second declaration is blocked while the first is undecided.
        $this->parent('open-2');
        $this->post($this->wardPath('/payment-claims'), $this->transfer(40000, 'SECOND'));
        $this->assertResponseCode(409);

        // The administrator rejects the first; the guardian is told.
        $this->admin('reject-1');
        $this->post($this->schoolPath('/payment-submissions/' . $submissionId . '/decision'), [
            'decision' => 'rejected', 'reason' => 'The reference does not match our statement.',
        ]);
        $this->assertResponseOk();
        $this->assertSame(1, $this->rowCount('ems_portal_notifications', [
            'user_id' => $this->parentId, 'kind' => 'payment_claim',
        ]));

        // With the road clear again, a corrected declaration is accepted.
        $this->parent('resubmit');
        $this->post($this->wardPath('/payment-claims'), $this->transfer(40000, 'CORRECTED'));
        $this->assertResponseCode(201);
    }

    // --- approval posts money and notifies -----------------------------------

    public function testApprovalMatchesAtDecisionPostsAndNotifies(): void
    {
        $this->parent('approve-declare');
        $this->post($this->wardPath('/payment-claims'), $this->transfer(40000, 'GTB-4410'));
        $this->assertResponseCode(201);
        $submissionId = $this->responseJson()['id'];

        // The parent's evidence passes scanning (no daemon in tests → quarantined
        // on write; a clean scan is simulated here), and the bank statement the
        // admin imports carries the matching credit row.
        $this->db->update('ems_finance_evidence', ['scan_status' => 'clean'], ['owner_id' => $submissionId]);
        $rowId = $this->seedStatementRow(40000, 'GTB-4410');

        $this->admin('approve-1');
        $this->post($this->schoolPath('/payment-submissions/' . $submissionId . '/decision'), [
            'decision' => 'approved', 'reason' => 'Evidence and statement row match.', 'statementRowId' => $rowId,
        ]);
        $this->assertResponseOk();

        // Money is posted to the ledger and the match is recorded on the decision.
        $this->assertSame(1, $this->rowCount('ems_finance_ledger_events', [
            'invoice_id' => $this->invoiceId, 'amount' => 40000,
        ]));
        $this->assertSame(1, $this->rowCount('ems_finance_decisions', [
            'request_type' => 'payment_submission', 'request_id' => $submissionId,
            'statement_row_id' => $rowId, 'decision' => 'approved',
        ]));
        // The declaring guardian gets the confirmation bell.
        $this->assertSame(1, $this->rowCount('ems_portal_notifications', [
            'user_id' => $this->parentId, 'kind' => 'payment_claim', 'title' => 'Payment confirmed',
        ]));
    }

    // --- helpers -------------------------------------------------------------

    private function seedInvoice(): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_invoices', [
            'id' => $id, 'school_id' => $this->schoolId, 'invoice_number' => 'TES/INV/' . substr($id, 0, 4),
            'student_id' => $this->studentId, 'student_name' => 'Ada Junior', 'class_group' => 'JSS 1A',
            'session' => '2025/2026', 'term' => 'First', 'issued_on' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'line_items' => json_encode([['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge']]),
            'total' => 100000, 'status' => 'issued',
        ]);

        return $id;
    }

    private function seedStatementRow(int $amount, string $reference): string
    {
        $batchId = Text::uuid();
        $rowId = Text::uuid();
        $this->db->insert('ems_bank_statement_batches', [
            'id' => $batchId, 'school_id' => $this->schoolId, 'source_name' => 'GTB August',
            'file_hash' => hash('sha256', $rowId), 'imported_by_user_id' => $this->adminId, 'created' => $this->now(),
        ]);
        $this->db->insert('ems_bank_statement_rows', [
            'id' => $rowId, 'school_id' => $this->schoolId, 'batch_id' => $batchId, 'occurred_on' => date('Y-m-d'),
            'reference' => $reference, 'description' => 'School fees transfer', 'amount' => $amount,
            'direction' => 'credit', 'row_hash' => hash('sha256', $rowId . 'row'),
        ]);

        return $rowId;
    }

    /**
     * A transfer declaration body with inline PDF evidence.
     *
     * @return array<string, mixed>
     */
    private function transfer(int $amount, string $reference): array
    {
        // Vary the bytes by reference: ems_finance_evidence is unique per
        // (school, content hash), so two declarations can't reuse one receipt.
        $pdf = "%PDF-1.4\nFamily transfer receipt " . $reference;

        return [
            'invoiceId' => $this->invoiceId,
            'method' => 'bank_transfer',
            'amount' => $amount,
            'reference' => $reference,
            'receivedOn' => date('Y-m-d'),
            'evidence' => [
                'filename' => 'receipt.pdf',
                'mediaType' => 'application/pdf',
                'base64' => base64_encode($pdf),
            ],
        ];
    }

    private function wardPath(string $suffix): string
    {
        return $this->schoolPath('/portal/wards/' . $this->studentId . $suffix);
    }

    /** Authenticate the linked guardian for a write, with an Idempotency-Key. */
    private function parent(string $key): void
    {
        $this->configRequest(['headers' => [
            'Authorization' => 'Bearer ' . $this->token('parent', $this->parentId, 'Pat Parent'),
            'Accept' => 'application/json', 'Idempotency-Key' => $key,
        ]]);
    }

    /** Authenticate the linked guardian for a read. */
    private function parentGet(): void
    {
        $this->configRequest(['headers' => [
            'Authorization' => 'Bearer ' . $this->token('parent', $this->parentId, 'Pat Parent'),
            'Accept' => 'application/json',
        ]]);
    }

    private function admin(string $key): void
    {
        $this->ensureUser('administrator', $this->adminId, 'Ada Admin');
        $this->configRequest(['headers' => [
            'Authorization' => 'Bearer ' . $this->token('administrator', $this->adminId, 'Ada Admin'),
            'Accept' => 'application/json', 'Idempotency-Key' => $key,
        ]]);
    }
}
