<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Bulk invoicing through the two-person flow (document.md §3.7). A bursar drafts
 * a batch — an approved plan version, the class groups to bill and a percentage
 * instalment template (or a single due date) — and a different administrator
 * approves it. Approval issues N immutable invoices in one transaction and
 * records the per-student outcome in ems_invoice_batch_rows (issued, or skipped
 * with a reason). The batch row itself is the request: its decision lives in
 * ems_finance_decisions under request_type invoice_batch, so status is derived,
 * never mutated, and the batch is immutable once drafted.
 */
class CreateInvoiceBatches extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_invoice_batches', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'char', ['limit' => 36])
            ->addColumn('school_id', 'char', ['limit' => 36])
            ->addColumn('batch_number', 'string', ['limit' => 60])
            ->addColumn('fee_plan_version_id', 'char', ['limit' => 36])
            ->addColumn('session', 'string', ['limit' => 20])
            ->addColumn('term', 'string', ['limit' => 6])
            ->addColumn('class_groups', 'json')
            ->addColumn('schedule_template', 'json', ['null' => true])
            ->addColumn('due_date', 'date', ['null' => true])
            ->addColumn('requested_by_user_id', 'char', ['limit' => 36])
            ->addColumn('requested_by_name', 'string', ['limit' => 190])
            ->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'batch_number'], ['unique' => true])
            ->addIndex(['school_id', 'fee_plan_version_id'])
            ->create();

        $this->table('ems_invoice_batch_rows', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'char', ['limit' => 36])
            ->addColumn('school_id', 'char', ['limit' => 36])
            ->addColumn('batch_id', 'char', ['limit' => 36])
            ->addColumn('student_id', 'char', ['limit' => 36])
            ->addColumn('invoice_id', 'char', ['limit' => 36, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 12])
            ->addColumn('skip_reason', 'string', ['limit' => 190, 'null' => true])
            ->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'batch_id'])
            ->create();

        // Tenant-consistent foreign keys, mirroring the secure ledger's style
        // (the composite (school_id,id) unique keys these reference were added
        // in 20260813000001). invoice_id is nullable — a skipped row has none —
        // and MySQL leaves a NULL foreign key unchecked.
        $this->execute('ALTER TABLE ems_invoice_batch_rows
            ADD CONSTRAINT fk_batch_row_batch FOREIGN KEY (batch_id) REFERENCES ems_invoice_batches(id),
            ADD CONSTRAINT fk_batch_row_student FOREIGN KEY (school_id,student_id) REFERENCES ems_students(school_id,id),
            ADD CONSTRAINT fk_batch_row_invoice FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id)');

        foreach (['ems_invoice_batches', 'ems_invoice_batch_rows'] as $table) {
            foreach (['UPDATE' => 'bu', 'DELETE' => 'bd'] as $operation => $suffix) {
                $this->execute(
                    "CREATE TRIGGER trg_{$table}_{$suffix} BEFORE {$operation} "
                    . "ON {$table} FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' "
                    . "THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END",
                );
            }
        }
    }

    public function down(): void
    {
        foreach (['ems_invoice_batches', 'ems_invoice_batch_rows'] as $table) {
            foreach (['bu', 'bd'] as $suffix) {
                $this->execute('DROP TRIGGER IF EXISTS trg_' . $table . '_' . $suffix);
            }
        }
        $this->table('ems_invoice_batch_rows')->drop()->save();
        $this->table('ems_invoice_batches')->drop()->save();
    }
}
