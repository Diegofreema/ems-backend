<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

final class FinanceDatabaseIntegrityTest extends EmsDbTestCase
{
    public function testImmutableTablesHaveUpdateAndDeleteGuards(): void
    {
        $rows = $this->db->execute("SELECT EVENT_OBJECT_TABLE,ACTION_TIMING,EVENT_MANIPULATION,ACTION_STATEMENT FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA=DATABASE() AND TRIGGER_NAME LIKE 'trg_ems_%'")->fetchAll('assoc');
        $guarded = [];
        foreach ($rows as $row) {
            $this->assertSame('BEFORE', $row['ACTION_TIMING']);
            $this->assertStringContainsString("SIGNAL SQLSTATE '45000'", $row['ACTION_STATEMENT']);
            $guarded[$row['EVENT_OBJECT_TABLE']][$row['EVENT_MANIPULATION']] = true;
        }
        foreach ([
            'ems_payments', 'ems_receipts', 'ems_finance_ledger_events',
            'ems_finance_evidence', 'ems_finance_decisions', 'ems_audit_events',
            'ems_payment_submissions', 'ems_finance_adjustment_requests',
            'ems_finance_adjustment_payouts', 'ems_bank_statement_batches',
            'ems_bank_statement_rows', 'ems_fee_plan_versions',
            'ems_invoice_change_requests', 'ems_invoice_events', 'ems_cash_batches',
        ] as $table) {
            $this->assertTrue($guarded[$table]['UPDATE'] ?? false, $table . ' update guard');
            $this->assertTrue($guarded[$table]['DELETE'] ?? false, $table . ' delete guard');
        }
    }

    public function testMoneyAndSeparationConstraintsExist(): void
    {
        $rows = $this->db->execute("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND CONSTRAINT_NAME IN ('chk_submission_amount','chk_finance_separation','chk_posted_payment_amount','chk_ledger_signed_amount','fk_payment_student_secure','fk_ledger_invoice')")->fetchAll('assoc');
        $this->assertSame(6, count($rows));
    }
}
