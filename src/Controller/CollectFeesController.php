<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

/**
 * CollectFees Controller
 *
 * @property \App\Model\Table\CollectFeesTable $CollectFees
 * @method \App\Model\Entity\CollectFee[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CollectFeesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $settings = $this->request->getSession()->read('settings');
        $admin = $this->request->getSession()->read('admin');
        
        // Get all unpaid invoices for the current session with pagination
        $invoicesTable = TableRegistry::get('Invoices');
        $invoices = $invoicesTable->find()
            ->contain(['Students.Departments', 'Students.ClassArms', 'Fees', 'Sessions'])
            ->where([
                'Invoices.session_id' => $settings->session_id,
                'Invoices.paystatus' => 'Unpaid'
            ])
            ->order(['Invoices.createdate' => 'DESC']);
        
        // Apply pagination
        $this->paginate = [
            'limit' => 20, // Show 20 invoices per page
            'order' => ['Invoices.createdate' => 'DESC']
        ];
        $invoices = $this->paginate($invoices);

        // Get payment statistics
        $totalUnpaid = $invoicesTable->find()
            ->where([
                'session_id' => $settings->session_id,
                'paystatus' => 'Unpaid'
            ])
            ->count();

        $totalPaid = $invoicesTable->find()
            ->where([
                'session_id' => $settings->session_id,
                'paystatus' => 'success'
            ])
            ->count();

        $totalAmount = $invoicesTable->find()
            ->where([
                'session_id' => $settings->session_id,
                'paystatus' => 'Unpaid'
            ])
            ->select(['total' => $invoicesTable->find()->func()->sum('amount')])
            ->first();

        $this->set(compact('invoices', 'totalUnpaid', 'totalPaid', 'totalAmount'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Invoice id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function view($id = null)
    {
        $invoicesTable = TableRegistry::get('Invoices');
        $invoice = $invoicesTable->get($id, [
            'contain' => ['Students.Departments', 'Students.ClassArms', 'Fees', 'Sessions', 'Transactions']
        ]);

        $this->set('invoice', $invoice);
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method - Record manual payment
     *
     * @param string|null $id Invoice id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $settings = $this->request->getSession()->read('settings');
        $admin = $this->request->getSession()->read('admin');
        
        $invoicesTable = TableRegistry::get('Invoices');
        $transactionsTable = TableRegistry::get('Transactions');
        
        // Get the invoice
        $invoice = $invoicesTable->get($id, [
            'contain' => ['Students.Departments', 'Students.ClassArms', 'Fees', 'Sessions']
        ]);

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Validate payment amount
            if (($data['amount'] +$data['discount']) > $invoice->amount) {
                $this->Flash->error(__('Payment amount cannot exceed invoice amount.'));
                return $this->redirect(['action' => 'add', $id]);
            } elseif (($data['amount']+$data['discount']) < $invoice->amount) {
                $this->Flash->error(__('Payment amount cannot be less than invoice amount.'));
                return $this->redirect(['action' => 'add', $id]);
            } else {

                // Create transaction record
                $transaction = $transactionsTable->newEmptyEntity();
                $transaction->student_id = $invoice->student_id;
                $transaction->transdate = new \DateTime();
                $transaction->amount = $data['amount'];
                $transaction->discount = $data['discount'];
                $transaction->paystatus = 'paid';
                $transaction->payref = 'MANUAL_' . strtoupper($data['payment_method']) . '_' . date('YmdHis') . '_' . $admin->id;
                $transaction->gresponse = 'success';
                $transaction->session_id = $settings->session_id;
                $transaction->fee_id = $invoice->fee_id;
                $transaction->invoice_id = $invoice->id;
                $transaction->pgateway = $data['payment_method'];
                $transaction->paymentlogid = 'MANUAL_PAYMENT_' . $admin->id . '_' . date('YmdHis');

                if ($transactionsTable->save($transaction)) {
                    // Update invoice status
                    $invoice->paystatus = 'success';
                    $invoice->payday = date('Y-m-d H:i:s');
                    
                    if ($invoicesTable->save($invoice)) {
                        $this->Flash->success(__('Payment has been recorded successfully.'));
                        return $this->redirect(['action' => 'studentInvoices', $invoice->student_id]);
                    } else {
                        $this->Flash->error(__('Payment recorded but failed to update invoice status.'));
                    }
                } else {
                    $this->Flash->error(__('Failed to record payment. Please try again.'));
                }
            }
        }

        $this->set('invoice', $invoice);
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Search method - Search for students by registration number or name
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function search()
    {
        $settings = $this->request->getSession()->read('settings');
        $students = [];
        $searchTerm = '';

        if ($this->request->is('post')) {
            $searchTerm = $this->request->getData('search_term');
            
            if (!empty($searchTerm)) {
                $studentsTable = TableRegistry::get('Students');
                $students = $studentsTable->find()
                    ->contain(['Departments'])
                    ->where([
                        'OR' => [
                            'Students.regno LIKE' => '%' . $searchTerm . '%',
                            'Students.fname LIKE' => '%' . $searchTerm . '%',
                            'Students.lname LIKE' => '%' . $searchTerm . '%',
                            'CONCAT(Students.fname, " ", Students.lname) LIKE' => '%' . $searchTerm . '%'
                        ],
                        'Students.status' => 'Admitted'
                    ])
                    ->limit(20)
                    ->all();
            }
        }

        $this->set(compact('students', 'searchTerm'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Student invoices method - Show all invoices for a specific student
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function studentInvoices($id = null)
    {
        $settings = $this->request->getSession()->read('settings');
        
        $studentsTable = TableRegistry::get('Students');
        $invoicesTable = TableRegistry::get('Invoices');
        
        $student = $studentsTable->get($id, [
            'contain' => ['Departments']
        ]);

        $invoices = $invoicesTable->find()
            ->contain(['Fees', 'Sessions', 'Transactions'])
            ->where([
                'Invoices.student_id' => $id,
                'Invoices.session_id' => $settings->session_id
            ])
            ->order(['Invoices.createdate' => 'DESC'])
            ->all();
       // debug(json_encode($invoices, JSON_PRETTY_PRINT)); exit;

        $this->set(compact('student', 'invoices'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Receipt method - Generate payment receipt
     *
     * @param string|null $id Invoice id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function receipt($id = null)
    {
        $settings = $this->request->getSession()->read('settings');
        $invoicesTable = TableRegistry::get('Invoices');
        $transactionsTable = TableRegistry::get('Transactions');
        
        // Debug: Log the invoice ID being requested
        \Cake\Log\Log::debug('Receipt requested for invoice ID: ' . $id);
        
        try {
            $invoice = $invoicesTable->get($id, [
                'contain' => ['Students', 'Fees', 'Sessions']
            ]);
            \Cake\Log\Log::debug('Invoice found: ' . json_encode($invoice->toArray()));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Invoice not found.'));
            return $this->redirect(['action' => 'index']);
        }

        // Debug: Check what transactions exist for this invoice
        $allTransactions = $transactionsTable->find()
            ->where([
                'Transactions.invoice_id' => $id,
                'Transactions.student_id' => $invoice->student_id
            ])
            ->toArray();
        
        // Log for debugging
        \Cake\Log\Log::debug('All transactions for invoice ' . $id . ': ' . json_encode($allTransactions));

        // Get the latest transaction for this invoice directly from Transactions table
        $transaction = $transactionsTable->find()
            ->where([
                'Transactions.invoice_id' => $id,
                'Transactions.student_id' => $invoice->student_id,
                'Transactions.paystatus IN' => ['paid', 'completed']
            ])
            ->contain([
                'Students' => [
                    'Departments',
                    'Users',
                    'States',
                    'Countries',
                    'Lgas'
                ],
                'Sessions',
                'Fees'
            ])
            ->order(['Transactions.transdate' => 'DESC'])
            ->first();

        // If no transaction found with paystatus filter, try without it
        if (!$transaction && !empty($allTransactions)) {
            $transaction = $transactionsTable->find()
                ->where([
                    'Transactions.invoice_id' => $id,
                    'Transactions.student_id' => $invoice->student_id
                ])
                ->contain([
                    'Students' => [
                        'Departments',
                        'Users',
                        'States',
                        'Countries',
                        'Lgas'
                    ],
                    'Sessions',
                    'Fees'
                ])
                ->order(['Transactions.transdate' => 'DESC'])
                ->first();
        }

        if (!$transaction) {
            // More detailed error message
            $this->Flash->error(__('No payment transaction found for this invoice. Found ' . count($allTransactions) . ' transactions total.'));
            return $this->redirect(['action' => 'view', $id]);
        }

        // Ensure we have all required data
        if (empty($settings)) {
            $this->Flash->error(__('System settings not found.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('invoice', 'transaction', 'settings'));
        $this->viewBuilder()->setLayout('backend'); // Use backend layout for proper styling
    }

    /**
     * Reports method - Generate payment reports
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function reports()
    {
        $settings = $this->request->getSession()->read('settings');
        
        $invoicesTable = TableRegistry::get('Invoices');
        $transactionsTable = TableRegistry::get('Transactions');
        
        // Get date range from request
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));
        $paymentMethod = $this->request->getQuery('payment_method', '');

        // Build query conditions
        $conditions = [
            'Transactions.transdate >=' => $startDate . ' 00:00:00',
            'Transactions.transdate <=' => $endDate . ' 23:59:59',
            'Transactions.paystatus' => 'success',
            'Transactions.session_id' => $settings->session_id
        ];

        if (!empty($paymentMethod)) {
            $conditions['Transactions.pgateway'] = $paymentMethod;
        }

        $payments = $transactionsTable->find()
            ->contain(['Students.Departments', 'Students.ClassArms', 'Fees', 'Invoices'])
            ->where($conditions)
            ->order(['Transactions.transdate' => 'DESC'])
            ->all();

        // Calculate totals
        $totalAmount = $transactionsTable->find()
            ->where($conditions)
            ->select(['total' => $transactionsTable->find()->func()->sum('amount')])
            ->first();

        $cashTotal = $transactionsTable->find()
            ->where(array_merge($conditions, ['Transactions.pgateway' => 'cash']))
            ->select(['total' => $transactionsTable->find()->func()->sum('amount')])
            ->first();

        $bankTransferTotal = $transactionsTable->find()
            ->where(array_merge($conditions, ['Transactions.pgateway' => 'bank_transfer']))
            ->select(['total' => $transactionsTable->find()->func()->sum('amount')])
            ->first();

        $this->set(compact('payments', 'totalAmount', 'cashTotal', 'bankTransferTotal', 'startDate', 'endDate', 'paymentMethod'));
        $this->viewBuilder()->setLayout('backend');
    }
}
