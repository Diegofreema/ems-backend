<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/** Seal the remaining finance context and require a second payout confirmer. */
class ExtendSecureFinanceGuards extends BaseMigration
{
    public function up(): void
    {
        foreach ([
            'ems_payment_submissions', 'ems_finance_adjustment_requests',
            'ems_finance_adjustment_payouts', 'ems_bank_statement_batches',
            'ems_bank_statement_rows', 'ems_fee_plan_versions',
            'ems_invoice_change_requests', 'ems_invoice_events',
        ] as $table) {
            $this->immutable($table);
        }

        $this->execute("CREATE TRIGGER trg_ems_cash_batches_bu BEFORE UPDATE ON ems_cash_batches FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' AND (OLD.closed_at IS NOT NULL OR NOT (NEW.school_id <=> OLD.school_id) OR NOT (NEW.batch_number <=> OLD.batch_number) OR NOT (NEW.collection_date <=> OLD.collection_date) OR NOT (NEW.recorder_user_id <=> OLD.recorder_user_id) OR NOT (NEW.created <=> OLD.created) OR NEW.closed_at IS NULL OR NEW.closed_by_user_id IS NULL OR NEW.counted_amount IS NULL OR NEW.expected_amount <> NEW.counted_amount) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END");
        $this->execute("CREATE TRIGGER trg_ems_cash_batches_bd BEFORE DELETE ON ems_cash_batches FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END");
    }

    public function down(): void
    {
        foreach ([
            'ems_payment_submissions', 'ems_finance_adjustment_requests',
            'ems_finance_adjustment_payouts', 'ems_bank_statement_batches',
            'ems_bank_statement_rows', 'ems_fee_plan_versions',
            'ems_invoice_change_requests', 'ems_invoice_events', 'ems_cash_batches',
        ] as $table) {
            foreach (['bu', 'bd'] as $suffix) {
                $this->execute('DROP TRIGGER IF EXISTS trg_' . $table . '_' . $suffix);
            }
        }
    }

    private function immutable(string $table): void
    {
        foreach (['UPDATE' => 'bu', 'DELETE' => 'bd'] as $operation => $suffix) {
            $this->execute("CREATE TRIGGER trg_{$table}_{$suffix} BEFORE {$operation} ON {$table} FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END");
        }
    }
}
