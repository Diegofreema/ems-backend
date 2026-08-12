<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Money;
use App\Ems\Serializer\FeeSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use DateTimeImmutable;

/**
 * Invoices — what one student owes, priced against the awards ledger at issue
 * (document.md §3.7). Balances, payment statuses and schedules are derived by
 * App\Ems\Fees, never stored, so they can never drift from the money received.
 * The office payment and provider-checkout entry points live here; the money
 * arithmetic is the engine's.
 */
class InvoicesController extends AppController
{
    /**
     * GET /invoices — status filters on the DERIVED paymentStatus; query over
     * studentName/invoiceNumber/classGroup; sort recent|balance|name.
     */
    public function index(): Response
    {
        $params = $this->pageParams();
        $query = strtolower(trim((string)$this->request->getQuery('query', '')));
        $status = (string)$this->request->getQuery('status', 'all');
        $term = (string)$this->request->getQuery('term', 'all');
        $sort = (string)$this->request->getQuery('sort', 'recent');

        $fees = $this->feesEngine();
        $rows = $this->tenant()->query('EmsInvoices')
            ->all()
            ->toList();
        $enriched = [];
        foreach ($rows as $i) {
            $wire = $fees->enrich($i);
            $matches = $query === ''
                || str_contains(strtolower($wire['studentName']), $query)
                || str_contains(strtolower($wire['invoiceNumber']), $query)
                || str_contains(strtolower($wire['classGroup']), $query);
            if (
                $matches
                && ($status === 'all' || $wire['paymentStatus'] === $status)
                && ($term === 'all' || $wire['term'] === $term)
            ) {
                $enriched[] = $wire;
            }
        }
        usort($enriched, static function ($a, $b) use ($sort) {
            switch ($sort) {
                case 'balance':
                    return $b['balance'] <=> $a['balance'];
                case 'name':
                    return strcmp($a['studentName'], $b['studentName']);
                default:
                    return strcmp((string)$b['issuedOn'], (string)$a['issuedOn'])
                        ?: strcmp($a['invoiceNumber'], $b['invoiceNumber']);
            }
        });

        $total = count($enriched);
        $page = array_slice($enriched, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated($page, $total, $params['page'], $params['pageSize']);
    }

    /**
     * GET /invoices/{id} — enriched invoice with its payments and refunds.
     */
    public function view(string $id): Response
    {
        return $this->json($this->feesEngine()->invoiceDetail($this->findInvoice($id)));
    }

    /**
     * POST /invoices/preview — the same pricing routine as issue, so the figure
     * on the form is the figure on the bill.
     */
    public function preview(): Response
    {
        $body = $this->body();
        $student = $this->findStudent((string)($body['studentId'] ?? ''));
        $plan=$this->approvedPlan((string)($body['feePlanVersionId']??''));
        if(array_key_exists('lineItems',$body))$this->fail(422,'Custom client supplied charge or discount lines are not accepted.');
        $lineItems=is_string($plan->items)?json_decode($plan->items,true):(array)$plan->items;

        return $this->json($this->feesEngine()->priceInvoice(
            $student,
            (string)$plan->session,
            (string)$plan->term,
            $lineItems
        ));
    }

    /**
     * POST /invoices — awards applied server-side; client award lines ignored.
     */
    public function add(): Response
    {
        $body = $this->body();
        if($this->viewer->role!=='bursar')$this->fail(403,'Only a bursar can issue an invoice from an approved fee plan.');
        $key=trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay=$this->financeSecurity()->replay($this->viewer,'invoice.issue',$key,$body);
        if($replay!==null)return$this->json($replay['body'],$replay['status']);
        $student = $this->findStudent((string)($body['studentId'] ?? ''));
        $plan=$this->approvedPlan((string)($body['feePlanVersionId']??''));
        if(array_key_exists('lineItems',$body))$this->fail(422,'Custom client supplied charge or discount lines are not accepted.');
        $session=(string)$plan->session;$term=(string)$plan->term;$lineItems=is_string($plan->items)?json_decode($plan->items,true):(array)$plan->items;

        $fees = $this->feesEngine();
        $priced = $fees->priceInvoice($student, $session, $term, $lineItems);
        $instalments = $fees->buildInstalments(
            is_array($body['instalments'] ?? null) ? $body['instalments'] : [],
            (int)$priced['total']
        );

        $code = Money::termCode($session, $term);
        $dueDate = $instalments !== []
            ? (string)$instalments[count($instalments) - 1]['dueOn']
            : (string)($body['dueDate'] ?? '');

        $invoices = $this->fetchTable('EmsInvoices');
        $invoice = $invoices->newEntity([
            'school_id' => $this->viewer->schoolId,
            'fee_plan_version_id' => (string)$plan->id,
            'invoice_number' => $fees->nextInvoiceNumber($code),
            'student_id' => (string)$student->id,
            'student_name' => trim((string)$student->first_name . ' ' . (string)$student->last_name),
            'class_group' => (string)$student->class_group,
            'session' => $session,
            'term' => $term,
            'issued_on' => FrozenDate::today(),
            'due_date' => $dueDate,
            'line_items' => $priced['lineItems'],
            'total' => (int)$priced['total'],
            'status' => 'issued',
            'instalments' => $instalments !== [] ? $instalments : null,
        ], ['validate' => false]);

        $result=$invoices->getConnection()->transactional(function()use($invoices,$invoice,$body,$key){
            $saved=$invoices->saveOrFail($invoice);$wire=FeeSerializer::invoice($saved);
            $this->audit()->log($this->viewer,'invoice.issued','invoice',(string)$saved->id,'An immutable student invoice was issued from an approved fee plan.');
            $this->financeSecurity()->remember($this->viewer,'invoice.issue',$key,$body,201,$wire);return$wire;
        });
        return $this->json($result, 201);
    }

    /**
     * POST /invoices/{id}/cancel — money already received blocks it.
     */
    public function cancel(string $id): Response
    {
        $this->fail(410, 'Direct cancellation is retired. Create an invoice change request for independent approval.');
        $invoice = $this->findInvoice($id);
        if ((string)$invoice->status === 'cancelled') {
            $this->fail(422, Messages::INVOICE_ALREADY_CANCELLED);
        }
        if ($this->feesEngine()->paidFor($id) > 0) {
            $this->fail(422, Messages::INVOICE_HAS_PAYMENTS);
        }
        $reason = trim((string)($this->body()['reason'] ?? ''));
        $invoices = $this->fetchTable('EmsInvoices');
        $invoice->status = 'cancelled';
        $invoice->cancelled_on = FrozenDate::today();
        $invoice->cancellation_reason = $reason !== '' ? $reason : 'Cancelled';

        return $this->json(FeeSerializer::invoice($invoices->saveOrFail($invoice)));
    }

    /**
     * POST /invoices/{id}/reschedule — the controlled credit process. Never
     * edits the frozen plan in place: it keeps the schedule it replaced as
     * history and refuses a plan that does not add up to the invoice total.
     */
    public function reschedule(string $id): Response
    {
        $this->fail(410, 'Direct rescheduling is retired. Create an invoice change request for independent approval.');
        $invoice = $this->findInvoice($id);
        if ((string)$invoice->status === 'cancelled') {
            $this->fail(422, Messages::INVOICE_CANCELLED_NO_RESCHEDULE);
        }
        $body = $this->body();
        $agreedWith = trim((string)($body['agreedWith'] ?? ''));
        if ($agreedWith === '') {
            $this->fail(422, Messages::RESCHEDULE_AGREED_WITH);
        }
        $reason = trim((string)($body['reason'] ?? ''));
        if ($reason === '') {
            $this->fail(422, Messages::RESCHEDULE_REASON);
        }
        $next = $this->feesEngine()->buildInstalments(
            is_array($body['instalments'] ?? null) ? $body['instalments'] : [],
            (int)$invoice->total
        );
        if ($next === []) {
            $this->fail(422, Messages::RESCHEDULE_NEEDS_INSTALMENT);
        }

        // Snapshot the schedule being replaced (a one-payment invoice becomes a
        // single lump-sum row, so history always reads as a plan once given).
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
            'revisedOn' => $this->feesToday(),
            'revisedBy' => $this->viewer->name,
            'agreedWith' => $agreedWith,
            'reason' => $reason,
            'previous' => $previous,
        ];

        $invoices = $this->fetchTable('EmsInvoices');
        $invoice->instalments = $next;
        $invoice->schedule_revisions = $revisions;
        $invoice->due_date = (string)$next[count($next) - 1]['dueOn'];
        $invoices->saveOrFail($invoice);

        $n = count($next);
        $this->audit()->log(
            $this->viewer,
            'invoice.rescheduled',
            'invoice',
            (string)$invoice->id,
            sprintf(
                'Rescheduled %s for %s into %d instalment%s ending %s, agreed with %s. '
                . 'The schedule it replaced is kept as history.',
                (string)$invoice->invoice_number,
                (string)$invoice->student_name,
                $n,
                $n === 1 ? '' : 's',
                (string)$next[$n - 1]['dueOn'],
                $agreedWith
            ),
            $reason
        );

        return $this->json(FeeSerializer::invoice($invoice));
    }

    /**
     * POST /invoices/{id}/payments — an office entry. Completed immediately, a
     * receipt number issued now, no channel.
     */
    public function addPayment(string $id): Response
    {
        $this->fail(410, 'Direct payment posting has been retired. Create a payment submission for independent approval.');
    }

    /** POST /invoices/{id}/payment-submissions */
    public function submitPayment(string $id): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can record an offline payment claim.');
        }
        $body = $this->body();
        $key = trim((string)$this->request->getHeaderLine('Idempotency-Key'));
        $replay = $this->financeSecurity()->replay($this->viewer, 'payment_submission.create', $key, $body);
        if ($replay !== null) return $this->json($replay['body'], $replay['status']);
        $submissions = $this->fetchTable('EmsPaymentSubmissions');
        $result = $submissions->getConnection()->transactional(function () use ($id, $body, $key) {
            $invoice = $this->tenant()->query('EmsInvoices')
                ->where(['id' => $id])
                ->epilog('FOR UPDATE')
                ->first();
            if ($invoice === null) {
                $this->fail(404, Messages::INVOICE_NOT_FOUND);
            }
            if ((string)$invoice->status === 'cancelled') {
                $this->fail(422, Messages::INVOICE_CANCELLED_NO_PAYMENTS);
            }
            $result = $this->financeSecurity()->createSubmission($invoice, $this->viewer, $body);
            $this->financeSecurity()->remember($this->viewer, 'payment_submission.create', $key, $body, 201, $result);
            return $result;
        });
        return $this->json($result, 201);
    }

    /**
     * POST /invoices/{id}/checkout — a guardian starts a provider payment. The
     * pending record counts toward nothing until the provider confirms.
     */
    public function checkout(string $id): Response
    {
        $this->fail(503, Messages::ONLINE_PAYMENTS_UNAVAILABLE);
    }

    /**
     * GET /students/{studentId}/ledger — the parent's complete fee position.
     */
    public function ledger(string $studentId): Response
    {
        $this->scope()->assertStudentAccess($studentId);
        $student = $this->findStudent($studentId);

        return $this->json($this->feesEngine()->studentLedger($student));
    }

    // --- helpers ----------------------------------------------------------

    private function findInvoice(string $id): EntityInterface
    {
        return $this->findOr404('EmsInvoices', $id, Messages::INVOICE_NOT_FOUND);
    }

    private function findStudent(string $id): EntityInterface
    {
        return $this->findOr404('EmsStudents', $id, Messages::FEE_STUDENT_NOT_FOUND);
    }

    private function approvedPlan(string $id): EntityInterface
    {
        $plan=$this->tenant()->query('EmsFeePlanVersions')->where(['id'=>$id])->first();
        if(!$plan)$this->fail(422,'Choose an approved fee plan version.');
        $approved=$this->tenant()->query('EmsFinanceDecisions')->where(['request_type'=>'fee_plan_version','request_id'=>$id,'decision'=>'approved'])->count()>0;
        if(!$approved)$this->fail(422,'Invoices can only be issued from an approved fee plan version.');
        return $plan;
    }

    private function feesToday(): string
    {
        return FrozenDate::today()->format('Y-m-d');
    }
}
