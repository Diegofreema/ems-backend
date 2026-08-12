<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Mailer\Mailer;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Utility\Xml;
use App\Controller\AppController;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Exception\NotFoundException;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 *
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TransactionsController extends AppController {

    public $tranx_id = "";

    //go to paystack for payment of application fee
    public function gotopaystack($email, $phone, $name, $amount, $student_id, $fee_id, $transref, $trans_id) {
        //initialize the transaction before going to paystack
        $settings = $this->request->getSession()->read('settings');
        //  echo $student_id; exit;
        // $split_to_cun = 0;
        $split_to_cun = $amount - 1500;
        // if($amount<=5500){$split_to_cun = 4000;}else{ $split_to_cun = 9000;}
        //base url
        $baseUrl = Router::url('/', true);
        // $baseurl = "https://portal.claretianuniversity.edu.ng";

        $subacc = 'ACCT_eyec9earijeztxb'; // sub-account code, you get this when you set up a split account.
        $cancel_url = $baseUrl . 'cancel/' . $transref . '/';
        //arrange and go to paystack

        /*         * *********************************** */
        /* initialize transaction */
        /*         * ********************************** */
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'callback_url' => $baseUrl . 'transactions/paymentverificationstack/' . $transref,
                'amount' => $amount . '00',
                'email' => $email,
                'name' => $name,
                // 'subaccount' => $subacc,
                'phone' => $phone,
                // 'bearer' => 'subaccount',
                'transaction_charge' => $split_to_cun . '00',
                // 'last_name' => $lname,
                'reference' => $transref,
                'metadata' => json_encode([
                    'cancel_action' => $cancel_url,
                    'name' => $name,
                    'fee_id' => $fee_id,
                    //'application_no' => $application_no,
                    'email' => $email,
                    'phone' => $phone,
                    'transaction_id' => $transref,
                    'student_id' => $student_id,
                    'tranx_id' => $trans_id,
                ]),
            ]),
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . getenv('LEGACY_PAYSTACK_SECRET_KEY'),
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        // Legacy payment key removed.
        // debug(json_encode( $response, JSON_PRETTY_PRINT));exit;

        if ($err) {
            // there was an error contacting the Paystack API
            die('Curl returned error: ' . $err);
        }

        $tranx = json_decode($response);

        if (!$tranx->status) {
            // there was an error from the API
            die('API returned error: ' . $tranx->message);
        }

        //  return $tranx->getData->authorization_url;
        return $this->redirect($tranx->data->authorization_url);
        // header('Location: ' . $tranx->getData->authorization_url);
    }

//verify payment and assign value Paystack
    public function paymentverificationstack($ref) {
        // echo $ref; exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($ref),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . getenv('LEGACY_PAYSTACK_SECRET_KEY'),
                "cache-control: no-cache"
            ],
        ));

        // Legacy payment key removed.


        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            // there was an error contacting the Paystack API
            die('Curl returned error: ' . $err);
        }

        $tranx = json_decode($response);
        // debug( $tranx);
        if (!$tranx->status) {
            // there was an error from the API
            die('API returned error: ' . $tranx->message);
        }
        //ensure payment was successful
        if ($tranx->status != "success") {
            $this->Flash->error('Sorry, the payment was not successful, please try again: ' . $tranx->message);
            return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices']);
        }

        // debug($tranx); exit;
        $trans_ref = $tranx->data->metadata->transaction_id;
        $trans_id = $tranx->data->metadata->tranx_id;
        //update transaction record

        $transaction = $this->Transactions->get($trans_id);
        $transaction->payref = $trans_ref;
        $transaction->amount = $tranx->data->amount / 100;
        $transaction->paystatus = 'completed';
        $transaction->gresponse = $tranx->data->status;
        $transaction->pgateway = "PayStack";
        $this->Transactions->save($transaction);
        //update invoice
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($transaction->invoice_id);
        $invoice->paystatus = "success";
        $invoice->payday = date('D d M, Y');
        $invoices_Table->save($invoice);

        //log activity
        $usercontroller = new UsersController();
        $title = "Payment via PayStack ";
        $user_id = $this->getUserId($transaction->student_id);
        $description = "Transaction Ref " . $transaction->payref;
        $ip = $this->request->clientIp();
        $type = "Add";
        $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
        //log this transaction
        $this->payattemptlogs($transaction->student_id, $transaction->payref, $tranx->data->status, $transaction->amount, "PayStack");

        //send payment alert via email
        $students_Table = TableRegistry::get('Students');
        $student = $students_Table->get($transaction->student_id);
        $messageforapplicants = "Your application is complete. We will get back to you shortly";
        $messageforstudents = "Your fee payment was successful. Please check your email for a copy of your receipt.";
        if (($transaction->fee_id == 2) || ($transaction->fee_id == 22)) {
            $this->Flash->success($messageforapplicants);
            //mail payment receipt to the student
            // $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
            return $this->redirect(['controller' => 'Students', 'action' => 'printapplicationform', $transaction->fee_id, $student->id, $student->fname]);
        } else {
            $this->Flash->success($messageforstudents);
            //mail payment receipt to the student
            // $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
            //back to invoices page
            return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices', $student->id]);
        }

        $this->Flash->success('Your application is complete. We will get back to you shortly ');

        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //verification page for credo payment
    public function verifycredo($ref) {
        // $demoBaseUrl = 'https://api.credodemo.com/transaction/'.$ref.'/verify';
        // $liveVeriyUrl = 'https://api.credocentral.com/transaction/'.$ref.'/verify'
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => CREDOVERYURL . $ref . '/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization:" . SECRETEKEY,
//                "cache-control: no-cache"
            ],
        ));

        // Legacy payment key removed.

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            // there was an error contacting the Paystack API
            die('Curl returned error: ' . $err);
        }

        $tranx = json_decode($response);
        //echo $tranx->data->metadata->payref;
        //debug(json_encode(  $tranx->data->metadata['bankAccount'] , JSON_PRETTY_PRINT)); exit;
        if (!$tranx->status) {
            // there was an error from the API
            die('API returned error: ' . $tranx->message);
        }
        //ensure payment was successful
        if ($tranx->status != 200) {
            $this->Flash->error('Sorry, the payment was not successful, please try again: ' . $tranx->message);
            return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices']);
        }
        //$trans_id =  $tranx->data->metadata->transaction_id; 
        //$pref = $tranx->data->metadata->payref;
        // echo substr($ref, 1); exit;
        //$amountpaid = $tranx->data->metadata->amountpaid;
        $transaction = $this->Transactions->find()->where(['payref' => substr($ref, 1)])->first();
        $transaction->payref = $ref;
        // $transaction->amount = $amountpaid / 100;
        $transaction->paystatus = 'completed';
        $transaction->gresponse = $tranx->data->status;
        $transaction->pgateway = "Credo";
        $this->Transactions->save($transaction);
        //update invoice
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($transaction->invoice_id);
        $invoice->paystatus = "success";
        $invoice->payday = date('D d M, Y');
        $invoices_Table->save($invoice);

        //log activity
        $usercontroller = new UsersController();
        $title = "Payment via Credo ";
        $user_id = $this->getUserId($transaction->student_id);
        $description = "Transaction Ref " . $transaction->payref;
        $ip = $this->request->clientIp();
        $type = "Add";
        $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
        //log this transaction
        $this->payattemptlogs($transaction->student_id, $transaction->payref, $tranx->data->status, $transaction->amount, "PayStack");

        //send payment alert via email
        $students_Table = TableRegistry::get('Students');
        $student = $students_Table->get($transaction->student_id);
        $messageforapplicants = "Your application is complete. We will get back to you shortly";
        $messageforstudents = "Your fee payment was successful. Please check your email for a copy of your receipt.";
        if (($transaction->fee_id == 2) || ($transaction->fee_id == 22)) {
            $this->Flash->success($messageforapplicants);
            //mail payment receipt to the student
            $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
            return $this->redirect(['controller' => 'Students', 'action' => 'printapplicationform', $transaction->fee_id, $student->id, $student->fname]);
        } else {
            $this->Flash->success($messageforstudents);
            //mail payment receipt to the student
            $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
            //back to invoices page
            return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices', $student->id]);
        }

        $this->Flash->success('Your application is complete. We will get back to you shortly ');

        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //verify payment and assign value remita
    public function paymentverification($orderid) {
        $user = $this->request->getSession()->read('usersinfo');

        $response = $this->remita_transaction_details($orderid);
       // $dres = $this->confirmremita($orderid);
       // debug(json_encode( $response, JSON_PRETTY_PRINT));
       // exit;

        $response_code = $response['status'];
        if (isset($response['RRR'])) {
            $rrr = $response['RRR'];
        }
        $response_message = $response['message'];

        if ($response_code == '01' || $response_code == '00') {
            //get and update the transaction
            $transaction = $this->Transactions->find()->where(['gresponse' => $orderid])->first();
            $transaction->status = $response_code;
            $transaction->paystatus = 'completed';
            $transaction->pgateway = 'Remita';
            $this->Transactions->save($transaction);
            //update invoice
            $invoices_Table = TableRegistry::get('Invoices');
            $invoice = $invoices_Table->get($transaction->invoice_id);
            $invoice->paystatus = "success";
            $invoice->payday = date('D d M, Y');
            $invoices_Table->save($invoice);
            //send payment alert via email
            $students_Table = TableRegistry::get('Students');
            $student = $students_Table->get($transaction->student_id);
            $messageforapplicants = "Your application is complete. We will get back to you shortly";
            $messageforstudents = "Your fee payment was successful. Please check your email for a copy of your receipt.";
            if ($user['role_id'] == 2) {
                $this->Flash->success($messageforstudents);
                //mail payment receipt to the student
                $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
                return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices']);
            } else {
                $this->Flash->success($messageforapplicants);
                //mail payment receipt to the student
                $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
                //takes her to a page where she will print her exam entrance form
                return $this->redirect(['action' => 'printentraceform', $student->id]);
            }
            $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
            // $this->Flash->success('Your application is complete. We will get back to you shortly ');
            //takes her to a page where she will print her exam entrance form
            return $this->redirect(['action' => 'printentraceform', $student->id]);
        } else {
            //get and update the transaction if not successful
            $transaction = $this->Transactions->find()->where(['gresponse' => $orderid])->first();
            $transaction->status = $response_code;
            $transaction->paystatus = 'incomplete';
            $this->Transactions->save($transaction);
        }
        $this->Flash->success('Your application is complete. We will get back to you shortly ');

        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('login');
    }

    //get student user id
    public function getUserId($student_id) {
        $students_Table = TableRegistry::get('Students');
        $student = $students_Table->get($student_id);
        return $student->user_id;
    }

    //verify transaction with remita after payment
    //Verify Transaction
    public function remita_transaction_details($orderId) {
        $mert = TESTMERCHANTID;
        $api_key = TESTAPIKEY;
        $concatString = $orderId . $api_key . $mert;
        $hash = hash('sha512', $concatString);
        
        $url = TESTCHECKSTATUSURL . $mert . '/' . $orderId . '/' . $hash . '/' . 'orderstatus.reg';
 return $url;
        //  Initiate curl
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }

    
    
    
    //confirm the status of a transaction on Remita
    public function confirmremitapay($rrr) {
         $user = $this->request->getSession()->read('usersinfo');
        $mert = MERCHANTID;
        $api_key = APIKEY;
        $concatString = $rrr . $api_key . $mert;
        $hash = hash('sha512', $concatString);
        $curl = curl_init();
        $url2 = 'https://demo.remita.net/remita/exapp/api/v1/send/api/'.$rrr.'status.reg';
 $statuspath = "https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/".$mert .'/'.$rrr.'/'.$hash.'/status.reg';
curl_setopt_array($curl, array(
  CURLOPT_URL => $statuspath,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: remitaConsumerKey='.MERCHANTID.',remitaConsumerToken='.$hash
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
$err = curl_error($curl);
   if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }

        curl_close($curl);
      //  debug(json_encode(  $response , JSON_PRETTY_PRINT)); exit;
      $decoded_response =  json_decode($response);
       $response_code = $decoded_response->status;
        if (isset($decoded_response->rrr)) {
            $rrr = $decoded_response->rrr;
        }
        
       if ($response_code == '01' || $response_code == '00') {
            //get and update the transaction
          // echo $rrr; exit;
            $transaction = $this->Transactions->find()->where(['payref' => $rrr])->first();
            $transaction->status = $response_code;
            $transaction->paystatus = 'completed';
            $transaction->pgateway = "Remita";
            $this->Transactions->save($transaction);
            //update invoice
            $invoices_Table = TableRegistry::get('Invoices');
            $invoice = $invoices_Table->get($transaction->invoice_id);
            $invoice->paystatus = "success";
            $invoice->payday = date('D d M, Y');
            $invoices_Table->save($invoice);
            //send payment alert via email
            $students_Table = TableRegistry::get('Students');
            $student = $students_Table->get($transaction->student_id);
            $messageforapplicants = "Your application is complete. We will get back to you shortly";
            $messageforstudents = "Your fee payment was successful. Please check your email for a copy of your receipt.";
            if ($user['role_id'] == 2) {
                $this->Flash->success($messageforstudents);
                //mail payment receipt to the student
                $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
                return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices']);
            } else {
                $this->Flash->success($messageforapplicants);
                //mail payment receipt to the student
                $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
                //takes her to a page where she will print her exam entrance form
                return $this->redirect(['action' => 'printentraceform', $student->id]);
            }
            $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
            // $this->Flash->success('Your application is complete. We will get back to you shortly ');
            //takes her to a page where she will print her exam entrance form
            return $this->redirect(['action' => 'printentraceform', $student->id]);
        } else {
            //get and update the transaction if not successful
           // $transaction = $this->Transactions->find()->where(['gresponse' => $orderid])->first();
            $transaction->status = $response_code;
            $transaction->paystatus = 'incomplete';
            $this->Transactions->save($transaction);
        }
        $this->Flash->success('Your application is complete. We will get back to you shortly ');

        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('login');  
        
        
        
        
    }
    
    
    
    //webhook method for credo reconfirmation
    public function reconfirmtrxcredo(){
        
        
    }
    
    
    //page for confirming Remita payment for CUN Online
    public function cunonlineconfirmpay($rrr){
         $mert = MERCHANTID;
        $api_key = APIKEY;
        $concatString = $rrr . $api_key . $mert;
        $hash = hash('sha512', $concatString);
        $curl = curl_init();
        $url2 = 'https://demo.remita.net/remita/exapp/api/v1/send/api/'.$rrr.'status.reg';
 $statuspath = "https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/".$mert .'/'.$rrr.'/'.$hash.'/status.reg';
curl_setopt_array($curl, array(
  CURLOPT_URL => $statuspath,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: remitaConsumerKey='.MERCHANTID.',remitaConsumerToken='.$hash
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
$err = curl_error($curl);
   if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }

        curl_close($curl);
      //  debug(json_encode(  $response , JSON_PRETTY_PRINT)); exit;
      $decoded_response =  json_decode($response);
       $response_code = $decoded_response->status;
        if (isset($decoded_response->rrr)) {
            $rrr = $decoded_response->rrr;
        }
        
       if ($response_code == '01' || $response_code == '00') {  //payment was successful 
           
       }
       else{
           //payment failed
       }
        
    }
    

    //methodthat sends a mail to the student confirming his payment 
    public function transactionconfirmationmail($amount, $student_id, $fee_id, $tref) {
        $students_table = TableRegistry::get('Students');
        $fees_table = TableRegistry::get('Fees');
        $fee = $fees_table->get($fee_id);
        $settings = $this->request->getSession()->read('settings');
        $student = $students_table->get($student_id, ['contain' => ['Departments']]);
        $app_no_or_regno = "";
        if (empty($student->regno)) {
            $app_no_or_regno = $student->application_no;
        } else {
            $app_no_or_regno = $student->regno;
        }

        $message = " Hello " . ucfirst($student->fname . ' ' . $student->mname . ' ' . $student->lname) . ' ' . ',<br />Your fee payment (' . $fee->name . ') was successful.<br /><br /> .'
                . ' <br />Please find the transaction details below: <br />';

        $message .= '<br />Name : ' . ucfirst($student->fname . ' ' . $student->mname . ' ' . $student->lname);
        $message .= '<br /> AppNo/RegNo : ' . $app_no_or_regno;
        $message .= '<br /> Transaction Ref : ' . $tref;
        $message .= '<br /> Department : ' . $student->department->name;
        $message .= '<br /> Payment Date : ' . date('D, d M Y');
        $message .= '<br /> Fee : ' . $fee->name;
        $message .= '<br /> Session : ' . $settings->session->name;
        $message .= '<br /> Amount : ₦' . number_format($amount * 1, 2);

        $message .= '<br /><br />'
                . 'Kind Regards,<br />'
                . SCHOOL . '<br />';

        // $statusmsg = "";
        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->email);
        $email->setBcc(['payments@claretianuniversity.edu.ng', MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('Fee Payment Receipt');
        if ($email->deliver($message)) {
            $this->Flash->success(__('A payment confirmation mail has been sent to your email address.'));
            return;
        }
        return;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index() {

        //search for transactions
        $settings = $this->request->getSession()->read('settings');
        //  echo 'am called 2'; exit;
        if ($this->request->is('post')) {
            $from = date('Y-m-d', strtotime(date($this->request->getData('startdate'))));

            $to = date('Y-m-d', strtotime(date($this->request->getData('enddate'))));

            $fee_id = $this->request->getData('fee_id');
            $class_id = $this->request->getData('level_id');
            $session_id = $this->request->getData('session_id');
            $payment_gatway = $this->request->getData('pgateway');
            $condition = [];
            if (!empty($fee_id)) {
                $condition['fee_id'] = $fee_id;
            }
            if (!empty($payment_gatway)) {
                $condition['pgateway'] = $payment_gatway;
            }
            if (!empty($class_id)) {
                $condition['Students.level_id'] = $class_id;
            }
            if (!empty($session_id)) {
                $condition['Transactions.session_id'] = $session_id;
            }

            $transactions = $this->Transactions->find()
                    ->contain(['Students.Levels', 'Students.Programmes', 'Fees', 'Sessions', 'Students.States'])
                    ->where(['DATE(transdate) >= ' => $from])
                    ->andwhere(['paystatus' => 'paid'])
                    ->andWhere(['DATE(transdate) <= ' => $to])
                    ->andWhere($condition)
                    ->order(['transdate' => 'DESC'])
                    ->limit(5000);

//             $transactions = $this->Transactions->find()
//                     ->contain(['Students','Fees','Sessions'])
//             ->where(function (QueryExpression $exp, Query $q) {
//                return $exp->between('DATE(transdate)', $from, $to)
//                        ->order(['transdate'=>'DESC']);
//    });
        } else {
            $transactions = $this->Transactions->find()
                    ->contain(['Fees', 'Sessions', 'Students.States'])
                    ->where(['paystatus' => 'paid', 'Transactions.session_id' => $settings->session_id])
                    ->order(['transdate' => 'DESC'])
                    ->limit(2000);
            //get the base url
        }
        // $baseUrl = Router::url('/', true);
        $fees = $this->Transactions->Fees->find('list')->order(['name' => 'ASC']);
        $sessions = $this->Transactions->Sessions->find('list')->order(['name' => 'DESC']);
        //$levelid = [2, 4];
        // $levels = $this->Transactions->Students->Levels->find('list')->order(['name' => 'DESC']);
        $this->set(compact('transactions', 'fees', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
    }
    
    
    //admin method for viewing incomplete transaction
    public function pendingtransactions(){
        //search for transactions
        $settings = $this->request->getSession()->read('settings');
        //  echo 'am called 2'; exit;
        if ($this->request->is('post')) {
            $startfrom = str_ireplace('/', '-', $this->request->getData('startdate'));
            $endon = str_ireplace('/', '-', $this->request->getData('enddate'));
            $from = "";
            $to = "";
            if (!empty($startfrom)) {
                $from = date('Y-m-d', strtotime($startfrom));
            }
            if (!empty($endon)) {
                $to = date('Y-m-d', strtotime($endon));
            }

            $fee_id = $this->request->getData('fee_id');
            $class_id = $this->request->getData('department_id');
            $session_id = $this->request->getData('session_id');
            $payment_gatway = $this->request->getData('pgateway');
            $condition = [];
            if (!empty($fee_id)) {
                $condition['fee_id'] = $fee_id;
            }
            if (!empty($payment_gatway)) {
                $condition['pgateway'] = $payment_gatway;
            }
            if (!empty($class_id)) {
                $condition['Students.department_id'] = $class_id;
            }
            if (!empty($session_id)) {
                $condition['Transactions.session_id'] = $session_id;
            }

            $transactions = $this->Transactions->find()
                    ->contain(['Fees', 'Sessions', 'Students.States', 'Students.Departments'])
                    ->where(['paystatus !=' => 'completed'])
                    ->andWhere($condition)
                    ->order(['transdate' => 'DESC'])
                    ->limit(5000);
            
            // Add date conditions only if dates are provided
            if (!empty($from) && ($from != "1970-01-01")) {
                $transactions = $transactions->andWhere(['DATE(transdate) >=' => $from]);
            }
            if (!empty($to) && ($to != "1970-01-01")) {
                $transactions = $transactions->andWhere(['DATE(transdate) <=' => $to]);
            }
        } else {
            $transactions = $this->Transactions->find()
                    ->contain(['Fees', 'Sessions', 'Students.States', 'Students.Departments'])
                    ->where(['paystatus !=' => 'completed', 'Transactions.session_id' => $settings->session_id])
                    ->order(['transdate' => 'DESC'])
                    ->limit(2000);
            //get the base url
        }
        // $baseUrl = Router::url('/', true);
        $fees = $this->Transactions->Fees->find('list')->order(['name' => 'ASC']);
        $sessions = $this->Transactions->Sessions->find('list')->order(['name' => 'DESC']);
        //$levelid = [2, 4];
        $departments = $this->Transactions->Students->Departments->find('list')->order(['name' => 'DESC']);
        $this->set(compact('transactions', 'fees', 'sessions', 'departments'));
        $this->viewBuilder()->setLayout('backend');    
        
    }
    
    
    
    
    

    //transaction search funtion
    public function searchtransactions() {
        //search for transactions
        $settings = $this->request->getSession()->read('settings');
        //  echo 'am called 2'; exit;
        if ($this->request->is('post')) {
            $startfrom = str_ireplace('/', '-', $this->request->getData('startdate'));
            $endon = str_ireplace('/', '-', $this->request->getData('enddate'));
            $from = "";
            $to = "";
            if (!empty($startfrom)) {
                $from = date('Y-m-d', strtotime(date($startfrom)));
            }
            if (!empty($endon)) {
                $to = date('Y-m-d', strtotime(date($endon)));
            }
            $fee_id = $this->request->getData('fee_id');
            $class_id = $this->request->getData('level_id');
            $session_id = $this->request->getData('session_id');
            $payment_gatway = $this->request->getData('gateway');
            $trans_ref = $this->request->getData('tref');
            //  echo $payment_gatway; exit;
            $condition = [];
            if (!empty($fee_id)) {
                $condition['fee_id'] = $fee_id;
            }
            if (!empty($trans_ref)) {
                $condition['payref'] = $trans_ref;
            }
            if (!empty($payment_gatway)) {
                $condition['pgateway'] = $payment_gatway;
            }
            if (!empty($from) && ($from != "1970-01-01")) {
                $condition['DATE(transdate) >='] = $from;
            }
            if (!empty($to) && ($to != "1970-01-01")) {
                $condition['DATE(transdate) <='] = $to;
            }
            if (!empty($class_id)) {
                $condition['Students.level_id'] = $class_id;
            }
            if (!empty($session_id)) {
                $condition['Transactions.session_id'] = $session_id;
            }
            // print_r($condition); exit;
            $transactions = $this->Transactions->find()
                    ->contain(['Fees', 'Sessions', 'Students.States'])
                    // ->where(['DATE(transdate) >= ' => $from])
                    ->andwhere(['paystatus' => 'paid'])
                    // ->andWhere(['DATE(transdate) <= ' => $to])
                    ->andWhere($condition)
                    ->order(['transdate' => 'DESC']);

//             $transactions = $this->Transactions->find()
//                     ->contain(['Students','Fees','Sessions'])
//             ->where(function (QueryExpression $exp, Query $q) {
//                return $exp->between('DATE(transdate)', $from, $to)
//                        ->order(['transdate'=>'DESC']);
//    });
        } else {
            $transactions = $this->Transactions->find()
                    ->contain(['Fees', 'Sessions', 'Students.States'])
                    ->where(['paystatus' => 'paid', 'Transactions.session_id' => $settings->session_id])
                    ->order(['transdate' => 'DESC']);
            //get the base url
        }
        // $baseUrl = Router::url('/', true);
        $fees = $this->Transactions->Fees->find('list')->order(['name' => 'ASC']);
        $sessions = $this->Transactions->Sessions->find('list')->order(['name' => 'DESC']);
        // $levelid = [5];
        // $levels = $this->Transactions->Students->Levels->find('list')->where(['id NOT IN' => $levelid])->order(['name' => 'DESC']);
        $this->set(compact('transactions', 'fees', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $transaction = $this->Transactions->get($id, [
            'contain' => ['Students']
        ]);

        $this->set('transaction', $transaction);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $transaction = $this->Transactions->newEntity();
        if ($this->request->is('post')) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again now.'));
        }
        $students = $this->Transactions->Students->find('list', ['limit' => 200]);
        $this->set(compact('transaction', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $transaction = $this->Transactions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $fees = $this->Transactions->Fees->find('list', ['limit' => 200]);
        $students = $this->Transactions->Students->find('list', ['limit' => 200])->where(['id' => $transaction->student_id]);
        $this->set(compact('transaction', 'students', 'fees'));
    }

    //api end point for customer and transaction validation
    public function transvalidate() { //echo 'am here'; exit;
        //verify interswitch IP
        $this->verifyKey();
        $this->response->getCharset("UTF-8");
        $this->response->getType("text/xml");
        header("Content-Type: text/xml; charset=utf-8");
        // $this->request->allowMethod(['post', 'put','patch']);

        if ($this->request->is('post')) {
            // $post =  Xml::build($this->request->getData());
            $post = file_get_contents('php://input');
            // debug(json_encode( $post, JSON_PRETTY_PRINT)); exit;
            $xmlArray = Xml::toArray(Xml::build($post));
            $xmlObject = Xml::fromArray($xmlArray, ['format' => 'tags']);
            $xmlString = $xmlObject->asXML();
            $xmlgetData = Xml::build($post);
            $cus_request_array = $xmlArray;

            // print_r( $cus_request_array['Envelope']['soapenv:Body']['CustomerInformationRequest']['CustReference']); exit; //['CustomerInformationRequest']['CustReference']; exit;
            // $xml = simplexml_load_string(implode('', $this->request->getData()),'SimpleXMLElement', LIBXML_NOCDATA);
            //           $xmlObject = Xml::fromArray($this->request->getData(), ['format' => 'tags']);
            // $xmlString = $xmlObject->asXML();
            // $cus_request_array =  Xml::toArray(Xml::build($xmlString));
            // $xml = new SimpleXMLElement($xmlString);
            // check if this is a customer validation request or payment notification


            if (!empty($cus_request_array['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['Amount']) ||
                    !empty($cus_request_array['PaymentNotificationRequest']['Payments']['Payment']['CustReference'])) {
                //this is not a customer information request
                libxml_use_internal_errors(true);

                // $post = file_get_contents('php://input');
                // $xmlgetData = "cvs/paynotification.xml";
                $validxml = simplexml_load_string($post);
                $res = $this->getpaymentnotification($validxml);
                return $res;
                exit;
            }

            // $amount = $cus_request_array['PaymentNotificationRequest']['Payments']['Payment']['Amount'];
            //check for possible change in request format
            if (!empty($cus_request_array['Envelope']['soapenv:Body']['CustomerInformationRequest']['CustReference'])) {

                $trans_ref = $cus_request_array['Envelope']['soapenv:Body']['CustomerInformationRequest']['CustReference'];
                $merchant_ref = $cus_request_array['Envelope']['soapenv:Body']['CustomerInformationRequest']['MerchantReference'];
            } elseif (!empty($cus_request_array['CustomerInformationRequest']['CustReference'])) {

                $trans_ref = $cus_request_array['CustomerInformationRequest']['CustReference'];
                $merchant_ref = $cus_request_array['CustomerInformationRequest']['MerchantReference'];
            } else {
                $trans_ref = "";
            }
            // echo $trans_ref.'what is it'; exit;
            if (empty($trans_ref)) {
                $xmlstring = "<CustomerInformationResponse>"
                        . "<MerchantReference>NOTRef</MerchantReference>"
                        . "<Customers>"
                        . "<Customer>"
                        . "<Status>1</Status>"
                        . "<CustReference></CustReference>"
                        . "<CustomerReferenceAlternate></CustomerReferenceAlternate>"
                        . "<StatusMessage></StatusMessage>"
                        . "<FirstName></FirstName>"
                        . "<LastName></LastName>"
                        . "<OtherName></OtherName>"
                        . "<Email></Email>"
                        . "<Phone></Phone>"
                        . "<ThirdPartyCode></ThirdPartyCode>"
                        . "<Amount>0</Amount>"
                        . "</Customer>"
                        . " </Customers>";
                $xmlstring .= "</CustomerInformationResponse>";

                $xmlObject = Xml::build($xmlstring); // Here will throw an exception
                $xmlarray = Xml::toArray($xmlObject);
                $xmlObject2 = Xml::fromArray($xmlarray);
                echo $xmlObject2->asXML();
                exit;
            }

            //validate merchant ref
            if ($merchant_ref != MERCHANTREF) {
                $xmlstring = "<CustomerInformationResponse>"
                        . "<MerchantReference>$merchant_ref</MerchantReference>"
                        . "<Customers>"
                        . "<Customer>"
                        . "<Status>1</Status>"
                        . "<CustReference></CustReference>"
                        . "<CustomerReferenceAlternate></CustomerReferenceAlternate>"
                        . "<StatusMessage>InvalidMerchantRef</StatusMessage>"
                        . "<FirstName></FirstName>"
                        . "<LastName></LastName>"
                        . "<OtherName></OtherName>"
                        . "<Email></Email>"
                        . "<Phone></Phone>"
                        . "<ThirdPartyCode></ThirdPartyCode>"
                        . "<Amount>0</Amount>"
                        . "</Customer>"
                        . " </Customers>";
                $xmlstring .= "</CustomerInformationResponse>";

                $xmlObject = Xml::build($xmlstring); // Here will throw an exception
                $xmlarray = Xml::toArray($xmlObject);
                $xmlObject2 = Xml::fromArray($xmlarray);
                echo $xmlObject2->asXML();
                exit;
            }
            $transaction = $this->Transactions->find()
                            ->contain(['Students', 'Fees'])
                            ->where(['payref' => $trans_ref, 'paystatus' => 'initialized'])->first();
            // echo $transaction->id; exit;
            if ($transaction) {
                //save transaction id on session so we can use it later
                $this->request->getSession()->delete('tranx_id');
                $this->request->getSession()->write('tranx_id', $transaction->id);

                $regno = $transaction->student->regno;
                $fname = $transaction->student->fname;
                $lname = $transaction->student->lname;
                $mname = $transaction->student->mname;
                $email = trim($transaction->student->email);
                $phone = $transaction->student->phone;
                $fee_name = trim($transaction->fee->name);
                $item_code = trim($transaction->fee->itemcode);

                $xmlstring = '<?xml version="1.0"?>'
                        . "<CustomerInformationResponse>"
                        . "<MerchantReference>$merchant_ref</MerchantReference>"
                        . "<Customers>"
                        . "<Customer>"
                        . "<Status>0</Status>"
                        . "<CustReference>$transaction->payref</CustReference>"
                        . "<CustomerReferenceAlternate>$regno</CustomerReferenceAlternate>"
                        . "<StatusMessage>$transaction->paystatus</StatusMessage>"
                        . "<FirstName>$fname</FirstName>"
                        . "<LastName>$lname</LastName>"
                        . "<OtherName>$mname</OtherName>"
                        . "<Email>$email</Email>"
                        . "<Phone>$phone</Phone>"
                        . "<ThirdPartyCode></ThirdPartyCode>"
                        . "<Amount>$transaction->amount</Amount>"
                        . "<PaymentItems>"
                        . "<Item>"
                        . "<ProductName>$fee_name</ProductName>"
                        . "<ProductCode>$item_code</ProductCode>"
                        . " <Quantity>1</Quantity>"
                        . "<Price>$transaction->amount</Price>"
                        . " <Subtotal>$transaction->amount</Subtotal>"
                        . " <Tax>0</Tax>"
                        . "<Total>$transaction->amount</Total>"
                        . " </Item>"
                        . "</PaymentItems>"
                        . "</Customer>"
                        . " </Customers>";
                $xmlstring .= "</CustomerInformationResponse>";

                try {
                    $xmlObject = Xml::build($xmlstring); // Here will throw an exception
                    $xmlarray = Xml::toArray($xmlObject);
                } catch (\Cake\Utility\Exception\XmlException $e) {
                    throw new InternalErrorException();
                }
                $output = array(
                    "CustomerInformationResponse" => array(// It needs one top element (http://book.cakephp.org/2.0/en/core-utility-libraries/xml.html#transforming-an-array-into-a-string-of-xml)
                        "status" => "OK",
                        "message" => "You are good"
                    )
                );

                $xmlObject2 = Xml::fromArray($xmlarray);
                echo $xmlObject2->asXML();
                exit;
            } else { //if record is not found, does not exist or already paid
                $xmlstring = "<CustomerInformationResponse>"
                        . "<MerchantReference>$merchant_ref</MerchantReference>"
                        . "<Customers>"
                        . "<Customer>"
                        . "<Status>1</Status>"
                        . "<CustReference></CustReference>"
                        . "<CustomerReferenceAlternate></CustomerReferenceAlternate>"
                        . "<StatusMessage>rocordnotfound</StatusMessage>"
                        . "<FirstName></FirstName>"
                        . "<LastName></LastName>"
                        . "<OtherName></OtherName>"
                        . "<Email></Email>"
                        . "<Phone></Phone>"
                        . "<ThirdPartyCode></ThirdPartyCode>"
                        . "<Amount>0</Amount>"
                        . "</Customer>"
                        . " </Customers>";
                $xmlstring .= "</CustomerInformationResponse>";

                $xmlObject = Xml::build($xmlstring); // Here will throw an exception
                $xmlarray = Xml::toArray($xmlObject);
                $xmlObject2 = Xml::fromArray($xmlarray);
                echo $xmlObject2->asXML();
                exit;
            }
        } else {
            echo 'not a post method';
            exit;
        }
    }

    //method that gets payment notifications from interswich
    public function getpaymentnotification() {
        $this->response->getCharset('UTF-8');
        $this->response->getType('text/xml');
        //verify interswitch IP
        $this->verifyKey();

        $post = file_get_contents('php://input');
        $xmlgetData = Xml::build($post);
        $xmlArray = Xml::toArray($xmlgetData);
        //get the customer ref
        $tranx_ref = "";
//ensure the right amount was paid successfully
        // print_r($xmlArray['PaymentNotification']); exit;
        //check for change in xml format
        $logid = "";
        if (!empty($xmlArray['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['Amount'])) {
            $amount = $xmlArray['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['Amount'];
            $status = $xmlArray['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['PaymentStatus'];
            $logid = $xmlArray['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['PaymentLogId'];
            $tranx_ref = $xmlArray['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['CustReference'];
            $isreversal = $xmlArray['PaymentNotification']['PaymentNotificationRequest']['Payments']['Payment']['IsReversal'];
        } elseif (!empty($xmlArray['PaymentNotificationRequest']['Payments']['Payment']['Amount'])) {
            $amount = $xmlArray['PaymentNotificationRequest']['Payments']['Payment']['Amount'];
            $status = $xmlArray['PaymentNotificationRequest']['Payments']['Payment']['PaymentStatus'];
            $logid = $xmlArray['PaymentNotificationRequest']['Payments']['Payment']['PaymentLogId'];
            $tranx_ref = $xmlArray['PaymentNotificationRequest']['Payments']['Payment']['CustReference'];
            $isreversal = $xmlArray['PaymentNotificationRequest']['Payments']['Payment']['IsReversal'];
        } else {
            //invalid customer or no ref supplied
            $xmlstring = "<CustomerInformationResponse>"
                    . "<MerchantReference>7178</MerchantReference>"
                    . "<Customers>"
                    . "<Customer>"
                    . "<Status>1</Status>"
                    . "<CustReference></CustReference>"
                    . "<CustomerReferenceAlternate></CustomerReferenceAlternate>"
                    . "<StatusMessage>InvalidAmountOrCustomerOrNoRef</StatusMessage>"
                    . "<FirstName></FirstName>"
                    . "<LastName></LastName>"
                    . "<OtherName></OtherName>"
                    . "<Email></Email>"
                    . "<Phone></Phone>"
                    . "<ThirdPartyCode></ThirdPartyCode>"
                    . "<Amount>0</Amount>"
                    . "</Customer>"
                    . " </Customers>";
            $xmlstring .= "</CustomerInformationResponse>";

            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }
        //  echo $tranx_ref ; exit;  
        $transaction = $this->Transactions->find()
                        ->contain(['Students', 'Fees'])
                        ->where(['payref' => $tranx_ref])->first();
        //log this transaction
        $this->payattemptlogs($transaction->student_id, $tranx_ref, $status, $amount, "Interswich BankBranch");
        // echo $transaction->payref; exit;
        if (empty($transaction->id)) {
            //transaction not found, return empty

            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>1</Status>"
                    . "<StatusMessage>NotFound</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }
        //check for 0.00 amount
        elseif ($amount == 0.00) {
            //transaction reversal
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>1</Status>"
                    . "<StatusMessage>InvalidAmount</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }
        //check if this is a reversal notification -ve amount
        elseif ($amount < 1) {
            //transaction reversal
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>0</Status>"
                    . "<StatusMessage>PaymentReversed</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            //update transaction as reversed
            // echo 'here to complete'; exit;
            $transaction->paystatus = "Reversed";
            $transaction->gresponse = $status;
            $transaction->paymentlogid = $logid;
            $transaction->pgateway = "InterSwitch";
            $transaction->transdate = date('Y-m-d H:i');
            $this->Transactions->save($transaction);

            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }
        //check for 0 amount
        elseif (($amount == 0.00) || ($amount < $transaction->amount)) {
            $dbamount = $transaction->amount;
            //echo $amount; exit;
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>1</Status>"
                    . "<StatusMessage>InvalidAmount</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }
        //check for reversal using tag
        elseif (!empty($isreversal) && ( $isreversal == "True")) {
            //transaction reversal
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>0</Status>"
                    . "<StatusMessage>PaymentReversed</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            //update transaction as reversed
            // echo 'here to complete'; exit;
            $transaction->paystatus = "Reversed";
            $transaction->gresponse = $status;
            $transaction->paymentlogid = $logid;
            $transaction->pgateway = "InterSwitch";
            $transaction->transdate = date('Y-m-d H:i');
            $this->Transactions->save($transaction);

            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }
        //check payment duplicate
        //check for reversal using tag
        elseif (!empty($isreversal) && ($isreversal == "False")) {
            //transaction reversal
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>0</Status>"
                    . "<StatusMessage>PDORNSV</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        } elseif ($transaction->paystatus === "completed") {
            //transaction already completed

            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>1</Status>"
                    . "<StatusMessage>AlreadyCompleted</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }

        //check for invalid amount
        elseif (number_format(($transaction->amount * 1), 2) != number_format(($amount * 1), 2)) {
            $dbamount = $transaction->amount;
            //echo $amount; exit;
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>1</Status>"
                    . "<StatusMessage>InvalidAmount</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        } elseif (($transaction->amount * 1) == ($amount * 1) && ($status == 0)) {
            // echo 'here to complete'; exit;
            $transaction->paystatus = "completed";
            $transaction->gresponse = $status;
            $transaction->paymentlogid = $logid;
            $transaction->pgateway = "InterSwitch";
            $transaction->transdate = date('Y-m-d H:i');
            $this->Transactions->save($transaction);
            //update invoice
            // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $invoices_Table = TableRegistry::get('Invoices');
            $invoice = $invoices_Table->get($transaction->invoice_id);

            $invoice->paystatus = "success";
            $invoice->payday = date('D d M,Y H:i');
            $invoices_Table->save($invoice);

            $xmlstrings = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>0</Status>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstrings .= "</PaymentNotificationResponse>";

            // return $this->response;
            $xmlObject_success = Xml::build($xmlstrings);
            $xmlarray_success = Xml::toArray($xmlObject_success);
            $xmlObject2_success = Xml::fromArray($xmlarray_success);
            echo $xmlObject2_success->asXML();
            //check if this is acceptance fee and assign reg number
            if ($transaction->fee_id == 3 || $transaction->fee_id == 21 || $transaction->fee_id == 25) {
                $studentcontroller = new StudentsController();
                // $student = $this->Students->get($transaction->student_id);
                $studentcontroller->getregno($transaction->student_id, $transaction->id);
            }
            //send transaction confirmation email to student
            $this->transactionconfirmationmail($amount, $transaction->student_id, $transaction->fee_id, $transaction->payref);
            //log activity
            $usercontroller = new UsersController();
            $title = "Payment via InterSwitch BBranch";
            $user_id = $this->getUserId($transaction->student_id);
            $description = "Transaction Ref " . $transaction->payref;
            $ip = $this->request->clientIp();
            $type = "Add";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);

            exit;
        } else { //if there was issue with the payment
            // echo 'here i am'; exit;
            $xmlstring = "<PaymentNotificationResponse>"
                    . "<Payments>"
                    . "<Payment>"
                    . "<PaymentLogId>$logid</PaymentLogId>"
                    . "<Status>1</Status>"
                    . "<StatusMessage>InvalidAmountorUsedRef</StatusMessage>"
                    . "</Payment>"
                    . "</Payments>";
            $xmlstring .= "</PaymentNotificationResponse>";
            $xmlObject = Xml::build($xmlstring); // Here will throw an exception
            $xmlarray = Xml::toArray($xmlObject);
            $xmlObject2 = Xml::fromArray($xmlarray);
            echo $xmlObject2->asXML();
            exit;
        }

        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            //log activity
            $usercontroller = new UsersController();

            $title = "Deleted a transaction " . $transaction->payref;
            $user_id = $this->Auth->user('id');
            $description = "Deleted a transaction with ref: " . $transaction->payref;
            $ip = $this->request->clientIp();
            $type = "Delete";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function verifyKey() {
        $clientIP = "";
        $ip = "41.223.145.174";
        $ip2 = "154.72.34.174";
        $ip3 = "41.223.145.179";

        $test1 = "41.223.145.177";
        $test2 = "41.223.145.179";
        // interswitch test ip address 41.223.145.177 and 41.223.145.179
        // interswitch Go Live ip address  41.223.145.174 and 
        // interswitch Go Live ip address 154.72.34.174

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $clientIP = trim($_SERVER['REMOTE_ADDR']);
        }
        /* else
          {
          $clientIP = "41.223.145.177";
          } */

        if (
        // trim($clientIP) == trim($test1) or trim($clientIP) == trim($test2) or
                trim($clientIP) == trim($ip3)
                or trim($clientIP) == trim($ip) or trim($clientIP) == trim($ip2)
        ) {
            return true;
        } else {
            exit;
            return false; // please uncomment later when u migrate to the live server
            //return true;			
        }
    }

    //method that renews transaction ref for a failed transaction so it can be redone
    public function updateref($ref) {
        $transaction = $this->Transactions->find()->where(['payref' => $ref])->first();
        if ($transaction->paystatus == "completed") {
            return;
        } else {
            $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
            $transaction->transdate = date('Y-m-d H:i');
            $this->Transactions->save($transaction);
            //echo $transaction->payref; exit;
            return $transaction;
        }

        return;
    }

    //interswitch webpay return method for payments made online
    public function getwebpayinterswitch() {

        //do the confirmation leg
        if ($this->request->is('post')) {
            $post_data = $this->request->getData();

            $trans_ref = $post_data['txnref'];
            $gresponse = $post_data['resp'];
            // echo  $gresponse; exit;
            $transaction = $this->Transactions->find()
                            ->contain(['Students.Users', 'Fees'])
                            ->where(['payref' => $trans_ref])->first();
            //  echo  $trans_ref;
            //  debug(json_encode($transaction , JSON_PRETTY_PRINT)); exit;
            //do transaction confirmation which is a requery
            $total = round($transaction->amount);
            //$total = round($amount_to_pay / SERVICE_CHARGE);
            //echo ($total * 100); exit;
            $tref = $this->retrypayment($total * 100, $post_data['txnref']);
            // debug(json_encode( $post_data , JSON_PRETTY_PRINT)); exit;
            if ($tref == $post_data['txnref']) {
                //send transaction confirmation email to student
                $this->transactionconfirmationmail($transaction->amount, $transaction->student_id, $transaction->fee_id, $transaction->payref);
                $this->Flash->success('Your payment was successful and your application has been completed.');
                //check if this is application or transcript fee payment
                if (($transaction->fee_id == 2) || ($transaction->fee_id == 22)) {

                    return $this->redirect(['controller' => 'Students', 'action' => 'printapplicationform', $transaction->fee_id, $transaction->student_id, $transaction->invoice_id]);
                } elseif ($transaction->fee_id == 23) { //transcript fee
                    return $this->redirect(['controller' => 'Invoices', 'action' => 'getreceipt', $transaction->invoice_id, $transaction->student_id, $transaction->fee_id]);
                } else { //this is transcript fee payment
                    return $this->redirect(['controller' => 'Invoices', 'action' => 'studentreceipt', $transaction->invoice_id, $transaction->student_id, $transaction->fee_id]);
                }
            } else {
                //payment failed or wrong ref
                $this->Flash->error('Your payment failed. Please try again');
                return $this->redirect(['controller' => 'Students', 'action' => 'generateapplicantpayeeid', $transaction->invoice_id, $transaction->student_id]);
            }
        }

        //  $payment_data = $this->request->getQuery();
        // $this->set('postdata',  $payment_data);
        $this->set('title', 'Application Fee Payment - Web Pay InterSwitch');

        $this->viewBuilder()->setLayout('loginlayout');
    }

    //admin method for remotely updating a completed transaction via this url
    public function updatetx($payref) {
        $transaction = $this->Transactions->find()
                        ->where(['payref' => $payref, 'paystatus !=' => 'completed'])->first();
        if (!empty($transaction->id)) {
            //update transaction
            $transaction->paystatus = "completed";
            $transaction->gresponse = 00;
            $transaction->paymentlogid = "010101012345";
            $transaction->transdate = date('Y-m-d H:i');
            $transaction->pgateway = "Paystack";
            // debug(json_encode( $this->request->getData() , JSON_PRETTY_PRINT)); exit; 
            $this->Transactions->save($transaction);
            // update invoice
            $invoices_Table = TableRegistry::get('Invoices');
            $invoice = $invoices_Table->get($transaction->invoice_id);
            $invoice->paystatus = "success";
            $invoice->payday = date('d M Y H:i a');
            $invoices_Table->save($invoice);
            //mail student
            $this->transactionconfirmationmail($transaction->amount, $transaction->student_id, $transaction->fee_id, $transaction->payref);
            $this->Flash->success('Your payment was successful and your application has been completed.');
            return $this->redirect(['controller' => 'Invoices', 'action' => 'index']);
        } else {
            //not found
            $this->Flash->error('Transaction record no found.');
            return $this->redirect(['controller' => 'Invoices', 'action' => 'index']);
        }
    }

    //this method is called after each webpay to check the status of the payment before value is assigned  
    public function retrypayment($amount, $payref) {
        // $amount_to_pay = round($amount / SERVICE_CHARGE);

        $subpdtid = PRODUCT_ID_LIVE; // your product ID is always constant
        $string_to_hash = PRODUCT_ID_LIVE . $payref . MACKEY_LIVE;  // concatenate the strings ("Prod_ID"."txn_ref"."mac") for hash again
        $hash = hash('sha512', $string_to_hash);  //hash to be passed in header

        $query_elements = array(
            "productid" => $subpdtid,
            "transactionreference" => $payref,
            "amount" => $amount
        );
        $link_query_values = http_build_query($query_elements);

        $url = "https://webpay.interswitchng.com/collections/w/pay" . $link_query_values; // json    
        //header details. Put hash here
        $headers = array(
            "GET /HTTP/1.1",
            "Host: webpay.interswitchng.com",
            "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1",
            //"Content-type:  multipart/form-data",
            //"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", 
            "Accept-Language: en-us,en;q=0.5",
            //"Accept-Encoding: gzip,deflate",
            //"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
            "Keep-Alive: 300",
            "Connection: keep-alive",
            "Hash: $hash" //hash value
        );
        //  print_r($url);
        $ch = curl_init();  //INITIALIZE CURL///////////////////////////////
//               
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, false);
//
        $data = curl_exec($ch);  //EXECUTE CURL STATEMENT///////////////////////////////
        $json = null;
        if (curl_errno($ch)) {
            print "Error: " . curl_error($ch) . "</br></br>";

            $errno = curl_errno($ch);
            $error_message = curl_strerror($errno);
            print $error_message . "</br></br>";

            print_r($headers);
            exit;
        } else {
            //update db
            // Show me the result
            $json = json_decode($data, TRUE);
            curl_close($ch);
            //log this attempt
            $transaction = $this->Transactions->find()
                            ->where(['payref' => $payref])->first();
            //change amount back to naira from kobo
            $naira_amount = $amount / 100;
            $this->payattemptlogs($transaction->student_id, $payref, $json["ResponseCode"], $naira_amount, "Interswich WebPay");
            // debug(json_encode( $json , JSON_PRETTY_PRINT)); exit;
            //update db if response code is 00
            if ($json["ResponseCode"] == '00') {
                // debug(json_encode( $json , JSON_PRETTY_PRINT)); exit;
                //update transaction
                $transaction->paystatus = "completed";
                $transaction->gresponse = $json["ResponseDescription"];
                $transaction->paymentlogid = $json["PaymentReference"];
                $transaction->transdate = date('Y-m-d H:i');
                $transaction->pgateway = "InterSwitch";
                // debug(json_encode( $this->request->getData() , JSON_PRETTY_PRINT)); exit; 
                $this->Transactions->save($transaction);
                // update invoice
                $invoices_Table = TableRegistry::get('Invoices');
                $invoice = $invoices_Table->get($transaction->invoice_id);
                $invoice->paystatus = "success";
                $invoice->payday = date('d M Y H:i a');
                $invoices_Table->save($invoice);
                return $transaction->payref;
            } else {
                //the transaction was not succesful
                $this->set('json', $json);
                //print_r(array_values($json));
                //debug(json_encode( $json , JSON_PRETTY_PRINT)); exit;
                // Display Array Elements///////////////
                echo "Transaction Amount: " . $json["Amount"] . "</br>";
                echo "Card Number: " . $json["CardNumber"] . "</br>";
                echo "Transaction Reference: " . $payref . "</br>";
                echo "Payment Reference: " . $json["PaymentReference"] . "</br>";
                echo "Retrieval Reference Number: " . $json["RetrievalReferenceNumber"] . "</br>";
                echo "Lead Bank CBN Code:" . $json["LeadBankCbnCode"] . "</br>";
                echo "Lead Bank Name: " . $json["LeadBankName"] . "</br>";
                //echo "Split Accounts: ".$json["SplitAccounts"]."</br>";
                echo "Transaction Date: " . $json["TransactionDate"] . "</br>";
                echo "Response Code: " . $json["ResponseCode"] . "</br>";
                echo "Response Description: " . $json["ResponseDescription"] . "</br>";
                // //////Display Array Elements////////////
                exit;
            }
        }

        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method for logging payment attemps
    public function payattemptlogs($student_id, $payref, $responsecode, $amount, $pmethod) {
        $paylog_Table = TableRegistry::get('Paylogs');
        $paylog = $paylog_Table->newEmptyEntity();
        $paylog->student_id = $student_id;
        $paylog->tref = $payref;
        $paylog->responsecode = $responsecode;
        $paylog->amount = $amount;
        $paylog->paymethod = $pmethod;
        $paylog_Table->save($paylog);
        return;
    }

    //method that updates a payment after it has been requeired
    public function updateonrequery($tref, $responsdescription, $interswitchref) {
        $transaction = $this->Transactions->find()
                        ->where(['payref' => $tref])->first();
        //update transaction
        $transaction->paystatus = "completed";
        $transaction->gresponse = $responsdescription;
        $transaction->paymentlogid = $interswitchref;
        $transaction->transdate = date('Y-m-d H:i');
        $transaction->pgateway = "InterSwitch";
        // debug(json_encode( $this->request->getData() , JSON_PRETTY_PRINT)); exit; 
        if ($this->Transactions->save($transaction)) {
            $this->Flash->success('Payment was successful and has ben updated');
            return;
        }

        return;
    }

    //redirect method for fluterwave
    public function getpaymentstatusfluter() {
        $post_data = $this->request->getQuery();
        $status = $post_data['status'];
        $ref = $post_data['tx_ref'];
        //check status
        if ($post_data['status'] == 'successful') {
            //update transaction record
            $transaction = $this->Transactions->find()->contain(['Students'])
                            ->where(['payref' => $ref])->first();
            // debug(json_encode(  $transaction , JSON_PRETTY_PRINT)); exit;
            $transaction->status = $post_data['status'];
            $transaction->transdate = date('Y-m-d H:i');
            $transaction->paymentlogid = $post_data['transaction_id'];
            $transaction->paystatus = 'completed';
            $transaction->pgateway = 'FlutterWave';
            $transaction->gresponse = $post_data['status'];
            $this->Transactions->save($transaction);
            // update invoice
            $invoices_Table = TableRegistry::get('Invoices');
            $invoice = $invoices_Table->get($transaction->invoice_id);
            $invoice->paystatus = $post_data['status'];
            $invoice->payday = date('d M Y H:i a');
            $invoices_Table->save($invoice);
            //send payment alert via email
            $this->transactionconfirmationmail($transaction->amount, $transaction->student_id, $transaction->fee_id, $transaction->payref);
            //   $this->payconfirmationmail($transaction->student->email, $transaction->student->fname.' '.$transaction->student->lname, $transaction->amount, $transaction->student_id);
            $this->Flash->success('Your payment was successful.');
            return $this->redirect(['controller' => 'Students', 'action' => 'printapplicationform', $transaction->fee_id, $transaction->student->id]);
        }
    }

    //admin method for removing unpaid transactions
    public function removetrans() {
        $transactions = $this->Transactions->find()->contain(['Students', 'Fees', 'Sessions'])
                        ->order(['Transactions.id' => 'DESC'])->limit(100);
        $this->set('transactions', $transactions);
        $this->viewBuilder()->setLayout('backend');
    }

    //method for validating and updating a failed payment on paystack by admin
    public function requeryfailedpayment() {


        if ($this->request->is('post')) {
            $ref = $this->request->getData('tref');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($ref),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "authorization: Bearer " . getenv('LEGACY_PAYSTACK_SECRET_KEY'),
                    "cache-control: no-cache"
                ],
            ));

            // Legacy payment key removed.

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if ($err) {
                // there was an error contacting the Paystack API
                die('Curl returned error: ' . $err);
            }

            $tranx = json_decode($response);
            // debug( $tranx);
            if (!$tranx->status) {
                // there was an error from the API
                die('API returned error: ' . $tranx->message);
            }
            //ensure transaction was succesful
            if ($tranx->data->status != "success") {
                $this->Flash->error('Sorry, the transaction was not successful');
                return $this->redirect(['action' => 'requeryfailedpayment']);
            }
            //debug(json_encode( $ref , JSON_PRETTY_PRINT)); exit;
            // debug($tranx); exit;
            // $trans_id = $tranx->data->metadata->tranxid;
            //update transaction record
            // echo  $trans_id; exit;
            $transaction = $this->Transactions->find()->where(['payref' => $ref])->first();
            $transaction->status = $tranx->status;
            $transaction->amount = $tranx->data->amount / 100;
            $transaction->paystatus = 'completed';
            $transaction->gresponse = $tranx->data->status;
            $transaction->pgateway = "PayStack";
            $this->Transactions->save($transaction);
            //update invoice
            $invoices_Table = TableRegistry::get('Invoices');
            $invoice = $invoices_Table->get($transaction->invoice_id);
            $invoice->paystatus = "success";
            $invoice->payday = date('D d M, Y');
            $invoices_Table->save($invoice);

            //send payment alert via email
            $students_Table = TableRegistry::get('Students');
            $student = $students_Table->get($transaction->student_id);
            $messageforapplicants = "The payment was successful";
            $messageforstudents = "The payment was successful.";
            if ($transaction->fee_id == 2) {
                $this->Flash->success($messageforapplicants);
                //mail payment receipt to the student
                $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
                return $this->redirect(['controller' => 'Students', 'action' => 'printapplicationform', $student->id, $student->fname]);
            } else {
                $this->Flash->success($messageforstudents);
                //mail payment receipt to the student
                $this->transactionconfirmationmail($transaction->amount, $student->id, $invoice->fee_id, $transaction->payref);
                //back to invoices page
                return $this->redirect(['controller' => 'Users', 'action' => 'dashboard']);
            }

            $this->Flash->success('Your application is complete. We will get back to you shortly ');
        }

        $this->viewBuilder()->setLayout('backend');
    }

    //got credo for application fee payment
    //credo //payment with credo API
    public function gotocredo($student_id, $fee_id, $invoice_id) {

# STEP 1 -> PAYMENT INITIALIZATION
        $transactions_Table = TableRegistry::get('Transactions');
        $students_Table = TableRegistry::get('Students');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $transaction = $transactions_Table->newEmptyEntity();
        $fee = $fees_Table->get(2);
        $invoice = $invoices_Table->get($invoice_id);

        //create invoice
        // $invoice_id = $this->creatnewinvoice($student_id, $fee_id,  $fee->amount);
        $student = $students_Table->get($student_id);
        $name = $student->fname . ' ' . $student->lname;
        //initialize the transaction before going to paystack
        $settings = $this->request->getSession()->read('settings');
        $amount = $invoice->amount;
        $transaction->student_id = $student_id;
        $transaction->fee_id = $fee_id;
        $transaction->session_id = $invoice->session_id;
        $transaction->gresponse = 'initialized';
        $transaction->amount = $invoice->amount;
        $transaction->payref = strtoupper(uniqid(TRANS_REF)) . date('dmHis');
        $transaction->paystatus = 'initialized';
        $transaction->invoice_id = $invoice_id;
        // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
        $transactions_Table->save($transaction);

        $demoBaseUrl = 'https://api.credodemo.com/transaction';
# live Base url = https://api.credocentral.com
        $publishableKey = '0PUB0214cW880hsk1hrmjLM6nT2l6vYR'; //'0PUB0312poF5J29cruYr0ZO1tcAh6V0J';
        $ref = $transaction->payref;

        $data = array(
            "amount" => $amount * 100, # amount is in kobo
            "bearer" => 1, # who bears the fee; 0 = customer, 1 = merchant
            "callbackUrl" => "https://portal.claretianuniversity.edu.ng/transactions/verifycredo/.{$ref}", # set this to your receipt URL as the case may be
            "channels" => array("card", "bank", "ussd"),
            "currency" => "NGN",
            "customerFirstName" => $student->fname,
            "customerLastName" => $student->lname,
            "customerPhoneNumber" => $student->phone,
            "email" => $student->email,
            # metadata is any extra info you want to record with the transaction
            "metadata" => array(
                "bankAccount" => "0114877128",
                "transaction_id" => $transaction->id,
                "payref" => $transaction->payref,
                "amountpaid" => $transaction->amount,
                "customFields" => array(array("variable_name" => "gender", "value" => "Male", "display_name" => "Gender"))
            ),
            "reference" => '.' . $ref, # This must be Unique, if not present we will generate one
            "serviceCode" => "003352ZMTZ3A" # If you have dynamic settlement service setup
        );

        $ex_transaction = curl_init(CREDOLIVEURL);

        curl_setopt($ex_transaction, CURLOPT_POST, true);
        curl_setopt($ex_transaction, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ex_transaction, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ex_transaction, CURLOPT_HTTPHEADER, [
            "Authorization: " . CREDOPUBKEY,
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ex_transaction);
        $err = curl_error($ex_transaction);

        if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }
        curl_close($ex_transaction);
        //debug(json_encode( $response, JSON_PRETTY_PRINT)); exit;
# STEP 2 -> REDIRECT CUSTOMER TO CREDO GATEWAY FOR PAYMENT
//debug(json_encode( $response, JSON_PRETTY_PRINT)); exit;
        $responseData = json_decode($response);

        if ($responseData->status == 200) {
            /*
             * perform other logic like storing the payment information
             * on your database.
             */
            //  debug(json_encode( $responseData, JSON_PRETTY_PRINT)); exit;
            header("Location: " . $responseData->data->authorizationUrl);
            die();
        }
        $this->viewBuilder()->setLayout('loginlayout');
    }

    // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        parent::beforeFilter($event);

        //$this->getEventManager()->off($this->Csrf);
        $this->FormProtection->setConfig('unlockedActions', ['retrypayment', 'transvalidate', 'paymentverification', 'getpaymentnotification', 'getwebpayinterswitch', 'clean', 'getwebpayinterswitchvaluer']);
        $this->Auth->allow(['getwebpayinterswitch', 'getpaymentstatusfluter', 'paymentverification', 'retrypayment', 'paymentverificationstack', 'confirmationmail', 'verifyKey',
            'transactionconfirmationmail', 'gotopaystack', 'gotocredo', 'printapplicationform', 'verifycredo', 'transvalidate', 'getpaymentnotification']);
        // $this->Security->setConfig('unlockedActions', ['transvalidate', 'paymentverification', 'getpaymentnotification', 'getwebpayinterswitch', 'clean', 'getwebpayinterswitchvaluer']);
        $actions = ['paymentverification', 'getpaymentnotification', 'transvalidate', 'requeryfailedpayment'];
        if (in_array($this->request->getParam('action'), $actions)) {

            // turn form protection 
            $this->FormProtection->setConfig('unlockedActions', ['paymentverification', 'getpaymentnotification', 'transvalidate', 'requeryfailedpayment']);
        }
    }
}
