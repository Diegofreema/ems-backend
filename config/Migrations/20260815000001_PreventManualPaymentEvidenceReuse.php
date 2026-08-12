<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/** A numbered cash acknowledgement may appear only once in its cash batch. */
class PreventManualPaymentEvidenceReuse extends BaseMigration
{
    public function up(): void
    {
        $this->table('ems_payment_submissions')
            ->addIndex(['school_id', 'cash_batch_id', 'cash_acknowledgement'], [
                'unique' => true,
                'name' => 'uq_submission_cash_acknowledgement',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('ems_payment_submissions')
            ->removeIndexByName('uq_submission_cash_acknowledgement')
            ->update();
    }
}
