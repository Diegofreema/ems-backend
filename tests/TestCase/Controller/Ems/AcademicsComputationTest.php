<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Academics;
use App\Ems\Grading;
use App\Ems\SubjectCatalog;
use Cake\Http\Exception\HttpException;
use Cake\ORM\Locator\LocatorInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

/**
 * Integration tests for the academics computation engine (src/Ems/Academics.php)
 * — the most dangerous logic in the system, because it decides the positions and
 * grades that get printed on a child's report card. These drive the real engine
 * against seeded exam/class/grade rows (the DB is genuinely queried) and pin the
 * three invariants the E2E suite can only brush past:
 *
 *   1. rank by average, highest first, with ties SHARING a position;
 *   2. a subject total exists only when BOTH the CA and the exam paper are in;
 *   3. a derived CA is the scored ratio scaled to caMax — a missing mark is
 *      excluded from the ratio (never counted as zero), and an explicit absence
 *      is excused, so "no mark yet", "absent", and "scored 0" are three different
 *      things.
 */
class AcademicsComputationTest extends EmsIntegrationTestCase
{
    /** FK-safe clear order: academics children first, then the base tenant rows. */
    protected const CLEANUP_TABLES = [
        'ems_assessment_scores',
        'ems_exam_grades',
        'ems_assessments',
        'ems_subjects',
        'ems_class_groups',
        'ems_exams',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_users',
        'ems_schools',
    ];

    private LocatorInterface $locator;
    private Academics $academics;
    private string $examId = '';
    private string $classGroupId = '';

    protected function setUp(): void
    {
        parent::setUp();
        SubjectCatalog::invalidate($this->schoolId);
        $this->locator = TableRegistry::getTableLocator();
        $grading = new Grading($this->locator, $this->schoolId);
        $this->academics = new Academics($this->locator, $this->schoolId, $grading);

        // One class + one unpublished exam (so grading uses the active/default
        // scale — no scheme rows needed). ca_max 40 + exam_max 60 = 100.
        $this->classGroupId = Text::uuid();
        $this->insertRow('ems_class_groups', [
            'id' => $this->classGroupId,
            'school_id' => $this->schoolId,
            'name' => 'JSS 1A',
            'level' => 'JSS 1',
            'stream' => 'A',
        ]);
        $this->examId = Text::uuid();
        $this->insertRow('ems_exams', [
            'id' => $this->examId,
            'school_id' => $this->schoolId,
            'title' => 'Mid-term',
            'session' => '2025/2026',
            'term' => 'First',
            'status' => 'grading',
            'start_date' => '2026-01-10',
            'end_date' => '2026-01-20',
            'ca_max' => 40,
            'exam_max' => 60,
        ]);
    }

    // --- 1. ranking with shared ties ---------------------------------------

    public function testBroadsheetRanksByAverageWithTiesSharingAPosition(): void
    {
        $english = $this->seedSubject('English');
        $maths = $this->seedSubject('Mathematics');

        // Averages engineered to 80 / 70 / 70 / 40 — two students tie at 70.
        $s1 = $this->seedStudent('Ade', 'Bola');    // 80, 80 → 80.0
        $s2 = $this->seedStudent('Bello', 'Chidi');  // 60, 80 → 70.0
        $s3 = $this->seedStudent('Cole', 'Dayo');    // 70, 70 → 70.0 (ties s2)
        $s4 = $this->seedStudent('Dele', 'Efe');     // 40, 40 → 40.0

        $this->seedGrade($s1, $english, 30, 50);
        $this->seedGrade($s1, $maths, 30, 50);
        $this->seedGrade($s2, $english, 20, 40);
        $this->seedGrade($s2, $maths, 30, 50);
        $this->seedGrade($s3, $english, 25, 45);
        $this->seedGrade($s3, $maths, 25, 45);
        $this->seedGrade($s4, $english, 10, 30);
        $this->seedGrade($s4, $maths, 10, 30);

        $sheet = $this->academics->broadsheet($this->examEntity(), $this->classGroupEntity());

        $position = [];
        $average = [];
        foreach ($sheet['rows'] as $row) {
            $position[(string)$row['student']['id']] = $row['position'];
            $average[(string)$row['student']['id']] = $row['average'];
        }

        $this->assertEqualsWithDelta(80.0, (float)$average[$s1], 0.001);
        $this->assertEqualsWithDelta(70.0, (float)$average[$s2], 0.001);
        $this->assertEqualsWithDelta(70.0, (float)$average[$s3], 0.001);
        $this->assertEqualsWithDelta(40.0, (float)$average[$s4], 0.001);

        // Top is 1; the two 70s SHARE position 2; the next distinct average is 4
        // (the shared position consumes the rank below it — competition ranking).
        $this->assertSame(1, $position[$s1]);
        $this->assertSame(2, $position[$s2]);
        $this->assertSame(2, $position[$s3]);
        $this->assertSame(4, $position[$s4]);
    }

    // --- 2. a total needs both halves --------------------------------------

    public function testSubjectTotalRequiresBothCaAndExam(): void
    {
        $english = $this->seedSubject('English');
        $maths = $this->seedSubject('Mathematics');
        $student = $this->seedStudent('Solo', 'Ada');

        $this->seedGrade($student, $english, 30, 50);  // complete → total 80
        $this->seedGrade($student, $maths, 30, null);  // exam missing → no total

        $sheet = $this->academics->broadsheet($this->examEntity(), $this->classGroupEntity());
        $row = $sheet['rows'][0];

        $this->assertEqualsWithDelta(80.0, (float)$row['cells']['English']['total'], 0.001);
        $this->assertNotNull($row['cells']['English']['grade']);

        // The half-entered subject yields no total and no grade...
        $this->assertNull($row['cells']['Mathematics']['total']);
        $this->assertNull($row['cells']['Mathematics']['grade']);

        // ...and is excluded from the average, so the average is 80, not 40.
        $this->assertEqualsWithDelta(80.0, (float)$row['average'], 0.001);
    }

    // --- 3. derived CA: missing ≠ absent ≠ zero ----------------------------

    public function testDerivedCaExcludesMissingMarksAndExcusesAbsence(): void
    {
        $english = $this->seedSubject('English');
        $student = $this->seedStudent('Roll', 'Up');

        // Three contributing assessments, 20 marks each.
        $a1 = $this->seedAssessment($english, 'Test 1', 20);
        $a2 = $this->seedAssessment($english, 'Test 2', 20);
        $a3 = $this->seedAssessment($english, 'Test 3', 20);

        $this->seedScore($a1, $student, 10);    // scored 10 / 20
        // a2: no score row at all → "no mark yet" (missing)
        $this->seedScore($a3, $student, null);  // explicit absence → excused

        $ca = $this->academics->derivedCa($this->examId, $this->classGroupId, $english, $student, 40);

        // Ratio is 10/20 (only the scored assessment counts), scaled to caMax 40:
        // round(0.5 * 40) = 20. If the missing mark were treated as 0 the ratio
        // would be 10/40 → 10; asserting 20 proves it is EXCLUDED, not zeroed.
        $this->assertSame(20, $ca['ca']);
        $this->assertSame(1, $ca['scoredCount']);
        $this->assertSame(1, $ca['missingCount']);   // a2 only — the absence isn't "missing"
        $this->assertSame(3, $ca['contributingCount']);
        $this->assertFalse($ca['complete']);          // a mark is still outstanding
    }

    public function testDerivedCaIsNullWhenNothingScored(): void
    {
        $english = $this->seedSubject('English');
        $student = $this->seedStudent('Empty', 'Set');
        $this->seedAssessment($english, 'Test 1', 20);
        $this->seedAssessment($english, 'Test 2', 20);
        // No scores at all → possible is 0 → no derivable CA (no divide-by-zero).

        $ca = $this->academics->derivedCa($this->examId, $this->classGroupId, $english, $student, 40);

        $this->assertNull($ca['ca']);
        $this->assertSame(0, $ca['scoredCount']);
        $this->assertSame(2, $ca['missingCount']);
        $this->assertFalse($ca['complete']);
    }

    // --- 4. the seam fails loudly on an unknown subject --------------------

    public function testGradesheetRejectsAnUnknownSubject(): void
    {
        $this->seedStudent('Loud', 'Fail');
        // Before the seam fix an unknown subject coerced to '' and returned an
        // empty-but-200 gradesheet; now it must refuse with the verbatim 422.
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        $this->academics->gradesheet($this->examEntity(), $this->classGroupEntity(), 'Nonexistent Subject');
    }

    // --- seeding helpers ----------------------------------------------------

    private function examEntity()
    {
        return $this->locator->get('EmsExams')->get($this->examId);
    }

    private function classGroupEntity()
    {
        return $this->locator->get('EmsClassGroups')->get($this->classGroupId);
    }

    private function seedSubject(string $name): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_subjects', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'name' => $name,
            'active' => 1,
        ]);
        SubjectCatalog::invalidate($this->schoolId);

        return $id;
    }

    private function seedStudent(string $last, string $first): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'admission_number' => 'ADM-' . substr($id, 0, 6),
            'first_name' => $first,
            'last_name' => $last,
            'date_of_birth' => '2012-03-04',
            'gender' => 'female',
            'class_group' => 'JSS 1A',
            'status' => 'enrolled',
            'enrolled_on' => '2026-01-10',
        ]);

        return $id;
    }

    private function seedGrade(string $studentId, string $subjectId, ?int $ca, ?int $exam): void
    {
        $this->insertRow('ems_exam_grades', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'exam_id' => $this->examId,
            'student_id' => $studentId,
            'class_group_id' => $this->classGroupId,
            'subject_id' => $subjectId,
            'ca' => $ca,
            'exam' => $exam,
        ]);
    }

    private function seedAssessment(string $subjectId, string $name, int $max): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_assessments', [
            'id' => $id,
            'school_id' => $this->schoolId,
            'exam_id' => $this->examId,
            'class_group_id' => $this->classGroupId,
            'subject_id' => $subjectId,
            'name' => $name,
            'maximum' => $max,
            'status' => 'closed',
            'created_by' => 'Test',
            'created_on' => $this->now(),
        ]);

        return $id;
    }

    private function seedScore(string $assessmentId, string $studentId, ?int $score): void
    {
        $this->insertRow('ems_assessment_scores', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'assessment_id' => $assessmentId,
            'student_id' => $studentId,
            'score' => $score,
            'entered_by' => 'Test',
        ]);
    }
}
