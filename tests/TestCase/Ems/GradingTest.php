<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Grading;
use App\Ems\Messages;
use Cake\TestSuite\TestCase;

/**
 * Invariant tests for the grading-scale authority (src/Ems/Grading.php). The
 * static half — gradeFor / validateBands / bandsEqual / cleanBands — is pure and
 * decides which band grades a mark and whether a submitted scale is even usable,
 * so a regression here silently mis-grades a report card. (The scheme-lookup half
 * that reads the DB is exercised through the Academics integration tests.)
 */
class GradingTest extends TestCase
{
    /** A valid descending scale to grade against. */
    private const BANDS = [
        ['letter' => 'A', 'min' => 70, 'label' => 'Excellent', 'tone' => 't'],
        ['letter' => 'B', 'min' => 50, 'label' => 'Credit', 'tone' => 't'],
        ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
    ];

    // --- gradeFor -----------------------------------------------------------

    public function testGradeForPicksFirstBandTheMarkClears(): void
    {
        $this->assertSame('A', Grading::gradeFor(85, self::BANDS)['letter']);
        $this->assertSame('A', Grading::gradeFor(70, self::BANDS)['letter']); // exact min is inclusive
        $this->assertSame('B', Grading::gradeFor(69.9, self::BANDS)['letter']);
        $this->assertSame('B', Grading::gradeFor(50, self::BANDS)['letter']);
        $this->assertSame('F', Grading::gradeFor(49, self::BANDS)['letter']);
        $this->assertSame('F', Grading::gradeFor(0, self::BANDS)['letter']);
    }

    public function testGradeForFallsBackToLastBandForOutOfRangeMarks(): void
    {
        // Above 100 still grades A (first cleared); a negative falls to the last band.
        $this->assertSame('A', Grading::gradeFor(150, self::BANDS)['letter']);
        $this->assertSame('F', Grading::gradeFor(-5, self::BANDS)['letter']);
    }

    // --- validateBands ------------------------------------------------------

    public function testValidateBandsAcceptsASoundScale(): void
    {
        $this->assertNull(Grading::validateBands(self::BANDS));
    }

    public function testDefaultBandsAreThemselvesValid(): void
    {
        // The shipped national default must always pass its own validator.
        $this->assertNull(Grading::validateBands(Grading::DEFAULT_BANDS));
    }

    public function testValidateBandsRejectsFewerThanTwo(): void
    {
        $this->assertSame(
            Messages::GRADING_MIN_BANDS,
            Grading::validateBands([['letter' => 'A', 'min' => 0, 'label' => 'All', 'tone' => 't']]),
        );
    }

    public function testValidateBandsRejectsEmptyLetterAndLabel(): void
    {
        $this->assertSame(
            Messages::GRADING_LETTER_REQUIRED,
            Grading::validateBands([
                ['letter' => '', 'min' => 50, 'label' => 'X', 'tone' => 't'],
                ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
            ]),
        );
        $this->assertSame(
            sprintf(Messages::GRADING_LABEL_REQUIRED, 'A'),
            Grading::validateBands([
                ['letter' => 'A', 'min' => 50, 'label' => '', 'tone' => 't'],
                ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
            ]),
        );
    }

    public function testValidateBandsRejectsMinOutOfRangeOrNonInteger(): void
    {
        $tooHigh = [
            ['letter' => 'A', 'min' => 120, 'label' => 'X', 'tone' => 't'],
            ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
        ];
        $this->assertSame(sprintf(Messages::GRADING_MIN_RANGE, 'A'), Grading::validateBands($tooHigh));

        $fractional = [
            ['letter' => 'A', 'min' => 70.5, 'label' => 'X', 'tone' => 't'],
            ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
        ];
        $this->assertSame(sprintf(Messages::GRADING_MIN_RANGE, 'A'), Grading::validateBands($fractional));
    }

    public function testValidateBandsAcceptsAWholeFloatMin(): void
    {
        // JSON has no integer type: 70.0 is a whole number and must pass.
        $wholeFloat = [
            ['letter' => 'A', 'min' => 70.0, 'label' => 'X', 'tone' => 't'],
            ['letter' => 'F', 'min' => 0.0, 'label' => 'Fail', 'tone' => 't'],
        ];
        $this->assertNull(Grading::validateBands($wholeFloat));
    }

    public function testValidateBandsRejectsNonDescendingMins(): void
    {
        $notDescending = [
            ['letter' => 'A', 'min' => 50, 'label' => 'X', 'tone' => 't'],
            ['letter' => 'B', 'min' => 50, 'label' => 'Y', 'tone' => 't'], // equal, not below
            ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
        ];
        $this->assertSame(Messages::GRADING_DESCENDING, Grading::validateBands($notDescending));
    }

    public function testValidateBandsRequiresLastBandAtZero(): void
    {
        $lastNotZero = [
            ['letter' => 'A', 'min' => 70, 'label' => 'X', 'tone' => 't'],
            ['letter' => 'F', 'min' => 40, 'label' => 'Fail', 'tone' => 't'],
        ];
        $this->assertSame(Messages::GRADING_LAST_ZERO, Grading::validateBands($lastNotZero));
    }

    public function testValidateBandsRejectsDuplicateLettersCaseInsensitively(): void
    {
        $dupes = [
            ['letter' => 'A', 'min' => 70, 'label' => 'X', 'tone' => 't'],
            ['letter' => 'a', 'min' => 50, 'label' => 'Y', 'tone' => 't'], // same letter, other case
            ['letter' => 'F', 'min' => 0, 'label' => 'Fail', 'tone' => 't'],
        ];
        $this->assertSame(Messages::GRADING_DISTINCT_LETTERS, Grading::validateBands($dupes));
    }

    // --- bandsEqual & cleanBands -------------------------------------------

    public function testBandsEqualDetectsIdenticalAndDivergentScales(): void
    {
        $this->assertTrue(Grading::bandsEqual(self::BANDS, self::BANDS));
        // Different length.
        $this->assertFalse(Grading::bandsEqual(self::BANDS, array_slice(self::BANDS, 0, 2)));
        // Same length, one min changed.
        $changed = self::BANDS;
        $changed[0]['min'] = 75;
        $this->assertFalse(Grading::bandsEqual(self::BANDS, $changed));
    }

    public function testCleanBandsTrimsAndRetypesMinToInt(): void
    {
        $clean = Grading::cleanBands([
            ['letter' => ' A ', 'min' => '70', 'label' => '  Excellent ', 'tone' => 't'],
        ]);
        $this->assertSame('A', $clean[0]['letter']);
        $this->assertSame('Excellent', $clean[0]['label']);
        $this->assertSame(70, $clean[0]['min']);
        $this->assertIsInt($clean[0]['min']);
    }
}
