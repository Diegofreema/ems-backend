<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Baseline schema for the EMS contract API (/api/ems).
 *
 * Every table is prefixed `ems_` so it can never collide with the 97 legacy
 * `tss` tables (which already use names like `students`, `sessions`, `users`).
 * All primary keys are CHAR(36) UUIDs — the contract requires opaque string
 * ids on the wire. Every tenant-owned table carries `school_id`.
 *
 * See document.md §2 (table catalog) and §3.10–3.14 / §3.18 for the shapes
 * these tables back.
 */
class CreateEmsSchema extends BaseMigration
{
    public function up(): void
    {
        // --- Tenant registry (the only unscoped table) -----------------------
        $this->table('ems_schools', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('slug', 'string', ['limit' => 120, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('short_name', 'string', ['limit' => 80, 'null' => false, 'default' => ''])
            ->addColumn('motto', 'string', ['limit' => 255, 'null' => false, 'default' => ''])
            ->addColumn('address', 'string', ['limit' => 255, 'null' => false, 'default' => ''])
            ->addColumn('logo', 'text', ['limit' => 16777215, 'null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['slug'], ['unique' => true])
            ->create();

        // --- Accounts (settings/auth) ---------------------------------------
        // email is globally unique ACROSS tenants (§3.18).
        // link_* columns serialize the PersonLink discriminated union.
        $this->table('ems_users', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('role', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'active'])
            ->addColumn('added_on', 'date', ['null' => false])
            ->addColumn('password_hash', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('invite_code', 'string', ['limit' => 9, 'null' => true, 'default' => null])
            ->addColumn('link_kind', 'string', ['limit' => 10, 'null' => true, 'default' => null])
            ->addColumn('link_teacher_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('link_student_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('link_student_ids', 'json', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['invite_code'])
            ->addIndex(['school_id'])
            ->create();

        // 30-minute, single-use password reset codes (§3.18).
        $this->table('ems_password_resets', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('code', 'string', ['limit' => 16, 'null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addColumn('used_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['user_id'])
            ->create();

        // --- Students module (§3.10) ----------------------------------------
        $this->table('ems_students', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('admission_number', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('first_name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('date_of_birth', 'date', ['null' => false])
            ->addColumn('gender', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('class_group', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('status', 'string', ['limit' => 12, 'null' => false, 'default' => 'enrolled'])
            ->addColumn('guardian_name', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('guardian_phone', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('enrolled_on', 'date', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->addIndex(['school_id', 'class_group'])
            ->addIndex(['school_id', 'admission_number'])
            ->create();

        $this->table('ems_guardians', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('first_name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('relationship', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('email', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('occupation', 'string', ['limit' => 120, 'null' => false, 'default' => ''])
            ->addColumn('is_primary', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->addIndex(['student_id'])
            ->create();

        // Per-session placement history — never overwritten by promotion.
        $this->table('ems_enrolments', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('session', 'string', ['limit' => 9, 'null' => false])
            ->addColumn('class_group', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('level', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('started_on', 'date', ['null' => false])
            ->addColumn('ended_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'active'])
            ->addColumn('outcome', 'string', ['limit' => 10, 'null' => true, 'default' => null])
            ->addColumn('promoted_to', 'string', ['limit' => 40, 'null' => true, 'default' => null])
            ->addColumn('average', 'decimal', ['precision' => 5, 'scale' => 1, 'null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->addIndex(['student_id'])
            ->create();

        // Attendance rows — upsert key (school, student, date) (§3.12).
        $this->table('ems_attendance_records', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 8, 'null' => false])
            ->addColumn('note', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'student_id', 'date'], ['unique' => true])
            ->addIndex(['school_id', 'date'])
            ->create();

        // One submitted register per class per date; corrections is a JSON
        // trail of {by, on, reason} appended on each re-submission.
        $this->table('ems_attendance_sessions', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('class_group_id', 'uuid', ['null' => false])
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('submitted_by', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('submitted_on', 'datetime', ['null' => false])
            ->addColumn('corrections', 'json', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'class_group_id', 'date'], ['unique' => true])
            ->create();

        // Migrated per-term academic summaries (transcript history).
        $this->table('ems_academic_term_records', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('session', 'string', ['limit' => 9, 'null' => false])
            ->addColumn('term', 'string', ['limit' => 6, 'null' => false])
            ->addColumn('class_group', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('average', 'decimal', ['precision' => 5, 'scale' => 1, 'null' => false])
            ->addColumn('position', 'integer', ['null' => false])
            ->addColumn('class_size', 'integer', ['null' => false])
            ->addColumn('remark', 'string', ['limit' => 255, 'null' => false, 'default' => ''])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->addIndex(['student_id'])
            ->create();

        // --- Teachers module (§3.11) ----------------------------------------
        $this->table('ems_teachers', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('staff_number', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('first_name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('phone', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('gender', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('subjects', 'json', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'active'])
            ->addColumn('hired_on', 'date', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->create();

        // --- Classes module (§3.12) -----------------------------------------
        $this->table('ems_class_groups', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('level', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('stream', 'string', ['limit' => 10, 'null' => false, 'default' => ''])
            ->addColumn('form_teacher_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('capacity', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->addIndex(['form_teacher_id'])
            ->create();

        $this->table('ems_subject_allocations', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('class_group_id', 'uuid', ['null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('teacher_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->addIndex(['class_group_id'])
            ->addIndex(['teacher_id'])
            ->create();

        $this->table('ems_timetable_slots', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('class_group_id', 'uuid', ['null' => false])
            ->addColumn('day', 'string', ['limit' => 3, 'null' => false])
            ->addColumn('period', 'integer', ['limit' => 3, 'null' => false])
            ->addColumn('subject', 'string', ['limit' => 80, 'null' => false])
            ->addColumn('teacher_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->addIndex(['class_group_id'])
            ->addIndex(['teacher_id'])
            ->create();

        // --- Calendar module (§3.13) ----------------------------------------
        $this->table('ems_academic_sessions', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 9, 'null' => false])
            ->addColumn('starts_on', 'date', ['null' => false])
            ->addColumn('ends_on', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 6, 'null' => false, 'default' => 'open'])
            ->addColumn('closed_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'name'], ['unique' => true])
            ->create();

        $this->table('ems_academic_terms', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('session_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 6, 'null' => false])
            ->addColumn('starts_on', 'date', ['null' => false])
            ->addColumn('ends_on', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 6, 'null' => false, 'default' => 'open'])
            ->addColumn('closed_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['session_id', 'name'], ['unique' => true])
            ->addIndex(['school_id'])
            ->create();

        $this->table('ems_campuses', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 120, 'null' => false])
            ->addColumn('address', 'string', ['limit' => 255, 'null' => false, 'default' => ''])
            ->addColumn('active', 'boolean', ['null' => false, 'default' => true])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->create();

        // --- Audit trail (§1.6) — append-only, no modified column -----------
        $this->table('ems_audit_events', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('actor', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('action', 'string', ['limit' => 60, 'null' => false])
            ->addColumn('entity_type', 'string', ['limit' => 30, 'null' => false])
            ->addColumn('entity_id', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('summary', 'string', ['limit' => 500, 'null' => false])
            ->addColumn('reason', 'string', ['limit' => 500, 'null' => true, 'default' => null])
            ->addColumn('at', 'datetime', ['null' => false])
            ->addIndex(['school_id', 'at'])
            ->addIndex(['school_id', 'entity_type', 'at'])
            ->create();
    }

    public function down(): void
    {
        foreach ([
            'ems_audit_events', 'ems_campuses', 'ems_academic_terms', 'ems_academic_sessions',
            'ems_timetable_slots', 'ems_subject_allocations', 'ems_class_groups', 'ems_teachers',
            'ems_academic_term_records', 'ems_attendance_sessions', 'ems_attendance_records',
            'ems_enrolments', 'ems_guardians', 'ems_students', 'ems_password_resets',
            'ems_users', 'ems_schools',
        ] as $table) {
            $this->table($table)->drop()->save();
        }
    }
}
