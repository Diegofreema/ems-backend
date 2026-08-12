<?php
declare(strict_types=1);

use Cake\Utility\Text;
use Migrations\BaseMigration;

/**
 * Stage 0 of the completeness pass: subjects become a first-class, per-school
 * entity (`ems_subjects`) and every table that stored a subject NAME string is
 * normalized onto a `subject_id` foreign key. Names now live in exactly one
 * place, so a rename is a single-row update that history follows.
 *
 * Backfill: each existing school's catalogue is seeded with the standard
 * curriculum list PLUS every distinct subject name already present in its data
 * (nothing ever fails to resolve); then each referencing row is joined back to
 * its catalogue row by (school_id, name); then the string columns are dropped.
 * `ems_teachers.subjects` (a JSON array of names) becomes an array of ids.
 */
class CreateSubjectsSchema extends BaseMigration
{
    /** Mirrors the frontend's former SUBJECTS constant — the seed catalogue. */
    private const STANDARD_SUBJECTS = [
        'English', 'Mathematics', 'Basic Science', 'Social Studies',
        'Civic Education', 'Agricultural Science', 'Computer Studies',
        'Business Studies', 'Physical Education', 'Fine Art', 'French',
        'Physics', 'Chemistry', 'Biology', 'Economics', 'Literature',
        'Geography', 'Government',
    ];

    /** Every table with an academic `subject` name column to normalize. */
    private const SUBJECT_TABLES = [
        'ems_exam_schedules',
        'ems_exam_grades',
        'ems_exam_papers',
        'ems_assessments',
        'ems_questions',
        'ems_subject_allocations',
        'ems_timetable_slots',
    ];

    public function up(): void
    {
        // --- The catalogue ---------------------------------------------------
        $this->table('ems_subjects', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('active', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'name'], ['unique' => true])
            ->create();

        // --- Seed every existing school --------------------------------------
        $schools = $this->fetchAll('SELECT id FROM ems_schools');
        foreach ($schools as $school) {
            $schoolId = $school['id'];
            $names = array_fill_keys(self::STANDARD_SUBJECTS, true);

            foreach (self::SUBJECT_TABLES as $tbl) {
                $rows = $this->fetchAll(sprintf(
                    "SELECT DISTINCT subject FROM %s WHERE school_id = '%s'",
                    $tbl,
                    $schoolId,
                ));
                foreach ($rows as $row) {
                    if ($row['subject'] !== '') {
                        $names[$row['subject']] = true;
                    }
                }
            }
            $teachers = $this->fetchAll(sprintf(
                "SELECT subjects FROM ems_teachers WHERE school_id = '%s' AND subjects IS NOT NULL",
                $schoolId,
            ));
            foreach ($teachers as $t) {
                foreach ((array)json_decode((string)$t['subjects'], true) as $name) {
                    if (is_string($name) && $name !== '') {
                        $names[$name] = true;
                    }
                }
            }

            $now = date('Y-m-d H:i:s');
            $inserts = [];
            foreach (array_keys($names) as $name) {
                $inserts[] = [
                    'id' => Text::uuid(),
                    'school_id' => $schoolId,
                    'name' => $name,
                    'active' => 1,
                    'created' => $now,
                    'modified' => $now,
                ];
            }
            if ($inserts !== []) {
                $this->table('ems_subjects')->insert($inserts)->saveData();
            }
        }

        // --- Normalize the seven referencing tables --------------------------
        foreach (self::SUBJECT_TABLES as $tbl) {
            $this->table($tbl)
                ->addColumn('subject_id', 'uuid', ['null' => true, 'default' => null])
                ->update();
            $this->execute(sprintf(
                'UPDATE %s t JOIN ems_subjects s
                    ON s.school_id = t.school_id AND s.name = t.subject
                    SET t.subject_id = s.id',
                $tbl,
            ));
        }

        // Index swaps: drop every index that includes `subject`, recreate on
        // `subject_id`, then drop the string column and pin NOT NULL.
        $this->table('ems_exam_grades')
            ->removeIndex(['school_id', 'exam_id', 'subject'])
            ->removeIndex(['school_id', 'exam_id', 'student_id', 'subject'])
            ->update();
        $this->table('ems_exam_grades')
            ->addIndex(['school_id', 'exam_id', 'subject_id'])
            ->addIndex(['school_id', 'exam_id', 'student_id', 'subject_id'], ['unique' => true])
            ->update();

        $this->table('ems_exam_papers')
            ->removeIndex(['school_id', 'exam_id', 'subject', 'level'])
            ->update();
        $this->table('ems_exam_papers')
            ->addIndex(['school_id', 'exam_id', 'subject_id', 'level'], ['unique' => true])
            ->update();

        $this->table('ems_assessments')
            ->removeIndex(['school_id', 'exam_id', 'class_group_id', 'subject'])
            ->update();
        $this->table('ems_assessments')
            ->addIndex(['school_id', 'exam_id', 'class_group_id', 'subject_id'])
            ->update();

        $this->table('ems_questions')
            ->removeIndex(['school_id', 'subject'])
            ->update();
        $this->table('ems_questions')
            ->addIndex(['school_id', 'subject_id'])
            ->update();

        foreach (self::SUBJECT_TABLES as $tbl) {
            $this->table($tbl)
                ->removeColumn('subject')
                ->changeColumn('subject_id', 'uuid', ['null' => false])
                ->update();
        }

        // --- Teacher qualifications: JSON names → JSON ids -------------------
        $teachers = $this->fetchAll(
            'SELECT id, school_id, subjects FROM ems_teachers WHERE subjects IS NOT NULL',
        );
        foreach ($teachers as $t) {
            $names = (array)json_decode((string)$t['subjects'], true);
            if ($names === []) {
                continue;
            }
            $ids = [];
            foreach ($names as $name) {
                if (!is_string($name) || $name === '') {
                    continue;
                }
                $row = $this->fetchRow(sprintf(
                    "SELECT id FROM ems_subjects WHERE school_id = '%s' AND name = '%s'",
                    $t['school_id'],
                    addslashes($name),
                ));
                if ($row) {
                    $ids[] = $row['id'];
                }
            }
            $this->execute(sprintf(
                "UPDATE ems_teachers SET subjects = '%s' WHERE id = '%s'",
                addslashes(json_encode($ids)),
                $t['id'],
            ));
        }
    }

    public function down(): void
    {
        // Restore the denormalized name columns from the catalogue, then drop it.
        foreach (self::SUBJECT_TABLES as $tbl) {
            $this->table($tbl)
                ->addColumn('subject', 'string', ['limit' => 80, 'null' => false, 'default' => ''])
                ->update();
            $this->execute(sprintf(
                'UPDATE %s t JOIN ems_subjects s ON s.id = t.subject_id SET t.subject = s.name',
                $tbl,
            ));
            $this->table($tbl)->removeColumn('subject_id')->update();
        }
        $teachers = $this->fetchAll(
            'SELECT id, subjects FROM ems_teachers WHERE subjects IS NOT NULL',
        );
        foreach ($teachers as $t) {
            $ids = (array)json_decode((string)$t['subjects'], true);
            $names = [];
            foreach ($ids as $id) {
                $row = is_string($id)
                    ? $this->fetchRow(sprintf("SELECT name FROM ems_subjects WHERE id = '%s'", $id))
                    : null;
                if ($row) {
                    $names[] = $row['name'];
                }
            }
            $this->execute(sprintf(
                "UPDATE ems_teachers SET subjects = '%s' WHERE id = '%s'",
                addslashes(json_encode($names)),
                $t['id'],
            ));
        }
        $this->table('ems_subjects')->drop()->save();
    }
}
