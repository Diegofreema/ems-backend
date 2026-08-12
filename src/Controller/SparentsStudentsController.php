<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Sparents Controller
 *
 * @property \App\Model\Table\SparentsTable $Sparents
 * @method \App\Model\Entity\Sparent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SparentsController extends AppController
{
    /**
     * Before filter callback
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Disable FormProtection for the takeassignmentforstudent action
        if ($this->request->getParam('action') === 'takeassignmentforstudent') {
            $this->FormProtection->setConfig('validate', false);
            $this->FormProtection->setConfig('requireSecure', false);
            $this->FormProtection->setConfig('requireAuth', false);
            $this->FormProtection->setConfig('unlockedFields', ['*']);
        }
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $sparents = $this->paginate($this->Sparents);

        $this->set(compact('sparents'));
    }



    //parents method for viewing their profile
    public function myprofile(){
        $parent = $this->isparent();
        $this->set(compact('parent'));
        $this->viewBuilder()->setLayout('parentsbackend');
    }


    //parents dashboard
    public function dashboard(){
        //ensure this is a valid parent
        $parent = $this->isparent();
        $students_Table = TableRegistry::get('Students');   
        $kids = $students_Table->find()
            ->contain(['Departments', 'ClassArms'])
            ->where(['Students.Sparent_id'=>$parent->id])
            ->all();
        
        // Get additional data for dashboard
        $settings = $this->request->getSession()->read('settings');
        
        // Count total students
        $totalStudents = $kids->count();
        
        // Count suspended students
        $suspendedStudents = $kids->filter(function($student) {
            return $student->studentstatus === 'Suspended';
        })->count();
        
        // Count active students
        $activeStudents = $totalStudents - $suspendedStudents;
        
        // Get recent assignments for all kids
        $setassignmentsTable = TableRegistry::get('Setassignments');
        $recentAssignments = $setassignmentsTable->find()
            ->contain(['Subjects'])
            ->order(['datecreated' => 'DESC'])
            ->limit(5)
            ->all();
        
        // Get unpaid invoices count
        $invoicesTable = TableRegistry::get('Invoices');
        $studentIds = $kids->extract('id')->toArray();
        $unpaidInvoices = 0;
        if (!empty($studentIds)) {
            $unpaidInvoices = $invoicesTable->find()
                ->where([
                    'student_id IN' => $studentIds,
                    'session_id' => $settings->session_id,
                    'paystatus' => 'Unpaid'
                ])
                ->count();
        }
        
        $this->set(compact('kids', 'parent', 'totalStudents', 'activeStudents', 'suspendedStudents', 'recentAssignments', 'unpaidInvoices', 'settings'));
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    //parents method for viewing their kid invoices
    public function mykidinvoices(){
        $students_Table = TableRegistry::get('Students'); 
        $invoices_Table = TableRegistry::get('Invoices');
         $parent = $this->isparent();
        $studentController = new StudentsController();
        $students = $students_Table->find()->contain(['Departments.Fees', 'ClassArms'])->where(['Sparent_id'=>$parent->id]);
        //check for their fees 
        $student_ids = [];
        foreach ($students as $student){
            array_push($student_ids, $student->id);
                //check for fees based on class
          foreach ($student->department->fees as $fee) {
             // debug(json_encode( $fee, JSON_PRETTY_PRINT));exit;
              //check for any fee assigned to this student class and if this fee has been paid
              if ($studentController->checkpayment($student->id, $fee->id) == 0) {
                  //fee has not been paid, check if there is an invoice for it already
                  if ( $studentController->checkinvoice($student->id, $fee->id) == 1) {
                      //there is an unpaid invoice for this fee, check the next fee
                     
                  } else {
                   //no invoices, create new one
                       $studentController->creatnewinvoice($student->id, $fee->id, $fee->amount);
                  }
              }
          }
        }
        
        // Check if parent has any students
        if (empty($student_ids)) {
            // No students found for this parent, return empty result
            $invoices = $invoices_Table->find()
                    ->contain(['Fees', 'Sessions','Students'])
                    ->where(['student_id' => 0]); // This will return no results
        } else {
            $invoices = $invoices_Table->find()
                    ->contain(['Fees', 'Sessions','Students'])
                    ->where(['student_id IN'=>$student_ids]);
        }
        
         $this->set(compact('students','invoices'));
         
        $this->viewBuilder()->setLayout('parentsbackend');
    }



    //method for getting the payee id for a student
    public function getmystudentpayeeid($invoice_id,$student_id){
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id, ['contain' => ['Sessions']]);
        $students_Table = TableRegistry::get('Students');
        $student = $students_Table->get($student_id, ['contain' => ['Departments', 'ClassArms',
                'States', 'Countries', 'Lgas', 'Users']]);
        
        // Check if student is suspended
        if($student->studentstatus === 'Suspended') {
            $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
            return $this->redirect(['action' => 'mykidinvoices']);
        }
        
        $fee = $fees_Table->get($invoice->fee_id);
        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $studentController = new StudentsController();
        $transaction = $studentController->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
        if ($transaction == "none") {
            $transaction = $transactions_Table->newEmptyEntity();
            $transaction->student_id = $student_id;
            $transaction->fee_id = $invoice->fee_id;
            $transaction->session_id = $invoice->session_id;
            $transaction->gresponse = 'initialized';
            $transaction->invoice_id = $invoice->id;
            $transaction->amount = $invoice->amount;
            $transaction->payref = strtoupper(uniqid(PRETRANS)) . date('dmHis');
            $transaction->paystatus = 'initialized';
            // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $transactions_Table->save($transaction);
            $transaction = $transactions_Table->get($transaction->id, ['contain' => ['Sessions']]);
        }
        

        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('parentsbackend');
    }




    //parents method for checking their kids results
    public function mykidsresults(){
        $semesters_Table = TableRegistry::get('Semesters'); 
          $results_Table = TableRegistry::get('Results'); 
      //ensure this is a parent
       $parent = $this->isparent();
            
        if ($this->request->is('post')) {

            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $student_id = $this->request->getData('student_id');
            $term = $semesters_Table->get($semester_id);
             $student = $results_Table->Students->get($student_id,['contain'=>['Departments', 'ClassArms']]);
             
             // Check if student is suspended
             if($student->studentstatus === 'Suspended') {
                 $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
                 return $this->redirect(['action' => 'mykidsresults']);
             }
            $conditions = [];
            if (!empty($semester_id)) {
                $conditions['Results.semester_id'] = $semester_id;
            }
            if (!empty($student_id)) {
                $conditions['Results.student_id'] = $student_id;
            }
            if (!empty($session_id)) {
                $conditions['Results.session_id'] = $session_id;
            }
            $results = $results_Table->find()
                    ->contain(['Departments', 'Subjects', 'Semesters', 'Sessions'])
                   // ->where(['student_id' => $student_id])
                    ->where($conditions)
                    ->where(['Results.approval_status' => 'approved']); // Only show approved results
        //    debug(json_encode($results, JSON_PRETTY_PRINT)); exit;
            $this->set('results', $results);
            $this->set('student', $student);
            $this->set('term', $term);
        } 
        // Get students with custom display format (first name + last name)
        $studentsQuery = $results_Table->Students->find('withFullNames')
            ->where(['Students.sparent_id' => $parent->id]);
        
        $students = [];
        foreach ($studentsQuery as $student) {
            $students[$student->id] = $student->fname . ' ' . $student->lname;
        }
        $semesters = $results_Table->Semesters->find('list', ['limit' => 200]);
        $sessions = $results_Table->Sessions->find('list', ['limit' => 200]);
        $this->set(compact('semesters', 'sessions', 'students'));

        $this->viewBuilder()->setLayout('parentsbackend'); 
    }

    public function downloadPdf($studentId = null, $sessionId = null, $semesterId = null) {
        // Ensure this is a parent
        $parent = $this->isparent();
        
        // Get student information and verify parent relationship
        $results_Table = TableRegistry::get('Results');
        $student = $results_Table->Students->get($studentId, ['contain' => ['Departments', 'ClassArms']]);
        
        // Verify parent relationship
        if ($student->sparent_id != $parent->id) {
            $this->Flash->error(__('You are not authorized to download this student\'s results'));
            return $this->redirect(['action' => 'mykidsresults']);
        }

        // Get approved results for this student
        $conditions = [];
        if (!empty($semesterId)) {
            $conditions['Results.semester_id'] = $semesterId;
        }
        if (!empty($sessionId)) {
            $conditions['Results.session_id'] = $sessionId;
        }
        if (!empty($studentId)) {
            $conditions['Results.student_id'] = $studentId;
        }

        $results = $results_Table->find()
                ->contain(['Departments', 'Subjects', 'Semesters', 'Sessions'])
                ->where($conditions)
                ->where(['Results.approval_status' => 'approved'])
                ->all();

        if ($results->isEmpty()) {
            $this->Flash->error(__('No approved results found for this student'));
            return $this->redirect(['action' => 'mykidsresults']);
        }

        // Get settings
        $settingsTable = TableRegistry::getTableLocator()->get('Settings');
        $settings = $settingsTable->find()->first();

        // Calculate totals
        $total_marks = 0;
        $subjects = 0;
        foreach ($results as $result) {
            $total_marks += $result->total;
            $subjects++;
        }

        // Set data for PDF view
        $this->set(compact('results', 'student', 'settings', 'total_marks', 'subjects'));

        // Generate HTML content
        $this->viewBuilder()->setLayout('pdf');
        $html = $this->render('download_pdf')->getBody();
        
        // Configure DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        $this->response = $this->response->withType('application/pdf');
        $this->response = $this->response->withHeader('Content-Disposition', 'attachment; filename="result_' . $student->regno . '_' . date('Y-m-d') . '.pdf"');
        $this->response = $this->response->withStringBody($dompdf->output());
        
        return $this->response;
    }

    /**
     * View method
     *
     * @param string|null $id Sparent id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sparent = $this->Sparents->get($id, [
            'contain' => ['Users', 'Students'],
        ]);

        $this->set(compact('sparent'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $sparent = $this->Sparents->newEmptyEntity();
        if ($this->request->is('post')) {
            //create parental login
            $username = $this->request->getData('pemailaddress');
            $fathername = $this->request->getData('fathersname');
            $mothername = $this->request->getData('mothersname');
            $mname = " ";
            $puser_id = $this->parentlogindata($username, $fathername, $mothername, $mname);
            $sparent = $this->Sparents->patchEntity($sparent, $this->request->getData());
            $sparent->user_id = $puser_id;
            if ($this->Sparents->save($sparent)) {
                $this->Flash->success(__('The parent has been saved.'));

                return $this->redirect(['controller'=>'Admins','action' => 'viewparents']);
            }
            $this->Flash->error(__('The parent could not be saved. Please, try again.'));
        }
       // $students = $this->Sparents->Students->find('list', ['limit' => 200]);
        $this->set(compact('sparent'));
        $this->set('title', 'Add Parent');
         $this->viewBuilder()->setLayout('backend');
    }




    //method that creates a parent login details
    private function parentlogindata($pemail, $fname, $lname, $mname) {
        $users_Table = TableRegistry::get('Users');
        $user = $users_Table->newEmptyEntity();
        $user->role_id = 4;
        $user->password = "parent123";
        $user->username = $pemail;
        $user->fname = $fname;
        $user->lname = $lname;
        $user->mname = $mname;
        $user->created_by = $this->Auth->user('id');
        // debug(json_encode(  $user, JSON_PRETTY_PRINT)); exit;
        if ($users_Table->save($user)) {
            return $user->id;
        } else {
            $this->Flash->error(__('Sorry, unable to create parent login data. Please, try again.'));
            return "Failed";
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Sparent id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sparent = $this->Sparents->get($id, [
            'contain' => ['Students'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sparent = $this->Sparents->patchEntity($sparent, $this->request->getData());
            if ($this->Sparents->save($sparent)) {
                $this->Flash->success(__('The sparent has been saved.'));

                return $this->redirect(['controller'=>'Admins','action' => 'viewparents']);
            }
            $this->Flash->error(__('The sparent could not be saved. Please, try again.'));
        }
        $users = $this->Sparents->Users->find('list', ['limit' => 200]);
        $students = $this->Sparents->Students->find('list', ['limit' => 200]);
        $this->set(compact('sparent', 'users', 'students'));
        $this->viewBuilder()->setLayout('backend');
    }



    //go to paystack for elijah test payment
    public function gotopaystacktest($student_id, $fee_id, $invoice_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $fee = $fees_Table->get($fee_id);
        $invoice = $invoices_Table->get($invoice_id);

        //create invoice
        // $invoice_id = $this->creatnewinvoice($student_id, $fee_id,  $fee->amount);
        $students_Table = TableRegistry::get('Students');
        $student = $students_Table->get($student_id);
        
        // Check if student is suspended
        if($student->studentstatus === 'Suspended') {
            $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
            return $this->redirect(['action' => 'mykidinvoices']);
        }
        
        $name = $student->fname . ' ' . $student->lname;
        //initialize the transaction before going to paystack
        $settings = $this->request->getSession()->read('settings');
        $transaction = $transactions_Table->newEmptyEntity();
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
        $split_to_cun = $transaction->amount - 1500;
        $baseUrl = Router::url('/', true);
        // $baseurl = "https://portal.claretianuniversity.edu.ng";

        $subacc = 'ACCT_eyec9earijeztxb'; // sub-account code, you get this when you set up a split account.
        $cancel_url = $baseUrl . 'cancel/' . $transaction->payref . '/';
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
                'callback_url' => $baseUrl . 'sparents/paymentverificationtest/' . $transaction->payref,
                'amount' => $transaction->amount . '00',
                'email' => $student->email,
                'name' => $name,
                // 'subaccount' => $subacc,
                'phone' => $student->phone,
                // 'bearer' => 'subaccount',
                // 'transaction_charge' => $split_to_cun . '00',
                // 'last_name' => $lname,
                'reference' => $transaction->payref,
                'metadata' => json_encode([
                    'cancel_action' => $cancel_url,
                    'name' => $name,
                    'fee_id' => $fee_id,
                    //'application_no' => $application_no,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'transaction_id' => $transaction->payref,
                    'student_id' => $student_id,
                    'tranx_id' => $transaction->id,
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

    //verify payment and assign value for elijah test payment
    public function paymentverificationtest($ref) {
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
            return $this->redirect(['controller' => 'Sparents', 'action' => 'mykidinvoices']);
        }

        // debug($tranx); exit;
        $trans_ref = $tranx->data->metadata->transaction_id;
        $trans_id = $tranx->data->metadata->tranx_id;
        //update transaction record
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->get($trans_id);
        $transaction->payref = $trans_ref;
        $transaction->amount = $tranx->data->amount / 100;
        $transaction->paystatus = 'completed';
        $transaction->gresponse = $tranx->data->status;
        $transaction->pgateway = "PayStack";
        
        $transactions_Table->save($transaction);
        // debug($transaction); exit;
        //update invoice
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($transaction->invoice_id);
        $invoice->paystatus = "success";
        $invoice->payday = date('D d M, Y');
        $invoices_Table->save($invoice);
        $transactions_controller = new TransactionsController();
        //log activity
        $usercontroller = new UsersController();
        $title = "Payment via PayStack ";
        $user_id = $transactions_controller->getUserId($transaction->student_id);
        $description = "Transaction Ref " . $transaction->payref;
        $ip = $this->request->clientIp();
        $type = "Add";
        $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
        //log this transaction
        
        $transactions_controller->payattemptlogs($transaction->student_id, $transaction->payref, $tranx->data->status, $transaction->amount, "PayStack");

        if ($transaction->fee_id == 19 || $transaction->fee_id == 40) {
            // $this->updatefeedingfee($transaction->student_id, $transaction->amount);
        }
        $this->Flash->success('Your payment was successful.');
        return $this->redirect(['action' => 'mykidinvoices', $tranx->data->metadata->student_id]);
    }



    //ensures that this is a parent
    public function isparent(){
        $parent = $this->Sparents->find()->where(['user_id'=>$this->Auth->user('id')])->first();
        return $parent;
    }

    /**
     * Delete method
     *
     * @param string|null $id Sparent id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sparent = $this->Sparents->get($id);
        if ($this->Sparents->delete($sparent)) {
            $this->Flash->success(__('The sparent has been deleted.'));
        } else {
            $this->Flash->error(__('The sparent could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Parents method for viewing their kids assignments
     */
    public function mykidsassignments() {
        $parent = $this->isparent();
        
        // Get all students belonging to this parent
        $studentsTable = TableRegistry::get('Students');
        $students = $studentsTable->find()
            ->contain(['Departments', 'ClassArms'])
            ->where(['Students.Sparent_id' => $parent->id])
            ->all();
        
        // Get all available tests for each student
        $setassignmentsTable = TableRegistry::get('Setassignments');
        $assignmentsTable = TableRegistry::get('Assignments');
        
        $allAssignments = [];
        $studentAssignments = [];
        
        foreach ($students as $student) {
            // Get available tests for this student's department/subjects
            $availableTests = $setassignmentsTable->find()
                ->contain(['Subjects.Departments'])
                ->where([
                    'Setassignments.status' => 'active',
                    'Subjects.department_id' => $student->department_id
                ])
                ->all();
            
            // If no tests found for this department, let's get all active tests
            if (empty($availableTests)) {
                $availableTests = $setassignmentsTable->find()
                    ->contain(['Subjects.Departments'])
                    ->where(['Setassignments.status' => 'active'])
                    ->all();
            }
            
            $studentAssignments[$student->id] = [];
            
            foreach ($availableTests as $test) {
                // Check if student has already taken this test
                $existingAssignment = $assignmentsTable->find()
                    ->where(['student_id' => $student->id, 'setassignment_id' => $test->id])
                    ->first();
                
                if ($existingAssignment) {
                    // Check the actual status of the assignment
                    $status = $existingAssignment->status;
                    if ($status === 'completed' || $status === 'submitted') {
                        $status = 'completed';
                    } elseif ($status === 'in_progress') {
                        $status = 'in_progress';
                    } else {
                        $status = 'available'; // fallback
                    }
                    
                    $assignmentData = [
                        'student' => $student,
                        'assignment' => $existingAssignment,
                        'setassignment' => $test,
                        'status' => $status
                    ];
                    
                    $allAssignments[] = $assignmentData;
                    $studentAssignments[$student->id][] = $assignmentData;
                } else {
                    // Student hasn't taken this test yet
                    $assignmentData = [
                        'student' => $student,
                        'assignment' => null,
                        'setassignment' => $test,
                        'status' => 'available'
                    ];
                    
                    $allAssignments[] = $assignmentData;
                    $studentAssignments[$student->id][] = $assignmentData;
                }
            }
        }
        
        $this->set(compact('students', 'allAssignments', 'studentAssignments'));
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    /**
     * Parent takes assignment on behalf of student
     */
    public function takeassignmentforstudent($setassignment_id = null, $student_id = null) {
        // Check if this is a favicon request or other invalid request
        if (strpos($setassignment_id, 'favicon') !== false || strpos($student_id, 'favicon') !== false ||
            strpos($setassignment_id, 'img') !== false || strpos($student_id, 'img') !== false) {
            $this->response = $this->response->withStatus(404);
            return $this->response;
        }
        
        // Validate parameters - ensure they are numeric IDs
        if (!is_numeric($setassignment_id) || !is_numeric($student_id)) {
            // For invalid parameters, just return without rendering anything
            $this->response = $this->response->withStatus(404);
            return $this->response;
        }
        
        // Convert to integers to ensure proper type
        $setassignment_id = (int)$setassignment_id;
        $student_id = (int)$student_id;
        
        // Completely disable FormProtection for this action by overriding its behavior
        $this->FormProtection->setConfig('validate', false);
        $this->FormProtection->setConfig('requireSecure', false);
        $this->FormProtection->setConfig('requireAuth', false);
        $this->FormProtection->setConfig('unlockedFields', ['*']); // Allow all fields
        
        // Also try to disable the component entirely
        if (isset($this->FormProtection)) {
            $this->FormProtection->setConfig('enabled', false);
        }
        
        $parent = $this->isparent();
        
        // Get parameters from route
        if (empty($setassignment_id) || empty($student_id)) {
            $this->Flash->error(__('Invalid parameters provided.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Verify this student belongs to the current parent
        $studentsTable = TableRegistry::get('Students');
        $student = $studentsTable->find()
            ->contain(['Departments', 'ClassArms'])
            ->where(['Students.id' => $student_id, 'Students.Sparent_id' => $parent->id])
            ->first();
        
        if (!$student) {
            $this->Flash->error(__('Access denied. This student does not belong to you.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Check if student is suspended
        if($student->studentstatus === 'Suspended') {
            $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Get the test details
        $setassignmentsTable = TableRegistry::get('Setassignments');
        
        // Add error handling for invalid setassignment ID
        if (empty($setassignment_id) || !is_numeric($setassignment_id)) {
            $this->Flash->error(__('Invalid test ID provided.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        try {
            // First try to find it without contain to see if the basic record exists
            $basicSetassignment = $setassignmentsTable->find()
                ->where(['Setassignments.id' => $setassignment_id])
                ->first();
            
            if (!$basicSetassignment) {
                $this->Flash->error(__('Test not found. Please check if the test still exists.'));
                return $this->redirect(['action' => 'mykidsassignments']);
            }
            
            // Now get it with contain
            $setassignment = $setassignmentsTable->get($setassignment_id, [
                'contain' => ['Subjects.Departments']
            ]);
            
        } catch (\Exception $e) {
            $this->Flash->error(__('Test not found. Please check if the test still exists.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Verify the setassignment exists and is active
        if (!$setassignment || $setassignment->status !== 'active') {
            $this->Flash->error(__('This test is not available or has been deactivated.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Check if assignment already exists for this student and test
        $assignmentsTable = TableRegistry::get('Assignments');
        $existingAssignment = $assignmentsTable->find()
            ->where(['student_id' => $student_id, 'setassignment_id' => $setassignment_id])
            ->first();
        
        if ($existingAssignment) {
            // Assignment exists, check if already completed
            if ($existingAssignment->status === 'submitted') {
                $this->Flash->info(__('This assignment has already been completed.'));
                return $this->redirect(['action' => 'viewstudentresult', $existingAssignment->id]);
            }
            $assignment = $existingAssignment;
        } else {
            // Create new assignment record
            $assignment = $assignmentsTable->newEmptyEntity();
            $assignment->student_id = $student_id;
            $assignment->setassignment_id = $setassignment_id;
            $assignment->subject_id = $setassignment->subject_id; // Set subject_id from setassignment
            $assignment->session_id = 8; // Set current session 2024/2025
            $assignment->status = 'in_progress';
            $assignment->start_time = date('Y-m-d H:i:s');
            $assignment->details = 'CBT Assignment submission for ' . $setassignment->title; // Set required details field
            
            if (!$assignmentsTable->save($assignment)) {
                $this->Flash->error(__('Could not create assignment record. Please, try again.'));
                return $this->redirect(['action' => 'mykidsassignments']);
            }
        }
        
        // Check if test is open (only for new tests, not in-progress ones)
        $now = new \DateTime();
        // Adjust for timezone difference (add 1 hour to match local time)
        $now->add(new \DateInterval('PT1H'));
        if (!empty($setassignment->opendate) && (!$existingAssignment || $existingAssignment->status !== 'in_progress')) {
            // Handle both FrozenDate/FrozenTime objects and string dates
            if ($setassignment->opendate instanceof \Cake\I18n\FrozenDate || 
                $setassignment->opendate instanceof \Cake\I18n\FrozenTime) {
                $openDate = $setassignment->opendate->format('Y-m-d H:i:s');
            } else {
                $openDate = $setassignment->opendate;
            }
            
            
            // Fix timezone issue: Convert both to same timezone for comparison
            $openDateTime = new \DateTime($openDate);
            $openDateTime->setTimezone($now->getTimezone());
            
            if ($now < $openDateTime) {
                $this->Flash->error(__('This test is not yet open.'));
                return $this->redirect(['action' => 'mykidsassignments']);
            }
        }
        
        if (!empty($setassignment->closedate)) {
            // Handle both FrozenDate/FrozenTime objects and string dates
            if ($setassignment->closedate instanceof \Cake\I18n\FrozenDate || 
                $setassignment->closedate instanceof \Cake\I18n\FrozenTime) {
                $closeDate = $setassignment->closedate->format('Y-m-d H:i:s');
            } else {
                $closeDate = $setassignment->closedate;
            }
            
            if ($now > new \DateTime($closeDate)) {
                $this->Flash->error(__('This test has closed.'));
                return $this->redirect(['action' => 'mykidsassignments']);
            }
        }
        
        // Get questions with options
        $questionsTable = TableRegistry::get('Questions');
        
        try {
            // Get questions with options
            $questions = $questionsTable->find()
                ->contain(['QuestionOptions' => function($q) {
                    return $q->order(['QuestionOptions.order_number' => 'ASC']);
                }])
                ->where(['Questions.setassignment_id' => $setassignment->id])
                ->order(['Questions.order_number' => 'ASC'])
                ->all();
                
                
        } catch (\Exception $e) {
            $this->Flash->error(__('Error loading test questions. Please try again.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Check if questions were found
        if (empty($questions)) {
            $this->Flash->error(__('No questions found for this test. Please contact the administrator.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Add all possible answer fields to unlocked fields
        $unlockedFields = ['answers'];
        foreach ($questions as $question) {
            $unlockedFields[] = "answers[{$question->id}]";
        }
        $this->FormProtection->setConfig('unlockedFields', $unlockedFields);
        
        if ($this->request->is('post')) {
            // Check if this is an AJAX request
            $isAjax = $this->request->is('ajax') || 
                      $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
            
            // Process the submission
            $data = $this->request->getData();
            
            
            // For AJAX requests, disable FormProtection validation
            if ($isAjax) {
                $this->FormProtection->setConfig('validate', false);
            }
            
            // Check if we have any answers
            if (empty($data) || !isset($data['answers'])) {
                $this->Flash->error(__('No answers received. Please try again.'));
                return;
            }
            
            // Update assignment status and required fields
            $assignment->status = 'completed'; // Use 'completed' instead of 'submitted' for proper status display
            $assignment->end_time = date('Y-m-d H:i:s');
            
            // Ensure subject_id is set if not already present
            if (empty($assignment->subject_id) && isset($setassignment->subject_id)) {
                $assignment->subject_id = $setassignment->subject_id;
            }
            
            // Ensure session_id is set if not already present (use current session)
            if (empty($assignment->session_id)) {
                $assignment->session_id = 8; // Default to current session 2024/2025
            }
            
            if ($assignmentsTable->save($assignment)) {
                // Save student answers
                $studentAnswersTable = TableRegistry::get('StudentAnswers');
                $answersSaved = 0;
                
                foreach ($questions as $question) {
                    if (isset($data['answers'][$question->id])) {
                        $answerValue = $data['answers'][$question->id];
                        
                        // CRITICAL FIX: Ensure we only process the actual selected value
                        if (empty($answerValue) || $answerValue === '') {
                            continue; // Skip if no answer provided
                        }
                        

                        
                        $studentAnswer = $studentAnswersTable->newEmptyEntity();
                        $studentAnswer->assignment_id = $assignment->id;

                        $studentAnswer->question_id = $question->id;
                        
                        if ($question->question_type === 'multiple_choice') {
                            // CRITICAL FIX: Validate that the selected option ID exists for this question
                            $validOptionId = false;
                            foreach ($question->question_options as $option) {
                                if ($option->id == $answerValue) {
                                    $validOptionId = true;
                                    break;
                                }
                            }
                            
                            if ($validOptionId) {
                                $studentAnswer->selected_option_id = $answerValue;
                            } else {
                                continue; // Skip this answer
                            }
                        } else {
                            $studentAnswer->theory_answer = $answerValue;
                        }
                        
                        if ($studentAnswersTable->save($studentAnswer)) {
                            $answersSaved++;

                }
            }
        }
            
            $successMessage = __('Assignment completed successfully on behalf of ' . $student->fname . ' ' . $student->lname . '. ' . $answersSaved . ' answers saved.');
                
                if ($isAjax) {
                    // Return JSON response for AJAX requests
                    $responseData = [
                        'success' => true,
                        'message' => $successMessage,
                        'redirect' => $assignment->id
                    ];
                    $this->response = $this->response->withType('application/json');
                    $this->response = $this->response->withStringBody(json_encode($responseData));
                    return $this->response;
                } else {
                    $this->Flash->success($successMessage);
                    return $this->redirect(['action' => 'viewstudentresult', $assignment->id]);
                }
            } else {
                $errorMessage = __('Could not save assignment. Please, try again.');
                
                if ($isAjax) {
                    // Return JSON response for AJAX requests
                    $responseData = [
                        'success' => false,
                        'message' => $errorMessage
                    ];
                    $this->response = $this->response->withType('application/json');
                    $this->response = $this->response->withStringBody(json_encode($responseData));
                    return $this->response;
                } else {
                    $this->Flash->error($errorMessage);
                }
            }
        }
        
        // If we've reached here and this was a POST request, we shouldn't render the view
        if ($this->request->is('post')) {
            return;
        }
        
        // Only set variables and render view if we have valid data
        if (isset($assignment) && isset($setassignment) && isset($questions) && isset($student) && 
            !empty($questions)) {
            $this->set(compact('assignment', 'setassignment', 'questions', 'student'));
            $this->viewBuilder()->setLayout('parentsbackend');
        } else {
            $this->Flash->error(__('Unable to load test data. Please try again.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
    }


    /**
     * Parent views student assignment result
     */
    public function viewstudentresult($assignmentId = null) {
        $parent = $this->isparent();
        
        // Get the assignment and verify it belongs to one of the parent's kids
        $assignmentsTable = TableRegistry::get('Assignments');
        $assignment = $assignmentsTable->get($assignmentId, [
            'contain' => ['Students', 'Setassignments.Subjects.Departments']
        ]);
        
        // Verify this student belongs to the current parent
        $studentsTable = TableRegistry::get('Students');
        $student = $studentsTable->find()
            ->contain(['Departments', 'ClassArms'])
            ->where(['Students.id' => $assignment->student_id, 'Students.Sparent_id' => $parent->id])
            ->first();
        
        if (!$student) {
            $this->Flash->error(__('Access denied. This assignment does not belong to your child.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Check if student is suspended
        if($student->studentstatus === 'Suspended') {
            $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
            return $this->redirect(['action' => 'mykidsassignments']);
        }
        
        // Get student answers and calculate score
        $studentAnswersTable = TableRegistry::get('StudentAnswers');
        $studentAnswers = $studentAnswersTable->find()
            ->contain(['Questions.QuestionOptions'])
            ->where(['StudentAnswers.assignment_id' => $assignmentId])
            ->all();
        

        
        $totalQuestions = count($studentAnswers);
        $correctAnswers = 0;
        $totalScore = 0;
        
        // Check if this submission has been graded by a teacher
        $isGraded = isset($assignment->total_score) && $assignment->total_score !== null;
        
        if ($isGraded) {
            // Use the teacher's graded scores
            $totalScore = $assignment->total_score;
            $correctAnswers = $totalScore; // Assuming 1 point per correct answer
        } else {
            // Calculate score from scratch (for ungraded submissions)
            foreach ($studentAnswers as $answer) {
                if ($answer->question->question_type === 'multiple_choice') {
                    if ($answer->selected_option_id) {
                        // Check if selected option is correct
                        foreach ($answer->question->question_options as $option) {
                            if ($option->id == $answer->selected_option_id && $option->is_correct) {
                                $correctAnswers++;
                                $totalScore += $answer->question->points ?? 1;
                                break;
                            }
                        }
                    }
                }
                // Theory questions are not auto-scored
            }
        }
        
        // Calculate maximum possible points
        $maxPossiblePoints = 0;
        foreach ($studentAnswers as $answer) {
            $maxPossiblePoints += $answer->question->points ?? 1;
        }
        
        $percentage = $maxPossiblePoints > 0 ? round(($totalScore / $maxPossiblePoints) * 100, 2) : 0;
        
        // Calculate duration
        $duration = 'Not recorded';
        if ($assignment->start_time && $assignment->end_time) {
            $startTime = ($assignment->start_time instanceof \Cake\I18n\FrozenTime || 
                         $assignment->start_time instanceof \Cake\I18n\FrozenDate) ? 
                        $assignment->start_time->format('Y-m-d H:i:s') : 
                        $assignment->start_time;
            $endTime = ($assignment->end_time instanceof \Cake\I18n\FrozenTime || 
                       $assignment->end_time instanceof \Cake\I18n\FrozenDate) ? 
                      $assignment->end_time->format('Y-m-d H:i:s') : 
                      $assignment->end_time;
            
            $start = new \DateTime($startTime);
            $end = new \DateTime($endTime);
            $diff = $start->diff($end);
            $duration = $diff->format('%H:%I:%S');
        }
        
        $this->set(compact('assignment', 'studentAnswers', 'totalQuestions', 'correctAnswers', 'totalScore', 'percentage', 'duration', 'student'));
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    // Debug method to check correct answers for a question
    public function debugquestion($questionId = null) {
        if (!$questionId) {
            $this->Flash->error('Question ID required');
            return $this->redirect(['action' => 'mykidsassignments']);
        }

        $questionsTable = TableRegistry::get('Questions');
        $question = $questionsTable->get($questionId, [
            'contain' => ['QuestionOptions']
        ]);

        $this->set('question', $question);
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    /**
     * View child's attendance report
     *
     * @return \Cake\Http\Response|void|null
     */
    public function childattendance()
    {
        $parent = $this->isparent();
        
        // Get all students belonging to this parent
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $students = $studentsTable->find()
            ->contain(['Departments', 'ClassArms'])
            ->where(['Students.Sparent_id' => $parent->id])
            ->all();

        // Get date range from query parameters
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));
        $studentId = $this->request->getQuery('student_id');

        // If no student selected, use the first one
        if (!$studentId && !empty($students)) {
            $firstStudent = $students->first();
            if ($firstStudent) {
                $studentId = $firstStudent->id;
            }
        }

        $selectedStudent = null;
        $studentAttendance = [];
        $attendanceStats = [];

        if ($studentId) {
            // Get the selected student
            $selectedStudent = $studentsTable->find()
                ->contain(['Departments', 'ClassArms'])
                ->where(['Students.id' => $studentId, 'Students.Sparent_id' => $parent->id])
                ->first();

            if ($selectedStudent) {
                // Check if student is suspended
                if($selectedStudent->studentstatus === 'Suspended') {
                    $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
                    return $this->redirect(['action' => 'childattendance']);
                }
                
                // Get attendance records for the selected student
                $attendancesTable = TableRegistry::getTableLocator()->get('Attendances');
                $studentAttendance = $attendancesTable->find()
                    ->contain(['Teachers'])
                    ->where([
                        'Attendances.student_id' => $studentId,
                        'Attendances.attendance_date >=' => $startDate,
                        'Attendances.attendance_date <=' => $endDate
                    ])
                    ->order(['Attendances.attendance_date' => 'DESC'])
                    ->all();

                // Calculate attendance statistics
                $totalRecords = $studentAttendance->count();
                $presentCount = 0;
                $absentCount = 0;
                $lateCount = 0;
                $excusedCount = 0;

                foreach ($studentAttendance as $record) {
                    switch ($record->status) {
                        case 'present':
                            $presentCount++;
                            break;
                        case 'absent':
                            $absentCount++;
                            break;
                        case 'late':
                            $lateCount++;
                            break;
                        case 'excused':
                            $excusedCount++;
                            break;
                    }
                }

                $attendanceStats = [
                    'total' => $totalRecords,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'late' => $lateCount,
                    'excused' => $excusedCount,
                    'rate' => $totalRecords > 0 ? round((($presentCount + $lateCount) / $totalRecords) * 100, 1) : 0
                ];
            }
        }

        $this->set(compact('parent', 'students', 'selectedStudent', 'studentAttendance', 'attendanceStats', 'startDate', 'endDate', 'studentId'));
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    /**
     * Print child's attendance report
     *
     * @return \Cake\Http\Response|void|null
     */
    public function printchildattendance()
    {
        $parent = $this->isparent();
        
        $studentId = $this->request->getQuery('student_id');
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));

        if (!$studentId) {
            $this->Flash->error(__('Please select a student.'));
            return $this->redirect(['action' => 'childattendance']);
        }

        // Get the student
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student = $studentsTable->find()
            ->contain(['Departments', 'ClassArms'])
            ->where(['Students.id' => $studentId, 'Students.Sparent_id' => $parent->id])
            ->first();

        if (!$student) {
            $this->Flash->error(__('Student not found.'));
            return $this->redirect(['action' => 'childattendance']);
        }
        
        // Check if student is suspended
        if($student->studentstatus === 'Suspended') {
            $this->Flash->message(__('This student is currently suspended. Please visit the school to resolve this issue.'));
            return $this->redirect(['action' => 'childattendance']);
        }

        // Get attendance records
        $attendancesTable = TableRegistry::getTableLocator()->get('Attendances');
        $attendance = $attendancesTable->find()
            ->contain(['Teachers'])
            ->where([
                'Attendances.student_id' => $studentId,
                'Attendances.attendance_date >=' => $startDate,
                'Attendances.attendance_date <=' => $endDate
            ])
            ->order(['Attendances.attendance_date' => 'DESC'])
            ->all();

        // Calculate statistics
        $totalRecords = $attendance->count();
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;

        foreach ($attendance as $record) {
            switch ($record->status) {
                case 'present':
                    $presentCount++;
                    break;
                case 'absent':
                    $absentCount++;
                    break;
                case 'late':
                    $lateCount++;
                    break;
                case 'excused':
                    $excusedCount++;
                    break;
            }
        }

        $attendanceStats = [
            'total' => $totalRecords,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'excused' => $excusedCount,
            'rate' => $totalRecords > 0 ? round((($presentCount + $lateCount) / $totalRecords) * 100, 1) : 0
        ];

        $this->set(compact('parent', 'student', 'attendance', 'attendanceStats', 'startDate', 'endDate'));
        $this->viewBuilder()->setLayout('ajax');
    }
    
    //admin method for deactivating parents
    public function disableparent($parentid){
        $this->request->allowMethod(['post', 'delete']);
        $parent = $this->Sparents->get($parentid);
        $parent->status = "deactivated";
        if($this->Sparents->save($parent)){
         $this->Flash->success(__('Parent deactivated.'));
            return $this->redirect(['controller'=>'Admins','action' => 'viewparents']);   
        }else{
         $this->Flash->error(__('Sorry, unable to deactivate parent. Please try again'));
            return $this->redirect(['controller'=>'Admins','action' => 'viewparents']);    
        }
        
    }
    
}
