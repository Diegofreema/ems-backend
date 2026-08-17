<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Fee reminders (document.md §3.7). A bursar sends overdue or due-soon nudges to
 * owing families — an in-app portal row and an e-mail to the primary guardian,
 * cloning the App\Ems\AbsenceAlerts two-phase pattern. This table is the dedup
 * marker log: one append-only row per (invoice, instalment, kind) each time a
 * reminder is sent, so a nightly or repeated run can suppress anyone reminded
 * inside the cooldown window (7 days) rather than re-nagging the same instalment
 * daily. A lump-sum invoice with no schedule uses instalment_number 0.
 */
class CreateFeeReminders extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_fee_reminders', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'char', ['limit' => 36])
            ->addColumn('school_id', 'char', ['limit' => 36])
            ->addColumn('invoice_id', 'char', ['limit' => 36])
            ->addColumn('student_id', 'char', ['limit' => 36])
            ->addColumn('instalment_number', 'integer', ['default' => 0])
            ->addColumn('kind', 'string', ['limit' => 12])
            ->addColumn('reminded_on', 'date')
            ->addColumn('sent_by', 'string', ['limit' => 190])
            ->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'invoice_id', 'instalment_number'])
            ->create();

        foreach (['UPDATE' => 'bu', 'DELETE' => 'bd'] as $operation => $suffix) {
            $this->execute(
                "CREATE TRIGGER trg_ems_fee_reminders_{$suffix} BEFORE {$operation} "
                . "ON ems_fee_reminders FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' "
                . "THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END",
            );
        }
    }

    public function down(): void
    {
        foreach (['bu', 'bd'] as $suffix) {
            $this->execute('DROP TRIGGER IF EXISTS trg_ems_fee_reminders_' . $suffix);
        }
        $this->table('ems_fee_reminders')->drop()->save();
    }
}
