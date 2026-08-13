<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Fees;
use App\Ems\Messages;
use Cake\Http\Exception\HttpException;
use Cake\Utility\Text;

/**
 * The fees computation engine (document.md §3.7) — the single backend authority
 * for every derived figure. Pure arithmetic lives in App\Ems\Money (already
 * covered by MoneyTest); these tests exercise what Fees itself owns: the DB
 * reads and the assembly on top — the net-paid rule (completed payments less
 * processed refunds) that every balance derives from, award pricing at issue,
 * instalment validation, and the ledger / reconciliation / report read-models.
 * Driven directly against seeded rows, not over HTTP (that is the Python E2E's
 * job); this pins the fine-grained engine math the E2E can only assert coarsely.
 */
class FeesTest extends EmsDbTestCase
{
    private function fees(string $today = '2025-10-15', ?string $schoolId = null): Fees
    {
        return new Fees($this->locator, $schoolId ?? $this->schoolId, $today);
    }

    // --- net paid -----------------------------------------------------------

    public function testPaidForNetsCompletedPaymentsAgainstProcessedRefunds(): void
    {
        $inv = $this->seedInvoice($this->schoolId);
        $sid = Text::uuid();
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 60000, 'state' => 'completed']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 30000, 'state' => 'completed']);
        // None of these count toward the balance:
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 50000, 'state' => 'pending']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 20000, 'state' => 'failed']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 10000, 'state' => 'reversed']);
        $p = $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 90000, 'state' => 'completed']);
        // Only a PROCESSED refund reduces the net; pending / rejected do not.
        $this->seedRefund($this->schoolId, $p, $inv, $sid, ['amount' => 20000, 'status' => 'processed']);
        $this->seedRefund($this->schoolId, $p, $inv, $sid, ['amount' => 5000, 'status' => 'pending']);
        $this->seedRefund($this->schoolId, $p, $inv, $sid, ['amount' => 8000, 'status' => 'rejected']);

        // (60000 + 30000 + 90000) − 20000 = 160000
        $fees = $this->fees();
        $this->assertSame(160000, $fees->paidFor($inv));
        $this->assertSame(160000, $fees->netPaidByInvoice()[$inv]);
        $this->assertSame(0, $fees->paidFor(Text::uuid()), 'an unknown invoice is zero, not an error');
    }

    public function testNetPaidByInvoiceIsMemoizedPerInstance(): void
    {
        $inv = $this->seedInvoice($this->schoolId);
        $sid = Text::uuid();
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 40000, 'state' => 'completed']);

        $fees = $this->fees();
        $this->assertSame(40000, $fees->paidFor($inv));

        // A payment recorded AFTER the first read is invisible to this instance
        // (the map is memoized) but visible to a fresh one.
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 25000, 'state' => 'completed']);
        $this->assertSame(40000, $fees->paidFor($inv), 'memoized within the instance');
        $this->assertSame(65000, $this->fees()->paidFor($inv), 'a fresh instance re-reads');
    }

    public function testRefundableRemainingExcludesOnlyRejectedRefunds(): void
    {
        $inv = $this->seedInvoice($this->schoolId);
        $sid = Text::uuid();
        $p = $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 100000, 'state' => 'completed']);
        $this->seedRefund($this->schoolId, $p, $inv, $sid, ['amount' => 30000, 'status' => 'processed']);
        $this->seedRefund($this->schoolId, $p, $inv, $sid, ['amount' => 20000, 'status' => 'pending']);
        // A rejected refund frees its amount back up.
        $this->seedRefund($this->schoolId, $p, $inv, $sid, ['amount' => 40000, 'status' => 'rejected']);

        $payment = $this->locator->get('EmsPayments')->get($p);
        // 100000 − (30000 processed + 20000 pending) = 50000
        $this->assertSame(50000, $this->fees()->refundableRemaining($payment));
    }

    // --- enrich -------------------------------------------------------------

    public function testEnrichLayersDerivedFiguresOntoALumpSumInvoice(): void
    {
        $inv = $this->seedInvoice($this->schoolId, [
            'line_items' => [
                ['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge'],
                ['name' => 'Scholarship', 'amount' => -30000, 'kind' => 'award'],
            ],
            'total' => 70000,
            'due_date' => '2025-10-01', // before `today`
        ]);
        $invoice = $this->locator->get('EmsInvoices')->get($inv);

        $wire = $this->fees('2025-10-15')->enrich($invoice, 20000);

        $this->assertSame(20000, $wire['paid']);
        $this->assertSame(50000, $wire['balance']);
        $this->assertSame(100000, $wire['charged']);
        $this->assertSame(-30000, $wire['awarded']);
        $this->assertSame('overdue', $wire['paymentStatus'], 'past due date with money owing');
        $this->assertSame([], $wire['schedule'], 'no instalments → empty schedule');
        $this->assertArrayNotHasKey('nextDue', $wire);
    }

    public function testEnrichReportsNextDueForAScheduledInvoice(): void
    {
        $inv = $this->seedInvoice($this->schoolId, [
            'total' => 70000,
            'due_date' => '2025-12-01',
            'instalments' => [
                ['number' => 1, 'label' => 'First', 'dueOn' => '2025-09-01', 'amount' => 40000],
                ['number' => 2, 'label' => 'Second', 'dueOn' => '2025-12-01', 'amount' => 30000],
            ],
        ]);
        $invoice = $this->locator->get('EmsInvoices')->get($inv);

        $wire = $this->fees('2025-10-15')->enrich($invoice, 40000);

        $this->assertCount(2, $wire['schedule']);
        $this->assertArrayHasKey('nextDue', $wire);
        $this->assertSame(2, $wire['nextDue']['number'], 'the oldest still-owing instalment');
    }

    // --- active awards ------------------------------------------------------

    public function testActiveAwardsForResolvesScopeTermAndSession(): void
    {
        $sid = Text::uuid();
        $student = $this->makeStudent($sid, 'JSS 1A'); // level "JSS 1"

        $own = $this->seedFeeAward($this->schoolId, ['name' => 'Own', 'scope' => 'student', 'student_id' => $sid, 'term' => 'all']);
        $levelTerm = $this->seedFeeAward($this->schoolId, ['name' => 'LevelTerm', 'scope' => 'level', 'level' => 'JSS 1', 'term' => 'First']);
        // Each of these must be excluded, for a different reason:
        $this->seedFeeAward($this->schoolId, ['name' => 'WrongLevel', 'scope' => 'level', 'level' => 'JSS 2', 'term' => 'all']);
        $this->seedFeeAward($this->schoolId, ['name' => 'OtherStudent', 'scope' => 'student', 'student_id' => Text::uuid(), 'term' => 'all']);
        $this->seedFeeAward($this->schoolId, ['name' => 'WrongTerm', 'scope' => 'level', 'level' => 'JSS 1', 'term' => 'Second']);
        $this->seedFeeAward($this->schoolId, ['name' => 'Ended', 'scope' => 'level', 'level' => 'JSS 1', 'term' => 'all', 'status' => 'ended']);
        $this->seedFeeAward($this->schoolId, ['name' => 'WrongSession', 'scope' => 'level', 'level' => 'JSS 1', 'term' => 'all', 'session' => '2024/2025']);

        $awards = $this->fees()->activeAwardsFor($student, '2025/2026', 'First');

        $ids = array_map(static fn($a) => $a['id'], $awards);
        sort($ids);
        $expected = [$own, $levelTerm];
        sort($expected);
        $this->assertSame($expected, $ids);
    }

    // --- pricing ------------------------------------------------------------

    public function testPriceInvoiceCleansChargesAndAppliesAwards(): void
    {
        $sid = Text::uuid();
        $student = $this->makeStudent($sid, 'JSS 1A');
        $this->seedFeeAward($this->schoolId, ['scope' => 'level', 'level' => 'JSS 1', 'basis' => 'percentage', 'value' => 10, 'term' => 'all']);

        $preview = $this->fees()->priceInvoice($student, '2025/2026', 'First', [
            ['name' => 'Tuition', 'amount' => 100000],
            ['name' => '   ', 'amount' => 999], // blank name → dropped
        ]);

        $this->assertSame(100000, $preview['charged']);
        $this->assertSame(-10000, $preview['awarded']);
        $this->assertSame(90000, $preview['total']);
        $this->assertCount(1, $preview['applied']);
        $this->assertSame(10000, $preview['applied'][0]['amount']);
        $this->assertSame(100000, $preview['applied'][0]['base']);
    }

    public function testPriceInvoiceRejectsWhenNoChargeSurvives(): void
    {
        $student = $this->makeStudent(Text::uuid(), 'JSS 1A');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage(Messages::INVOICE_NO_LINE_ITEMS);
        $this->fees()->priceInvoice($student, '2025/2026', 'First', [['name' => '', 'amount' => 5000]]);
    }

    // --- instalments --------------------------------------------------------

    public function testBuildInstalmentsOrdersNumbersAndDefaultsLabels(): void
    {
        $out = $this->fees()->buildInstalments([
            ['label' => 'Second', 'dueOn' => '2025-12-01', 'amount' => 40000],
            ['label' => '', 'dueOn' => '2025-09-01', 'amount' => 60000],
        ], 100000);

        $this->assertSame(['2025-09-01', '2025-12-01'], array_column($out, 'dueOn'));
        $this->assertSame([1, 2], array_column($out, 'number'));
        $this->assertSame([60000, 40000], array_column($out, 'amount'));
        $this->assertSame('Instalment 1', $out[0]['label'], 'a blank label gets a default');
        $this->assertSame('Second', $out[1]['label']);
    }

    public function testBuildInstalmentsWithNoRowsReturnsEmpty(): void
    {
        $this->assertSame([], $this->fees()->buildInstalments([
            ['label' => '', 'dueOn' => '', 'amount' => 0],
        ], 100000));
    }

    public function testBuildInstalmentsRequiresADate(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage(Messages::INSTALMENT_NEEDS_DATE);
        $this->fees()->buildInstalments([['label' => 'One', 'dueOn' => '', 'amount' => 100000]], 100000);
    }

    public function testBuildInstalmentsRequiresAPositiveAmount(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage(Messages::INSTALMENT_NEEDS_AMOUNT);
        $this->fees()->buildInstalments([['label' => 'One', 'dueOn' => '2025-09-01', 'amount' => 0]], 100000);
    }

    public function testBuildInstalmentsRejectsASumThatDoesNotMatchTheBill(): void
    {
        // The message is an sprintf template with ₦ figures — assert type + code.
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        $this->fees()->buildInstalments([['label' => 'One', 'dueOn' => '2025-09-01', 'amount' => 50000]], 100000);
    }

    // --- student ledger -----------------------------------------------------

    public function testStudentLedgerExcludesCancelledFromTotalsAndSortsInvoices(): void
    {
        $sid = Text::uuid();
        $student = $this->makeStudent($sid, 'JSS 1A', ['first_name' => 'Ada', 'last_name' => 'Pupil']);
        $live = $this->seedInvoice($this->schoolId, ['student_id' => $sid, 'total' => 100000, 'status' => 'issued', 'issued_on' => '2025-09-01']);
        $cancelled = $this->seedInvoice($this->schoolId, ['student_id' => $sid, 'total' => 50000, 'status' => 'cancelled', 'issued_on' => '2025-10-01']);
        $this->seedPayment($this->schoolId, $live, $sid, ['amount' => 30000, 'state' => 'completed']);

        // Awards that belong to this child (own + their level) come along.
        $this->seedFeeAward($this->schoolId, ['scope' => 'student', 'student_id' => $sid]);
        $this->seedFeeAward($this->schoolId, ['scope' => 'level', 'level' => 'JSS 1']);
        $this->seedFeeAward($this->schoolId, ['scope' => 'student', 'student_id' => Text::uuid()]); // someone else

        $ledger = $this->fees()->studentLedger($student);

        $this->assertSame('Ada Pupil', $ledger['studentName']);
        $this->assertSame(100000, $ledger['totalInvoiced'], 'cancelled invoice excluded');
        $this->assertSame(30000, $ledger['totalPaid']);
        $this->assertSame(70000, $ledger['totalBalance']);
        $this->assertSame($cancelled, $ledger['invoices'][0]['id'], 'newest issuedOn first');
        $this->assertCount(2, $ledger['awards']);
    }

    // --- reconciliation -----------------------------------------------------

    public function testReconciliationBucketsPaymentsAndKeepsCompletedGross(): void
    {
        $sid = Text::uuid();
        $inv = $this->seedInvoice($this->schoolId, ['student_id' => $sid, 'student_name' => 'Ada Pupil', 'invoice_number' => 'INV-001']);
        $p1 = $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 60000, 'state' => 'completed', 'receipt_number' => 'RCP-001']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 40000, 'state' => 'completed']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 25000, 'state' => 'pending']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 15000, 'state' => 'failed', 'failed_on' => '2025-09-16']);
        $this->seedPayment($this->schoolId, $inv, $sid, ['amount' => 10000, 'state' => 'reversed']);
        $this->seedRefund($this->schoolId, $p1, $inv, $sid, ['amount' => 20000, 'status' => 'processed']);
        $this->seedRefund($this->schoolId, $p1, $inv, $sid, ['amount' => 5000, 'status' => 'pending']);

        $recon = $this->fees()->reconciliation();

        $this->assertSame(2, $recon['completedCount']);
        $this->assertSame(100000, $recon['completedAmount'], 'completed total is GROSS, not net of refunds');
        $this->assertSame(25000, $recon['pendingAmount']);
        $this->assertSame(1, $recon['reversedCount']);
        $this->assertSame(20000, $recon['refundedAmount']);
        $this->assertSame(1, $recon['refundedCount']);
        $this->assertCount(1, $recon['pending']);
        $this->assertCount(1, $recon['failed']);

        $this->assertCount(1, $recon['refundsPending']);
        $row = $recon['refundsPending'][0];
        $this->assertSame('Ada Pupil', $row['studentName']);
        $this->assertSame('INV-001', $row['invoiceNumber']);
        $this->assertSame('RCP-001', $row['receiptNumber']);
        $this->assertSame(60000, $row['paymentAmount']);
    }

    // --- report -------------------------------------------------------------

    public function testReportAggregatesNetCollectionAndPositiveAwards(): void
    {
        $sid = Text::uuid();
        $i1 = $this->seedInvoice($this->schoolId, [
            'student_id' => $sid,
            'term' => 'First',
            'class_group' => 'JSS 1A',
            'line_items' => [
                ['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge'],
                ['name' => 'Discount', 'amount' => -20000, 'kind' => 'award'],
            ],
            'total' => 80000,
            'due_date' => '2025-12-01',
        ]);
        $this->seedInvoice($this->schoolId, ['term' => 'First', 'class_group' => 'JSS 2B', 'total' => 50000, 'due_date' => '2025-12-01', 'line_items' => [['name' => 'Tuition', 'amount' => 50000, 'kind' => 'charge']]]);
        $this->seedInvoice($this->schoolId, ['term' => 'Second', 'class_group' => 'JSS 1A', 'total' => 30000]); // filtered out
        $this->seedInvoice($this->schoolId, ['term' => 'First', 'status' => 'cancelled', 'total' => 99999]); // excluded
        $p = $this->seedPayment($this->schoolId, $i1, $sid, ['amount' => 30000, 'state' => 'completed']);
        $this->seedRefund($this->schoolId, $p, $i1, $sid, ['amount' => 10000, 'status' => 'processed']); // net paid = 20000

        $report = $this->fees('2025-10-15')->report('First');

        $this->assertSame(130000, $report['totalInvoiced']);
        $this->assertSame(20000, $report['totalCollected'], 'net of the processed refund');
        $this->assertSame(20000, $report['totalAwarded'], 'reported positive');
        $this->assertSame(110000, $report['totalOutstanding']);
        $this->assertSame(2, $report['invoiceCount']);
        $this->assertEqualsWithDelta(20000 / 130000, $report['collectionRate'], 0.0001);

        // byClass sorted by outstanding DESC: JSS 1A (60000) before JSS 2B (50000).
        $this->assertSame('JSS 1A', $report['byClass'][0]['key']);
        $this->assertSame(60000, $report['byClass'][0]['outstanding']);
        $this->assertSame('JSS 2B', $report['byClass'][1]['key']);
    }

    public function testReportCollectionRateIsZeroGuardedWithNoInvoices(): void
    {
        $report = $this->fees()->report('First');

        $this->assertSame(0, $report['invoiceCount']);
        $this->assertSame(0, $report['totalInvoiced']);
        $this->assertSame(0, $report['collectionRate'], 'no divide-by-zero when nothing is invoiced');
    }

    // --- numbering ----------------------------------------------------------

    public function testPrefixUsesShortNameOrFallsBackToSch(): void
    {
        $green = $this->seedSchool('Greenfield Academy', 'Greenfield Academy');
        $this->assertSame('GRE', $this->fees('2025-10-15', $green)->prefix());

        $blank = $this->seedSchool('No Short Name', '');
        $this->assertSame('SCH', $this->fees('2025-10-15', $blank)->prefix());
    }

    public function testInvoiceAndReceiptNumbersIncrementPerScope(): void
    {
        $green = $this->seedSchool('Greenfield Academy', 'Greenfield Academy');
        $fees = $this->fees('2025-10-15', $green);

        $this->assertSame('GRE/INV/2526T1/0001', $fees->nextInvoiceNumber('2526T1'));
        $this->assertSame('GRE/INV/2526T1/0002', $fees->nextInvoiceNumber('2526T1'));
        // A different term has its own sequence.
        $this->assertSame('GRE/INV/2526T2/0001', $fees->nextInvoiceNumber('2526T2'));

        // Receipts are one school-wide sequence.
        $this->assertSame('GRE/RCP/00001', $fees->nextReceiptNumber());
        $this->assertSame('GRE/RCP/00002', $fees->nextReceiptNumber());
    }

    // --- helpers ------------------------------------------------------------

    /** Seed a student and return its loaded entity (Fees reads class_group). */
    private function makeStudent(string $id, string $classGroup, array $over = [])
    {
        $this->seedStudent($this->schoolId, ['id' => $id, 'class_group' => $classGroup] + $over);

        return $this->locator->get('EmsStudents')->get($id);
    }
}
