<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\HttpException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorInterface;
use Cake\Utility\Text;

/** Server authority for immutable finance writes and their integrity seals. */
final class FinanceSecurity
{
    private LocatorInterface $locator;
    private string $schoolId;
    private Fees $fees;
    private Audit $audit;

    public function __construct(LocatorInterface $locator, string $schoolId, Fees $fees, Audit $audit)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->fees = $fees;
        $this->audit = $audit;
    }

    public function assertWritable(): void
    {
        $locked = $this->tenant('EmsFinanceIntegrityLocks')->where(['cleared_at IS' => null])->count() > 0;
        if ($locked) {
            throw new HttpException('Finance writes are locked because an integrity check failed. Reads remain available.', 423);
        }
        $this->signingKey();
    }

    /** @return array<string,mixed>|null */
    public function replay(Viewer $viewer, string $action, string $key, array $body): ?array
    {
        if (trim($key) === '') {
            throw new HttpException('An Idempotency-Key header is required for every finance write.', 400);
        }
        $row = $this->tenant('EmsFinanceIdempotency')->where([
            'actor_user_id' => $viewer->userId,
            'action' => $action,
            'request_key' => $key,
        ])->first();
        if ($row === null) {
            return null;
        }
        if (!hash_equals((string)$row->request_hash, $this->requestHash($body))) {
            $this->audit->log($viewer, 'finance.idempotency_conflict', 'idempotency', (string)$row->id, 'An idempotency key was reused with different request data.', null, 'denied');
            throw new HttpException('This Idempotency-Key was already used for a different request.', 409);
        }
        $response = is_string($row->response_body) ? json_decode($row->response_body, true) : $row->response_body;

        return ['status' => (int)$row->status_code, 'body' => (array)$response];
    }

    public function remember(Viewer $viewer, string $action, string $key, array $request, int $status, array $response): void
    {
        $table = $this->locator->get('EmsFinanceIdempotency');
        $table->saveOrFail($table->newEntity([
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'actor_user_id' => $viewer->userId,
            'action' => $action, 'request_key' => $key, 'request_hash' => $this->requestHash($request),
            'status_code' => $status, 'response_body' => $response, 'created' => FrozenTime::now('UTC'),
        ], ['validate' => false]));
    }

    public function createSubmission(EntityInterface $invoice, Viewer $viewer, array $body): array
    {
        $this->assertWritable();
        $amount = (int)($body['amount'] ?? 0);
        $method = (string)($body['method'] ?? '');
        $reference = strtoupper(trim((string)($body['reference'] ?? '')));
        $payerName = trim((string)($body['payerName'] ?? ''));
        $relationship = trim((string)($body['payerRelationship'] ?? ''));
        $receivedOn = (string)($body['receivedOn'] ?? '');
        $cashAck = trim((string)($body['cashAcknowledgement'] ?? ''));
        if ($amount <= 0 || !in_array($method, ['cash', 'bank_transfer', 'pos', 'cheque'], true)) {
            throw new HttpException('Enter a valid amount and payment method.', 422);
        }
        if ($payerName === '' || $relationship === '') {
            throw new HttpException('Payer name and relationship to the student are required.', 422);
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $receivedOn);
        if ($date === false || $date->format('Y-m-d') !== $receivedOn || $receivedOn > date('Y-m-d')) {
            throw new HttpException('Enter a valid payment date that is not in the future.', 422);
        }
        if ($method === 'cash' && ($cashAck === '' || trim((string)($body['cashBatchId'] ?? '')) === '')) {
            throw new HttpException('Cash requires a numbered acknowledgement and an open daily cash batch.', 422);
        }
        if ($method !== 'cash' && ($reference === '' || !isset($body['evidence']))) {
            throw new HttpException('Transfer, POS and cheque claims require a reference and private evidence.', 422);
        }
        $balance = (int)$invoice->total - $this->fees->paidFor((string)$invoice->id);
        if ($amount > $balance || $balance <= 0) {
            throw new HttpException('The amount cannot exceed the invoice balance.', 422);
        }

        $id = Text::uuid();
        $evidence = null;
        if ($method !== 'cash') {
            $evidence = $this->saveEvidence('payment_submission', $id, $viewer, (array)$body['evidence']);
        }
        $table = $this->locator->get('EmsPaymentSubmissions');
        $row = $table->newEntity([
            'id' => $id, 'school_id' => $this->schoolId, 'invoice_id' => (string)$invoice->id,
            'student_id' => (string)$invoice->student_id, 'amount' => $amount, 'method' => $method,
            'normalized_reference' => $reference !== '' ? $reference : null,
            'payer_name' => $payerName, 'payer_relationship' => $relationship, 'received_on' => $receivedOn,
            'cash_acknowledgement' => $cashAck !== '' ? $cashAck : null,
            'statement_row_id' => $body['statementRowId'] ?? null, 'cash_batch_id' => $body['cashBatchId'] ?? null,
            'recorded_by_user_id' => $viewer->userId, 'recorded_by_name' => $viewer->name,
            'provenance' => 'offline', 'created' => FrozenTime::now('UTC'),
        ], ['validate' => false]);
        $table->saveOrFail($row);
        $this->audit->log($viewer, 'payment_submission.created', 'payment_submission', $id, 'An offline payment claim was recorded and is awaiting independent verification.', null, 'success', null, ['invoiceId' => (string)$invoice->id, 'studentId' => (string)$invoice->student_id, 'amount' => $amount, 'method' => $method]);

        return $this->submissionWire($row, null, $evidence);
    }

    public function decideSubmission(EntityInterface $submission, Viewer $viewer, string $decision, string $reason): array
    {
        $this->assertWritable();
        if ($viewer->role !== 'administrator') {
            throw new HttpException('Only an administrator can decide a finance request.', 403);
        }
        if ((string)$submission->recorded_by_user_id === $viewer->userId) {
            throw new HttpException('The person who recorded this request cannot approve or reject it.', 403);
        }
        if (!in_array($decision, ['approved', 'rejected'], true) || trim($reason) === '') {
            throw new HttpException('Choose approve or reject and record a reason.', 422);
        }
        if ($this->tenant('EmsFinanceDecisions')->where(['request_type' => 'payment_submission', 'request_id' => (string)$submission->id])->count() > 0) {
            throw new HttpException('This payment submission already has a decision.', 409);
        }
        if ($decision === 'approved') {
            $this->assertSubmissionReconciled($submission);
        }
        $decisions = $this->locator->get('EmsFinanceDecisions');
        $decisionRow = $decisions->newEntity([
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'request_type' => 'payment_submission',
            'request_id' => (string)$submission->id, 'decision' => $decision, 'reason' => trim($reason),
            'requested_by_user_id' => (string)$submission->recorded_by_user_id,
            'decided_by_user_id' => $viewer->userId, 'decided_by_name' => $viewer->name,
            'decided_at' => FrozenTime::now('UTC'),
        ], ['validate' => false]);
        $decisions->saveOrFail($decisionRow);
        $paymentWire = null;
        if ($decision === 'approved') {
            $paymentWire = $this->postApprovedPayment($submission, $viewer);
        }
        $this->audit->log($viewer, 'payment_submission.' . $decision, 'payment_submission', (string)$submission->id, 'The payment submission was ' . $decision . '.', $reason, 'success');

        return ['submission' => $this->submissionWire($submission, $decisionRow), 'payment' => $paymentWire];
    }

    public function postAdjustment(EntityInterface $request, Viewer $viewer, string $decision, string $reason): array
    {
        $this->assertWritable();
        if ($viewer->role !== 'administrator' || (string)$request->requested_by_user_id === $viewer->userId) {
            throw new HttpException('A different administrator must decide this request.', 403);
        }
        if (!in_array($decision, ['approved','rejected'], true) || trim($reason) === '') {
            throw new HttpException('Choose approve or reject and record a reason.', 422);
        }
        if ($this->tenant('EmsFinanceDecisions')->where(['request_type' => 'adjustment','request_id' => (string)$request->id])->count() > 0) {
            throw new HttpException('This adjustment request already has a decision.', 409);
        }
        if ($decision === 'approved' && (string)$request->kind === 'reversal') {
            $posted = $this->ledgerPaidForPayment((string)$request->payment_id);
            if ((int)$request->amount > $posted) {
                throw new HttpException('This reversal exceeds the amount still posted for the payment.', 409);
            }
        }
        $decisions = $this->locator->get('EmsFinanceDecisions');
        $decisionRow = $decisions->newEntity([
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'request_type' => 'adjustment',
            'request_id' => (string)$request->id, 'decision' => $decision, 'reason' => trim($reason),
            'requested_by_user_id' => (string)$request->requested_by_user_id,
            'decided_by_user_id' => $viewer->userId, 'decided_by_name' => $viewer->name, 'decided_at' => FrozenTime::now('UTC'),
        ], ['validate' => false]);
        $decisions->saveOrFail($decisionRow);
        if ($decision === 'approved' && (string)$request->kind === 'reversal') {
            $this->appendLedger((string)$request->invoice_id, (string)$request->student_id, (string)$request->payment_id, (string)$request->id, 'reversal', -(int)$request->amount, 'offline_adjustment');
        }
        $this->audit->log($viewer, 'adjustment.' . $decision, 'adjustment_request', (string)$request->id, 'The finance adjustment was ' . $decision . '.', $reason);

        return ['id' => (string)$request->id, 'decision' => $decision, 'kind' => (string)$request->kind];
    }

    public function requestAdjustment(EntityInterface $payment, Viewer $viewer, array $body): array
    {
        $this->assertWritable();
        if ($viewer->role !== 'bursar') {
            throw new HttpException('Only a bursar can request a payment correction.', 403);
        }
        $kind = (string)($body['kind'] ?? 'reversal');
        $amount = (int)($body['amount'] ?? $payment->amount);
        $reason = trim((string)($body['reason'] ?? ''));
        if (!in_array($kind, ['reversal','refund'], true) || $amount <= 0 || $amount > (int)$payment->amount || $reason === '') {
            throw new HttpException('Enter a valid correction type, amount and reason.', 422);
        }
        $available = $this->ledgerPaidForPayment((string)$payment->id);
        foreach ($this->tenant('EmsFinanceAdjustmentRequests')->where(['payment_id' => (string)$payment->id])->all() as $existing) {
            $decision = $this->tenant('EmsFinanceDecisions')->where(['request_type' => 'adjustment','request_id' => (string)$existing->id])->first();
            $paidOut = (string)$existing->kind === 'refund' && $this->tenant('EmsFinanceAdjustmentPayouts')->where(['adjustment_request_id' => (string)$existing->id])->count() > 0;
            if ($decision === null || ((string)$decision->decision === 'approved' && (string)$existing->kind === 'refund' && !$paidOut)) {
                $available -= (int)$existing->amount;
            }
        }
        if ($amount > $available) {
            throw new HttpException('This correction exceeds the unreserved posted amount.', 409);
        }
        $table = $this->locator->get('EmsFinanceAdjustmentRequests');
        $row = $table->newEntity([
            'id' => Text::uuid(), 'school_id' => $this->schoolId, 'payment_id' => (string)$payment->id,
            'invoice_id' => (string)$payment->invoice_id, 'student_id' => (string)$payment->student_id,
            'kind' => $kind, 'amount' => $amount, 'reason' => $reason,
            'requested_by_user_id' => $viewer->userId, 'requested_by_name' => $viewer->name,
            'created' => FrozenTime::now('UTC'),
        ], ['validate' => false]);
        $table->saveOrFail($row);
        $this->audit->log($viewer, 'adjustment.requested', 'adjustment_request', (string)$row->id, 'A finance correction was requested.', $reason);

        return ['id' => (string)$row->id, 'paymentId' => (string)$payment->id, 'kind' => $kind, 'amount' => $amount, 'status' => 'pending'];
    }

    public function confirmRefundPayout(EntityInterface $request, Viewer $viewer, array $body): array
    {
        $this->assertWritable();
        if ($viewer->role !== 'administrator' || (string)$request->kind !== 'refund') {
            throw new HttpException('Only an administrator can confirm an approved refund payout.', 403);
        }
        $decision = $this->tenant('EmsFinanceDecisions')->where(['request_type' => 'adjustment','request_id' => (string)$request->id,'decision' => 'approved'])->first();
        if (!$decision) {
            throw new HttpException('The refund must be approved before payout can be confirmed.', 422);
        }
        if ((string)$decision->decided_by_user_id === $viewer->userId) {
            throw new HttpException('A different administrator must confirm the refund payout.', 403);
        }
        $rowId = (string)($body['statementRowId'] ?? '');
        $statement = $this->tenant('EmsBankStatementRows')->where(['id' => $rowId,'direction' => 'debit','amount' => (int)$request->amount])->first();
        if (!$statement) {
            throw new HttpException('Match the payout to a debit statement row for the approved amount.', 422);
        }
        if (!isset($body['evidence'])) {
            throw new HttpException('Refund payout evidence is required.', 422);
        }
        $evidence = $this->saveEvidence('refund_payout', (string)$request->id, $viewer, (array)$body['evidence']);
        if ($evidence['scanStatus'] !== 'clean') {
            throw new HttpException('Payout evidence must pass malware scanning before confirmation.', 422);
        }
        $table = $this->locator->get('EmsFinanceAdjustmentPayouts');
        $payout = $table->newEntity(['id' => Text::uuid(),'school_id' => $this->schoolId,'adjustment_request_id' => (string)$request->id,'evidence_id' => $evidence['id'],'statement_row_id' => $rowId,'recorded_by_user_id' => $viewer->userId,'confirmed_by_user_id' => $viewer->userId,'confirmed_at' => FrozenTime::now('UTC'),'created' => FrozenTime::now('UTC')], ['validate' => false]);
        $table->saveOrFail($payout);
        $this->appendLedger((string)$request->invoice_id, (string)$request->student_id, (string)$request->payment_id, (string)$request->id, 'refund', -(int)$request->amount, 'confirmed_refund_payout');
        $this->audit->log($viewer, 'refund.payout_confirmed', 'adjustment_request', (string)$request->id, 'An approved refund payout was reconciled and posted.');

        return ['id' => (string)$request->id,'kind' => 'refund','status' => 'paid','amount' => (int)$request->amount];
    }

    public function submissionWire(EntityInterface $row, ?EntityInterface $decision = null, ?array $evidence = null): array
    {
        $out = ['id' => (string)$row->id,'invoiceId' => (string)$row->invoice_id,'studentId' => (string)$row->student_id,'amount' => (int)$row->amount,'method' => (string)$row->method,'payerName' => (string)$row->payer_name,'payerRelationship' => (string)$row->payer_relationship,'receivedOn' => (string)$row->received_on,'recordedBy' => (string)$row->recorded_by_name,'status' => $decision ? (string)$decision->decision : 'pending'];
        if ($row->normalized_reference) {
            $out['reference'] = (string)$row->normalized_reference;
        }
        if ($row->cash_acknowledgement) {
            $out['cashAcknowledgement'] = (string)$row->cash_acknowledgement;
        }
        if ($row->statement_row_id) {
            $out['statementRowId'] = (string)$row->statement_row_id;
        }
        if ($evidence) {
            $out['evidence'] = $evidence;
        }
        if ($decision) {
            $out['decision'] = ['reason' => (string)$decision->reason,'decidedBy' => (string)$decision->decided_by_name,'decidedAt' => (string)$decision->decided_at];
        }

        return $out;
    }

    private function postApprovedPayment(EntityInterface $submission, Viewer $viewer): array
    {
        $invoice = $this->tenant('EmsInvoices')->where(['id' => (string)$submission->invoice_id])->epilog('FOR UPDATE')->firstOrFail();
        $paid = $this->ledgerPaid((string)$invoice->id);
        if ((int)$submission->amount > (int)$invoice->total - $paid) {
            throw new HttpException('Approval would overpay this invoice.', 409);
        }
        $payments = $this->locator->get('EmsPayments');
        $receivedOn = is_object($submission->received_on) && method_exists($submission->received_on, 'format') ? $submission->received_on->format('Y-m-d') : trim((string)$submission->received_on);
        if ($receivedOn === '') {
            throw new HttpException('The payment submission has no valid received date.', 409);
        }
        $payment = $payments->newEntity([
            'id' => Text::uuid(),'school_id' => $this->schoolId,'submission_id' => (string)$submission->id,
            'invoice_id' => (string)$invoice->id,'student_id' => (string)$invoice->student_id,
            'receipt_number' => $this->fees->nextReceiptNumber(),'amount' => (int)$submission->amount,
            'method' => (string)$submission->method,'reference' => $submission->normalized_reference,
            'paid_on' => $receivedOn,'state' => 'completed','channel' => 'office','provenance' => 'offline_approved',
        ], ['validate' => false]);
        $payments->saveOrFail($payment);
        $this->appendLedger((string)$invoice->id, (string)$invoice->student_id, (string)$payment->id, null, 'payment', (int)$payment->amount, 'offline_approved');
        $paidAfter = $paid + (int)$payment->amount;
        $receipt = $this->locator->get('EmsReceipts');
        $snapshot = ['invoiceNumber' => (string)$invoice->invoice_number,'studentName' => (string)$invoice->student_name,'method' => (string)$payment->method,'reference' => $payment->reference,'paidOn' => (string)$payment->paid_on];
        $receiptRow = $receipt->newEntity([
            'id' => Text::uuid(),'school_id' => $this->schoolId,'payment_id' => (string)$payment->id,'invoice_id' => (string)$invoice->id,
            'student_id' => (string)$invoice->student_id,'receipt_number' => (string)$payment->receipt_number,
            'amount' => (int)$payment->amount,'paid_to_date' => $paidAfter,'balance_after' => (int)$invoice->total - $paidAfter,
            'snapshot' => $snapshot,'issued_at' => FrozenTime::now('UTC'),
        ], ['validate' => false]);
        $receipt->saveOrFail($receiptRow);
        $notifications = $this->locator->get('EmsNotifications');
        $notifications->saveOrFail($notifications->newEntity([
            'id' => Text::uuid(),'school_id' => $this->schoolId,'channel' => 'in_app','kind' => 'finance',
            'subject' => 'Payment verified','body' => 'An offline payment was verified and a receipt is now available.',
            'audience_label' => 'Linked family for student ' . (string)$invoice->student_id,'recipient_count' => 0,
            'sent_on' => date('Y-m-d'),'sent_by' => $viewer->name,'created' => FrozenTime::now('UTC'),'modified' => FrozenTime::now('UTC'),
        ], ['validate' => false]));

        return ['id' => (string)$payment->id,'invoiceId' => (string)$invoice->id,'studentId' => (string)$invoice->student_id,'receiptNumber' => (string)$payment->receipt_number,'amount' => (int)$payment->amount,'method' => (string)$payment->method,'paidOn' => (string)$payment->paid_on,'state' => 'completed'];
    }

    private function appendLedger(string $invoiceId, string $studentId, ?string $paymentId, ?string $adjustmentId, string $type, int $amount, string $provenance): void
    {
        [$keyId,$key] = $this->signingKey();
        $this->locator->get('EmsSchools')->find()->select(['id'])->where(['id' => $this->schoolId])->epilog('FOR UPDATE')->firstOrFail();
        $events = $this->locator->get('EmsFinanceLedgerEvents');
        $chainRows = $this->tenant('EmsFinanceLedgerEvents')
            ->select(['event_hash', 'previous_hash'])
            ->enableHydration(false)
            ->all()
            ->toList();
        try {
            $previous = FinanceChain::tipHash($chainRows);
        } catch (\RuntimeException $exception) {
            throw new HttpException('Finance writes are locked because the ledger chain is invalid.', 423, $exception);
        }
        $id = Text::uuid();
        $at = FrozenTime::now('UTC');
        $canonical = implode('|', [$id,$this->schoolId,$invoiceId,$studentId,$paymentId ?? '',$adjustmentId ?? '',$type,$amount,$provenance,$at->format('Y-m-d H:i:s'),$previous]);
        $row = $events->newEntity(['id' => $id,'school_id' => $this->schoolId,'invoice_id' => $invoiceId,'student_id' => $studentId,'payment_id' => $paymentId,'adjustment_request_id' => $adjustmentId,'event_type' => $type,'amount' => $amount,'provenance' => $provenance,'key_id' => $keyId,'previous_hash' => $previous,'event_hash' => hash_hmac('sha256', $canonical, $key),'occurred_at' => $at], ['validate' => false]);
        $events->saveOrFail($row);
    }

    private function assertSubmissionReconciled(EntityInterface $submission): void
    {
        if ((string)$submission->method === 'cash') {
            $batch = $this->tenant('EmsCashBatches')->where(['id' => (string)$submission->cash_batch_id])->first();
            if (!$batch || $batch->closed_at === null) {
                throw new HttpException('Close and reconcile the daily cash batch before approval.', 422);
            }

            return;
        }
        $evidence = $this->tenant('EmsFinanceEvidence')->where(['owner_type' => 'payment_submission','owner_id' => (string)$submission->id,'scan_status' => 'clean'])->first();
        if (!$evidence) {
            throw new HttpException('Payment evidence must pass malware scanning before approval.', 422);
        }
        $row = $this->tenant('EmsBankStatementRows')->where(['id' => (string)$submission->statement_row_id,'direction' => 'credit','amount' => (int)$submission->amount])->first();
        if (!$row || strtoupper(trim((string)$row->reference)) !== (string)$submission->normalized_reference) {
            throw new HttpException('Match the payment to a credit statement row with the same amount and reference.', 422);
        }
        if ((string)$submission->method === 'cheque' && stripos((string)$row->description, 'cleared') === false) {
            throw new HttpException('A cheque must be cleared before approval.', 422);
        }
    }

    private function saveEvidence(string $ownerType, string $ownerId, Viewer $viewer, array $file): array
    {
        $media = (string)($file['mediaType'] ?? '');
        $name = trim((string)($file['filename'] ?? 'evidence'));
        $body = base64_decode((string)($file['base64'] ?? ''), true);
        if ($body === false || $body === '' || strlen($body) > 10 * 1024 * 1024 || !in_array($media, ['application/pdf','image/jpeg','image/png'], true)) {
            throw new HttpException('Evidence must be a PDF, JPEG or PNG up to 10 MB.', 422);
        }
        $magic = substr($body, 0, 8);
        $valid = ($media === 'application/pdf' && str_starts_with($magic, '%PDF-')) || ($media === 'image/png' && str_starts_with($magic, "\x89PNG\r\n\x1a\n")) || ($media === 'image/jpeg' && str_starts_with($magic, "\xff\xd8\xff"));
        if (!$valid) {
            throw new HttpException('The evidence content does not match its declared file type.', 422);
        }
        $id = Text::uuid();
        $path = $this->schoolId . '/finance/' . $ownerType . '/' . $ownerId . '/' . $id;
        $objects = $this->locator->get('EmsDocumentObjects');
        $objects->saveOrFail($objects->newEntity(['storage_path' => $path,'content_type' => $media,'size_bytes' => strlen($body),'body' => $body], ['validate' => false]));
        $scan = (new ClamAv())->scan($body);
        $table = $this->locator->get('EmsFinanceEvidence');
        $row = $table->newEntity(['id' => $id,'school_id' => $this->schoolId,'owner_type' => $ownerType,'owner_id' => $ownerId,'storage_path' => $path,'filename' => $name,'content_hash' => hash('sha256', $body),'media_type' => $media,'size_bytes' => strlen($body),'scan_status' => $scan,'created_by_user_id' => $viewer->userId,'created' => FrozenTime::now('UTC')], ['validate' => false]);
        $table->saveOrFail($row);

        return ['id' => $id,'filename' => $name,'mediaType' => $media,'sizeBytes' => strlen($body),'scanStatus' => $scan,'contentHash' => (string)$row->content_hash];
    }

    private function ledgerPaid(string $invoiceId): int
    {
        $row = $this->tenant('EmsFinanceLedgerEvents')->select(['total' => 'SUM(amount)'])->where(['invoice_id' => $invoiceId])->first();

        return $row ? (int)$row->total : 0;
    }

    private function ledgerPaidForPayment(string $paymentId): int
    {
        $row = $this->tenant('EmsFinanceLedgerEvents')->select(['total' => 'SUM(amount)'])->where(['payment_id' => $paymentId])->first();

        return $row ? (int)$row->total : 0;
    }

    private function tenant(string $table)
    {
        return $this->locator->get($table)->find()->where([$table . '.school_id' => $this->schoolId]);
    }

    private function requestHash(array $body): string
    {
        return hash('sha256', (string)json_encode($this->sort($body), JSON_UNESCAPED_SLASHES));
    }

    private function sort(array $value): array
    {
        ksort($value);
        foreach ($value as &$v) {
            if (is_array($v)) {
                $v = $this->sort($v);
            }
        }

        return $value;
    }

    private function signingKey(): array
    {
        try {
            return FinanceKeys::active();
        } catch (\RuntimeException $exception) {
            throw new HttpException('Finance signing keys are not configured; writes are disabled.', 503);
        }
    }
}
