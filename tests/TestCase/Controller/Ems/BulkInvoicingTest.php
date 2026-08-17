<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Money;
use Cake\Utility\Text;

/**
 * Bulk invoicing (document.md §3.7). A bursar drafts a batch — an approved plan
 * version, the class groups to bill, a percentage instalment template — and a
 * different administrator approves it, issuing N invoices atomically. Awards are
 * priced per student; anyone already carrying a live invoice from the plan is
 * skipped, never double-billed; the percentage template splits each student's
 * own total to the kobo. Companion to FinanceReconnectTest.
 */
final class BulkInvoicingTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_fee_awards',
        'ems_students',
        'ems_users',
        'ems_schools',
    ];

    private string $bursarId;
    private array $students = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->bursarId = Text::uuid();
        $this->students = [
            'ada' => $this->seedStudent('JSS 1A', 'Ada', 'Aigbe'),
            'bola' => $this->seedStudent('JSS 1A', 'Bola', 'Bello'),
            'chidi' => $this->seedStudent('JSS 1A', 'Chidi', 'Chukwu'),
        ];
    }

    public function testSplitByPercentPutsTheRemainderOnTheLastInstalment(): void
    {
        $template = [
            ['label' => 'First', 'dueOn' => '2026-09-01', 'percent' => 33],
            ['label' => 'Second', 'dueOn' => '2026-11-01', 'percent' => 33],
            ['label' => 'Third', 'dueOn' => '2027-01-01', 'percent' => 34],
        ];
        $split = Money::splitByPercent(100001, $template);
        $this->assertSame([33000, 33000, 34001], array_column($split, 'amount'));
        $this->assertSame(100001, array_sum(array_column($split, 'amount')));
        // A fully-awarded (zero) bill yields no schedule.
        $this->assertSame([], Money::splitByPercent(0, $template));
    }

    public function testDraftApproveIssuesEveryInvoiceWithAwardsAndSchedule(): void
    {
        $planId = $this->approvedPlan();
        // Ada carries a 50% scholarship; her bill prices to 50,000.
        $this->seedAward($this->students['ada'], 50);

        $schedule = [
            ['label' => 'First', 'dueOn' => '2026-09-01', 'percent' => 60],
            ['label' => 'Second', 'dueOn' => '2026-11-01', 'percent' => 40],
        ];

        // Preview resolves the roster and prices every student.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-preview');
        $this->post($this->schoolPath('/invoice-batches/preview'), [
            'feePlanVersionId' => $planId, 'classGroups' => ['JSS 1A'], 'schedule' => $schedule,
        ]);
        $this->assertResponseOk();
        $preview = $this->responseJson();
        $this->assertSame(3, $preview['issueCount']);
        $this->assertSame(0, $preview['skipCount']);
        $this->assertSame(250000, $preview['totalAmount'], '50,000 (Ada) + 100,000 + 100,000');

        // A different administrator cannot be the drafter, and the drafter
        // cannot decide — draft first.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-draft');
        $this->post($this->schoolPath('/invoice-batches'), [
            'feePlanVersionId' => $planId, 'classGroups' => ['JSS 1A'], 'schedule' => $schedule,
        ]);
        $this->assertResponseCode(201);
        $batch = $this->responseJson();
        $this->assertSame('pending', $batch['status']);
        $this->assertNotEmpty($batch['batchNumber']);
        $batchId = $batch['id'];

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-self-decide');
        $this->post($this->schoolPath('/invoice-batches/' . $batchId . '/decision'), ['decision' => 'approved', 'reason' => 'Me.']);
        $this->assertResponseCode(403);

        // The administrator approves; every invoice is issued in one go.
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'batch-approve');
        $this->post($this->schoolPath('/invoice-batches/' . $batchId . '/decision'), ['decision' => 'approved', 'reason' => 'Class roster confirmed.']);
        $this->assertResponseOk();
        $this->assertSame('approved', $this->responseJson()['status']);
        $this->assertSame(3, $this->responseJson()['issueCount']);

        $this->assertSame(3, $this->rowCount('ems_invoices', ['fee_plan_version_id' => $planId, 'status' => 'issued']));
        $this->assertSame(3, $this->rowCount('ems_invoice_batch_rows', ['batch_id' => $batchId, 'status' => 'issued']));

        // Ada's bill is 50,000 split 60/40 = 30,000 + 20,000.
        $ada = $this->db->execute('SELECT total, instalments FROM ems_invoices WHERE student_id=?', [$this->students['ada']])->fetch('assoc');
        $this->assertSame(50000, (int)$ada['total']);
        $this->assertSame([30000, 20000], array_column(json_decode((string)$ada['instalments'], true), 'amount'));
        // Bola's full bill splits 60,000 + 40,000.
        $bola = $this->db->execute('SELECT total, instalments FROM ems_invoices WHERE student_id=?', [$this->students['bola']])->fetch('assoc');
        $this->assertSame(100000, (int)$bola['total']);
        $this->assertSame([60000, 40000], array_column(json_decode((string)$bola['instalments'], true), 'amount'));
    }

    public function testAlreadyInvoicedStudentsAreSkippedNotDoubleBilled(): void
    {
        $planId = $this->approvedPlan();
        // Issue a batch to the whole class.
        $this->draftAndApprove($planId, ['JSS 1A']);
        $this->assertSame(3, $this->rowCount('ems_invoices', ['fee_plan_version_id' => $planId]));

        // A second batch for the same plan/class skips every already-billed
        // student, so re-running is safe — and refuses when nothing is left.
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-2-preview');
        $this->post($this->schoolPath('/invoice-batches/preview'), [
            'feePlanVersionId' => $planId, 'classGroups' => ['JSS 1A'], 'dueDate' => '2026-12-01',
        ]);
        $this->assertResponseOk();
        $this->assertSame(0, $this->responseJson()['issueCount']);
        $this->assertSame(3, $this->responseJson()['skipCount']);

        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-2-draft');
        $this->post($this->schoolPath('/invoice-batches'), [
            'feePlanVersionId' => $planId, 'classGroups' => ['JSS 1A'], 'dueDate' => '2026-12-01',
        ]);
        $this->assertResponseCode(422);
        $this->assertStringContainsString('already invoiced', $this->responseJson()['message']);
        // Still exactly one invoice per student.
        $this->assertSame(3, $this->rowCount('ems_invoices', ['fee_plan_version_id' => $planId]));
    }

    public function testPercentageTemplateMustTotalOneHundred(): void
    {
        $planId = $this->approvedPlan();
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'batch-bad-schedule');
        $this->post($this->schoolPath('/invoice-batches/preview'), [
            'feePlanVersionId' => $planId, 'classGroups' => ['JSS 1A'],
            'schedule' => [
                ['label' => 'First', 'dueOn' => '2026-09-01', 'percent' => 60],
                ['label' => 'Second', 'dueOn' => '2026-11-01', 'percent' => 30],
            ],
        ]);
        $this->assertResponseCode(422);
        $this->assertStringContainsString('90%', $this->responseJson()['message']);
    }

    public function testAdministratorCannotDraftAndDecisionIsOnce(): void
    {
        $planId = $this->approvedPlan();
        // Only a bursar drafts.
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'admin-draft');
        $this->post($this->schoolPath('/invoice-batches'), [
            'feePlanVersionId' => $planId, 'classGroups' => ['JSS 1A'], 'dueDate' => '2026-12-01',
        ]);
        $this->assertResponseCode(403);

        $batchId = $this->draftAndApprove($planId, ['JSS 1A']);
        // A second decision is refused.
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'batch-approve-again');
        $this->post($this->schoolPath('/invoice-batches/' . $batchId . '/decision'), ['decision' => 'rejected', 'reason' => 'Change my mind.']);
        $this->assertResponseCode(409);
    }

    // --- helpers -------------------------------------------------------------

    private function draftAndApprove(string $planId, array $classGroups): string
    {
        $this->finance('bursar', $this->bursarId, 'Bola Bursar', 'draft-' . Text::uuid());
        $this->post($this->schoolPath('/invoice-batches'), [
            'feePlanVersionId' => $planId, 'classGroups' => $classGroups, 'dueDate' => '2026-12-01',
        ]);
        $this->assertResponseCode(201);
        $batchId = $this->responseJson()['id'];
        $this->finance('administrator', $this->adminId, 'Ada Admin', 'approve-' . Text::uuid());
        $this->post($this->schoolPath('/invoice-batches/' . $batchId . '/decision'), ['decision' => 'approved', 'reason' => 'Approved.']);
        $this->assertResponseOk();

        return $batchId;
    }

    /** An approved 100,000-kobo Tuition plan for JSS 1, First term. */
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

    private function seedAward(string $studentId, int $percent): void
    {
        $this->insertRow('ems_fee_awards', [
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'name' => 'Scholarship',
            'kind' => 'scholarship', 'basis' => 'percentage', 'value' => $percent, 'applies_to_item' => 'all',
            'scope' => 'student', 'student_id' => $studentId, 'session' => '2025/2026', 'term' => 'First',
            'status' => 'active', 'awarded_by' => 'Ada Admin', 'awarded_on' => '2025-08-01',
        ]);
    }

    private function seedStudent(string $classGroup, string $first, string $last): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id, 'school_id' => $this->schoolId, 'admission_number' => 'ADM-' . substr($id, 0, 6),
            'first_name' => $first, 'last_name' => $last, 'date_of_birth' => '2014-05-10',
            'gender' => 'male', 'class_group' => $classGroup, 'status' => 'enrolled', 'enrolled_on' => '2025-09-01',
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
