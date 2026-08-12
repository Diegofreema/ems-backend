<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Phase 3 schema — Academics (document.md §3.1–§3.5).
 *
 * Exams and their sittings, the score matrix (ExamGrade), exam papers built
 * from the question bank, versioned grading schemes (never mutated in place so
 * a released report card stays reproducible), teacher-created assessments and
 * their scores (the derived-CA source), and result releases that pin the
 * grading scheme version used.
 *
 * Same conventions as earlier phases: `ems_` prefix, CHAR(36) UUID PKs, a
 * `school_id` on every tenant table. Money-free — scores are DECIMAL so a
 * half-mark survives; grade bands / question options / paper question lists
 * ride as JSON value objects.
 */
class CreatePhase3Schema extends BaseMigration
{
    public function up(): void
    {
        // --- Exams ----------------------------------------------------------
        $this->table('ems_exams', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('title', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('session', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('term', 'string', ['limit' => 6, 'null' => false])
            ->addColumn('start_date', 'date', ['null' => false])
            ->addColumn('end_date', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'draft'])
            ->addColumn('ca_max', 'integer', ['null' => false, 'default' => 40])
            ->addColumn('exam_max', 'integer', ['null' => false, 'default' => 60])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Exam schedules (sittings) --------------------------------------
        // Times ride as "09:00"-style strings; the wire never carries seconds.
        $this->table('ems_exam_schedules', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('exam_id', 'uuid', ['null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('level', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('start_time', 'string', ['limit' => 5, 'null' => false, 'default' => ''])
            ->addColumn('end_time', 'string', ['limit' => 5, 'null' => false, 'default' => ''])
            ->addColumn('venue', 'string', ['limit' => 120, 'null' => false, 'default' => ''])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'exam_id'])
            ->create();

        // --- Exam grades (the score matrix) ---------------------------------
        // Upsert key: exam_id + student_id + subject. ca/exam are nullable —
        // null is "not entered", never a zero.
        $this->table('ems_exam_grades', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('exam_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('class_group_id', 'uuid', ['null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('ca', 'decimal', ['precision' => 6, 'scale' => 2, 'null' => true, 'default' => null])
            ->addColumn('exam', 'decimal', ['precision' => 6, 'scale' => 2, 'null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'exam_id', 'subject'])
            ->addIndex(['school_id', 'exam_id', 'student_id', 'subject'], ['unique' => true])
            ->create();

        // --- Exam papers (ordered question lists) ---------------------------
        // Upsert key: exam_id + subject + level. question_ids preserves order.
        $this->table('ems_exam_papers', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('exam_id', 'uuid', ['null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('level', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('question_ids', 'json', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'exam_id', 'subject', 'level'], ['unique' => true])
            ->create();

        // --- Result releases (pin the grading scheme version) ---------------
        $this->table('ems_result_releases', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('exam_id', 'uuid', ['null' => false])
            ->addColumn('version', 'integer', ['null' => false])
            ->addColumn('released_on', 'datetime', ['null' => false])
            ->addColumn('released_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('status', 'string', ['limit' => 12, 'null' => false, 'default' => 'released'])
            ->addColumn('superseded_on', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('scheme_version', 'integer', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'exam_id'])
            ->create();

        // --- Grading schemes (versioned scale; never mutated in place) ------
        $this->table('ems_grading_schemes', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('version', 'integer', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'active'])
            ->addColumn('bands', 'json', ['null' => false])
            ->addColumn('effective_from', 'date', ['null' => false])
            ->addColumn('created_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('note', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'version'], ['unique' => true])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Assessments (teacher-created CA source) ------------------------
        $this->table('ems_assessments', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('exam_id', 'uuid', ['null' => false])
            ->addColumn('class_group_id', 'uuid', ['null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('maximum', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('due_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'draft'])
            ->addColumn('created_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('created_on', 'datetime', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'exam_id', 'class_group_id', 'subject'])
            ->create();

        // --- Assessment scores ----------------------------------------------
        // Upsert key: assessment_id + student_id. score null = marked absent.
        $this->table('ems_assessment_scores', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('assessment_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('score', 'decimal', ['precision' => 6, 'scale' => 2, 'null' => true, 'default' => null])
            ->addColumn('absent_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('entered_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'assessment_id'])
            ->addIndex(['school_id', 'assessment_id', 'student_id'], ['unique' => true])
            ->create();

        // --- Question bank --------------------------------------------------
        $this->table('ems_questions', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('level', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('type', 'string', ['limit' => 10, 'null' => false, 'default' => 'objective'])
            ->addColumn('difficulty', 'string', ['limit' => 8, 'null' => false, 'default' => 'medium'])
            ->addColumn('topic', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('text', 'text', ['null' => false])
            ->addColumn('options', 'json', ['null' => false])
            ->addColumn('answer', 'text', ['null' => false, 'default' => ''])
            ->addColumn('marks', 'integer', ['null' => false, 'default' => 1])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'subject'])
            ->create();
    }

    public function down(): void
    {
        foreach ([
            'ems_questions', 'ems_assessment_scores', 'ems_assessments',
            'ems_grading_schemes', 'ems_result_releases', 'ems_exam_papers',
            'ems_exam_grades', 'ems_exam_schedules', 'ems_exams',
        ] as $table) {
            $this->table($table)->drop()->save();
        }
    }
}
