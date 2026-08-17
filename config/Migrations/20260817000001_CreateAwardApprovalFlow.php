<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Awards re-enter through the two-person flow: a bursar drafts a grant (the
 * award row itself is the request, like a fee-plan version) and requests an
 * ending through its own request row, so a rejected end request never blocks a
 * later one. Decisions live in ems_finance_decisions under request_type
 * fee_award / fee_award_end.
 */
class CreateAwardApprovalFlow extends BaseMigration
{
    public function up(): void
    {
        // Who drafted the award — the separation-of-duties anchor. Null for
        // rows that predate the approval flow (already active via seed).
        $this->table('ems_fee_awards')
            ->addColumn('created_by_user_id', 'char', ['limit' => 36, 'null' => true, 'default' => null])
            ->update();

        $this->table('ems_fee_award_end_requests', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'char', ['limit' => 36])
            ->addColumn('school_id', 'char', ['limit' => 36])
            ->addColumn('award_id', 'char', ['limit' => 36])
            ->addColumn('reason', 'string', ['limit' => 500])
            ->addColumn('requested_by_user_id', 'char', ['limit' => 36])
            ->addColumn('requested_by_name', 'string', ['limit' => 190])
            ->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'award_id'])
            ->create();

        foreach (['UPDATE' => 'bu', 'DELETE' => 'bd'] as $operation => $suffix) {
            $this->execute(
                "CREATE TRIGGER trg_ems_fee_award_end_requests_{$suffix} BEFORE {$operation} "
                . "ON ems_fee_award_end_requests FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' "
                . "THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END",
            );
        }
    }

    public function down(): void
    {
        foreach (['bu', 'bd'] as $suffix) {
            $this->execute('DROP TRIGGER IF EXISTS trg_ems_fee_award_end_requests_' . $suffix);
        }
        $this->table('ems_fee_award_end_requests')->drop()->save();
        $this->table('ems_fee_awards')->removeColumn('created_by_user_id')->update();
    }
}
