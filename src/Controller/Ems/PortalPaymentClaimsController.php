<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * The family's payment settlement front door (document.md §3.19). A linked
 * guardian declares an offline payment (transfer / POS / cheque) they have made
 * against one of their ward's outstanding invoices. The declaration enters the
 * SAME ems_payment_submissions queue a bursar-entered claim does — stamped
 * provenance 'parent' — and an administrator verifies and posts it through the
 * unchanged approval path.
 *
 * Cash is never offered here: it needs an open daily cash batch at the bursary,
 * which a remote family has no part in. Only one open declaration is allowed per
 * invoice, so a family can't stack duplicates before a reviewer has looked.
 */
final class PortalPaymentClaimsController extends AppController
{
    /**
     * GET /portal/wards/{studentId}/payment-claims — this ward's own family
     * declarations, newest first, with decision status. Never evidence bytes.
     */
    public function index(string $studentId): Response
    {
        $this->scope()->assertStudentAccess($studentId);
        $rows = $this->tenant()->query('EmsPaymentSubmissions')
            ->where(['student_id' => $studentId, 'provenance' => 'parent'])
            ->orderByDesc('created')
            ->all()->toList();
        $invoicesById = [];
        foreach ($this->tenant()->query('EmsInvoices')->where(['student_id' => $studentId])->all() as $i) {
            $invoicesById[(string)$i->id] = $i;
        }
        $items = [];
        foreach ($rows as $r) {
            $decision = $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'payment_submission', 'request_id' => (string)$r->id])
                ->first();
            $items[] = $this->claimWire($r, $invoicesById[(string)$r->invoice_id] ?? null, $decision);
        }

        return $this->json(['items' => $items, 'total' => count($items)]);
    }

    /**
     * POST /portal/wards/{studentId}/payment-claims — declare a payment against
     * one outstanding invoice. Body: invoiceId, method, reference, amount,
     * receivedOn, evidence{filename,mediaType,base64}.
     */
    public function add(string $studentId): Response
    {
        $this->scope()->assertStudentAccess($studentId);
        $body = $this->body();
        $invoiceId = (string)($body['invoiceId'] ?? '');
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['studentId' => $studentId];
        $replay = $this->financeSecurity()->replay($this->viewer, 'payment_claim.create', $key, $request);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        // The family always speaks as themselves — never trust a browser-supplied
        // payer identity on a declaration.
        $body['payerName'] = $this->viewer->name;
        $body['payerRelationship'] = trim((string)($body['payerRelationship'] ?? 'parent')) ?: 'parent';

        $submissions = $this->fetchTable('EmsPaymentSubmissions');
        $result = $submissions->getConnection()->transactional(function () use ($studentId, $invoiceId, $body, $key, $request) {
            $invoice = $this->tenant()->query('EmsInvoices')
                ->where(['id' => $invoiceId])
                ->epilog('FOR UPDATE')
                ->first();
            if ($invoice === null || (string)$invoice->student_id !== $studentId) {
                $this->fail(404, Messages::INVOICE_NOT_FOUND);
            }
            if ((string)$invoice->status === 'cancelled') {
                $this->fail(422, Messages::INVOICE_CANCELLED_NO_PAYMENTS);
            }
            $result = $this->financeSecurity()->createSubmission($invoice, $this->viewer, $body, [
                'provenance' => 'parent',
                'allowCash' => false,
                'oneOpenPerInvoice' => true,
            ]);
            $this->financeSecurity()->remember($this->viewer, 'payment_claim.create', $key, $request, 201, $result);

            return $result;
        });

        return $this->json($result, 201);
    }

    /**
     * The family-safe claim shape: status and (once decided) the reason, never
     * the evidence bytes or the reviewer's identity beyond the outcome.
     *
     * @return array<string, mixed>
     */
    private function claimWire(EntityInterface $row, ?EntityInterface $invoice, ?EntityInterface $decision): array
    {
        $out = [
            'id' => (string)$row->id,
            'invoiceId' => (string)$row->invoice_id,
            'amount' => (int)$row->amount,
            'method' => (string)$row->method,
            'reference' => $row->normalized_reference === null ? null : (string)$row->normalized_reference,
            'receivedOn' => (string)$row->received_on,
            'submittedOn' => (string)$row->created,
            'status' => $decision ? (string)$decision->decision : 'pending',
        ];
        if ($invoice !== null) {
            $out['invoiceNumber'] = (string)$invoice->invoice_number;
        }
        if ($decision) {
            $out['decision'] = [
                'reason' => (string)$decision->reason,
                'decidedOn' => (string)$decision->decided_at,
            ];
        }

        return $out;
    }
}
