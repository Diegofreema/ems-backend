<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;

/**
 * The finance overview endpoints (document.md §3.7): the reconciliation desk and
 * the financial report. Both are assembled by App\Ems\Fees from the same net-paid
 * authority the invoice screen uses, so collection figures subtract a processed
 * refund exactly as the ledger does.
 */
class FeesController extends AppController
{
    /**
     * GET /reconciliation — provider/office payments by state; completed figures
     * gross, refunded figures processed-only.
     */
    public function reconciliation(): Response
    {
        return $this->json($this->feesEngine()->reconciliation());
    }

    /**
     * GET /fees/report — collected net of processed refunds; byClass sorted by
     * outstanding desc; recentPayments not term-filtered.
     */
    public function report(): Response
    {
        $term = (string)$this->request->getQuery('term', 'all');

        return $this->json($this->feesEngine()->report($term));
    }
}
