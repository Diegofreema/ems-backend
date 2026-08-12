<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;
/**
 * Sponsorships Controller
 *
 * @property \App\Model\Table\SponsorshipsTable $Sponsorships
 * @method \App\Model\Entity\Sponsorship[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SponsorshipsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Sponsors', 'Sessions', 'Admins','Students'],
        ];
        $sponsorships = $this->paginate($this->Sponsorships);
       // debug(json_encode( $sponsorships, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('sponsorships'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Sponsorship id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sponsorship = $this->Sponsorships->get($id, [
            'contain' => ['Sponsors', 'Sessions', 'Admins', 'Students', 'Fees','Sponsorshippayments'],
        ]);

        $this->set(compact('sponsorship'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addnew()
    {
        $sponsorship = $this->Sponsorships->newEmptyEntity();
          $departments_Table = TableRegistry::get('Departments');
        $Levels_Table = TableRegistry::get('Levels');
        //ensure this is a loggedin admin
        $admincontroller = new AdminsController();
      $admin =   $admincontroller->isadmin();
         
        if ($this->request->is('post')) {
            $sponsorship = $this->Sponsorships->patchEntity($sponsorship, $this->request->getData());
           $sponsorship->admin_id =  $admin->id;
            if ($this->Sponsorships->save($sponsorship)) {
                $feeids = $this->request->getData('fees._ids');
                foreach ($feeids as $feeid){
              //  debug(json_encode( $sponsorship, JSON_PRETTY_PRINT)); exit;

                //update transaction table
                $this->createtransaction($sponsorship->student_id, $feeid);
                }
                $this->Flash->success(__('The sponsorship has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sponsorship could not be saved. Please, try again.'));
        }
        $sponsors = $this->Sponsorships->Sponsors->find('list', ['limit' => 200])->all();
        $sessions = $this->Sponsorships->Sessions->find('list', ['limit' => 20])->all();
       // $admins = $this->Sponsorships->Admins->find('list', ['limit' => 200])->all();
        $students = $this->Sponsorships->Students->find('list', ['limit' => 200])->all();
        $departments =  $departments_Table->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $levels =  $Levels_Table->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $fees = $this->Sponsorships->Fees->find('list', ['limit' => 200])->all();
        $this->set(compact('sponsorship', 'sponsors', 'sessions', 'students', 'fees','departments','levels'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Sponsorship id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sponsorship = $this->Sponsorships->get($id, [
            'contain' => ['Students', 'Fees'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sponsorship = $this->Sponsorships->patchEntity($sponsorship, $this->request->getData());
            if ($this->Sponsorships->save($sponsorship)) {
                $this->Flash->success(__('The sponsorship has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sponsorship could not be saved. Please, try again.'));
        }
        $sponsors = $this->Sponsorships->Sponsors->find('list', ['limit' => 200])->all();
        $sessions = $this->Sponsorships->Sessions->find('list', ['limit' => 200])->all();
        $admins = $this->Sponsorships->Admins->find('list', ['limit' => 200])->all();
        $students = $this->Sponsorships->Students->find('list', ['limit' => 200])->all();
        $fees = $this->Sponsorships->Fees->find('list', ['limit' => 200])->all();
        $this->set(compact('sponsorship', 'sponsors', 'sessions', 'admins', 'students', 'fees'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    
    //creates a transaction record
    public function createtransaction($student_id, $fee_id) {
         $fees_Table = TableRegistry::get('Fees');
        //get session data
         $fee = $fees_Table->get($fee_id);
        $settings = $this->request->getSession()->read('settings');
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->newEmptyEntity();
        $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
        $transaction->amount =  $fee->amount;
        $transaction->student_id = $student_id;
        $transaction->paystatus = "completed";
        $transaction->session_id = $settings->session_id;
        $transaction->semester_id = $settings->semester_id;
        $transaction->gresponse = "Sponsored_payment"; //the order id
        $transaction->fee_id = $fee_id;
        $transaction->invoice_id = $this->createinvoice($student_id, $fee_id);
        // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
        if ($transactions_Table->save($transaction)) {
            $this->Flash->success(__(' payment updated '));
            return;
        } else {
            $this->Flash->error(__(' Sorry, unable to initiate transaction.Please try again'));
            return;
        }
    }
    
    
    
    
    //method that generates invoice
    //method that creates an invoice and assigns a fee to a student manually
    public function createinvoice($student_id, $fee_id) {

          $fees_Table = TableRegistry::get('Fees');
        //get session data
         $fee = $fees_Table->get($fee_id);
        $settings = $this->request->getSession()->read('settings');
        //get the invoice table
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->newEmptyEntity();
        $invoice->student_id = $student_id;
        $invoice->fee_id = $fee_id;
        $invoice->amount = $fee->amount;
        $invoice->paystatus = "success";
        $invoice->session_id = $settings->session_id;
        $invoice->invoiceid = TRANS_REF . $fee_id . 'INV' . $student_id;
        $invoices_Table->save($invoice);
        return $invoice->id;
    }
    
    
      //method that gets students based on chosen department or level
    public function getstudentsindept($deptid) {
           $students_Table = TableRegistry::get('Students');
          $students = $students_Table->find('list')->where(['department_id'=>$deptid]);  
          $this->set(compact( 'students'));
    }


//method that gets the students based on selected level
    public function getstudentsinlevel($levelid) {
       $students_Table = TableRegistry::get('Students');
          $students = $students_Table->find('list')->where(['level_id'=>$levelid]);  
          $this->set(compact( 'students'));  
    }
    
    
    
    //method that shows the student her sponsored fees
    public function sponsoredfees(){
          $student = $this->request->getSession()->read('student');
          $sponsored_Table = TableRegistry::get('Sponsorships');
           $transactions_Table = TableRegistry::get('Transactions');
          $sponsored_fees = $transactions_Table->find()->contain(['Fees','Sessions'])
                  ->where(['student_id'=>$student->id,'gresponse'=>'Sponsored_payment'])
                  ->order(['Transactions.session_id'=>'DESC']);
          
           $this->set(compact( 'sponsored_fees')); 
            $this->viewBuilder()->setLayout('studentsbackend');
          
        
    }
    
    /**
     * Delete method
     *
     * @param string|null $id Sponsorship id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sponsorship = $this->Sponsorships->get($id);
        if ($this->Sponsorships->delete($sponsorship)) {
            $this->Flash->success(__('The sponsorship has been deleted.'));
        } else {
            $this->Flash->error(__('The sponsorship could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
