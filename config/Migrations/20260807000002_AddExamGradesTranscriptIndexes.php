<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * The transcript (§3.5) reads a student's grades across every published exam,
 * then the cohort that sat each exam in that student's class. The existing
 * indexes lead (school_id, exam_id, …), so "this student across all exams" and
 * "this class within one exam" both had to scan an exam's whole grade sheet.
 * These two composites turn each into a direct seek:
 *   - (school_id, student_id, exam_id) — the student's grades, all exams at once
 *   - (school_id, exam_id, class_group_id) — one exam's cohort, one class only
 */
class AddExamGradesTranscriptIndexes extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_exam_grades')
            ->addIndex(['school_id', 'student_id', 'exam_id'], ['name' => 'idx_ems_grades_school_student_exam'])
            ->addIndex(['school_id', 'exam_id', 'class_group_id'], ['name' => 'idx_ems_grades_school_exam_class'])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_exam_grades')
            ->removeIndexByName('idx_ems_grades_school_student_exam')
            ->removeIndexByName('idx_ems_grades_school_exam_class')
            ->update();
    }
}
