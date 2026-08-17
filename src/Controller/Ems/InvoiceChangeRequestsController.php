<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\FeeSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

/**
 * The correction path for issued invoices (document.md §3.7). A bursar files a
 * cancel or reschedule request (Invoices::requestChange); a different
 * administrator decides here. Approval applies the change atomically and
 * records an ems_invoice_events row, so an invoice's history is append-only
 * even though the invoice row itself moves.
 */
final class InvoiceChangeRequestsController extends AppController
{
    /**
     * GET /invoice-change-requests — every request with its decision state,
     * newest first, joined to its invoice for the approvals desk.
     */
    public function index(): Response
    {
        $invoicesById = [];
        foreach ($this->tenant()->query('EmsInvoices')->all() as $i) {
            $invoicesById[(string)$i->id] = $i;
        }
        $items = [];
        foreach ($this->tenant()->query('EmsInvoiceChangeRequests')->orderByDesc('created')->all() as $r) {
            $decision = $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'invoice_change', 'request_id' => (string)$r->id])
                ->first();
            $items[] = self::changeRequestWire($r, $invoicesById[(string)$r->invoice_id] ?? null, $decision);
        }

        return $this->json([
            'items' => $items,
            'total' => count($items),
            'page' => 1,
            'pageSize' => count($items),
        ]);
    }

    /**
     * POST /invoice-change-requests/{id}/decision — a different administrator
     * approves or rejects; approval cancels or reschedules the invoice now.
     */
    public function decide(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'invoice_change.decide', $key, $request);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $invoices = $this->fetchTable('EmsInvoices');
        $result = $invoices->getConnection()->transactional(function () use ($id, $body, $key, $request, $invoices) {
            $this->financeSecurity()->assertWritable();
            $row = $this->tenant()->query('EmsInvoiceChangeRequests')->where(['id' => $id])->first();
            if ($row === null) {
                $this->fail(404, 'Invoice change request not found.');
            }
            if ((string)$row->requested_by_user_id === $this->viewer->userId) {
                $this->fail(403, 'The person who requested this change cannot decide it.');
            }
            $existing = $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'invoice_change', 'request_id' => $id])
                ->count();
            if ($existing > 0) {
                $this->fail(409, 'This change request already has a decision.');
            }
            $decision = (string)($body['decision'] ?? '');
            $reason = trim((string)($body['reason'] ?? ''));
            if (!in_array($decision, ['approved', 'rejected'], true) || $reason === '') {
                $this->fail(422, 'Choose approve or reject and give a reason.');
            }
            $invoice = $this->tenant()->query('EmsInvoices')
                ->where(['id' => (string)$row->invoice_id])
                ->epilog('FOR UPDATE')
                ->first();
            if ($invoice === null) {
                $this->fail(404, Messages::INVOICE_NOT_FOUND);
            }
            $decisions = $this->fetchTable('EmsFinanceDecisions');
            $decisionRow = $decisions->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'request_type' => 'invoice_change',
                'request_id' => $id,
                'decision' => $decision,
                'reason' => $reason,
                'requested_by_user_id' => (string)$row->requested_by_user_id,
                'decided_by_user_id' => $this->viewer->userId,
                'decided_by_name' => $this->viewer->name,
                'decided_at' => FrozenTime::now('UTC'),
            ], ['validate' => false]);
            $decisions->saveOrFail($decisionRow);
            if ($decision === 'approved') {
                $this->apply($row, $invoice, (string)$decisionRow->id);
            }
            $this->audit()->log(
                $this->viewer,
                'invoice_change.' . $decision,
                'invoice',
                (string)$invoice->id,
                sprintf(
                    'The %s request for %s was %s.',
                    (string)$row->kind,
                    (string)$invoice->invoice_number,
                    $decision,
                ),
                $reason,
            );
            $result = self::changeRequestWire($row, $invoice, $decisionRow);
            $this->financeSecurity()->remember($this->viewer, 'invoice_change.decide', $key, $request, 200, $result);

            return $result;
        });

        return $this->json($result);
    }

    /** Applies an approved change to the locked invoice and records the event. */
    private function apply(EntityInterface $row, EntityInterface $invoice, string $decisionId): void
    {
        if ((string)$invoice->status === 'cancelled') {
            $this->fail(422, Messages::INVOICE_ALREADY_CANCELLED);
        }
        $payload = is_string($row->payload) ? (array)json_decode($row->payload, true) : (array)$row->payload;
        $invoices = $this->fetchTable('EmsInvoices');
        if ((string)$row->kind === 'cancel') {
            // Re-checked at decision time — money may have landed since the
            // request was filed, and the invariant is authoritative here.
            if ($this->feesEngine()->paidFor((string)$invoice->id) > 0) {
                $this->fail(422, Messages::INVOICE_HAS_PAYMENTS);
            }
            $invoice->status = 'cancelled';
            $invoice->cancelled_on = FrozenDate::today();
            $invoice->cancellation_reason = (string)$row->reason;
            $invoices->saveOrFail($invoice);
            $this->recordEvent($invoice, 'cancelled', ['reason' => (string)$row->reason], $decisionId);

            return;
        }

        // Reschedule: re-validate the requested schedule against the invoice
        // total (both frozen since request time, but the check is cheap), then
        // swap it in, keeping the schedule it replaced as history.
        $next = $this->feesEngine()->buildInstalments(
            (array)($payload['instalments'] ?? []),
            (int)$invoice->total,
        );
        if ($next === []) {
            $this->fail(422, Messages::RESCHEDULE_NEEDS_INSTALMENT);
        }
        $stored = FeeSerializer::invoice($invoice);
        $previous = ($stored['instalments'] ?? []) !== []
            ? $stored['instalments']
            : [[
                'number' => 1,
                'label' => 'Full payment',
                'dueOn' => $stored['dueDate'],
                'amount' => (int)$invoice->total,
            ]];
        $revisions = $stored['scheduleRevisions'] ?? [];
        $revisions[] = [
            'revisedOn' => FrozenDate::today()->format('Y-m-d'),
            'revisedBy' => (string)($payload['requestedByName'] ?? 'Bursar'),
            'agreedWith' => (string)($payload['agreedWith'] ?? ''),
            'reason' => (string)$row->reason,
            'previous' => $previous,
        ];
        $invoice->instalments = $next;
        $invoice->schedule_revisions = $revisions;
        $invoice->due_date = (string)$next[count($next) - 1]['dueOn'];
        $invoices->saveOrFail($invoice);
        $this->recordEvent($invoice, 'rescheduled', [
            'agreedWith' => (string)($payload['agreedWith'] ?? ''),
            'instalments' => $next,
        ], $decisionId);
    }

    /** Append the invoice's own history row (ems_invoice_events, insert-only). */
    private function recordEvent(EntityInterface $invoice, string $kind, array $payload, string $decisionId): void
    {
        $events = $this->fetchTable('EmsInvoiceEvents');
        $events->saveOrFail($events->newEntity([
            'id' => Text::uuid(),
            'school_id' => $this->viewer->schoolId,
            'invoice_id' => (string)$invoice->id,
            'student_id' => (string)$invoice->student_id,
            'kind' => $kind,
            'payload' => $payload,
            'decision_id' => $decisionId,
            'occurred_at' => FrozenTime::now('UTC'),
        ], ['validate' => false]));
    }

    /**
     * The change-request wire shape, shared with Invoices::requestChange so a
     * fresh request and a listed one read identically.
     *
     * @return array<string, mixed>
     */
    public static function changeRequestWire(
        EntityInterface $row,
        ?EntityInterface $invoice,
        ?EntityInterface $decision,
    ): array {
        $payload = is_string($row->payload) ? (array)json_decode($row->payload, true) : (array)$row->payload;
        $out = [
            'id' => (string)$row->id,
            'invoiceId' => (string)$row->invoice_id,
            'kind' => (string)$row->kind,
            'reason' => (string)$row->reason,
            'requestedBy' => (string)($payload['requestedByName'] ?? ''),
            'requestedOn' => (string)$row->created,
            'status' => $decision ? (string)$decision->decision : 'pending',
        ];
        if (isset($payload['agreedWith'])) {
            $out['agreedWith'] = (string)$payload['agreedWith'];
        }
        if (isset($payload['instalments'])) {
            $out['instalments'] = (array)$payload['instalments'];
        }
        if ($invoice !== null) {
            $out['invoiceNumber'] = (string)$invoice->invoice_number;
            $out['studentName'] = (string)$invoice->student_name;
            $out['invoiceStatus'] = (string)$invoice->status;
        }
        if ($decision) {
            $out['decision'] = [
                'reason' => (string)$decision->reason,
                'decidedBy' => (string)$decision->decided_by_name,
                'decidedAt' => (string)$decision->decided_at,
            ];
        }

        return $out;
    }
}
