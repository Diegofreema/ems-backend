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
    /** @var \Cake\ORM\Locator\LocatorInterface */
    private $locator;

    /** @var string */
    private $schoolId;

    /** @var string YYYY-MM-DD */
    private $today;

    /** @var array<string, int>|null memoized net-paid-per-invoice map */
    private $netPaid = null;

    /** @var \App\Ems\Tenant|null */
    private $tenantScope;

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
        $studentIds=array_values(array_unique(array_filter(array_map('strval',$studentIds))));
        if($studentIds===[]) return [];
        $invoices=$this->tenant()->query('EmsInvoices')->select(['id','student_id','total','due_date','status'])->where(['student_id IN'=>$studentIds])->all()->toList();
        $invoiceIds=array_map(static fn($i)=>(string)$i->id,$invoices);$paid=[];$pending=[];
        if($invoiceIds!==[]){foreach($this->tenant()->query('EmsFinanceLedgerEvents')->select(['invoice_id','amount'])->where(['invoice_id IN'=>$invoiceIds])->all() as $e)$paid[(string)$e->invoice_id]=($paid[(string)$e->invoice_id]??0)+(int)$e->amount;
            $decided=[];foreach($this->tenant()->query('EmsFinanceDecisions')->select(['request_id'])->where(['request_type'=>'payment_submission'])->all() as $d)$decided[(string)$d->request_id]=true;
            foreach($this->tenant()->query('EmsPaymentSubmissions')->select(['id','student_id'])->where(['invoice_id IN'=>$invoiceIds])->all() as $s)if(!isset($decided[(string)$s->id]))$pending[(string)$s->student_id]=true;}
        $groups=[];foreach($invoices as $i)if((string)$i->status!=='cancelled')$groups[(string)$i->student_id][]=$i;
        $out=[];foreach($studentIds as $studentId){$rows=$groups[$studentId]??[];if($rows===[]){$out[$studentId]='not_invoiced';continue;}$hasBalance=false;$part=false;$overdue=false;foreach($rows as $i){$p=$paid[(string)$i->id]??0;$balance=(int)$i->total-$p;if($balance>0){$hasBalance=true;if($p>0)$part=true;if((string)$i->due_date<$this->today)$overdue=true;}}$out[$studentId]=$overdue?'overdue':(isset($pending[$studentId])?'pending_verification':($part?'part_paid':($hasBalance?'unpaid':'clear')));}
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
            'applied' => array_values(array_map(static fn ($a) => [
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
                422
            );
        }
        usort($cleaned, static fn ($a, $b) => strcmp((string)$a['dueOn'], (string)$b['dueOn']));
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

    // --- read models --------------------------------------------------------

    /** InvoiceDetail: enriched invoice + its payments and refunds. */
    public function invoiceDetail(EntityInterface $invoice): array
    {
        $id = (string)$invoice->id;
        $payments = $this->tenant()->query('EmsPayments')
            ->where(['invoice_id' => $id])
            ->all()
            ->toList();
        usort($payments, static fn ($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));
        $refunds = $this->tenant()->query('EmsRefunds')
            ->where(['invoice_id' => $id])
            ->all()
            ->toList();
        usort($refunds, static fn ($a, $b) => strcmp((string)$b->requested_on, (string)$a->requested_on));

        return $this->enrich($invoice) + [
            'payments' => array_map([FeeSerializer::class, 'payment'], $payments),
            'refunds' => array_map([FeeSerializer::class, 'refund'], $refunds),
            'paymentSubmissions' => $this->paymentSubmissions(['invoice_id' => $id]),
        ];
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
        $invoices = array_map(fn ($i) => $this->enrich($i), $invoiceRows);
        usort($invoices, static fn ($a, $b) => strcmp((string)$b['issuedOn'], (string)$a['issuedOn']));

        $paymentRows = $this->tenant()->query('EmsPayments')
            ->where(['student_id' => $studentId])
            ->all()
            ->toList();
        usort($paymentRows, static fn ($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));

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
            $mine = (string)$a->scope === 'student'
                ? (string)$a->student_id === $studentId
                : (string)$a->level === $level;
            if ($mine) {
                $awards[] = $a;
            }
        }
        usort($awards, static fn ($a, $b) =>
            strcmp((string)$a->status, (string)$b->status)
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

    private function paymentSubmissions(array $conditions): array
    {
        $rows=$this->tenant()->query('EmsPaymentSubmissions')->where($conditions)->orderByDesc('created')->all()->toList();
        if($rows===[])return[];$ids=array_map(static fn($r)=>(string)$r->id,$rows);$decisions=[];
        foreach($this->tenant()->query('EmsFinanceDecisions')->where(['request_type'=>'payment_submission','request_id IN'=>$ids])->all() as $d)$decisions[(string)$d->request_id]=$d;
        return array_map(static function($r)use($decisions){$d=$decisions[(string)$r->id]??null;$out=['id'=>(string)$r->id,'invoiceId'=>(string)$r->invoice_id,'studentId'=>(string)$r->student_id,'amount'=>(int)$r->amount,'method'=>(string)$r->method,'payerName'=>(string)$r->payer_name,'payerRelationship'=>(string)$r->payer_relationship,'receivedOn'=>(string)$r->received_on,'recordedBy'=>(string)$r->recorded_by_name,'status'=>$d?(string)$d->decision:'pending'];if($r->normalized_reference)$out['reference']=(string)$r->normalized_reference;if($r->cash_acknowledgement)$out['cashAcknowledgement']=(string)$r->cash_acknowledgement;return$out;},$rows);
    }

    /** Provider + office payments by state, for the bursar's reconciliation. */
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
        $enrichRow = function (EntityInterface $p) use ($invoiceById): array {
            $inv = $invoiceById[(string)$p->invoice_id] ?? null;

            return FeeSerializer::payment($p) + [
                'studentName' => $inv !== null ? (string)$inv->student_name : '—',
                'invoiceNumber' => $inv !== null ? (string)$inv->invoice_number : '—',
            ];
        };

        $completed = array_filter($payments, static fn ($p) => (string)$p->state === 'completed');
        $pending = array_filter($payments, static fn ($p) => (string)$p->state === 'pending');
        $failed = array_filter($payments, static fn ($p) => (string)$p->state === 'failed');

        $pendingRows = array_map($enrichRow, array_values($pending));
        usort($pendingRows, static fn ($a, $b) => strcmp((string)$b['paidOn'], (string)$a['paidOn']));
        $failedRows = array_map($enrichRow, array_values($failed));
        usort($failedRows, static fn ($a, $b) => strcmp((string)($b['failedOn'] ?? ''), (string)($a['failedOn'] ?? '')));

        $refunds = $this->tenant()->query('EmsRefunds')->all()->toList();
        $processed = array_filter($refunds, static fn ($r) => (string)$r->status === 'processed');
        $refundContext = function (EntityInterface $r) use ($invoiceById, $paymentById): array {
            $inv = $invoiceById[(string)$r->invoice_id] ?? null;
            $pay = $paymentById[(string)$r->payment_id] ?? null;

            return FeeSerializer::refund($r) + [
                'studentName' => $inv !== null ? (string)$inv->student_name : '—',
                'invoiceNumber' => $inv !== null ? (string)$inv->invoice_number : '—',
                'receiptNumber' => $pay !== null ? (string)$pay->receipt_number : '—',
                'paymentAmount' => $pay !== null ? (int)$pay->amount : 0,
            ];
        };
        $refundsPending = array_map($refundContext, array_values(array_filter(
            $refunds,
            static fn ($r) => (string)$r->status === 'pending'
        )));
        usort($refundsPending, static fn ($a, $b) => strcmp((string)$b['requestedOn'], (string)$a['requestedOn']));

        $sum = static function (array $rows): int {
            $t = 0;
            foreach ($rows as $r) {
                $t += (int)$r->amount;
            }

            return $t;
        };

        return [
            'pending' => $pendingRows,
            'failed' => $failedRows,
            'completedCount' => count($completed),
            'completedAmount' => $sum($completed),
            'pendingAmount' => $sum($pending),
            'reversedCount' => count(array_filter($payments, static fn ($p) => (string)$p->state === 'reversed')),
            'refundsPending' => $refundsPending,
            'refundedAmount' => $sum($processed),
            'refundedCount' => count($processed),
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
        usort($byClass, static fn ($a, $b) =>
            ($b['outstanding'] <=> $a['outstanding']) ?: strcmp((string)$a['label'], (string)$b['label']));

        $recent = $this->tenant()->query('EmsPayments')
            ->where(['state' => 'completed'])
            ->all()
            ->toList();
        usort($recent, static fn ($a, $b) => strcmp((string)$b->paid_on, (string)$a->paid_on));
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
