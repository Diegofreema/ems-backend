<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * Payments — reversals, receipts and the provider confirmation seam
 * (document.md §3.7). Financial records are append-only: a reversal is a state
 * change kept for the audit trail, never a deletion. The confirm endpoint stands
 * in for Paystack's signature-verified webhook/verify — idempotent by design so
 * a replayed event never double-records money.
 */
class PaymentsController extends AppController
{
    /**
     * POST /payments/{id}/reverse — a mistaken entry or bounced cheque. Retained
     * but no longer counts toward the balance.
     */
    public function reverse(string $id): Response
    {
        $this->fail(410, 'Direct reversal has been retired. Create an adjustment request for independent approval.');
    }

    public function requestAdjustment(string $id): Response
    {
        $payment = $this->findPayment($id, Messages::PAYMENT_NOT_FOUND);
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['paymentId' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'adjustment.request', $key, $request);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $table = $this->fetchTable('EmsFinanceAdjustmentRequests');
        $result = $table->getConnection()->transactional(function () use ($payment, $body, $key, $request) {
            $result = $this->financeSecurity()->requestAdjustment($payment, $this->viewer, $body);
            $this->financeSecurity()->remember($this->viewer, 'adjustment.request', $key, $request, 201, $result);

            return $result;
        });

        return $this->json($result, 201);
    }

    /**
     * GET /payments/{id}/receipt — a receipt exists only once the payment is
     * confirmed. A reversed payment still yields its receipt.
     */
    public function receipt(string $id): Response
    {
        $payment = $this->findPayment($id, Messages::RECEIPT_NOT_FOUND);
        if (in_array((string)$payment->state, ['pending', 'failed'], true)) {
            $this->fail(422, Messages::RECEIPT_NOT_CONFIRMED);
        }
        $this->scope()->assertStudentAccess((string)$payment->student_id);
        $invoice = $this->tenant()->query('EmsInvoices')
            ->where(['id' => (string)$payment->invoice_id])
            ->first();
        if ($invoice === null) {
            $this->fail(404, Messages::INVOICE_NOT_FOUND);
        }

        return $this->json($this->feesEngine()->receipt($payment, $invoice));
    }

    /**
     * POST /checkout/{paymentId}/confirm — the provider's verdict. Idempotent:
     * a replayed completed/failed verdict returns unchanged.
     */
    public function confirm(string $id): Response
    {
        $this->fail(503, Messages::ONLINE_PAYMENTS_UNAVAILABLE);
    }

    private function findPayment(string $id, string $notFound): EntityInterface
    {
        return $this->findOr404('EmsPayments', $id, $notFound);
    }
}
