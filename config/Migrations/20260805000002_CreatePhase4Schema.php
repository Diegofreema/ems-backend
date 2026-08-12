<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Phase 4 schema — Fees & payments (document.md §3.7).
 *
 * Five stored entities: fee structures (what a level owes), fee awards
 * (scholarships/discounts applied only at issue), invoices (what one student
 * owes), payments (money received) and refunds (money paid back). Balances,
 * payment statuses, ledgers and the financial report are all DERIVED by
 * App\Ems\Fees from these rows — never stored — so the numbers can never drift
 * from the money received.
 *
 * Money convention (§1.5 / §3.7): every amount is integer **kobo** — never a
 * float, never Naira. A full year's fee times a roster runs into billions of
 * kobo, so every money column is BIGINT. Line items, instalments, schedule
 * revisions, award schedules and paid-schedule rows ride as JSON value objects.
 * "On" timestamps are plain dates (the mock stamps them from a date-only clock),
 * stored as DATE and echoed back as YYYY-MM-DD.
 *
 * Same conventions as earlier phases: `ems_` prefix, CHAR(36) UUID PKs, a
 * `school_id` on every tenant table. Financial records are append-only (§2):
 * a reversal, a refund and a schedule revision are new rows or new JSON history,
 * never a destructive edit.
 */
class CreatePhase4Schema extends BaseMigration
{
    public function up(): void
    {
        // --- Fee structures (the bill a whole level owes for a term) ---------
        $this->table('ems_fee_structures', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('session', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('term', 'string', ['limit' => 6, 'null' => false])
            ->addColumn('level', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('items', 'json', ['null' => false])
            ->addColumn('total', 'biginteger', ['null' => false, 'default' => 0])
            ->addColumn('schedule', 'json', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'term'])
            ->create();

        // --- Fee awards (scholarships & discounts; applied only at issue) -----
        $this->table('ems_fee_awards', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('kind', 'string', ['limit' => 12, 'null' => false])
            ->addColumn('basis', 'string', ['limit' => 12, 'null' => false])
            // percent 1..100, or kobo — always integer.
            ->addColumn('value', 'biginteger', ['null' => false, 'default' => 0])
            ->addColumn('applies_to_item', 'string', ['limit' => 190, 'null' => false, 'default' => 'all'])
            ->addColumn('scope', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('student_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('student_name', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('level', 'string', ['limit' => 40, 'null' => true, 'default' => null])
            ->addColumn('session', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('term', 'string', ['limit' => 6, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'active'])
            ->addColumn('note', 'text', ['null' => true, 'default' => null])
            ->addColumn('awarded_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('awarded_on', 'date', ['null' => false])
            ->addColumn('ended_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('ended_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Invoices (what one student owes; awards land as negative lines) --
        $this->table('ems_invoices', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('invoice_number', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('student_name', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('class_group', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('session', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('term', 'string', ['limit' => 6, 'null' => false])
            ->addColumn('issued_on', 'date', ['null' => false])
            ->addColumn('due_date', 'date', ['null' => false])
            ->addColumn('line_items', 'json', ['null' => false])
            ->addColumn('total', 'biginteger', ['null' => false, 'default' => 0])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'issued'])
            ->addColumn('instalments', 'json', ['null' => true, 'default' => null])
            ->addColumn('schedule_revisions', 'json', ['null' => true, 'default' => null])
            ->addColumn('cancelled_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('cancellation_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'student_id'])
            ->create();

        // --- Payments (money received; only completed count toward balance) --
        $this->table('ems_payments', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('invoice_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('receipt_number', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('amount', 'biginteger', ['null' => false, 'default' => 0])
            ->addColumn('method', 'string', ['limit' => 16, 'null' => false])
            ->addColumn('reference', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('paid_on', 'date', ['null' => false])
            ->addColumn('note', 'text', ['null' => true, 'default' => null])
            ->addColumn('state', 'string', ['limit' => 12, 'null' => false, 'default' => 'completed'])
            ->addColumn('reversed_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('reversal_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('channel', 'string', ['limit' => 10, 'null' => true, 'default' => null])
            ->addColumn('failed_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('failure_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'invoice_id'])
            ->create();

        // --- Refunds (a request, then a decision; money leaves on process) ---
        $this->table('ems_refunds', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('payment_id', 'uuid', ['null' => false])
            ->addColumn('invoice_id', 'uuid', ['null' => false])
            ->addColumn('student_id', 'uuid', ['null' => false])
            ->addColumn('amount', 'biginteger', ['null' => false, 'default' => 0])
            ->addColumn('reason', 'text', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'pending'])
            ->addColumn('requested_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('requested_on', 'date', ['null' => false])
            ->addColumn('decided_by', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('decided_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('decision_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'invoice_id'])
            ->addIndex(['school_id', 'payment_id'])
            ->create();
    }

    public function down(): void
    {
        foreach ([
            'ems_refunds', 'ems_payments', 'ems_invoices',
            'ems_fee_awards', 'ems_fee_structures',
        ] as $table) {
            $this->table($table)->drop()->save();
        }
    }
}
