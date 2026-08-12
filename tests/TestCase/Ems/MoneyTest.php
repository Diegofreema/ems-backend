<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Money;
use Cake\TestSuite\TestCase;

/**
 * Invariant tests for the fees money engine (src/Ems/Money.php). Pure functions
 * over plain values — no database, no clock (`today` is always injected) — so
 * these lock the arithmetic the invoice screen and the financial report both
 * lean on, at the boundaries the E2E suite can't cheaply reach: rounding edges,
 * non-compounding awards, floor-at-zero, and byte-exact currency formatting.
 */
class MoneyTest extends TestCase
{
    // --- jsRound (round half up, non-negative) -----------------------------

    public function testJsRoundRoundsHalfUp(): void
    {
        $this->assertSame(3, Money::jsRound(2.5));
        $this->assertSame(3, Money::jsRound(2.6));
        $this->assertSame(2, Money::jsRound(2.49));
        $this->assertSame(2, Money::jsRound(2.4));
        $this->assertSame(1, Money::jsRound(0.5));
        $this->assertSame(10, Money::jsRound(10));
        $this->assertSame(10, Money::jsRound(10.0));
        $this->assertSame(0, Money::jsRound(0));
    }

    // --- line sums ----------------------------------------------------------

    public function testChargeAndAwardTotalsSplitByKind(): void
    {
        $lines = [
            ['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge'],
            ['name' => 'Books', 'amount' => 20000], // no kind → treated as a charge
            ['name' => 'Discount', 'amount' => -5000, 'kind' => 'award'],
        ];
        $this->assertSame(120000, Money::chargeTotal($lines));
        $this->assertSame(-5000, Money::awardTotal($lines));
    }

    // --- award labels -------------------------------------------------------

    public function testAwardLineLabelReadsForEachBasisAndTarget(): void
    {
        $this->assertSame(
            'Scholarship (50% of the bill)',
            Money::awardLineLabel(['name' => 'Scholarship', 'basis' => 'percentage', 'value' => 50, 'appliesToItem' => 'all']),
        );
        $this->assertSame(
            'Sibling (10% of Tuition)',
            Money::awardLineLabel(['name' => 'Sibling', 'basis' => 'percentage', 'value' => 10, 'appliesToItem' => 'Tuition']),
        );
        // A fixed award on the whole bill reads as just its name.
        $this->assertSame(
            'Bursary',
            Money::awardLineLabel(['name' => 'Bursary', 'basis' => 'fixed', 'value' => 5000, 'appliesToItem' => 'all']),
        );
        $this->assertSame(
            'Book aid (off Books)',
            Money::awardLineLabel(['name' => 'Book aid', 'basis' => 'fixed', 'value' => 5000, 'appliesToItem' => 'Books']),
        );
    }

    // --- award ordering -----------------------------------------------------

    public function testAwardOrderPrefersStudentScopeThenDateThenName(): void
    {
        $student = ['scope' => 'student', 'awardedOn' => '2026-05-01', 'name' => 'Z'];
        $level = ['scope' => 'level', 'awardedOn' => '2026-01-01', 'name' => 'A'];
        // A personal scholarship gets first claim even though it is newer and Z<A.
        $this->assertSame(-1, Money::awardOrder($student, $level));
        $this->assertSame(1, Money::awardOrder($level, $student));

        // Same scope: oldest awarded first.
        $older = ['scope' => 'level', 'awardedOn' => '2026-01-01', 'name' => 'Z'];
        $newer = ['scope' => 'level', 'awardedOn' => '2026-02-01', 'name' => 'A'];
        $this->assertLessThan(0, Money::awardOrder($older, $newer));

        // Same scope and date: by name.
        $a = ['scope' => 'level', 'awardedOn' => '2026-01-01', 'name' => 'Alpha'];
        $b = ['scope' => 'level', 'awardedOn' => '2026-01-01', 'name' => 'Beta'];
        $this->assertLessThan(0, Money::awardOrder($a, $b));
    }

    // --- applyAwards --------------------------------------------------------

    public function testApplyAwardsDoesNotCompoundAndFloorsAtZero(): void
    {
        $charges = [['name' => 'Tuition', 'amount' => 100000]];
        // Two 50%-of-bill awards. Distinct names → deterministic order (AAA, BBB).
        $awards = [
            ['id' => 'a1', 'name' => 'AAA', 'basis' => 'percentage', 'value' => 50, 'appliesToItem' => 'all'],
            ['id' => 'a2', 'name' => 'BBB', 'basis' => 'percentage', 'value' => 50, 'appliesToItem' => 'all'],
        ];
        $result = Money::applyAwards($charges, $awards);

        $this->assertCount(2, $result['applied']);
        // Non-compounding: the SECOND award is still 50% of the ORIGINAL 100000
        // (50000), not 50% of the reduced balance (which would be 25000).
        $this->assertSame(50000, $result['applied'][0]['amount']);
        $this->assertSame(50000, $result['applied'][1]['amount']);

        // Floor at zero: awards remove exactly the charge, never more.
        $awardSum = Money::awardTotal($result['lineItems']);
        $this->assertSame(-100000, $awardSum);
        $this->assertSame(0, Money::chargeTotal($charges) + $awardSum);
    }

    public function testApplyAwardsSkipsAwardForAFeeNotOnTheBill(): void
    {
        $charges = [['name' => 'Tuition', 'amount' => 100000]];
        $awards = [
            ['id' => 'a1', 'name' => 'Boarding aid', 'basis' => 'fixed', 'value' => 5000, 'appliesToItem' => 'Boarding'],
        ];
        $result = Money::applyAwards($charges, $awards);
        $this->assertSame([], $result['applied']);
        $this->assertCount(1, $result['lineItems']); // the charge only
    }

    public function testApplyAwardsCapsFixedAwardAtItsTargetAmount(): void
    {
        $charges = [['name' => 'Tuition', 'amount' => 30000]];
        $awards = [
            // A ₦500 (50000 kobo) fixed award against a ₦300 fee only removes ₦300.
            ['id' => 'a1', 'name' => 'Big', 'basis' => 'fixed', 'value' => 50000, 'appliesToItem' => 'Tuition'],
        ];
        $result = Money::applyAwards($charges, $awards);
        $this->assertSame(30000, $result['applied'][0]['amount']);
    }

    // --- instalment schedules ----------------------------------------------

    public function testInstalmentStatesSettleOldestFirst(): void
    {
        $instalments = [
            ['number' => 1, 'label' => 'First', 'amount' => 40000, 'dueOn' => '2026-01-01'],
            ['number' => 2, 'label' => 'Second', 'amount' => 60000, 'dueOn' => '2026-06-01'],
        ];
        $states = Money::instalmentStates($instalments, 50000, '2026-03-01');

        $this->assertSame(40000, $states[0]['paid']);
        $this->assertSame(0, $states[0]['balance']);
        $this->assertSame('paid', $states[0]['status']);

        // Remaining 10000 lands on the second, which is not yet due → part_paid.
        $this->assertSame(10000, $states[1]['paid']);
        $this->assertSame(50000, $states[1]['balance']);
        $this->assertSame('part_paid', $states[1]['status']);

        $this->assertSame(2, Money::nextDueInstalment($states)['number']);
    }

    public function testInstalmentBecomesOverduePastItsDueDate(): void
    {
        $instalments = [['number' => 1, 'label' => 'First', 'amount' => 40000, 'dueOn' => '2026-01-01']];
        $states = Money::instalmentStates($instalments, 0, '2026-07-01');
        $this->assertSame('overdue', $states[0]['status']);
    }

    public function testNextDueInstalmentNullWhenAllSettled(): void
    {
        $instalments = [['number' => 1, 'label' => 'First', 'amount' => 40000, 'dueOn' => '2026-01-01']];
        $states = Money::instalmentStates($instalments, 40000, '2026-07-01');
        $this->assertNull(Money::nextDueInstalment($states));
    }

    // --- derived invoice status --------------------------------------------

    public function testPaymentStatusForEachOutcome(): void
    {
        $today = '2026-03-01';
        $future = ['total' => 100000, 'dueDate' => '2026-12-01'];
        $past = ['total' => 100000, 'dueDate' => '2026-01-01'];

        $this->assertSame('cancelled', Money::paymentStatusFor(['status' => 'cancelled', 'total' => 100000, 'dueDate' => '2026-12-01'], 0, $today));
        $this->assertSame('paid', Money::paymentStatusFor($future, 100000, $today));
        $this->assertSame('part_paid', Money::paymentStatusFor($future, 50000, $today));
        $this->assertSame('unpaid', Money::paymentStatusFor($future, 0, $today));
        $this->assertSame('overdue', Money::paymentStatusFor($past, 0, $today));
    }

    public function testPaymentStatusOverdueFromInstalmentEvenWhenBillNotYetDue(): void
    {
        $invoice = [
            'total' => 100000,
            'dueDate' => '2026-12-01', // whole bill not due...
            'instalments' => [
                ['number' => 1, 'label' => 'First', 'amount' => 40000, 'dueOn' => '2026-01-01'], // ...but this one is
                ['number' => 2, 'label' => 'Second', 'amount' => 60000, 'dueOn' => '2026-11-01'],
            ],
        ];
        $this->assertSame('overdue', Money::paymentStatusFor($invoice, 0, '2026-03-01'));
    }

    // --- numbering & display ------------------------------------------------

    public function testTermCode(): void
    {
        $this->assertSame('2526T1', Money::termCode('2025/2026', 'First'));
        $this->assertSame('2526T2', Money::termCode('2025/2026', 'Second'));
        $this->assertSame('2526T3', Money::termCode('2025/2026', 'Third'));
        $this->assertSame('2526T1', Money::termCode('2025/2026', 'Unknown')); // fallback
    }

    public function testLevelOfStripsTheStream(): void
    {
        $this->assertSame('JSS 1', Money::levelOf('JSS 1A'));
        $this->assertSame('SSS 3', Money::levelOf('SSS 3B'));
    }

    public function testFormatCurrencyIsByteExact(): void
    {
        // The three values called out in the docblock, verbatim.
        $this->assertSame('₦90,000', Money::formatCurrency(9000000));
        $this->assertSame('₦8,333.25', Money::formatCurrency(833325));
        $this->assertSame('-₦500', Money::formatCurrency(-50000));
        // Zero and sub-naira remainders.
        $this->assertSame('₦0', Money::formatCurrency(0));
        $this->assertSame('₦1', Money::formatCurrency(100));
        $this->assertSame('₦1.05', Money::formatCurrency(105));
    }
}
