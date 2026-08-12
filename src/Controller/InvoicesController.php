<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Routing\Router;

/**
 * Invoices Controller
 *
 * @property \App\Model\Table\InvoicesTable $Invoices
 *
 * @method \App\Model\Entity\Invoice[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InvoicesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
         //search for transactions
          if ($this->request->is('post')) {
             //  debug(json_encode($this->request->getData(), JSON_PRETTY_PRINT)); exit;
              //  $from = date('Y-m-d', strtotime(date('Y-m-d',$this->request->getData('startdate'))));
         
            $start_date = $this->request->getData('startdate');
            $date = str_replace('/', '-',  $start_date);
           
             // $to = $this->request->getData('enddate');
            $enddate = $this->request->getData('enddate');
            $to_date = str_replace('/', '-',  $enddate);
           
             //  echo $to.' from '. $from; exit;
            
              $fee_id = $this->request->getData('fee_id');
              $status = $this->request->getData('status');
              $condition = [];
              if(!empty($fee_id)){
              $condition['fee_id'] =   $fee_id;   
              }
               if(!empty($status)){
              $condition['paystatus'] = $status;   
              }
               if (!empty($start_date)) {
                 $from = date('Y-m-d', strtotime($date)); 
            }
            if (!empty($to_date)) { 
              $to = date('Y-m-d', strtotime($to_date));
               
                
            }
             
              $invoices = $this->Invoices->find()
                      ->contain(['Students.Departments', 'Students.ClassArms', 'Fees', 'Sessions']);
              
              // Add date conditions only if dates are provided
              if (!empty($start_date) && !empty($enddate)) {
                  $invoices->where(['DATE(Invoices.createdate) >= ' => $from])
                          ->andWhere(['DATE(Invoices.createdate) <= ' => $to]);
              }
              
              // Add other conditions
              if (!empty($condition)) {
                  $invoices->andWhere($condition);
              }
              
              $invoices->order(['Invoices.createdate' => 'DESC']);

          } else {
              $invoices = $this->Invoices->find()->contain(['Fees', 'Students.Departments', 'Students.ClassArms','Sessions'])
                      ->order(['Invoices.createdate' => 'DESC'])->limit(3000);
             // $this->paginate($invoices);
              // debug(json_encode(  $invoices, JSON_PRETTY_PRINT)); exit;
          }
        $fees = $this->Invoices->Fees->find('list')->order(['name'=>'DESC']);
        $this->set(compact('invoices','fees'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Invoice id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewinvoice($id = null)
    {
        $invoice = $this->Invoices->get($id, [
            'contain' => ['Fees', 'Sessions','Students.Departments',
                'Students.States','Students.Countries','Students.Lgas','Students.Users']
        ]);
         $baseUrl = Router::url('/', true);
        $this->set('baseurl',$baseUrl);
          // debug(json_encode( $invoice, JSON_PRETTY_PRINT)); exit;
        $this->set('invoice', $invoice);
        $this->viewBuilder()->setLayout('backend');
    }

    
    
    //method that verifies payment from etarnsact
    public function verifyetransact(){
       // $success = $this->request->getData('SUCCESS');
       // $amount = $this->request->getData('AMOUNT');
       // $transaction_id = $this->request->getData('TRANSACTION_ID');
        $payment_data = $this->request->getQuery();
        $success = $payment_data['SUCCESS']; $amount = $payment_data['AMOUNT'];
       $transaction_id = $payment_data['TRANSACTION_ID']; 
        
         // $post_data = $this->request->getData();
        if($success==0 ){
              $transactions_Table = TableRegistry::get('Transactions');
              $transaction = $transactions_Table->find()
                      ->contain(['Students.Departments','Students.Programmes','Students.Levels','Sessions','Fees'])
                      ->where(['payref'=>$transaction_id])->first();
            // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
              if($transaction->amount== $amount){
                  //UPDATE TRANSACTION
               $transaction->paystatus = "completed";
              $transaction->gresponse =  $success;
               $transaction->paymentlogid = 'E_'.$this->request->getData('TRANS_NUM');
               $transaction->pgateway = "eTransact";
               $transaction->transdate = date('Y-m-d H:i');
              $transactions_Table->save($transaction);  
              //update invoice
           // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
              $invoices_Table = TableRegistry::get('Invoices');
              $invoice = $invoices_Table->get($transaction->invoice_id);

              $invoice->paystatus = "success";
              $invoice->payday = date('D d M,Y H:i');
              $invoices_Table->save($invoice);
              $this->set('transaction',$transaction );
              //log activity
                  $usercontroller = new UsersController();
                  $title = "Payment via eTransact";
                  $transactioncontroller = new TransactionsController();
                  $user_id =  $transactioncontroller->getUserId($transaction->student_id);
                  $description = "Transaction Ref " . $transaction->payref;
                  $ip = $this->request->clientIp();
                  $type = "Add";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
              //send transaction confirmation email to student
              $transactioncontroller->transactionconfirmationmail($amount, $transaction->student_id, $transaction->fee_id);
            $this->Flash->success(__('The fee payment was sucessful.'));
            return $this->redirect(['controller' => 'Students', 'action' => 'printapplicationform', $transaction->student_id, 'printapplicationform']);
              }
        }
      //   debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
         
         $this->viewBuilder()->setLayout('backend');
    }

    
    
     //method for etransact pin vending
    public function gettransactiondata($transref){
        //fetch data based on transaction ref
          $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()
                ->contain(['Students.Users','Students.Departments','Students.Faculties',
                    'Students.Levels','Sessions','Fees','Students.Programmes'])
                ->where(['payref'=>$transref,'paystatus !='=>'completed'])->first();
        $name = $transaction->student->fname.' '.$transaction->student->lname;
        $faculty = $transaction->student->faculty->name;
        $department = $transaction->student->department->name;
        $level = $transaction->student->level->name;
        $program = $transaction->student->programe->name;
        $session = $transaction->session->name;
        $amount = number_format($transaction->amount,2);
        $fee = 'UAES'.$transaction->fee->name; $regno = $transaction->student->regno;
        $email = $transaction->student->user->username; $phone = $transaction->student->phone;
     $paymentdate =   "PayeeName=$name~Faculty=$faculty~Department= $department ~"
        . "Level=$level~ProgrammeType=Regular~StudyType=$program~Session=$session~"
                . "PayeeID=$transaction->payref~Amount= $amount~FeeStatus=Fee has not been paid~"
                . "Semester=Not Applicable~PaymentType= $fee~MatricNumber=$regno~"
                . "Email= $email~PhoneNumber=$phone";
     echo  $paymentdate;
     exit;
    }


    
    //method that gets payment data from e-transact for payments made via bank branch, pin vending
    public function getpinvendingpayment($postdata){
         //get posted data
         // $postdata = file_get_contents('php://input');
        if(!empty($postdata)){
              $data = explode('&',$postdata);
     $data = explode('&',$postdata);
      debug(json_encode($data, JSON_PRETTY_PRINT)); exit;
      $data[4]; //get payment date
     $data[6];  //get transaction ref
     $tref = explode('=',$data[6]); 
     $transaction_ref = $tref[1];
      //get receipt number
     $receipt = explode('=',$data[0]); $receiptno = $receipt[1];
     $damount = explode('=',$data[3]); $amount =  $damount[1];  //get amount paid
     if(empty($amount)){echo 'invalid amount supplied'; exit;}
          echo $amount.'-'. $receiptno.' - '.$transaction_ref; exit;   
        }
        else{echo "its empty "; exit;}
        $payload = "RECEIPT_NO=5002005121318&PAYMENT_CODE=500852781589300997666&MERCHANT_CODE=7006024HER&TRANS_AMOUNT=5500.0&TRANS_DATE=2020/05/12 16:30:11&TRANS_DESCR=Uchechukwu%20Nzewi-ACCEPTANCE%20FEE%20-001-IMSCONS5E3197C1D0A93290&CUSTOMER_ID=IMOPOLY5ECF71BE473882805080934&BANK_CODE=500&BRANCH_CODE=001&SERVICE_ID=IMSCONS5E3197C1D0A932901143337&CUSTOMER_NAME=Uchechukwu%20Nzewi&CUSTOMER_ADDRESS=2019/DTS/1-2019/DTS/1&TELLER_ID=etzbankteller&USERNAME=%20&PASSWORD=%20&BANK_NAME=eTranzact%20Intl%20Plc&BRANCH_NAME=ETRANZACT&CHANNEL_NAME=Bank&PAYMENT_METHOD_NAME=Cash&PAYMENT_CURRENCY=566&TRANS_TYPE=004&TRANS_FEE=0.0&TYPE_NAME=ACCEPTANCE%20FEE&LEAD_BANK_CODE=700&LEAD_BANK_NAME=eTranzact%20Intl%20Plc&COL1=2019/2020&COL2= College Levy&COL3=Health Sciences&COL4= Dentistry&COL5=ND/HND&COL6=YEAR 1&COL7=Regular&COL8=Not Applicable&COL9=2019/DTS/1&COL10= nzewiuc@gmail.com&COL11=08060407160&COL12=null&COL13=";
        //   print_r( $postdata.' from here'); exit;
     $data = explode('&',$postdata);
     $data = explode('&',$postdata);
     $damount = explode('=',$data[3]); $amount =  $damount[1];  //get amount paid
     if(empty($amount)){echo 'invalid amount supplied'; exit;}
 
     
    // echo   $transaction_ref;  
    
    // echo $receiptno; exit;
       //fetch data based on transaction ref
          $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()
                ->contain(['Students.Users','Students.Departments','Students.Faculties',
                    'Students.Levels','Sessions','Fees','Students.Programmes'])
                ->where(['payref'=>$tref[1],'paystatus !='=>'completed'])->first();
        if(number_format($transaction->amount,2) == number_format($amount,2) ){
               $transaction->paystatus = "completed";
              $transaction->gresponse = 0;
              $transaction->pgateway = "eTransact";
              $transaction->paymentlogid = $receiptno; //save the receipt number
              $transaction->transdate = date('Y-m-d H:i');
            
             $transactions_Table->save($transaction);
              //update invoice
              $invoices_Table = TableRegistry::get('Invoices');
              $invoice = $invoices_Table->get($transaction->invoice_id);
              $invoice->paystatus = "success";
              $invoice->payday = date('D d M,Y H:i');
              $invoices_Table->save($invoice);
              //log activity
                  $usercontroller = new UsersController();
                  $title = "Payment via eTransact";
                  $transactioncontroller = new TransactionsController();
                  $user_id =  $transactioncontroller->getUserId($transaction->student_id);
                  $description = "Transaction Ref " . $transaction->payref;
                  $ip = $this->request->clientIp();
                  $type = "Add";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
              //send payment confirmation mail to student
              $transactioncontroller->transactionconfirmationmail($amount, $transaction->student_id, $transaction->fee_id);
              exit;
        }
        
        echo 'transaction not found'; exit;
        // debug(json_encode( $data, JSON_PRETTY_PRINT)); exit;
        
        
    }

    
   

    //METHOD that updates a successful transaction
    private function updatetransaction($trans_ref,$amount,$status){
        $transactions_Table = TableRegistry::get('Transactions');
              $transaction = $transactions_Table->find()->where(['payref'=>$trans_ref])->first();
              if($transaction->amount== $amount){
                  //UPDATE TRANSACTION
               $transaction->paystatus = "completed";
              $transaction->gresponse =  $status;
               $transaction->paymentlogid = 'E_'.$this->request->getData('TRANS_NUM');
               $transaction->transdate = date('Y-m-d H:i');
              $this->Transactions->save($transaction);  
              //update invoice
           // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
              $invoices_Table = TableRegistry::get('Invoices');
              $invoice = $invoices_Table->get($transaction->invoice_id);

              $invoice->paystatus = "success";
              $invoice->payday = date('D d M,Y H:i');
              $invoices_Table->save($invoice);
              }
    }

    

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $invoice = $this->Invoices->newEmptyEntity();
        if ($this->request->is('post')) {
            $invoice = $this->Invoices->patchEntity($invoice, $this->request->getData());
            if ($this->Invoices->save($invoice)) {
                $this->Flash->success(__('The invoice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The invoice could not be saved. Please, try again.'));
        }
        $fees = $this->Invoices->Fees->find('list', ['limit' => 200]);
        $students = $this->Invoices->Students->find('list', ['limit' => 200]);
        $this->set(compact('invoice', 'fees', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Invoice id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editinvoice($id = null)
    {
        //ensure this is an admin
        $admincontroller = new AdminsController();
         $admincontroller->isadmin();
        $invoice = $this->Invoices->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $invoice = $this->Invoices->patchEntity($invoice, $this->request->getData());
            if ($this->Invoices->save($invoice)) {
                //log activity
                  $usercontroller = new UsersController();

                  $title = "Edited an Invoice" .  $invoice->id;
                  $user_id = $this->Auth->user('id');
                  $description = "Edited an Invoice " .  $invoice->amount;
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The invoice has been saved.'));

                return $this->redirect(['action' => 'viewinvoice', $invoice->id, $invoice->amount]);
            }
            $this->Flash->error(__('The invoice could not be saved. Please, try again.'));
        }
        $fees = $this->Invoices->Fees->find('list', ['limit' => 200]);
        $students = $this->Invoices->Students->find('list', ['limit' => 200]);
        $sessions = $this->Invoices->Sessions->find('list', ['limit' => 200]);
        $this->set(compact('invoice', 'fees', 'students','sessions'));
          $this->viewBuilder()->setLayout('backend');
    }

    
    //admin method for updating fees paid with etransact on classes365
    public function updateoldpayment($invoice_id, $student_id){
        $invoice = $this->Invoices->get($invoice_id);
        $invoice->paystatus = "success";
        $invoice->payday = date('D d M, Y H:i');
       $this->Invoices->save($invoice);
       //update transaction
       $transactions_Table = TableRegistry::get('Transactions');
      $transaction = $transactions_Table->find()->where(['student_id'=>$student_id,'invoice_id'=>$invoice_id])
              ->first();
      if($transaction){
          $transaction->paystatus = "admin_completed";
          $transaction->gresponse = "Admin_Updated";
          $transaction->paymentlogid = "0000000";
        // $transactions_Table->save($transaction);
          //log activity
                  $usercontroller = new UsersController();

                  $title = "Admin updated Invoice for fee paid in Classe365 " .  $this->Auth->user('username');
                  $user_id = $this->Auth->user('id');
                  $description = "Fee payment update by" .  $this->Auth->user('username');
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
         $this->Flash->success(__('The student fee payment record has been updated'));
          return $this->redirect(['controller'=>'Students','action' => 'studentinvoices',$invoice_id, $student_id]);
      }
      else{
          //log activity
                  $usercontroller = new UsersController();

                  $title = "Admin updated Invoice for fee paid in Classe365 " .  $this->Auth->user('username');
                  $user_id = $this->Auth->user('id');
                  $description = "Fee payment update by" .  $this->Auth->user('username');
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
          $this->Flash->success(__('Invoice updated'));

              return $this->redirect(['controller'=>'Students','action' => 'studentinvoices',$invoice_id, $student_id]);
      }
        
    }

    
    
    //admin method for printing payment invoice for students
    public function viewpayment($transaction_id){
      $transactions_Table = TableRegistry::get('Transactions');
      $transaction =   $transactions_Table->get($transaction_id,['contain'=>['Students.Departments','Sessions','Fees',
          'Invoices',  'Students.States','Students.Countries','Students.Lgas','Students.Users']]);
        $this->set('transaction',$transaction);
         $this->viewBuilder()->setLayout('backend');
    }

    
    
    //methdod that gets the student receipts
    public function studentreceipt($invoice_id,$student_id){
        $transactions_Table = TableRegistry::get('Transactions');
      $transaction =   $transactions_Table->find()
              ->where(['Transactions.invoice_id'=>$invoice_id,'Transactions.student_id'=>$student_id,'paystatus'=>'paid'])
              ->contain(['Students.Departments','Sessions','Fees','Students.States',
                  'Students.Countries','Students.Lgas','Students.Users'])
              ->first();
    // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
        $this->set('transaction',$transaction);
         $this->viewBuilder()->setLayout('studentsbackend');
         
    }


    public function mystudentreceipt($invoice_id,$student_id){
        $transactions_Table = TableRegistry::get('Transactions');
      $transaction =   $transactions_Table->find()
              ->where(['Transactions.invoice_id'=>$invoice_id,'Transactions.student_id'=>$student_id,'paystatus'=>'completed'])
              ->contain(['Students.Departments','Sessions','Fees','Students.States',
                  'Students.Countries','Students.Lgas','Students.Users'])
              ->first();
        $this->set('transaction',$transaction);
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    
    //method that present the candidate with their transcript payment receipt
    public function getreceipt($invoice_id,$student_id){
        $transactions_Table = TableRegistry::get('Transactions');
      $transaction =   $transactions_Table->find()
              ->where(['Transactions.invoice_id'=>$invoice_id,'Transactions.student_id'=>$student_id,'paystatus'=>'completed'])
              ->contain(['Students.Departments','Sessions','Fees','Students.Levels','Students.Programmes','Students.States',
                  'Students.Countries','Students.Lgas','Students.Users','Students.Faculties'])
              ->first();
     
        $this->set('transaction',$transaction);
        $this->viewBuilder()->setLayout('loginlayout');
         
    }

        /**
     * Delete method
     *
     * @param string|null $id Invoice id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invoice = $this->Invoices->get($id);
        if ($this->Invoices->delete($invoice)) {
            //delete related transaction
            $transactions_Table = TableRegistry::get('Transactions');
            $transaction = $transactions_Table->find()->where(['invoice_id'=>$invoice->id])->first();
            if($transaction){
            $transactions_Table->delete($transaction);
            }
            //log activity
                  $usercontroller = new UsersController();

                  $title = "Deleted a student invoice " . $invoice->student_id.' '.$invoice->paystatus;
                  $user_id = $this->Auth->user('id');
                  $description = "Deleted a student invoice";
                  $ip = $this->request->clientIp();
                  $type = "Delete";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The invoice has been deleted.'));
        } else {
            $this->Flash->error(__('The invoice could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
   public function beforeFilter(EventInterface $event) {
//     if (in_array($this->request->getParam('action'), ['verifyetransact','gettransactiondata','getpinvendingpayment'])) {
//         $this->setEventManager()->off($this->Csrf);
//     }
      $this->Auth->allow(['verifyetransact','gettransactiondata','getpinvendingpayment','getreceipt']);
 }
 
         //method that transforms the url into something prety
       public  function GenerateUrl ($s) {
  //Convert accented characters, and remove parentheses and apostrophes
  $from = explode (',', "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,(,),[,],'");
  $to = explode (',', 'c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,,,,,,');
  //Do the replacements, and convert all other non-alphanumeric characters to spaces
  $s = preg_replace ('~[^a-zA-Z0-9]+~', '-', str_replace ($from, $to, trim ($s)));
  //Remove a - at the beginning or end and make lowercase
  return strtolower (preg_replace ('/^-/', '', preg_replace ('/-$/', '', $s)));
}


}
