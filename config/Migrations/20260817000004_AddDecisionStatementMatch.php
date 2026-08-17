<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * A family-declared payment claim (provenance 'parent') arrives with no bank
 * match — the parent cannot see the statement feed. Because ems_payment_submissions
 * is immutable once written, the reviewing administrator cannot attach the match
 * to the claim after the fact. So the statement match becomes part of the
 * verification act: recorded on the (insert-only) decision row at approval.
 *
 * The column is nullable and unused by bursar-entered claims, which still carry
 * their match on the claim itself.
 */
class AddDecisionStatementMatch extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_finance_decisions')
            ->addColumn('statement_row_id', 'char', [
                'limit' => 36,
                'null' => true,
                'default' => null,
                'after' => 'request_id',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_finance_decisions')->removeColumn('statement_row_id')->update();
    }
}
