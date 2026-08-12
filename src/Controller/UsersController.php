<?php

declare(strict_types = 1);

namespace App\Controller;

use Cake\Routing\Router;
use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use Exception;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController {

    public function login() {
        //get the logo on the login page
        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        if ($this->request->is('post')) {
         $id_token =  $this->request->getData('id_token'); 
       //  echo '$id_token'; exit;
            //   debug(json_encode($this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $user = $this->Auth->identify();
            // Create user binding. This is optional as binding is not required to send SMS notifications.
            // $this->Users->bindUser($user);
            // Send welcome message. if binding is disabled, do not add the last arg `true`.
            // $this->Users->notifyUser($user, 'Welcome to Cake Notifier', true);
            //handle sign in
           // require_once 'vendor/autoload.php';

// Get $id_token via HTTPS POST.

//$client = new Google_Client(['client_id' => CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
//$payload = $client->verifyIdToken($id_token);
//if ($payload) {
//  $userid = $payload['sub'];
//  // If request specified a G Suite domain:
//  //$domain = $payload['hd'];
//} else {
//  // Invalid ID token
//}
          

            if ($user && $user['userstatus'] != 'Disabled') {
                $this->Auth->setUser($user);
                $RolesTable = TableRegistry::get('Roles');
                $roles = $RolesTable->get($user['role_id']);
                $this->updateLogout($user['id']);
                $this->createLogin($user['id']);
                //get the system settings and put it in session
                // $settings = $settings_Table->get(1);
                $this->request->getSession()->write('settings', $settings);
                $this->request->getSession()->write('usersinfo', $user);
                $this->request->getSession()->write('usersroles', $roles);
                if ($user['role_id'] == 2) {
                    //get the student and put it in session
                    $studentsTable = TableRegistry::get('Students');
                    $student = $studentsTable->find()->where(['user_id' => $user['id'], 'status' => 'Admitted'])->first();
                    if (!$student) {//not yet admitted
                        $this->Flash->error('Invalid access. Student not admitted yet');

                        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                    } elseif (empty($student->passporturl)) {
                        //yet to update profile
                        $this->Flash->error(__('Sorry, you must update your profile to continue. Please ensure you select your state '
                                        . 'of origin, current class/level, program and uplaod a passport(less than 1mb jpg, jpeg or png).'));
                        //hide the navigations
                        $is_owing = 'is_owing';
                        $this->request->getSession()->write('is_owing', $is_owing);
                        $this->request->getSession()->write('student', $student);
                        return $this->redirect(['controller' => 'Students', 'action' => 'updateprofile']);
                    }
                     elseif ($student->studentstatus == "Suspended") {
                        //yet to update profile
                        $this->Flash->error(__('Sorry, your account has been suspended. Please contact admin or visit the ICT unit for assistance'));
                      
                        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                    }
                    $this->request->getSession()->write('student', $student);
                    //check student fee payment for the session
             $paidfees =     $this->checkfees($student->id);
             if($paidfees<4){
                $this->Flash->error(__('Sorry, you still have some outstanding fees, you need to pay up'
                 . ' else you wont be able to access your results')); 
             }
                    return $this->redirect(['controller' => 'Students', 'action' => 'dashboard']);
                    
                } elseif ($user['role_id'] == 3) {
                    //get the teacher or employee details and put them in session
                    $teachersTable = TableRegistry::get('Teachers');
                    $teacher = $teachersTable->find()->where(['user_id' => $user['id']])->first();
                    $this->request->getSession()->write('teacher', $teacher);
                    return $this->redirect(['controller' => 'Teachers', 'action' => 'dashboard']);
                } elseif($user['role_id']==4){
                    //get the parent details and put them in session
                      $parentsTable = TableRegistry::get('Sparents');
                    $parent =  $parentsTable->find()->where(['user_id'=>$user['id']])->first();
                     $this->request->getSession()->write('parent', $parent);
                   return $this->redirect(['controller' => 'Sparents', 'action' => 'dashboard']);
                } elseif (($user['role_id'] == 1) || ($user['role_id'] == 5) || $user['role_id'] == 7) {
                    //get the admin and put it in session
                    $adminsTable = TableRegistry::get('Admins');
                    $admin = $adminsTable->find()->contain(['Privileges'])->where(['user_id' => $user['id']])->first();
                    // debug(json_encode(   $admin , JSON_PRETTY_PRINT)); exit;
                    $this->request->getSession()->write('admin', $admin);

                    return $this->redirect(['controller' => 'Users', 'action' => 'dashboard']);
                }
            } else {
                $this->Flash->error('Bad Credentials or account disabled. Please check your credentials or contact admin for assistance');
            }
        }
        $this->set('logo', $settings);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method that creates a cookie and redirects the admin with HR privilege to the HR system
    public function ishr($privilegid) {
        $hradmin = $this->request->getSession()->read('admin');
        $privileges = [];
        // debug(json_encode($admindata->privileges, JSON_PRETTY_PRINT)); exit;
        foreach ($hradmin->privileges as $privilege) {
            array_push($privileges, $privilege->id);
        }
        if (in_array($privilegid, $privileges)) {
            $cookie = (new Cookie('HRUSERCUN'))
                    ->withValue('1')
                    ->withExpiry(new DateTime('+1 year'))
                    ->withPath('/')
                    ->withDomain('example.com')
                    ->withSecure(false)
                    ->withHttpOnly(true);
            // Create a new collection
            $cookies = new CookieCollection([$cookie]);

// Add to an existing collection
            $cookies = $cookies->add($cookie);
// Check if a cookie exists
            $cookies->has('remember_me');
            return 1;
        }
    }
    
    
    //method that checks students fee payment
    public function checkfees($studentid){
   // $studentscontroller = New StudentsController();
    // $student =   $studentscontroller->isstudent();
              $transactions_table = TableRegistry::get('Transactions');
              $invoices_table = TableRegistry::get('Invoices');
              $session = $this->request->getSession()->read('settings');
              //$past_session_id = $session->session_id - 1;
     //check payment from both Transactions and Invoices tables
     $transaction_payments =  $transactions_table->find()
             ->where(['student_id'=>$studentid,'session_id'=>$session->session_id,'paystatus'=>'completed'])
             ->count();
             
     $invoice_payments = $invoices_table->find()
             ->where(['student_id'=>$studentid,'session_id'=>$session->session_id,'paystatus'=>'success'])
             ->count();
             
     return $transaction_payments + $invoice_payments;
//     if(  $payment>=4){
//         return;
//     }
//     else{ //put in session that this person is owing
//         $isowing = $this->request->getSession()->write('isowing','isowing');
//         $this->Flash->error(__('Sorry, you still have some outstanding fees, you need to pay up'
//                 . ' else you wont be able to access your results'));
//       return $this->redirect(['controller'=>'Students','action' => 'dashboard']);  
//     }    
    }





    //the admin  user dashboard
    public function dashboard() {
        $settings = $this->request->getSession()->read('settings');
        $admin = $this->Users->get($this->Auth->user('id'));
        $students_Table = TableRegistry::get('Students');
        $departments_Table = TableRegistry::get('Departments');
        $courseregistrations_Table = TableRegistry::get('Courseregistrations');
        $teachers_Table = TableRegistry::get('Teachers');
        $fees_Table = TableRegistry::get('Fees');
        $admins_Table = TableRegistry::get('Admins');
        $hostels_Table = TableRegistry::get('Hostels');
        $subjects_Table = TableRegistry::get('Subjects');
        $trequests_Table = TableRegistry::get('Trequests');
        $trequests = $trequests_Table->find()->where(['deliverystatus !=' => 'Delivered'])->count();
        $transactions_table = TableRegistry::get('Transactions');
        $classes = $departments_Table->find()->count();
        $fees = $fees_Table->find()->count();
        $hostels = $hostels_Table->find()->count();
        $admins = $admins_Table->find()->count();
        $years = [date('Y'), date('Y') - 1, date('Y') - 2,date('Y')-3,date('Y')-4];
        $years_conditions['admissiondate IN'] = $years;
        $parents_Table = TableRegistry::get('Sparents');
      
        $condition = array(DATE('admissiondate') . ' BETWEEN NOW() AND ' . (date('Y') - 4));
        $current_students = $students_Table->find()->where($years_conditions)->count();
        $trsnactions_graph = $transactions_table->find()
                ->where(['DATE(transdate) > DATE(DATE_SUB(NOW(), INTERVAL 180 DAY))','paystatus' => 'completed']);
       $trsnactions_graph->select([
            'totalvalue' => $trsnactions_graph->func()->sum('amount'),
           'count' => $trsnactions_graph->func()->count('id'),
            'duration' => 'DATE(transdate)'
        ])
        ->group('MONTH(transdate)');
        // debug(json_encode( $trsnactions_graph, JSON_PRETTY_PRINT)); exit;
        //get alumnai
        $alumni = $students_Table->find()->where(['level_id' => 5])->count();
        $course_regs = $courseregistrations_Table->find()->where(['session_id' => $settings->session_id,
                    'semester_id' => $settings->semester_id])->count();
        $payments = $transactions_table->find()->where(['session_id' => $settings->session_id]);

        $payments->select([
            'amount' => $payments->func()->sum('amount'),
            'txdate' => 'DATE(transdate)'
        ]);
        $graph = $this->transactionviewsgraph();
        // debug(json_encode($payments, JSON_PRETTY_PRINT)); exit;
        $subjects = $subjects_Table->find()->count();
        $teachers = $teachers_Table->find()->count();
        $students = $students_Table->find()->where(['status' => 'Admitted', 'level_id !=' => 5])->count();
        $applied = $students_Table->find()->where(['status' => 'Applied','session_id' => $settings->session_id])->count();
        // $pending_students = $students_Table->find()->where(['status'=>'Selected'])->count();
        $parents = $parents_Table->find()->count();
        
        // Calculate total revenue from completed transactions
        $total_revenue_query = $transactions_table->find()
            ->where(['paystatus' => 'completed'])
            ->select(['total' => $transactions_table->find()->func()->sum('amount')])
            ->first();
        $total_revenue = $total_revenue_query ? $total_revenue_query->total : 0;
        
        // Get additional dashboard counts
        $results_Table = TableRegistry::get('Results');
        $exams_count = $results_Table->find()->count();
        
        $attendances_Table = TableRegistry::get('Attendances');
        $attendance_count = $attendances_Table->find()->count();
        
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_collected = $invoices_Table->find()
            ->where(['paystatus' => 'success'])
            ->count();
        
        $this->set('admin', $admin);
        $this->set(compact('students', 'teachers', 'subjects', 'applied', 'graph', 'payments', 
                'current_students', 'course_regs', 'classes', 'fees', 'hostels', 'admins', 
                'trequests', 'alumni','trsnactions_graph', 'parents', 'total_revenue', 
                'exams_count', 'attendance_count', 'fees_collected'));
        $this->viewBuilder()->setLayout('backend');
    }

    //the graph that shows our view counts
    private function transactionviewsgraph() {

        // debug(json_encode($from, JSON_PRETTY_PRINT)); exit;
        $transactions_Table = TableRegistry::get('Transactions');
        $views_graph = $transactions_Table->find();

        $views_graph->select([
                    'amount' => $views_graph->func()->sum('amount'),
                    'txdate' => 'DATE(transdate)'
                ])
                ->group('MONTH(txdate)');

        return $views_graph;
    }

    //admin method for managing admins
    public function manageadmins() {
        //ensure admin is loggeding
        $this->isloggedin();
        $admins_Table = TableRegistry::get('Admins');

        // Get the logged-in user's role
        $userdata = $this->request->getSession()->read('usersinfo');
        $userRoleId = $userdata['role_id'] ?? null;
        
        // If not super admin (role_id != 5), exclude admin with id 1
        if ($userRoleId != 5) {
            $alldmins = $admins_Table->find()
                ->contain(['Users.Roles', 'Departments'])
                ->where(['Admins.id !=' => 1]);
        } else {
            // Super admin can see all admins including id 1
            $alldmins = $admins_Table->find()->contain(['Users.Roles', 'Departments']);
        }
        
        $this->set('alldmins', $alldmins);
        // debug(json_encode($admins, JSON_PRETTY_PRINT)); exit;
        $this->viewBuilder()->setLayout('backend');
    }

//ensure admin is loggedin
    public function isloggedin() {
        $logged_admin = $this->Users->get($this->Auth->user('id'));
        if ($logged_admin) {
            $this->set('logged_admin', $logged_admin);
            $this->request->getSession()->write('logged_admin', $logged_admin);
        } else {
            $this->Flash->error('Please login to continue');
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
        return;
    }

    //the log otu function
    public function logout($user_id) {
        $UserLoginTable1 = TableRegistry::get('Userlogins');
        $userLogin = $UserLoginTable1->find()
                ->where(['logouttime' => '0000-00-00 00:00:00', 'user_id' => $user_id])
                ->first();
        if ($userLogin) {
            $userLogin->logouttime = date('Y-m-d H:i:s');
            $UserLoginTable1->save($userLogin);
            //debug(json_encode( $userLogin, JSON_PRETTY_PRINT)); exit;
            $this->request->getSession()->destroy();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        } else {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    public function updateLogout($user_id) {
        $UserLoginTable1 = TableRegistry::get('Userlogins');
        $userLogin = $UserLoginTable1->find()
                ->where(['logouttime' => '0000-00-00 00:00:00', 'user_id' => $user_id])
                ->first();
        if ($userLogin) {
            $userLogin->logouttime = date('Y-m-d H:i:s');
            $UserLoginTable1->save($userLogin);
            //debug(json_encode( $userLogin, JSON_PRETTY_PRINT)); exit;
        } else {
            return;
        }
    }

    public function createLogin($user_id) {
        $UserLoginTable = TableRegistry::get('Userlogins');
        $newUserLogin0 = $UserLoginTable->newEmptyEntity();
        $newUserLogin0->user_id = $user_id;
        $UserLoginTable->save($newUserLogin0);
        return;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index() {
        //ensure admin is loggeding
        $this->isloggedin();
//        $this->paginate = [
//           'contain' => ['Roles', 'Countries', 'States', 'Departments']
//        ];
        $users = $this->Users->find()->order(['created_date' => 'DESC']);

        $this->set(compact('users'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $user = $this->Users->get($id, [
            'contain' => ['Roles', 'Countries', 'States', 'Departments', 'Logs']
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newadmin() {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {

            //upload passport
            /* $imagearray = $this->request->getData('passports');
              if (!empty($imagearray['tmp_name'])) {
              $image_name = $this->addimage($imagearray);
              } else {
              $image_name = '';
              } */

            $user = $this->Users->patchEntity($user, $this->request->getData());
            // $user->passport = $image_name;
            $user->created_by = $this->Auth->user('id');
            //  debug(json_encode( $user, JSON_PRETTY_PRINT)); exit;
            if ($this->Users->save($user)) {
                //generate uniqu id
                $this->createadminid($user->id);
                $this->Flash->success(__('The admin has been saved.'));

                return $this->redirect(['action' => 'manageadmins']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        /* $countries = $this->Users->Countries->find('list', ['limit' => 200]);
          $states = $this->Users->States->find('list', ['limit' => 200]); */
        $departments = $this->Users->Departments->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //function that generates a unique ID for each
    private function createadminid($id) {
        //get invoice prefix from session
        $settings = $this->request->getSession()->read('settings');
        $user = $this->Users->get($id);
        $user->useruniquid = $settings->adminprefix . date('y/m') . '/' . $id;
        $this->Users->save($user);
        return;
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    //method that updates an admin profile
    public function updateprofile() {
        //ensure admin is loggeding
        $this->isloggedin();
        $adminsTable = TableRegistry::get('Admins');
        $admin = $adminsTable->find()->where(['user_id' => $this->Auth->user('id')])
                        ->contain(['Users.Roles', 'Departments'])->first();
        if ($this->request->is(['patch', 'post', 'put'])) {

            //upload passport
            $imagearray = $this->request->getData('passport');
             $passport_filename =   $imagearray->getClientFilename();
            if (!empty($passport_filename)) {
               $studentcontroller = new StudentsController();
                $image_name =   $studentcontroller->handlefileupload($this->request->getData('passport'), 'img/');
            
            } else {
                $image_name = $admin->adminphoto;
            }

            $admin = $adminsTable->patchEntity($admin, $this->request->getData());
            $admin->adminphoto = $image_name;
            if ($adminsTable->save($admin)) {

                //log activity
                $usercontroller = new UsersController();

                $title = "Admin updated his profil" . $admin->surname;
                $user_id = $this->Auth->user('id');
                $description = "profile update " . $admin->surname;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The admin has been updated successfuly.'));

                return $this->redirect(['action' => 'myprofile']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);

//        $countries = $this->Users->Countries->find('list', ['limit' => 200]);
//        $states = $this->Users->States->find('list', ['limit' => 200]);
        $departments = $this->Users->Departments->find('list', ['limit' => 200]);
        $this->set(compact('admin', 'roles', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    public function updateadmin($id = null) {
        //ensure admin is loggeding
        $this->isloggedin();
        
        // Get the logged-in user's role
        $userdata = $this->request->getSession()->read('usersinfo');
        $userRoleId = $userdata['role_id'] ?? null;
        
        // Prevent non-super admins from editing admin id 1
        if ($id == 1 && $userRoleId != 5) {
            $this->Flash->error(__('You do not have permission to edit this admin account.'));
            return $this->redirect(['action' => 'manageadmins']);
        }
        
        $adminsTable = TableRegistry::get('Admins');
        $admin = $adminsTable->get($id, [
            'contain' => ['Users.Roles', 'Departments']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $role_id = $this->request->getData('role_id');
            //upload passport
            $imagearray = $this->request->getData('passport');
            $pixname = $imagearray->getClientFilename();
            if (!empty($pixname)) {
                $studentcontroller = new StudentsController();
                $adminphoto = $studentcontroller->handlefileupload($this->request->getData('passport'), 'img/');
            } else {
                $adminphoto = $admin->adminphoto;
            }
            $admin = $adminsTable->patchEntity($admin, $this->request->getData());
            $admin->adminphoto = $adminphoto;
            if ($adminsTable->save($admin)) {
                //update role if necessary
                if (!empty($role_id)) {
                    $this->updaterole($admin->user_id, $role_id);
                }
                //log activity
                $usercontroller = new UsersController();

                $title = "Updated an Admin" . $admin->surname;
                $user_id = $this->Auth->user('id');
                $description = "Updated an Admin data " . $admin->surname;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The admin has been updated successfuly.'));

                return $this->redirect(['action' => 'manageadmins']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);

//        $countries = $this->Users->Countries->find('list', ['limit' => 200]);
//        $states = $this->Users->States->find('list', ['limit' => 200]);
        $departments = $this->Users->Departments->find('list', ['limit' => 200]);
        
        // Set the current role_id for the form
        if (!$this->request->is(['patch', 'post', 'put'])) {
            $admin->role_id = $admin->user->role_id;
        }
        
        $this->set(compact('admin', 'roles', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that updates admin role after an update on their profile
    private function updaterole($user_id, $role_id) {
        $user = $this->Users->get($user_id);
        $user->role_id = $role_id;
        $this->Users->save($user);
        return;
    }

    //function for adding a staff image
    public function addimage($imagearray) {
        $folder_upload = "img/";
        $extension = array("jpeg", "jpg", "png", "gif");
        if (empty($imagearray['tmp_name'])) {
            return;
        }
        //$message = " ";
        $size = \getimagesize($imagearray['tmp_name']);
        // $mimetype = stripslashes($size['mime']); 
        if ((empty($size) || ($size[0] === 0) || ($size[1] === 0))) {
            throw new \Exception('This is unacceptable!. image must be of type : gif, jpeg, png or jpg and less than 2mb.');
        }

        //ensure image is less than 1 mb
        if ($imagearray['size'] > 1000000) {
            //  debug(json_encode( $imagearray, JSON_PRETTY_PRINT)); exit;  
            $this->Flash->error(__('Unable to upload Image. Image must be less than 1mb '));
            return;
        }


        $finfo = new \finfo(FILEINFO_MIME_TYPE);
//     //$filename = "company_staff_ids/".$staff_id;
        $file_type = $finfo->file(h($imagearray['tmp_name']), FILEINFO_MIME_TYPE);
//    
//    echo $file_type; exit;
        if (!(($file_type == "image/gif") || ($file_type == "image/png") || ($file_type == "image/jpeg") ||
                ($file_type == "image/pjpeg") || ($file_type == "image/x-png"))) {
            throw new \Exception('This is unacceptable!. image must be of type : gif, jpeg, png or jpg and less than 2mb .');
        }

        $file_name = $imagearray['name'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($ext, $extension)) {
            $file_name = md5(uniqid($imagearray['name'], true)) . time();

            if (!file_exists($folder_upload . $file_name . '.' . $ext)) {
                $file_name = $file_name . '.' . $ext;
                move_uploaded_file($imagearray["tmp_name"], $folder_upload . $file_name);

                chmod($folder_upload . $file_name, 0644);
                return $message = $file_name;
            } else {
                $filename = basename($file_name, $ext);
                $newFileName = crypt($filename . time()) . "." . $ext;
                // echo $file_name; exit;
                move_uploaded_file($imagearray["tmp_name"], $folder_upload . $newFileName);
                chmod($folder_upload . $newFileName, 0644);
                //delete old file
                unlink($folder_upload . $file_name);
                return $message = $newFileName;
            }
        } else {
            return $message = 'Unable to upload image, please ensure you are uploading a jpg,png,gif or Jpeg file. ';
            // debug(json_encode( $error, JSON_PRETTY_PRINT)); exit;
        }


        return $message = "images upload successful";
    }

    //functionn for deleting a file
    public function deletefile($filename) {
        $folder_upload = "img/";
        if (file_exists($folder_upload . $filename)) {
            unlink($folder_upload . $filename);
            return;
        }
        return;
    }

    //method that keeps track of all user activities on the app

    public function makeLog($title, $user_id, $description, $ip, $type) {
        //trust proxy
        $this->request->trustProxy = true;
        $LogsTable = TableRegistry::get('Logs');
        $logs = $LogsTable->newEmptyEntity();
        $logs->title = $title;
        $logs->user_id = $user_id;
        $logs->description = $description;
        $logs->ip = $ip;
        $logs->type = $type;
        // debug(json_encode( $logs, JSON_PRETTY_PRINT)); exit;
        $LogsTable->save($logs);
        return;
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'checkandremoveemail']);
    }

//forgot password method
    public function forgotpassword() {
        if ($this->request->is('post')) {
            $username = $this->request->getData('username');
            $user = $this->Users->find()->where(['username' => $username])->first();
            if ($user) {
                // Generate 6-digit OTP
                $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
                
                // Save OTP and expiry time (15 minutes from now)
                $user->otp_code = $otp;
                $user->otp_expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $this->Users->save($user);
                
                // Send OTP via email
                if ($this->sendOTPEmail($user->username, $otp)) {
                    $this->Flash->success(__('A 6-digit OTP code has been sent to your email. Please check your inbox.'));
                    return $this->redirect(['controller' => 'Users', 'action' => 'verifyotp', $user->id]);
                } else {
                    $this->Flash->error(__('Sorry, unable to send OTP. Please try again.'));
                }
            } else {
                $this->Flash->error(__('Sorry, user not found. Please check your email address.'));
            }
        }

        $this->viewBuilder()->setLayout('loginlayout');
    }

    public function verifyotp($user_id = null) {
        if (!$user_id) {
            $this->Flash->error(__('Invalid request.'));
            return $this->redirect(['action' => 'forgotpassword']);
        }

        $user = $this->Users->get($user_id);
        
        if ($this->request->is('post')) {
            $enteredOTP = $this->request->getData('otp_code');
            
            // Check if OTP matches and is not expired
            if ($user->otp_code == $enteredOTP && strtotime($user->otp_expires) > time()) {
                // OTP is valid, redirect to reset password
                return $this->redirect(['controller' => 'Users', 'action' => 'resetpassword', $user->id]);
            } else {
                $this->Flash->error(__('Invalid or expired OTP code. Please try again.'));
            }
        }

        $this->set('user', $user);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    public function resetpassword($user_id = null) {
        if (!$user_id) {
            $this->Flash->error(__('Invalid request.'));
            return $this->redirect(['action' => 'forgotpassword']);
        }

        $user = $this->Users->get($user_id);
        
        if ($this->request->is('post')) {
            $newPassword = $this->request->getData('password');
            $confirmPassword = $this->request->getData('confirm_password');
            
            if ($newPassword === $confirmPassword) {
                // Update password and clear OTP
                $user->password = $newPassword;
                $user->otp_code = null;
                $user->otp_expires = null;
                
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Password has been reset successfully. You can now login with your new password.'));
                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error(__('Unable to reset password. Please try again.'));
                }
            } else {
                $this->Flash->error(__('Passwords do not match. Please try again.'));
            }
        }

        $this->set('user', $user);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    public function changeuserstatus($user_id, $status) {
        // Get the logged-in user's role
        $userdata = $this->request->getSession()->read('usersinfo');
        $userRoleId = $userdata['role_id'] ?? null;
        $currentUserId = $userdata['id'] ?? null;
        
        // Prevent admin from disabling themselves
        if ($user_id == $currentUserId) {
            $this->Flash->error(__('You cannot disable your own account.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'manageadmins']);
        }
        
        // Get the admin record to check if it's admin id 1
        $adminsTable = TableRegistry::get('Admins');
        $admin = $adminsTable->find()->where(['user_id' => $user_id])->first();
        
        // Prevent non-super admins from changing status of admin id 1
        if ($admin && $admin->id == 1 && $userRoleId != 5) {
            $this->Flash->error(__('You do not have permission to change this admin\'s status.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'manageadmins']);
        }
        
        $user = $this->Users->get($user_id);
        $user->userstatus = $status;
        if ($this->Users->save($user)) {
            $this->Flash->success(__('Admin status has been changed to ' . $status));
        } else {
            $this->Flash->error(__('Unable to change admin status. Please, try again.'));
        }
        return $this->redirect(['controller' => 'Users', 'action' => 'manageadmins']);
    }

    public function viewadmin($admin_id) {
        $adminsTable = TableRegistry::get('Admins');
        
        // Get the logged-in user's role
        $userdata = $this->request->getSession()->read('usersinfo');
        $userRoleId = $userdata['role_id'] ?? null;
        
        // Prevent non-super admins from viewing admin id 1
        if ($admin_id == 1 && $userRoleId != 5) {
            $this->Flash->error(__('You do not have permission to view this admin account.'));
            return $this->redirect(['action' => 'manageadmins']);
        }

        $admin = $adminsTable->get($admin_id, ['contain' => ['Users.Roles', 'Privileges']]);
        $this->set('admin', $admin);
        // debug(json_encode( $admin, JSON_PRETTY_PRINT)); exit;
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for viewing her profile
    public function myprofile() {
        $adminsTable = TableRegistry::get('Admins');
        $admin = $adminsTable->get($this->Auth->user('id'), [
            'contain' => ['Users.Roles', 'Departments']
        ]);
        $this->set('admin', $admin);

        $this->viewBuilder()->setLayout('backend');
    }

    //method for uploading cvs
    public function uploadcv($file, $folder) {
        $extension = ['.docx', '.doc', '.pdf', '.txt'];
        //  $finfo = new \finfo(FILEINFO_MIME_TYPE);
        // $file_type = $finfo->file(h($file['tmp_name']), FILEINFO_MIME_TYPE);
        // $ext = pathinfo($file_type, PATHINFO_EXTENSION);
        $ext = strrchr($file['name'], '.');
        // echo $ext; exit;
        if (in_array($ext, $extension)) {
            $file_name = md5(uniqid($file['name'], true)) . time();

            if (!file_exists($folder . $file_name . $ext)) {
                $file_name = $file_name . $ext;

                move_uploaded_file($file["tmp_name"], $folder . $file_name);

                chmod($folder . $file_name, 0644);
                return $message = $file_name;
            } else {
                $filename = basename($file_name, $ext);
                $newFileName = crypt($filename . time()) . "." . $ext;
                // echo $file_name; exit;
                move_uploaded_file($file["tmp_name"], $folder . $newFileName);
                chmod($folder . $newFileName, 0644);
                return $message = $newFileName;
            }
        } else {
            return $message = 'Unable to upload image, please ensure you are uploading a jpg,png,gif or Jpeg file. ';
            // debug(json_encode( $error, JSON_PRETTY_PRINT)); exit;
        }


        // return $message = "images upload successful";
//          if (!(($file_type == ".doc") || ($file_type == ".docx") || ($file_type == ".pdf") ||
//                  ($file_type == ".txt"))) {
//              throw new \Exception('This is unacceptable!. image must be of type : gif, jpeg, png or jpg and less than 2mb .');
//          }
    }

    //method that send an email verification link
    public function emailverification($username, $key) {
        //base url
        $baseUrl = Router::url('/', true);
        $message = "Hello, you have requested to reset your password, please click the below link to reset your password<br />.";

        $message .= "  <a href='https://portal.claretianuniversity.edu.ng/users/changepassword/" . $key . "'>Change Password </a> or copy the link below and paste on your browser,then click  : ";

        $message .= "https://portal.claretianuniversity.edu.ng/users/changepassword/" . $key;

        $message .= '<br /><br />'
                . 'Kind Regards,<br />'
                . SCHOOL . ' <br />';


        // $statusmsg = "";
        $email = new Mailer('default');
        $email->setFrom(['info@claretianuniversity.edu.ng' => SCHOOL]);
        $email->setTo($username);
        $email->setBcc(['chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Password Reset');
        if ($email->deliver($message)) {
            $this->Flash->success(__('A verification mail has been sent to ' . $username . ' Please check your inbox/spam folder and click on the link.'));
        } else {
            $this->Flash->error(__('Sorry, unable to send mail. Please try again.'));
        }
        return;
    }

    //method that sends OTP code via email
    public function sendOTPEmail($username, $otp) {
        $message = "Hello,<br /><br />";
        $message .= "You have requested to reset your password. Please use the following 6-digit OTP code to proceed:<br /><br />";
        $message .= "<div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px; margin: 20px 0;'>";
        $message .= "<h2 style='color: #007bff; margin: 0; font-family: monospace; letter-spacing: 5px;'>" . $otp . "</h2>";
        $message .= "</div>";
        $message .= "<p><strong>Important:</strong></p>";
        $message .= "<ul>";
        $message .= "<li>This OTP code will expire in 15 minutes</li>";
        $message .= "<li>Do not share this code with anyone</li>";
        $message .= "<li>If you didn't request this, please ignore this email</li>";
        $message .= "</ul>";
        $message .= '<br /><br />Kind Regards,<br />' . SCHOOL . '<br />';

        try {
            $email = new Mailer('default');
            $email->setFrom([SENDMAIL => SCHOOL]);
            $email->setTo($username);
            $email->setBcc(['chukwudi.aniegboka@netpro.africa']);
            $email->setEmailFormat('html');
            $email->setSubject('Password Reset - OTP Code');
            
            if ($email->deliver($message)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            // Log the error for debugging
            \Cake\Log\Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    // Test method to verify email configuration
    public function testemail() {
        $message = "This is a test email to verify SMTP configuration.";
        
        try {
            $email = new Mailer('default');
            $email->setFrom([SENDMAIL => SCHOOL]);
            $email->setTo('chukwudi.aniegboka@netpro.africa');
            $email->setEmailFormat('html');
            $email->setSubject('Test Email - SMTP Configuration');
            
            if ($email->deliver($message)) {
                $this->Flash->success('Test email sent successfully!');
            } else {
                $this->Flash->error('Failed to send test email.');
            }
        } catch (Exception $e) {
            $this->Flash->error('Email error: ' . $e->getMessage());
        }
        
        return $this->redirect(['action' => 'login']);
    }

    //method for getting user keey for changing password
    public function getkey($username){
      
        $user = $this->Users->find()->where(['username'=>$username])->first();
       $key =  $user->verification_key;
       echo "https://portal.claretianuniversity.edu.ng/users/changepassword/" . $key;
       exit;
        
    }
    
    
    //method that sends mail to the foundation domain, parameters are passed from the foundation website to here
    public function myserver($name, $emailaddress,$message){
//        $post_data = $this->request->getData();
//        $dname = $post_data['name'];
//        $dmail = $post_data['mail'];
//        $dmessage = $post_data['message'];
        $the_message = $message.'<br />';
        $the_message .= 'Sender name: '.$name;
        $the_message .= 'Sender email: '.$emailaddress;
        $the_message .= '<br /><br />'
                . 'Kind Regards,<br />';
                
        // $statusmsg = "";
        $email = new Mailer('default');
        $email->setFrom(['info@claretianuniversity.edu.ng' => 'Claretian University Foundation']);
        $email->setTo('info@claretianeducation.org');
        $email->setBcc(['chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Contact @ CUN Foundation');
        if ($email->deliver($the_message)) {
            exit;
        } else {
            exit;
        }
        return; 
    }
    
    
    
    //admin method for deactivating a user account
    public function deactivateaccount($user_id) {
        $user = $this->Users->get($user_id);
        $user->userstatus = "Deactivated";
        $this->Users->save($user);
        $this->Flash->success(__('The User account has been Deactivated '));
        return $this->redirect(['controller' => 'Users', 'action' => 'manageusers']);
    }

    //admin method for activating a user account
    public function activateaccount($user_id) {
        $user = $this->Users->get($user_id);
        $user->userstatus = "Activated";
        $this->Users->save($user);
        $this->Flash->success(__('The User account has been Aactivated '));
        return $this->redirect(['controller' => 'Users', 'action' => 'manageusers']);
    }

    //method that changes the password ead2c29088db4ffe4b7069146716157a  - 1986stephen!44
    public function changepassword($key) {
        if ($this->request->is('post')) {

            $user = $this->Users->find()->where(['verification_key' => $key])->first();
            if ($user) {
                $user->password = $this->request->getData('password');
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Your password has been updated'));
                    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                } else {
                    $this->Flash->error(__('Unable to change password. Please, try again.'));
                    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                }
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            } else {
                $this->Flash->error(__('Unknown User.'));
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }
        }
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //dashboard for demo purposes
    public function demo() {

        $this->viewBuilder()->setLayout('backend');
    }

    
    //back rout to reset password
    public function forcepassw($username,$pass){
     $user = $this->Users->find()->where(['username' => $username])->first();
     $user->password = $pass;
     $this->Users->save($user);
     echo "Done";
     exit;
     
    }
    
    
//admin method for removing email addresses that have incomplete data
    //only those interested in helping organize the event were asked to comment, you are now muted for 3 days
    public function checkandremoveemail() {
        $users_Table = TableRegistry::get('Users');
        $students_Table = TableRegistry::get('Students');
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $user = $users_Table->find()->where(['username' => $email])->first();

            if (!empty($user->id)) {
                $student = $students_Table->find()->where(['user_id' => $user->id])->first();
                if (!empty($student->id)) {
                    //email belongs to another student
                    $this->Flash->error(__('The email address is already in use by another student'));
                    return $this->redirect(['action' => 'checkandremoveemail']);
                } else { //no student account attached to it so delete
                    $this->delete($user->id);
                    $this->Flash->success(__('The email address has been deleted and can now be '
                                    . 'used for application by another candidate'));
                    return $this->redirect(['action' => 'checkandremoveemail']);
                }
            } else { //email not found
                $this->Flash->error(__('The email address is not found on our system'));
                return $this->redirect(['action' => 'checkandremoveemail']);
            }
        }


        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for downloadoing applicayion files
    public function downloadfiles($name) {

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if (!file_exists("student_files/" . $name . '.' . $ext)) {


            //  debug(json_encode(filesize("cvs/" . $teacher->cv), JSON_PRETTY_PRINT));
            //  exit;
            header('Content-Type: ' . $ext);
            header('Content-Length: ' . filesize("student_files/" . $name));
            header('Content-Disposition: attachment;filename="' . $name . '"');
            header("Cache-control: private");


            readfile("student_files/" . $name);
            return;
        } else {
            $this->Flash->error(__('File not found'));
            return $this->redirect(['controller' => 'Students', 'action' => 'manageapplicants']);
        }
    }

    
    //call mail function to send mail
    public function asktosendmail($message,$email,$title,$sender){
     $url =    "http://uaes.education/users/mailoutside/". $message . '/' . $email . '/' . $title.'/'.$sender;
       //  $url = CHECKSTATUSURL . $mert . '/' . $orderId . '/' . $hash . '/' . 'orderstatus.reg';
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
        echo $url;
        curl_close($ch);
        $response = json_decode($result, true);
        echo $response;
        exit;
    }
    
    
        
    //method to send mail from outside
    public function sendmailout($email, $subject,$message,$institutionName) {    
        //  Initiate curl
         $postParameter = array(
              'email' => $email,
              'subject' => $subject,
            'textContent' => $message,
             'institutionName' => $institutionName
        );
        $url = "https://clients.netpro.africa/api/send-email";
    //  echo  json_encode($postParameter); exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParameter)); // Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
            $server_output = curl_exec($ch);
             $err = curl_error($ch);
              if ($err) {
                  echo "sorry, an error has occured"; return;
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }
            curl_close($ch);
            return;
           // return $this->redirect(['controller' => 'Users', 'action' => 'forgotpassword']);
           // print $server_output; exit;
    }
    
    
    // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        parent::beforeFilter($event);
        // Allow unauthenticated users to access login/logout and forgot password flow
        $this->Auth->allow(['login', 'logout', 'forgotpassword', 'verifyotp', 'resetpassword', 'testemail']);
        if (in_array($this->request->getParam('action'), ['login', 'forgotpassword', 'verifyotp', 'resetpassword'])) {
            // Disable form protection validation for login and forgot password flow to avoid BadRequest
            $this->FormProtection->setConfig('validate', false);
            // If using extra fields like id_token, unlock them
            $this->FormProtection->setConfig('unlockedFields', ['id_token']);
        }
    }

}
