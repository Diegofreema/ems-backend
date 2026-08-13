<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Automatic guardian absence alerts. `ems_absence_alerts` is the once-only
 * marker: one row per (school, student, date) means "the guardian has been
 * alerted for this absence" — a correction that flips a student back to absent
 * on the same date finds the marker and stays silent. `ems_portal_notifications`
 * is the family portal's in-app inbox: one row per portal USER (unlike
 * `ems_message_recipients`, which is keyed by guardian contact), with `read_at`
 * carrying the open-marks-all-read state.
 */
class CreateAbsenceAlertTables extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_absence_alerts', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('date', 'date', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'student_id', 'date'], ['unique' => true])
            ->create();

        $this->table('ems_portal_notifications', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('kind', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('body', 'string', ['limit' => 255, 'null' => false, 'default' => ''])
            ->addColumn('student_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('date', 'date', ['null' => true, 'default' => null])
            ->addColumn('read_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'user_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('ems_portal_notifications')->drop()->save();
        $this->table('ems_absence_alerts')->drop()->save();
    }
}
