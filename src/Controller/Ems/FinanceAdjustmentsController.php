<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;

final class FinanceAdjustmentsController extends AppController
{
    public function index(): Response
    {
        $items = [];
        foreach ($this->tenant()->query('EmsFinanceAdjustmentRequests')->orderByDesc('created')->all() as $r) {
            $d = $this->tenant()->query('EmsFinanceDecisions')->where(['request_type' => 'adjustment','request_id' => (string)$r->id])->first();
            $items[] = ['id' => (string)$r->id,'paymentId' => (string)$r->payment_id,'invoiceId' => (string)$r->invoice_id,'studentId' => (string)$r->student_id,'kind' => (string)$r->kind,'amount' => (int)$r->amount,'reason' => (string)$r->reason,'requestedBy' => (string)$r->requested_by_name,'status' => $d ? (string)$d->decision : 'pending'];
        
}return $this->json(['items' => $items,'total' => count($items),'page' => 1,'pageSize' => count($items)]);
    }

    public function decide(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'adjustment.decide', $key, $request);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $table = $this->fetchTable('EmsFinanceAdjustmentRequests');
        $result = $table->getConnection()->transactional(function () use ($id, $body, $key, $request) {
            $row = $this->tenant()->query('EmsFinanceAdjustmentRequests')->where(['id' => $id])->epilog('FOR UPDATE')->first();
            if (!$row) {
                $this->fail(404, 'Adjustment request not found.');
            }
            $result = $this->financeSecurity()->postAdjustment($row, $this->viewer, (string)($body['decision'] ?? ''), trim((string)($body['reason'] ?? '')));
            $this->financeSecurity()->remember($this->viewer, 'adjustment.decide', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }

    public function confirmPayout(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'refund.payout', $key, $request);
        if ($replay) {
            return $this->json($replay['body'], $replay['status']);
        }
        $table = $this->fetchTable('EmsFinanceAdjustmentPayouts');
        $result = $table->getConnection()->transactional(function () use ($id, $body, $key, $request) {
            $row = $this->tenant()->query('EmsFinanceAdjustmentRequests')->where(['id' => $id])->epilog('FOR UPDATE')->first();
            if (!$row) {
                $this->fail(404, 'Refund request not found.');
            }
            $result = $this->financeSecurity()->confirmRefundPayout($row, $this->viewer, $body);
            $this->financeSecurity()->remember($this->viewer, 'refund.payout', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }
}
