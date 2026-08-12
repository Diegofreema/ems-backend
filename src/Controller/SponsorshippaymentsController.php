<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;

/**
 * Sponsorshippayments Controller
 *
 * @property \App\Model\Table\SponsorshippaymentsTable $Sponsorshippayments
 * @method \App\Model\Entity\Sponsorshippayment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SponsorshippaymentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Sponsorships.Sponsors','Sponsorships.Sessions'],
        ];
        $sponsorshippayments = $this->paginate($this->Sponsorshippayments);
        $sessions_Table = TableRegistry::get('Sessions');
        $sessions = $sessions_Table->find('list');
        $this->set(compact('sponsorshippayments','sessions'));
           $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Sponsorshippayment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewpayment($id = null)
    {
        $sponsorshippayment = $this->Sponsorshippayments->get($id, [
            'contain' => ['Sponsorships.Sponsors'],
        ]);

        $this->set(compact('sponsorshippayment'));
           $this->viewBuilder()->setLayout('backend');
    }

    
    
    //admin method for viewing student sponsorship payment receipt
    public function viewreceipt($fee_id,$sponsorship_id){
          $fees_Table = TableRegistry::get('Fees');
          $sponsorship_Table = TableRegistry::get('Sponsorships');
        //    $sponsorship_payment_Table = TableRegistry::get('Sponsorshippayments');
          $paydetails =  $sponsorship_Table->get($sponsorship_id,['contain'=>['Sponsorshippayments','Sessions','Students.Levels']]);
        $fee = $fees_Table->get($fee_id);
        // $sponsorship_payment =  $sponsorship_payment_Table->get($sponsorship_id);
       // debug(json_encode(   $paydetails, JSON_PRETTY_PRINT)); exit;
          $this->set(compact('paydetails','fee'));
          $this->viewBuilder()->setLayout('backend');
    }
    
    
    
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $sponsorshippayment = $this->Sponsorshippayments->newEmptyEntity();
        if ($this->request->is('post')) {
            $sponsorshippayment = $this->Sponsorshippayments->patchEntity($sponsorshippayment, $this->request->getData());
            if ($this->Sponsorshippayments->save($sponsorshippayment)) {
                $this->Flash->success(__('The sponsorshippayment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sponsorshippayment could not be saved. Please, try again.'));
        }
        $sponsorships = $this->Sponsorshippayments->Sponsorships->find('list', ['limit' => 200]);
        $this->set(compact('sponsorshippayment', 'sponsorships'));
           $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Sponsorshippayment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function updatepayment($id = null)
    {
        $sponsorshippayment = $this->Sponsorshippayments->get($id, [
            'contain' => [],
        ]);
        // debug(json_encode(   $sponsorshippayment, JSON_PRETTY_PRINT)); exit;
        $checkifictfeeispaid = '';
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sponsorshippayment = $this->Sponsorshippayments->patchEntity($sponsorshippayment, $this->request->getData());
         //check if ict fee has been updated
          if($sponsorshippayment->isictfee == "Yes"){ 
              //this is ict fees
          // $checkifictfeeispaid = $this->checksponsordictfee($sponsorshippayment->sponsorship_id);    
             // return;
          }
          else{ //other fees
              $checkifictfeeispaid = $this->checksponsordictfee($sponsorshippayment->sponsorship_id);  
             if($checkifictfeeispaid == 0){
             $this->Flash->error(__('Please complete the ICT fee payment first'));    
                return $this->redirect(['action' => 'index']); 
             }
          }
            if ($this->Sponsorshippayments->save($sponsorshippayment)) {
                
                if(($sponsorshippayment->paystatus=="completed") && ($checkifictfeeispaid != 0)){
             $this->updatefeesforsponsoredstudents($sponsorshippayment->sponsorship_id,$sponsorshippayment->isictfee);
              $this->Flash->success(__('The sponsorship payment has been updated for all related students.'));
                //log activity
                    $usercontroller = new UsersController();

                    $title = "Updated a sponsorship payment ".$sponsorshippayment->paystatus;
                    $user_id = $this->Auth->user('id');
                    $description = "Updated a bulk payment " . $sponsorshippayment->sref;
                    $ip = $this->request->clientIp();
                    $type = "Edit";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type); 
             }elseif(($sponsorshippayment->paystatus=="completed") && ($sponsorshippayment->isictfee == "Yes")){
                 //update sponsored ict fees
              $this->updatefeesforsponsoredstudents($sponsorshippayment->sponsorship_id,$sponsorshippayment->isictfee);
              $this->Flash->success(__('The sponsord ICT fee payment has been updated for all related students.'));
                //log activity
                    $usercontroller = new UsersController();

                    $title = "Updated a sponsorship ICT fee payment ".$sponsorshippayment->paystatus;
                    $user_id = $this->Auth->user('id');
                    $description = "Updated a bulk payment " . $sponsorshippayment->sref;
                    $ip = $this->request->clientIp();
                    $type = "Edit";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type);     
             }
         
                $this->Flash->success(__('The bulk payment update was successful'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sponsorship payment could not be saved. Please, try again.'));
        }
        $sponsorships = $this->Sponsorshippayments->Sponsorships->find('list', ['limit' => 200]);
        $this->set(compact('sponsorshippayment', 'sponsorships'));
           $this->viewBuilder()->setLayout('backend');
    }
    
    
    //method that checks to be sure ICT fee has been paid
    public function checksponsordictfee($sponsorshipid){
        $ict_pay_status = $this->Sponsorshippayments->find()
                ->where(['sponsorship_id'=>$sponsorshipid,'isictfee'=>'Yes'])->first();
         //debug(json_encode(   $ict_pay_status, JSON_PRETTY_PRINT)); exit;
        if(($ict_pay_status->paystatus == "completed")){
        return $ict_pay_status->sponsorship_id;
        }else{ return 0;}
    }


    //method that gets a sponsored student and update their fees
    public function updatefeesforsponsoredstudents($ponsorshipid, $isictfee) {
           $sponsorships_Table = TableRegistry::get('Sponsorships');
           $sponsorships = $sponsorships_Table->get($ponsorshipid,['contain'=>['Students']]);
           foreach($sponsorships->students as $student){
              if($isictfee=="Yes"){ //update ict fee
                  $this->updateictfeesponsord($student->id);} 
              else{ //update other fees
           $this->updatesponsoredfee($student->id,$student->faculty_id,$student->level_id); 
              }
           }
           
        
    }

    
    
    //method that updates fees for sponsored candidates
    public function updatesponsoredfee($student_id,$faculty_id,$level_id){
        
        //verify student is in year 1 and a science student and assign fee
        if (($level_id == 1)&&( ($faculty_id==1) || ($faculty_id==2)
                    || ($faculty_id==4)))  {
            $science_fees = [3,17,6];
            foreach ($science_fees as $fee){
           $invoice_id =   $this->checkinvoice($student_id, $fee);  
          $transaction_id = $this->checkpayment($student_id, $fee,$invoice_id);
            }
            
                    }
    //verify student is in year 1 and arts student
            elseif (($level_id == 1)&& ($faculty_id==3))  {
             $arts_fees = [3,17,1];  
                foreach ( $arts_fees as $fee){
           $invoice_id =   $this->checkinvoice($student_id, $fee);  
          $transaction_id = $this->checkpayment($student_id, $fee,$invoice_id);
            } 
            
            }
  //verify student is not in year 1 and a science student and assign fee for sponsorship
        elseif (($level_id != 1)&&( ($faculty_id==1) || ($faculty_id==2)
                    || ($faculty_id==4)))  {
            $science_fees = [17,6];
            foreach ($science_fees as $fee){
           $invoice_id =   $this->checkinvoice($student_id, $fee);  
          $transaction_id = $this->checkpayment($student_id, $fee,$invoice_id);
            }
                    }
  //verify student is not in year 1 and arts student
            elseif (($level_id != 1)&& ($faculty_id==3))  {
               $arts_fees = [17,1];  
             foreach ( $arts_fees as $fee){
           $invoice_id =   $this->checkinvoice($student_id, $fee);  
          $transaction_id = $this->checkpayment($student_id, $fee,$invoice_id);
            } 
            }
        
      return;  
    }

    
    
    //update ict fee for sponsored students
    public function updateictfeesponsord($student_id){
        $ict_fee_id = 5;
        $invoice_id =   $this->checkinvoice($student_id, $ict_fee_id);  
          $transaction_id = $this->checkpayment($student_id, $ict_fee_id,$invoice_id);
          return;
        
    }




    //method that checks if a given payment has been made
    private function checkpayment($student_id, $fee_id,$invoice_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
         $fees_Table = TableRegistry::get('Fees');
          $fee =  $fees_Table->get($fee_id);
        $current_session = $this->request->getSession()->read('settings');
        
        // Check Transactions table for completed payments
        $payment = $transactions_Table->find()
                        ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'session_id' => $current_session->session_id,
                            'paystatus' => 'completed'])->first();

        // Also check Invoices table for successful payments
        $invoice = $invoices_Table->find()
                        ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'session_id' => $current_session->session_id,
                            'paystatus' => 'success'])->first();

        if (empty($payment->id) && empty($invoice->id)) {
            $transaction = $transactions_Table->newEmptyEntity();
            $transaction->student_id = $student_id;
            $transaction->fee_id = $fee_id;
            $transaction->session_id = $current_session->session_id;
            $transaction->gresponse = 'sponsord';
            $transaction->invoice_id = $invoice_id;
            $transaction->amount =   $fee->amount;
            $transaction->payref = strtoupper(uniqid(TRANS_REF) .date('dmyHis'));
            $transaction->paystatus = 'completed';
            //debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $transactions_Table->save($transaction);
            return $transaction->id;
        } else{
           $payment->gresponse = 'sponsord';
           $payment->paystatus = 'completed'; 
           $transactions_Table->save($payment);
           return $payment->id;
        }
    }
    
    
    //check if there is an exisiting invoice for a particular fee
    private function checkinvoice($student_id, $fee_id) {
        $invoices_sTable = TableRegistry::get('Invoices');
          $fees_Table = TableRegistry::get('Fees');
          $fee =  $fees_Table->get($fee_id);
        $current_session = $this->request->getSession()->read('settings');
        $payment = $invoices_sTable->find()
                        ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'session_id' => $current_session['session_id'],
                            'paystatus' => 'Unpaid'])->first();

        if (!empty($payment->id)) {
             $payment->paystatus = "success";
              $invoices_sTable->save($payment);
             return $payment->id;
         
        }
        else{
        //get the invoice table
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->newEmptyEntity();
        $invoice->student_id = $student_id;
        $invoice->fee_id = $fee_id;
        $invoice->amount =   $fee->amount;
        $invoice->session_id = $current_session['session_id'];
        $invoice->invoiceid = INVOICEPREFIX. $fee_id . '/' . $student_id;
        $invoice->paystatus = "success";
        $invoices_Table->save($invoice);
        return $invoice->id;
       
        }
    }

    

    /**
     * Delete method
     *
     * @param string|null $id Sponsorshippayment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sponsorshippayment = $this->Sponsorshippayments->get($id);
        if ($this->Sponsorshippayments->delete($sponsorshippayment)) {
            $this->Flash->success(__('The sponsorshippayment has been deleted.'));
            //log activity
                    $usercontroller = new UsersController();

                    $title = "Deleted a sponsorship ICT fee payment ".$sponsorshippayment->paystatus;
                    $user_id = $this->Auth->user('id');
                    $description = "Deleted a bulk payment " . $sponsorshippayment->sref;
                    $ip = $this->request->clientIp();
                    $type = "Delete";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type);   
        } else {
            $this->Flash->error(__('The sponsorshippayment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
