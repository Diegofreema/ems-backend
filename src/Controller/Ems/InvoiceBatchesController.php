<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Money;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

/**
 * Bulk invoicing (document.md §3.7). A bursar drafts a batch — an approved plan
 * version, the class groups to bill and a percentage instalment template (or a
 * single due date) — and a different administrator approves it. Approval
 * re-resolves the roster and prices FRESH (issue-time authoritative), then
 * issues N immutable invoices in ONE transaction: all-or-nothing. Anyone already
 * carrying a live invoice from the plan is skipped and reported, never
 * double-billed. The batch row is the request; its decision lives in
 * ems_finance_decisions under request_type invoice_batch, so status is derived.
 */
final class InvoiceBatchesController extends AppController
{
    /**
     * POST /invoice-batches/preview — the dry run. Resolves the roster, prices
     * every student against the plan's awards and the schedule template, and
     * lists who would be issued and who would be skipped. No write.
     */
    public function preview(): Response
    {
        [$plan, $classGroupIds, $classGroupNames, $template, $dueDate] = $this->criteria($this->body());

        return $this->json($this->previewWire($plan, $classGroupIds, $classGroupNames, $template, $dueDate));
    }

    /**
     * POST /invoice-batches — a bursar drafts the batch (criteria only). The
     * preview echoed back is advisory; the roster is recomputed at approval.
     */
    public function add(): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can draft an invoice batch.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay($this->viewer, 'invoice_batch.create', $key, $body);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        [$plan, $classGroupIds, $classGroupNames, $template, $dueDate] = $this->criteria($body);
        $resolved = $this->feesEngine()->resolveInvoiceBatch($plan, $classGroupIds, $classGroupNames, $template, $dueDate);
        if ($resolved['issueCount'] === 0) {
            $this->fail(422, Messages::BATCH_NOTHING_TO_ISSUE);
        }

        $batches = $this->fetchTable('EmsInvoiceBatches');
        $result = $batches->getConnection()->transactional(function () use ($batches, $plan, $classGroupIds, $classGroupNames, $template, $dueDate, $body, $key) {
            $this->financeSecurity()->assertWritable();
            $row = $batches->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'batch_number' => $this->feesEngine()->nextInvoiceBatchNumber(Money::termCode((string)$plan->session, (string)$plan->term)),
                'fee_plan_version_id' => (string)$plan->id,
                'session' => (string)$plan->session,
                'term' => (string)$plan->term,
                'class_groups' => array_values($classGroupNames),
                'class_group_ids' => array_values($classGroupIds),
                'schedule_template' => $template !== [] ? $template : null,
                'due_date' => $template === [] ? $dueDate : null,
                'requested_by_user_id' => $this->viewer->userId,
                'requested_by_name' => $this->viewer->name,
                'created' => FrozenTime::now('UTC'),
            ], ['validate' => false]);
            $batches->saveOrFail($row);
            $wire = $this->batchWire($row, null) + [
                'preview' => $this->previewWire($plan, $classGroupIds, $classGroupNames, $template, $dueDate),
            ];
            $this->audit()->log(
                $this->viewer,
                'invoice_batch.requested',
                'invoice_batch',
                (string)$row->id,
                sprintf(
                    'A bulk invoice batch for %s was drafted and awaits independent approval.',
                    implode(', ', $classGroupNames),
                ),
            );
            $this->financeSecurity()->remember($this->viewer, 'invoice_batch.create', $key, $body, 201, $wire);

            return $wire;
        });

        return $this->json($result, 201);
    }

    /** GET /invoice-batches — every batch with its derived status, newest first. */
    public function index(): Response
    {
        $decisions = [];
        foreach (
            $this->tenant()->query('EmsFinanceDecisions')
                ->where(['request_type' => 'invoice_batch'])
                ->all() as $d
        ) {
            $decisions[(string)$d->request_id] = $d;
        }
        $rowsByBatch = [];
        foreach ($this->tenant()->query('EmsInvoiceBatchRows')->all() as $r) {
            $rowsByBatch[(string)$r->batch_id][] = $r;
        }
        $items = [];
        foreach ($this->tenant()->query('EmsInvoiceBatches')->orderByDesc('created')->all() as $b) {
            $decision = $decisions[(string)$b->id] ?? null;
            $extra = [];
            if ($decision !== null && (string)$decision->decision === 'approved') {
                $batchRows = $rowsByBatch[(string)$b->id] ?? [];
                $issued = array_filter($batchRows, static fn($r) => (string)$r->status === 'issued');
                $extra = ['issueCount' => count($issued), 'skipCount' => count($batchRows) - count($issued)];
            }
            $items[] = $this->batchWire($b, $decision, $extra);
        }

        return $this->json([
            'items' => $items,
            'total' => count($items),
            'page' => 1,
            'pageSize' => count($items),
        ]);
    }

    /**
     * GET /invoice-batches/{id} — detail. A pending batch carries a FRESH
     * preview (recomputed now); a decided batch carries its recorded rows.
     */
    public function view(string $id): Response
    {
        $batch = $this->findBatch($id);
        $decision = $this->decisionFor($id);
        $wire = $this->batchWire($batch, $decision);
        if ($decision === null) {
            $plan = $this->approvedPlan((string)$batch->fee_plan_version_id);
            $template = is_string($batch->schedule_template)
                ? (array)json_decode($batch->schedule_template, true)
                : (array)($batch->schedule_template ?? []);
            $wire['preview'] = $this->previewWire(
                $plan,
                $this->classGroupIdsOf($batch),
                $this->classGroupsOf($batch),
                $template,
                $this->dueDateStr($batch),
            );
        } else {
            $wire['rows'] = $this->batchRowWire($id);
        }

        return $this->json($wire);
    }

    /**
     * POST /invoice-batches/{id}/decision — a different administrator approves or
     * rejects. Approval recomputes the roster and issues every invoice in one
     * transaction; any hard failure rolls the whole batch back.
     */
    public function decide(string $id): Response
    {
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $request = $body + ['id' => $id];
        $replay = $this->financeSecurity()->replay($this->viewer, 'invoice_batch.decide', $key, $request);
        if ($replay !== null) {
            return $this->json($replay['body'], $replay['status']);
        }
        $batches = $this->fetchTable('EmsInvoiceBatches');
        $result = $batches->getConnection()->transactional(function () use ($id, $body, $key, $request) {
            $this->financeSecurity()->assertWritable();
            $batch = $this->tenant()->query('EmsInvoiceBatches')->where(['id' => $id])->first();
            if ($batch === null) {
                $this->fail(404, Messages::BATCH_NOT_FOUND);
            }
            if ((string)$batch->requested_by_user_id === $this->viewer->userId) {
                $this->fail(403, 'The person who drafted this batch cannot decide it.');
            }
            if ($this->decisionFor($id) !== null) {
                $this->fail(409, Messages::BATCH_ALREADY_DECIDED);
            }
            $decision = (string)($body['decision'] ?? '');
            $reason = trim((string)($body['reason'] ?? ''));
            if (!in_array($decision, ['approved', 'rejected'], true) || $reason === '') {
                $this->fail(422, 'Choose approve or reject and give a reason.');
            }

            $decisionRow = $this->recordDecision($batch, $decision, $reason);
            $summary = ['issued' => 0, 'skipped' => 0, 'totalAmount' => 0];
            if ($decision === 'approved') {
                $summary = $this->issueBatch($batch);
            }
            $this->audit()->log(
                $this->viewer,
                'invoice_batch.' . $decision,
                'invoice_batch',
                (string)$batch->id,
                sprintf(
                    'The bulk invoice batch %s was %s%s.',
                    (string)$batch->batch_number,
                    $decision,
                    $decision === 'approved'
                        ? sprintf(' — %d invoice%s issued, %d skipped', $summary['issued'], $summary['issued'] === 1 ? '' : 's', $summary['skipped'])
                        : '',
                ),
                $reason,
            );
            $wire = $this->batchWire($batch, $decisionRow, [
                'issueCount' => $summary['issued'],
                'skipCount' => $summary['skipped'],
                'totalAmount' => $summary['totalAmount'],
            ]);
            $this->financeSecurity()->remember($this->viewer, 'invoice_batch.decide', $key, $request, 200, $wire);

            return $wire;
        });

        return $this->json($result);
    }

    // --- issue ---------------------------------------------------------------

    /**
     * Issue every invoice in an approved batch atomically, recording a batch row
     * per student (issued with its invoice id, or skipped with a reason).
     *
     * @return array{issued:int, skipped:int, totalAmount:int}
     */
    private function issueBatch(EntityInterface $batch): array
    {
        $plan = $this->approvedPlan((string)$batch->fee_plan_version_id);
        $template = is_string($batch->schedule_template)
            ? (array)json_decode($batch->schedule_template, true)
            : (array)($batch->schedule_template ?? []);
        $resolved = $this->feesEngine()->resolveInvoiceBatch(
            $plan,
            $this->classGroupIdsOf($batch),
            $this->classGroupsOf($batch),
            $template,
            $this->dueDateStr($batch),
        );
        if ($resolved['issueCount'] === 0) {
            $this->fail(422, Messages::BATCH_NOTHING_TO_ISSUE);
        }
        $code = Money::termCode((string)$plan->session, (string)$plan->term);
        $invoices = $this->fetchTable('EmsInvoices');
        $rows = $this->fetchTable('EmsInvoiceBatchRows');
        $fees = $this->feesEngine();
        $total = 0;
        foreach ($resolved['toIssue'] as $item) {
            $invoice = $invoices->newEntity([
                'school_id' => $this->viewer->schoolId,
                'fee_plan_version_id' => (string)$plan->id,
                'invoice_number' => $fees->nextInvoiceNumber($code),
                'student_id' => $item['studentId'],
                'student_name' => $item['studentName'],
                'class_group' => $item['classGroup'],
                'session' => (string)$plan->session,
                'term' => (string)$plan->term,
                'issued_on' => FrozenDate::today(),
                'due_date' => $item['dueOn'],
                'line_items' => $item['lineItems'],
                'total' => (int)$item['total'],
                'status' => 'issued',
                'instalments' => $item['instalments'] !== [] ? $item['instalments'] : null,
            ], ['validate' => false]);
            $invoices->saveOrFail($invoice);
            $rows->saveOrFail($rows->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'batch_id' => (string)$batch->id,
                'student_id' => $item['studentId'],
                'invoice_id' => (string)$invoice->id,
                'status' => 'issued',
                'created' => FrozenTime::now('UTC'),
            ], ['validate' => false]));
            $total += (int)$item['total'];
        }
        foreach ($resolved['skipped'] as $item) {
            $rows->saveOrFail($rows->newEntity([
                'id' => Text::uuid(),
                'school_id' => $this->viewer->schoolId,
                'batch_id' => (string)$batch->id,
                'student_id' => $item['studentId'],
                'invoice_id' => null,
                'status' => 'skipped',
                'skip_reason' => (string)$item['reason'],
                'created' => FrozenTime::now('UTC'),
            ], ['validate' => false]));
        }

        return ['issued' => $resolved['issueCount'], 'skipped' => $resolved['skipCount'], 'totalAmount' => $total];
    }

    private function recordDecision(EntityInterface $batch, string $decision, string $reason): EntityInterface
    {
        $decisions = $this->fetchTable('EmsFinanceDecisions');
        $row = $decisions->newEntity([
            'id' => Text::uuid(),
            'school_id' => $this->viewer->schoolId,
            'request_type' => 'invoice_batch',
            'request_id' => (string)$batch->id,
            'decision' => $decision,
            'reason' => $reason,
            'requested_by_user_id' => (string)$batch->requested_by_user_id,
            'decided_by_user_id' => $this->viewer->userId,
            'decided_by_name' => $this->viewer->name,
            'decided_at' => FrozenTime::now('UTC'),
        ], ['validate' => false]);
        $decisions->saveOrFail($row);

        return $row;
    }

    // --- helpers -------------------------------------------------------------

    /**
     * Validate and resolve a batch's criteria from the request body.
     *
     * A class dropdown sends `classGroupIds` (each arm billed on its own id); the
     * legacy `classGroups` name list is still accepted (resolution stays name-
     * based, billing every arm that carries the name); with neither, the batch
     * defaults to every class at the plan's level.
     *
     * @param array<string, mixed> $body
     * @return array{0:\Cake\Datasource\EntityInterface, 1:array<int,string>, 2:array<int,string>, 3:array<int,array<string,mixed>>, 4:?string}
     */
    private function criteria(array $body): array
    {
        $plan = $this->approvedPlan((string)($body['feePlanVersionId'] ?? ''));
        $idInput = array_values(array_filter(array_map(
            static fn($g) => trim((string)$g),
            is_array($body['classGroupIds'] ?? null) ? $body['classGroupIds'] : [],
        ), static fn($g) => $g !== ''));
        $nameInput = array_values(array_filter(array_map(
            static fn($g) => trim((string)$g),
            is_array($body['classGroups'] ?? null) ? $body['classGroups'] : [],
        ), static fn($g) => $g !== ''));

        if ($idInput !== []) {
            $rows = $this->tenant()->query('EmsClassGroups')
                ->select(['id', 'name'])
                ->where(['id IN' => $idInput])
                ->all()
                ->toList();
            $classGroupIds = array_map(static fn($r) => (string)$r->id, $rows);
            $classGroupNames = array_map(static fn($r) => (string)$r->name, $rows);
        } elseif ($nameInput !== []) {
            $classGroupIds = [];
            $classGroupNames = $nameInput;
        } else {
            $rows = $this->classGroupRowsForLevel((string)$plan->level);
            $classGroupIds = array_map(static fn($r) => (string)$r->id, $rows);
            $classGroupNames = array_map(static fn($r) => (string)$r->name, $rows);
        }
        if ($classGroupIds === [] && $classGroupNames === []) {
            $this->fail(422, Messages::BATCH_NO_CLASS_GROUPS);
        }
        $template = $this->feesEngine()->normalizeScheduleTemplate(
            is_array($body['schedule'] ?? null) ? $body['schedule'] : [],
        );
        $dueDate = null;
        if ($template === []) {
            $dueDate = trim((string)($body['dueDate'] ?? ''));
            if ($dueDate === '') {
                $this->fail(422, Messages::BATCH_NEEDS_SCHEDULE_OR_DUE);
            }
        }

        return [$plan, $classGroupIds, $classGroupNames, $template, $dueDate];
    }

    /**
     * The classes defined at a plan's level (the default roster) — id + name.
     *
     * @return array<int,\Cake\Datasource\EntityInterface>
     */
    private function classGroupRowsForLevel(string $level): array
    {
        if ($level === '') {
            return [];
        }

        return $this->tenant()->query('EmsClassGroups')
            ->select(['id', 'name'])
            ->where(['level' => $level])
            ->all()
            ->toList();
    }

    /** @return array<int,string> */
    private function classGroupsOf(EntityInterface $batch): array
    {
        $groups = is_string($batch->class_groups)
            ? (array)json_decode($batch->class_groups, true)
            : (array)$batch->class_groups;

        return array_values(array_map('strval', $groups));
    }

    /**
     * The batch's class ids. Empty for batches drafted before the id column
     * existed — those resolve through their name list instead.
     *
     * @return array<int,string>
     */
    private function classGroupIdsOf(EntityInterface $batch): array
    {
        $ids = is_string($batch->class_group_ids)
            ? (array)json_decode($batch->class_group_ids, true)
            : (array)($batch->class_group_ids ?? []);

        return array_values(array_filter(
            array_map('strval', $ids),
            static fn(string $v): bool => $v !== '',
        ));
    }

    /** ISO the batch's stored due date — a Cake Date's (string) cast is locale-formatted. */
    private function dueDateStr(EntityInterface $batch): ?string
    {
        $value = $batch->due_date;
        if ($value === null) {
            return null;
        }

        return is_object($value) && method_exists($value, 'format') ? $value->format('Y-m-d') : (string)$value;
    }

    private function decisionFor(string $batchId): ?EntityInterface
    {
        return $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'invoice_batch', 'request_id' => $batchId])
            ->first();
    }

    private function findBatch(string $id): EntityInterface
    {
        return $this->findOr404('EmsInvoiceBatches', $id, Messages::BATCH_NOT_FOUND);
    }

    private function approvedPlan(string $id): EntityInterface
    {
        $plan = $this->tenant()->query('EmsFeePlanVersions')->where(['id' => $id])->first();
        if (!$plan) {
            $this->fail(422, 'Choose an approved fee plan version.');
        }
        $approved = $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'fee_plan_version', 'request_id' => $id, 'decision' => 'approved'])
            ->count() > 0;
        if (!$approved) {
            $this->fail(422, 'Invoices can only be issued from an approved fee plan version.');
        }

        return $plan;
    }

    /**
     * The advisory dry-run: the resolved roster with per-student totals and the
     * skip list, plus the batch-wide totals.
     *
     * @param array<int,string> $classGroupIds
     * @param array<int,string> $classGroupNames
     * @param array<int,array<string,mixed>> $template
     * @return array<string,mixed>
     */
    private function previewWire(
        EntityInterface $plan,
        array $classGroupIds,
        array $classGroupNames,
        array $template,
        ?string $dueDate,
    ): array {
        $resolved = $this->feesEngine()->resolveInvoiceBatch($plan, $classGroupIds, $classGroupNames, $template, $dueDate);

        return [
            'feePlanVersionId' => (string)$plan->id,
            'session' => (string)$plan->session,
            'term' => (string)$plan->term,
            'level' => (string)$plan->level,
            'classGroupIds' => array_values($classGroupIds),
            'classGroups' => array_values($classGroupNames),
            'schedule' => array_values($template),
            'dueDate' => $dueDate,
            'toIssue' => $resolved['toIssue'],
            'skipped' => $resolved['skipped'],
            'issueCount' => $resolved['issueCount'],
            'skipCount' => $resolved['skipCount'],
            'studentCount' => $resolved['studentCount'],
            'totalAmount' => $resolved['totalAmount'],
        ];
    }

    /**
     * @param array<string,mixed> $extra
     * @return array<string,mixed>
     */
    private function batchWire(EntityInterface $b, ?EntityInterface $decision, array $extra = []): array
    {
        $template = is_string($b->schedule_template)
            ? (array)json_decode($b->schedule_template, true)
            : (array)($b->schedule_template ?? []);
        $out = [
            'id' => (string)$b->id,
            'batchNumber' => (string)$b->batch_number,
            'feePlanVersionId' => (string)$b->fee_plan_version_id,
            'session' => (string)$b->session,
            'term' => (string)$b->term,
            'classGroupIds' => $this->classGroupIdsOf($b),
            'classGroups' => $this->classGroupsOf($b),
            'schedule' => array_values($template),
            'dueDate' => $this->dueDateStr($b),
            'requestedBy' => (string)$b->requested_by_name,
            'requestedOn' => (string)$b->created,
            'status' => $decision ? (string)$decision->decision : 'pending',
        ];
        if ($decision !== null) {
            $out['decision'] = [
                'reason' => (string)$decision->reason,
                'decidedBy' => (string)$decision->decided_by_name,
                'decidedAt' => (string)$decision->decided_at,
            ];
        }

        return $out + $extra;
    }

    /**
     * The recorded per-student outcome of a decided batch, joined to the issued
     * invoice for its number and total.
     *
     * @return array<int,array<string,mixed>>
     */
    private function batchRowWire(string $batchId): array
    {
        $rows = $this->tenant()->query('EmsInvoiceBatchRows')
            ->where(['batch_id' => $batchId])
            ->all()
            ->toList();
        if ($rows === []) {
            return [];
        }
        $invoiceIds = array_values(array_filter(array_map(static fn($r) => $r->invoice_id !== null ? (string)$r->invoice_id : null, $rows)));
        $invoices = [];
        if ($invoiceIds !== []) {
            foreach (
                $this->tenant()->query('EmsInvoices')
                    ->where(['id IN' => $invoiceIds])
                    ->all() as $i
            ) {
                $invoices[(string)$i->id] = $i;
            }
        }

        return array_map(static function ($r) use ($invoices) {
            $invoice = $r->invoice_id !== null ? ($invoices[(string)$r->invoice_id] ?? null) : null;
            $out = [
                'studentId' => (string)$r->student_id,
                'status' => (string)$r->status,
            ];
            if ($r->skip_reason !== null) {
                $out['skipReason'] = (string)$r->skip_reason;
            }
            if ($invoice !== null) {
                $out['invoiceId'] = (string)$invoice->id;
                $out['invoiceNumber'] = (string)$invoice->invoice_number;
                $out['studentName'] = (string)$invoice->student_name;
                $out['total'] = (int)$invoice->total;
            }

            return $out;
        }, $rows);
    }
}
