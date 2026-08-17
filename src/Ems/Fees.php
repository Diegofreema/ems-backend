<?php
declare(strict_types=1);

namespace App\Ems;

use App\Ems\Serializer\FeeSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\HttpException;
use Cake\ORM\Locator\LocatorInterface;

/**
 * The fees computation engine (document.md §3.7) — the single backend authority
 * for every derived figure. It layers balances, payment statuses, schedules,
 * ledgers, the reconciliation desk and the financial report on top of the stored
 * rows, all from one net-paid rule so a processed refund shows up everywhere at
 * once. Pure arithmetic lives in App\Ems\Money; this class owns the DB reads and
 * the pricing that turns a bursar's charges into a bill.
 *
 * `today` is captured once per request (the server's real date — §1.5) and
 * threaded into every overdue judgement.
 */
class Fees
{
    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var string
     */
    private string $schoolId;

    /**
     * @var string YYYY-MM-DD
     */
    private string $today;

    /**
     * @var array<string, int>|null memoized net-paid-per-invoice map
     */
    private ?array $netPaid = null;

    /**
     * @var \App\Ems\Tenant|null
     */
    private ?Tenant $tenantScope = null;

    /**
     * @param \Cake\ORM\Locator\LocatorInterface $locator Table locator for tenant-scoped reads.
     */
    public function __construct(LocatorInterface $locator, string $schoolId, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->today = $today;
    }

    /**
     * This engine's tenant-scope choke point — reads narrowed to $this->schoolId
     * by construction. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    // --- numbering (real per-scope DB sequences; the mock's format kept) -----

    /** "{shortName[:3]}" upper, or "SCH" — the invoice/receipt number prefix. */
    public function prefix(): string
    {
        $school = $this->locator->get('EmsSchools')->find()
            ->where(['id' => $this->schoolId])->first();
        $p = strtoupper(substr($school !== null ? (string)$school->short_name : '', 0, 3));

        return $p !== '' ? $p : 'SCH';
    }

    /** "{PREFIX}/INV/{termCode}/{seq 4-digit}" — per-term sequence. */
    public function nextInvoiceNumber(string $termCode): string
    {
        $seq = (new Sequences($this->locator))->next($this->schoolId, 'invoice:' . $termCode, 0);

        return sprintf('%s/INV/%s/%04d', $this->prefix(), $termCode, $seq);
    }

    /** "{PREFIX}/RCP/{seq 5-digit}" — school-wide sequence. */
    public function nextReceiptNumber(): string
    {
        $seq = (new Sequences($this->locator))->next($this->schoolId, 'receipt', 0);

        return sprintf('%s/RCP/%05d', $this->prefix(), $seq);
    }

    // --- net paid (every balance derives from this) -------------------------

    /**
     * Net paid per invoice for the whole school in one pass:
     * Σ completed payments − Σ processed refunds. Memoized.
     *
     * @return array<string, int>
     */
    public function netPaidByInvoice(): array
    {
        if ($this->netPaid !== null) {
            return $this->netPaid;
        }
        $map = [];
        $events = $this->tenant()->query('EmsFinanceLedgerEvents')
            ->select(['invoice_id', 'amount'])
            ->all();
        foreach ($events as $event) {
            $map[(string)$event->invoice_id] = ($map[(string)$event->invoice_id] ?? 0) + (int)$event->amount;
        }

        return $this->netPaid = $map;
    }

    /** Net paid for one invoice (completed payments less processed refunds). */
    public function paidFor(string $invoiceId): int
    {
        return $this->netPaidByInvoice()[$invoiceId] ?? 0;
    }

    /** Batched student status; callers never accept this value on writes. */
    public function statusesForStudents(array $studentIds): array
    {
        $studentIds = array_values(array_unique(array_filter(array_map('strval', $studentIds))));
        if ($studentIds === []) {
            return [];
        }
        $invoices = $this->tenant()->query('EmsInvoices')->select(['id','student_id','total','due_date','status'])->where(['student_id IN' => $studentIds])->all()->toList();
        $invoiceIds = array_map(static fn($i) => (string)$i->id, $invoices);
        $paid = [];
        $pending = [];
        if ($invoiceIds !== []) {
            foreach ($this->tenant()->query('EmsFinanceLedgerEvents')->select(['invoice_id','amount'])->where(['invoice_id IN' => $invoiceIds])->all() as $e) {
                $paid[(string)$e->invoice_id] = ($paid[(string)$e->invoice_id] ?? 0) + (int)$e->amount;
            }
            $decided = [];
            foreach ($this->tenant()->query('EmsFinanceDecisions')->select(['request_id'])->where(['request_type' => 'payment_submission'])->all() as $d) {
                $decided[(string)$d->request_id] = true;
            }
            foreach ($this->tenant()->query('EmsPaymentSubmissions')->select(['id','student_id'])->where(['invoice_id IN' => $invoiceIds])->all() as $s) {
                if (!isset($decided[(string)$s->id])) {
                    $pending[(string)$s->student_id] = true;
                }
            }
        }
        $groups = [];
        foreach ($invoices as $i) {
            if ((string)$i->status !== 'cancelled') {
                $groups[(string)$i->student_id][] = $i;
            }
        }
        $out = [];
        foreach ($studentIds as $studentId) {
            $rows = $groups[$studentId] ?? [];
            if ($rows === []) {
                $out[$studentId] = 'not_invoiced';
                continue;
            }$hasBalance = false;
            $part = false;
            $overdue = false;
            foreach ($rows as $i) {
                $p = $paid[(string)$i->id] ?? 0;
                $balance = (int)$i->total - $p;
                if ($balance > 0) {
                    $hasBalance = true;
                    if ($p > 0) {
                        $part = true;
                    }if ((string)$i->due_date < $this->today) {
                        $overdue = true;
                    }
                }
            }$out[$studentId] = $overdue ? 'overdue' : (isset($pending[$studentId]) ? 'pending_verification' : ($part ? 'part_paid' : ($hasBalance ? 'unpaid' : 'clear')));
        }

        return $out;
    }

    /**
     * How much of a completed payment is still refundable: its amount less every
     * refund already requested or processed against it (a rejected one frees up).
     */
    public function refundableRemaining(EntityInterface $payment): int
    {
        $claimed = 0;
        $rows = $this->tenant()->query('EmsRefunds')
            ->select(['amount', 'status'])
            ->where(['payment_id' => (string)$payment->id])
            ->all();
        foreach ($rows as $r) {
            if ((string)$r->status !== 'rejected') {
                $claimed += (int)$r->amount;
            }
        }

        return (int)$payment->amount - $claimed;
    }

    // --- enrichment ---------------------------------------------------------

    /** Layer the derived balance / status / schedule onto a stored invoice. */
    public function enrich(EntityInterface $invoice, ?int $paid = null): array
    {
        $wire = FeeSerializer::invoice($invoice);
        $paid = $paid ?? $this->paidFor((string)$invoice->id);
        $instalments = $wire['instalments'] ?? [];
        $schedule = $instalments !== [] ? Money::instalmentStates($instalments, $paid, $this->today) : [];

        $wire['paid'] = $paid;
        $wire['balance'] = $wire['total'] - $paid;
        $wire['paymentStatus'] = Money::paymentStatusFor([
            'status' => $wire['status'],
            'total' => $wire['total'],
            'dueDate' => $wire['dueDate'],
            'instalments' => $instalments,
        ], $paid, $this->today);
        $wire['charged'] = Money::chargeTotal($wire['lineItems']);
        $wire['awarded'] = Money::awardTotal($wire['lineItems']);
        $wire['schedule'] = $schedule;
        $next = Money::nextDueInstalment($schedule);
        if ($next !== null) {
            $wire['nextDue'] = $next;
        }

        return $wire;
    }

    // --- pricing (awards applied at issue) ----------------------------------

    /**
     * Which awards are live for this child, this session, this term. A child
     * gets both their own scholarships and their level's discounts.
     *
     * @return array<int, array<string, mixed>> FeeAward wire arrays
     */
    public function activeAwardsFor(EntityInterface $student, string $session, string $term): array
    {
        $level = Money::levelOf((string)$student->class_group);
        $rows = $this->tenant()->query('EmsFeeAwards')
            ->where(['status' => 'active', 'session' => $session])
            ->all();
        $out = [];
        foreach ($rows as $a) {
            $termOk = (string)$a->term === 'all' || (string)$a->term === $term;
            if (!$termOk) {
                continue;
            }
            $scopeOk = (string)$a->scope === 'student'
                ? (string)$a->student_id === (string)$student->id
                : (string)$a->level === $level;
            if ($scopeOk) {
                $out[] = FeeSerializer::award($a);
            }
        }

        return $out;
    }

    /**
     * Clean the charges a bursar typed, then let the awards ledger have its say.
     * Returns an InvoicePreview array. 422 when no charge survives.
     *
     * @param array<int, array<string, mixed>> $lineItems raw {name, amount}
     */
    public function priceInvoice(EntityInterface $student, string $session, string $term, array $lineItems): array
    {
        $charges = [];
        foreach ($lineItems as $i) {
            $name = trim((string)($i['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $charges[] = [
                'name' => $name,
                'amount' => max(0, Money::jsRound($i['amount'] ?? 0)),
                'kind' => 'charge',
            ];
        }
        if ($charges === []) {
            throw new HttpException(Messages::INVOICE_NO_LINE_ITEMS, 422);
        }

        $result = Money::applyAwards($charges, $this->activeAwardsFor($student, $session, $term));
        $priced = FeeSerializer::lineItems($result['lineItems']);
        $charged = Money::chargeTotal($priced);
        $awarded = Money::awardTotal($priced);

        return [
            'lineItems' => $priced,
            'charged' => $charged,
            'awarded' => $awarded,
            'total' => $charged + $awarded,
            'applied' => array_values(array_map(static fn($a) => [
                'award' => $a['award'],
                'amount' => (int)$a['amount'],
                'base' => (int)$a['base'],
            ], $result['applied'])),
        ];
    }

    /**
     * Turn the schedule a bursar entered into stored instalments. Checked hard:
     * a schedule that does not add up to the bill is a debt nobody is asked for.
     *
     * @param array<int, array<string, mixed>> $rows raw {label, dueOn, amount}
     * @return array<int, array<string, mixed>>
     */
    public function buildInstalments(array $rows, int $total): array
    {
        $cleaned = [];
        foreach ($rows as $r) {
            $label = trim((string)($r['label'] ?? ''));
            $dueOn = (string)($r['dueOn'] ?? '');
            $amount = (float)($r['amount'] ?? 0);
            if ($label !== '' || $dueOn !== '' || $amount > 0) {
                $cleaned[] = ['label' => (string)($r['label'] ?? ''), 'dueOn' => $dueOn, 'amount' => $amount];
            }
        }
        if ($cleaned === []) {
            return [];
        }
        foreach ($cleaned as $r) {
            if ($r['dueOn'] === '') {
                throw new HttpException(Messages::INSTALMENT_NEEDS_DATE, 422);
            }
        }
        foreach ($cleaned as $r) {
            if (Money::jsRound($r['amount']) <= 0) {
                throw new HttpException(Messages::INSTALMENT_NEEDS_AMOUNT, 422);
            }
        }
        $sum = 0;
        foreach ($cleaned as $r) {
            $sum += Money::jsRound($r['amount']);
        }
        if ($sum !== $total) {
            throw new HttpException(
                sprintf(Messages::INSTALMENT_SUM_MISMATCH, Money::formatCurrency($sum), Money::formatCurrency($total)),
                422,
            );
        }
        usort($cleaned, static fn($a, $b) => strcmp((string)$a['dueOn'], (string)$b['dueOn']));
        $out = [];
        foreach ($cleaned as $i => $r) {
            $label = trim((string)$r['label']);
            $out[] = [
                'number' => $i + 1,
                'label' => $label !== '' ? $label : 'Instalment ' . ($i + 1),
                'dueOn' => (string)$r['dueOn'],
                'amount' => Money::jsRound($r['amount']),
            ];
        }

        return $out;
    }

    // --- bulk invoicing -----------------------------------------------------

    /** "{PREFIX}/IB/{termCode}/{seq 4-digit}" — per-term batch sequence. */
    public function nextInvoiceBatchNumber(string $termCode): string
    {
        $seq = (new Sequences($this->locator))->next($this->schoolId, 'invoice_batch:' . $termCode, 0);

        return sprintf('%s/IB/%s/%04d', $this->prefix(), $termCode, $seq);
    }

    /** True when the student already carries a non-cancelled invoice from this plan version. */
    public function hasLiveInvoiceForPlan(string $studentId, string $planVersionId): bool
    {
        return $this->tenant()->query('EmsInvoices')
            ->where(['student_id' => $studentId, 'fee_plan_version_id' => $planVersionId, 'status !=' => 'cancelled'])
            ->count() > 0;
    }

    /**
     * Clean and validate a bulk batch's PERCENTAGE instalment template. Each row
     * needs a date and a share, and the shares must come to exactly 100% — the
     * proportional analogue of buildInstalments' sum-to-total check, since each
     * student's own total differs. Sorted by date. Returns [] for no rows, which
     * means a lump-sum batch (one shared due date instead of a schedule).
     *
     * @param array<int, array<string, mixed>> $rows raw {label, dueOn, percent}
     * @return array<int, array<string, mixed>>
     */
    public function normalizeScheduleTemplate(array $rows): array
    {
        $cleaned = [];
        foreach ($rows as $r) {
            $label = trim((string)($r['label'] ?? ''));
            $dueOn = (string)($r['dueOn'] ?? '');
            $percent = (int)($r['percent'] ?? 0);
            if ($label !== '' || $dueOn !== '' || $percent !== 0) {
                $cleaned[] = ['label' => $label, 'dueOn' => $dueOn, 'percent' => $percent];
            }
        }
        if ($cleaned === []) {
            return [];
        }
        foreach ($cleaned as $r) {
            if ($r['dueOn'] === '') {
                throw new HttpException(Messages::INSTALMENT_NEEDS_DATE, 422);
            }
            if ($r['percent'] <= 0) {
                throw new HttpException(Messages::SCHEDULE_SHARE_POSITIVE, 422);
            }
        }
        $sum = 0;
        foreach ($cleaned as $r) {
            $sum += (int)$r['percent'];
        }
        if ($sum !== 100) {
            throw new HttpException(sprintf(Messages::FEE_SCHEDULE_PERCENT, $sum), 422);
        }
        usort($cleaned, static fn($a, $b) => strcmp((string)$a['dueOn'], (string)$b['dueOn']));

        return $cleaned;
    }

    /**
     * Resolve a bulk-invoicing batch: enumerate the enrolled students in the
     * chosen class groups, price each against the plan and its per-student
     * awards, split the total by the percentage template, and skip anyone who
     * already carries a live invoice from this plan version. Recomputed fresh at
     * preview AND at approval, so the roster and price are always issue-time
     * authoritative — a draft's figures are advisory.
     *
     * @param array<int, string> $classGroups
     * @param array<int, array<string, mixed>> $template normalized {label, dueOn, percent}
     * @return array<string, mixed>
     */
    public function resolveInvoiceBatch(EntityInterface $plan, array $classGroups, array $template, ?string $dueDate): array
    {
        $session = (string)$plan->session;
        $term = (string)$plan->term;
        $lineItems = is_string($plan->items) ? json_decode($plan->items, true) : (array)$plan->items;

        $students = $this->tenant()->query('EmsStudents')
            ->where(['class_group IN' => $classGroups, 'status' => 'enrolled'])
            ->all()
            ->toList();
        usort(
            $students,
            static fn($a, $b) => strcmp(
                (string)$a->class_group . (string)$a->last_name . (string)$a->first_name,
                (string)$b->class_group . (string)$b->last_name . (string)$b->first_name,
            ),
        );

        $alreadyInvoiced = [];
        foreach (
            $this->tenant()->query('EmsInvoices')
                ->select(['student_id'])
                ->where(['fee_plan_version_id' => (string)$plan->id, 'status !=' => 'cancelled'])
                ->all() as $inv
        ) {
            $alreadyInvoiced[(string)$inv->student_id] = true;
        }

        $toIssue = [];
        $skipped = [];
        $totalAmount = 0;
        foreach ($students as $student) {
            $studentId = (string)$student->id;
            $name = trim((string)$student->first_name . ' ' . (string)$student->last_name);
            if (isset($alreadyInvoiced[$studentId])) {
                $skipped[] = [
                    'studentId' => $studentId,
                    'studentName' => $name,
                    'classGroup' => (string)$student->class_group,
                    'reason' => Messages::INVOICE_DUPLICATE_PLAN,
                ];
                continue;
            }
            $priced = $this->priceInvoice($student, $session, $term, $lineItems);
            $total = (int)$priced['total'];
            $instalments = Money::splitByPercent($total, $template);
            $due = $instalments !== []
                ? (string)$instalments[count($instalments) - 1]['dueOn']
                : (string)($dueDate ?? '');
            $toIssue[] = [
                'studentId' => $studentId,
                'studentName' => $name,
                'classGroup' => (string)$student->class_group,
                'total' => $total,
                'awarded' => (int)$priced['awarded'],
                'lineItems' => $priced['lineItems'],
                'instalments' => $instalments,
                'dueOn' => $due,
            ];
            $totalAmount += $total;
        }

        return [
            'toIssue' => $toIssue,
            'skipped' => $skipped,
            'issueCount' => count($toIssue),
            'skipCount' => count($skipped),
            'studentCount' => count($students),
            'totalAmount' => $totalAmount,
        ];
    }

    // --- read models --------------------------------------------------------

    /** InvoiceDetail: enriched invoice + its payments and refunds. */
    public function invoiceDetail(EntityInterface $invoice): array
    {
        $id = (string)$invoice->id;
        $payments = $this->tenant()->query('EmsPayments')
            ->where(['invoice_id' => $id])
            ->all()
            ->toList();
        usort($payments, static fn($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));
        $refunds = $this->tenant()->query('EmsRefunds')
            ->where(['invoice_id' => $id])
            ->all()
            ->toList();
        usort($refunds, static fn($a, $b) => strcmp((string)$b->requested_on, (string)$a->requested_on));

        return $this->enrich($invoice) + [
            'payments' => array_map([FeeSerializer::class, 'payment'], $payments),
            'refunds' => array_map([FeeSerializer::class, 'refund'], $refunds),
            'paymentSubmissions' => $this->paymentSubmissions(['invoice_id' => $id]),
            'adjustments' => $this->adjustments(['invoice_id' => $id]),
            'changeRequests' => $this->changeRequests($id),
        ];
    }

    /**
     * Adjustment requests (reversal/refund) with their decision and payout
     * state — the live correction trail that replaced mutable refunds.
     *
     * @param array<string, mixed> $conditions Tenant-scoped request filters.
     * @return array<int, array<string, mixed>>
     */
    public function adjustments(array $conditions): array
    {
        $rows = $this->tenant()->query('EmsFinanceAdjustmentRequests')
            ->where($conditions)
            ->orderByDesc('created')
            ->all()
            ->toList();
        if ($rows === []) {
            return [];
        }
        $ids = array_map(static fn($r) => (string)$r->id, $rows);
        $decisions = [];
        foreach (
            $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'adjustment', 'request_id IN' => $ids])
            ->all() as $d
        ) {
            $decisions[(string)$d->request_id] = $d;
        }
        $paidOut = [];
        foreach (
            $this->tenant()->query('EmsFinanceAdjustmentPayouts')
            ->where(['adjustment_request_id IN' => $ids])
            ->all() as $p
        ) {
            $paidOut[(string)$p->adjustment_request_id] = true;
        }

        return array_map(function ($r) use ($decisions, $paidOut) {
            $d = $decisions[(string)$r->id] ?? null;
            $status = 'pending';
            if ($d !== null) {
                $status = (string)$d->decision;
                if ($status === 'approved' && (string)$r->kind === 'refund') {
                    $status = isset($paidOut[(string)$r->id]) ? 'paid' : 'awaiting_payout';
                }
            }
            $out = [
                'id' => (string)$r->id,
                'paymentId' => (string)$r->payment_id,
                'invoiceId' => (string)$r->invoice_id,
                'studentId' => (string)$r->student_id,
                'kind' => (string)$r->kind,
                'amount' => (int)$r->amount,
                'reason' => (string)$r->reason,
                'requestedBy' => (string)$r->requested_by_name,
                'requestedOn' => (string)$r->created,
                'status' => $status,
            ];
            if ($d !== null) {
                $out['decision'] = [
                    'reason' => (string)$d->reason,
                    'decidedBy' => (string)$d->decided_by_name,
                    'decidedAt' => (string)$d->decided_at,
                ];
            }

            return $out;
        }, $rows);
    }

    /**
     * Change requests (cancel/reschedule) for one invoice with decision state.
     *
     * @return array<int, array<string, mixed>>
     */
    private function changeRequests(string $invoiceId): array
    {
        $rows = $this->tenant()->query('EmsInvoiceChangeRequests')
            ->where(['invoice_id' => $invoiceId])
            ->orderByDesc('created')
            ->all()
            ->toList();
        if ($rows === []) {
            return [];
        }
        $ids = array_map(static fn($r) => (string)$r->id, $rows);
        $decisions = [];
        foreach (
            $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'invoice_change', 'request_id IN' => $ids])
            ->all() as $d
        ) {
            $decisions[(string)$d->request_id] = $d;
        }

        return array_map(static function ($r) use ($decisions) {
            $d = $decisions[(string)$r->id] ?? null;
            $payload = is_string($r->payload) ? (array)json_decode($r->payload, true) : (array)$r->payload;
            $out = [
                'id' => (string)$r->id,
                'kind' => (string)$r->kind,
                'reason' => (string)$r->reason,
                'requestedBy' => (string)($payload['requestedByName'] ?? ''),
                'requestedOn' => (string)$r->created,
                'status' => $d ? (string)$d->decision : 'pending',
            ];
            if (isset($payload['agreedWith'])) {
                $out['agreedWith'] = (string)$payload['agreedWith'];
            }
            if (isset($payload['instalments'])) {
                $out['instalments'] = (array)$payload['instalments'];
            }
            if ($d !== null) {
                $out['decision'] = [
                    'reason' => (string)$d->reason,
                    'decidedBy' => (string)$d->decided_by_name,
                    'decidedAt' => (string)$d->decided_at,
                ];
            }

            return $out;
        }, $rows);
    }

    /** Receipt for one payment. Reversed still yields a receipt; pending/failed do not. */
    public function receipt(EntityInterface $payment, EntityInterface $invoice): array
    {
        $receipt = $this->tenant()->query('EmsReceipts')->where(['payment_id' => (string)$payment->id])->first();
        if ($receipt === null) {
            throw new HttpException(Messages::RECEIPT_NOT_FOUND, 404);
        }
        $snapshot = is_string($receipt->snapshot) ? json_decode($receipt->snapshot, true) : $receipt->snapshot;

        return ['payment' => FeeSerializer::payment($payment), 'invoice' => FeeSerializer::invoice($invoice),
            'paidToDate' => (int)$receipt->paid_to_date, 'balanceAfter' => (int)$receipt->balance_after,
            'snapshot' => (array)$snapshot, 'issuedAt' => (string)$receipt->issued_at];
    }

    /** A student's complete fee position — the parent's history. */
    public function studentLedger(EntityInterface $student): array
    {
        $studentId = (string)$student->id;
        $invoiceRows = $this->tenant()->query('EmsInvoices')
            ->where(['student_id' => $studentId])
            ->all()
            ->toList();
        $invoices = array_map(fn($i) => $this->enrich($i), $invoiceRows);
        usort($invoices, static fn($a, $b) => strcmp((string)$b['issuedOn'], (string)$a['issuedOn']));

        $paymentRows = $this->tenant()->query('EmsPayments')
            ->where(['student_id' => $studentId])
            ->all()
            ->toList();
        usort($paymentRows, static fn($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));

        $totalInvoiced = 0;
        foreach ($invoices as $i) {
            if ($i['status'] !== 'cancelled') {
                $totalInvoiced += (int)$i['total'];
            }
        }
        $totalPaid = 0;
        foreach ($invoices as $i) {
            $totalPaid += (int)$i['paid'];
        }

        $level = Money::levelOf((string)$student->class_group);
        $awardRows = $this->tenant()->query('EmsFeeAwards')
            ->all()
            ->toList();
        $awards = [];
        foreach ($awardRows as $a) {
            // Families see granted history only — a draft awaiting approval
            // (or a rejected one) is internal until it becomes active.
            if (!in_array((string)$a->status, ['active', 'ended'], true)) {
                continue;
            }
            $mine = (string)$a->scope === 'student'
                ? (string)$a->student_id === $studentId
                : (string)$a->level === $level;
            if ($mine) {
                $awards[] = $a;
            }
        }
        usort($awards, static fn($a, $b) => strcmp((string)$a->status, (string)$b->status)
            ?: strcmp((string)$b->awarded_on, (string)$a->awarded_on));

        return [
            'studentId' => $studentId,
            'studentName' => trim((string)$student->first_name . ' ' . (string)$student->last_name),
            'classGroup' => (string)$student->class_group,
            'invoices' => array_values($invoices),
            'payments' => array_map([FeeSerializer::class, 'payment'], $paymentRows),
            'paymentSubmissions' => $this->paymentSubmissions(['student_id' => $studentId]),
            'awards' => array_map([FeeSerializer::class, 'award'], $awards),
            'totalInvoiced' => $totalInvoiced,
            'totalPaid' => $totalPaid,
            'totalBalance' => $totalInvoiced - $totalPaid,
        ];
    }

    /**
     * @param array<string, mixed> $conditions Tenant-scoped submission filters.
     * @return array<int, array<string, mixed>>
     */
    private function paymentSubmissions(array $conditions): array
    {
        $rows = $this->tenant()->query('EmsPaymentSubmissions')->where($conditions)->orderByDesc('created')->all()->toList();
        if ($rows === []) {
            return [];
        }

        $ids = array_map(static fn($r) => (string)$r->id, $rows);
        $decisions = [];
        $financeDecisions = $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'payment_submission', 'request_id IN' => $ids])
            ->all();
        foreach ($financeDecisions as $d) {
            $decisions[(string)$d->request_id] = $d;
        }

        return array_map(
            static function ($r) use ($decisions) {
                $d = $decisions[(string)$r->id] ?? null;
                $out = [
                    'id' => (string)$r->id,
                    'invoiceId' => (string)$r->invoice_id,
                    'studentId' => (string)$r->student_id,
                    'amount' => (int)$r->amount,
                    'method' => (string)$r->method,
                    'payerName' => (string)$r->payer_name,
                    'payerRelationship' => (string)$r->payer_relationship,
                    'receivedOn' => (string)$r->received_on,
                    'recordedBy' => (string)$r->recorded_by_name,
                    'status' => $d ? (string)$d->decision : 'pending',
                ];
                if ($r->normalized_reference) {
                    $out['reference'] = (string)$r->normalized_reference;
                }
                if ($r->cash_acknowledgement) {
                    $out['cashAcknowledgement'] = (string)$r->cash_acknowledgement;
                }

                return $out;
            },
            $rows,
        );
    }

    /**
     * The control desk's read model: verified money, correction totals from
     * the ledger, and every queue an administrator still owes a decision —
     * pending adjustments, approved refunds awaiting payout, undecided claims.
     */
    public function reconciliation(): array
    {
        $invoiceById = [];
        foreach ($this->tenant()->query('EmsInvoices')->all() as $i) {
            $invoiceById[(string)$i->id] = $i;
        }
        $payments = $this->tenant()->query('EmsPayments')->all()->toList();
        $paymentById = [];
        foreach ($payments as $p) {
            $paymentById[(string)$p->id] = $p;
        }
        $completed = array_filter($payments, static fn($p) => (string)$p->state === 'completed');
        $completedAmount = 0;
        foreach ($completed as $p) {
            $completedAmount += (int)$p->amount;
        }

        // Corrections come from the ledger, the single money authority:
        // reversal/refund events carry negative amounts.
        $reversedCount = 0;
        $reversedAmount = 0;
        $refundedCount = 0;
        $refundedAmount = 0;
        $events = $this->tenant()->query('EmsFinanceLedgerEvents')
            ->select(['event_type', 'amount'])
            ->where(['event_type IN' => ['reversal', 'refund']])
            ->all();
        foreach ($events as $e) {
            if ((string)$e->event_type === 'reversal') {
                $reversedCount++;
                $reversedAmount += -(int)$e->amount;
            } else {
                $refundedCount++;
                $refundedAmount += -(int)$e->amount;
            }
        }

        $withContext = function (array $row) use ($invoiceById, $paymentById): array {
            $inv = $invoiceById[$row['invoiceId']] ?? null;
            $pay = $paymentById[$row['paymentId']] ?? null;

            return $row + [
                'studentName' => $inv !== null ? (string)$inv->student_name : '—',
                'invoiceNumber' => $inv !== null ? (string)$inv->invoice_number : '—',
                'receiptNumber' => $pay !== null ? (string)$pay->receipt_number : '—',
                'paymentAmount' => $pay !== null ? (int)$pay->amount : 0,
            ];
        };
        $adjustments = $this->adjustments([]);
        $adjustmentsPending = array_values(array_map(
            $withContext,
            array_filter($adjustments, static fn($a) => $a['status'] === 'pending'),
        ));
        $payoutsPending = array_values(array_map(
            $withContext,
            array_filter($adjustments, static fn($a) => $a['status'] === 'awaiting_payout'),
        ));

        $claimIds = $this->tenant()->query('EmsPaymentSubmissions')
            ->select(['id'])
            ->all()
            ->extract('id')
            ->toList();
        $decidedClaims = $claimIds === [] ? 0 : $this->tenant()->query('EmsFinanceDecisions')
            ->where(['request_type' => 'payment_submission', 'request_id IN' => array_map('strval', $claimIds)])
            ->count();

        return [
            'completedCount' => count($completed),
            'completedAmount' => $completedAmount,
            'reversedCount' => $reversedCount,
            'reversedAmount' => $reversedAmount,
            'refundedCount' => $refundedCount,
            'refundedAmount' => $refundedAmount,
            'adjustmentsPending' => $adjustmentsPending,
            'payoutsPending' => $payoutsPending,
            'submissionsPending' => count($claimIds) - $decidedClaims,
        ];
    }

    /** The financial overview for a term (or all). Collected is net of refunds. */
    public function report(string $termFilter): array
    {
        $invoiceRows = $this->tenant()->query('EmsInvoices')
            ->where(['status !=' => 'cancelled'])
            ->all()
            ->toList();
        $invoices = [];
        foreach ($invoiceRows as $i) {
            if ($termFilter === 'all' || (string)$i->term === $termFilter) {
                $invoices[] = $this->enrich($i);
            }
        }

        $totalInvoiced = 0;
        $totalCollected = 0;
        $totalAwarded = 0;
        $paidCount = 0;
        $overdueCount = 0;
        $groups = [];
        foreach ($invoices as $i) {
            $totalInvoiced += (int)$i['total'];
            $totalCollected += (int)$i['paid'];
            $totalAwarded += (int)$i['awarded'];
            if ($i['paymentStatus'] === 'paid') {
                $paidCount++;
            }
            if ($i['paymentStatus'] === 'overdue') {
                $overdueCount++;
            }
            $g = $i['classGroup'];
            if (!isset($groups[$g])) {
                $groups[$g] = ['invoiced' => 0, 'collected' => 0];
            }
            $groups[$g]['invoiced'] += (int)$i['total'];
            $groups[$g]['collected'] += (int)$i['paid'];
        }
        $byClass = [];
        foreach ($groups as $key => $g) {
            $byClass[] = [
                'key' => (string)$key,
                'label' => (string)$key,
                'invoiced' => $g['invoiced'],
                'collected' => $g['collected'],
                'outstanding' => $g['invoiced'] - $g['collected'],
                'collectionRate' => $g['invoiced'] === 0 ? 0 : $g['collected'] / $g['invoiced'],
            ];
        }
        usort($byClass, static fn($a, $b) => $b['outstanding'] <=> $a['outstanding'] ?: strcmp((string)$a['label'], (string)$b['label']));

        $recent = $this->tenant()->query('EmsPayments')
            ->where(['state' => 'completed'])
            ->all()
            ->toList();
        usort($recent, static fn($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));
        $recent = array_slice($recent, 0, 8);

        return [
            'term' => $termFilter,
            'totalInvoiced' => $totalInvoiced,
            'totalCollected' => $totalCollected,
            'totalAwarded' => -$totalAwarded,
            'totalOutstanding' => $totalInvoiced - $totalCollected,
            'collectionRate' => $totalInvoiced === 0 ? 0 : $totalCollected / $totalInvoiced,
            'invoiceCount' => count($invoices),
            'paidCount' => $paidCount,
            'overdueCount' => $overdueCount,
            'byClass' => $byClass,
            'recentPayments' => array_map([FeeSerializer::class, 'payment'], $recent),
        ];
    }
}
