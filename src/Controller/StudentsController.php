<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Routing\Router;
use Cake\Mailer\Mailer;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper;
use Cake\ORM\Query;
use Cake\I18n\Date;
use \Cake\Database\Expression\QueryExpression;
use Cake\I18n\DateTime;
use Cake\I18n\FrozenTime;
use Cake\Datasource\ConnectionManager;

/**
 * Students Controller
 *
 * @property \App\Model\Table\StudentsTable $Students
 *
 * @method \App\Model\Entity\Student[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StudentsController extends AppController {



    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index() {

        // $this->viewBuilder()->setLayout('webland');
    }

    public function managestudents() {
        if ($this->request->is('post')) {
            $department_id = $this->request->getData('department_id');
            $class_arm_id = $this->request->getData('class_arm_id');
            $programetype_id = $this->request->getData('programetype_id');
            $category_id = $this->request->getData('category_id');
            $level_id = $this->request->getData('level_id');
            $session_id = $this->request->getData('session_id');
            $condition = [];
            if (!empty($session_id)) {
                $condition['Students.session_id'] = $session_id;
            }
            if (!empty($department_id)) {
                $condition['Students.department_id'] = $department_id;
            }
            if (!empty($class_arm_id)) {
                $condition['Students.class_arm_id'] = $class_arm_id;
            }
            if (!empty($level_id)) {
                $condition['Students.level_id'] = $level_id;
            }
            if (!empty($category_id)) {
                $condition['Students.category_id'] = $category_id;
            }
            if (!empty($programetype_id)) {
                $condition['Students.programetype_id'] = $programetype_id;
            }
            $students = $this->Students->find()
                    ->contain(['Departments', 'ClassArms', 'States', 'Users',                         'Lgas',  'Hostelrooms.Hostels'])
                    ->where(['Students.status' => 'Admitted'])
                    ->andWhere($condition)
                    // ->limit(2000)
                    ->order(['joindate' => 'DESC']);
            //  $this->paginate($students);
        } else {
            $students = $this->Students->find()
                    ->contain(['Departments', 'ClassArms', 'States', 'Users',
                        'Lgas', 'Hostelrooms.Hostels'])
                    ->where(['Students.status' => 'Admitted'])
                    ->limit(1500)
                    ->order(['joindate' => 'DESC']);
        }
        // debug(json_encode($students, JSON_PRETTY_PRINT)); exit;
        // $modes = $this->Students->Modes->find('list');
        $sessions = $this->Students->Sessions->find('list')->order(['id' => 'DESC']);
        // $categories = $this->Students->Categories->find('list');
        // $programetypes = $this->Students->Programetypes->find('list');
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        $this->set(compact('students', 'sessions', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for generating invoices for students
    public function getstudents() {
        //ensure this is an active admin
        $adminController = new AdminsController();
        $admin = $adminController->isadmin();
        if ($this->request->is('post')) {
            $department_id = $this->request->getData('department_id');
            $condition = [];
            if (!empty($department_id)) {
                $condition['Students.department_id'] = $department_id;
            }
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Countries'])
                    ->where(['status' => 'Admitted'])
                    ->andWhere($condition)
                    ->order(['joindate' => 'DESC']);
            $this->set('students', $students);
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);

        $this->set(compact('departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for searching student
    public function searchstudents() {
        $searchterm = $this->request->getData('searchparam');

        $students = $this->Students->find()
                //->contain(['Departments','Users','Faculties'])
                ->where(function (QueryExpression $exp) {
            $searchterm = $this->request->getData('searchparam');
            $orConditions = $exp->or(['Students.fname' => $searchterm])->add(['Students.lname' => $searchterm])
                    ->add(['regno' => $searchterm]);
            return $exp
            ->add($orConditions);
        });
        //  debug(json_encode($students, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('students'));
        $this->set('title', 'Search');

        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewstudent($id = null) {
        
        // First check if student exists and has a parent
        $student = $this->Students->find()
            ->select(['id', 'sparent_id'])
            ->where(['id' => $id])
            ->first();
        
        if (empty($student->sparent_id) || $student->sparent_id == 0) {
            $this->Flash->error(__('The student you are trying to access has not updated their profile. Kindly update their profile to access their information.'));
            return $this->redirect(['controller' => 'Students', 'action' => 'managestudents']);
        }
        
        // If student has parent, load with all associations
        $student = $this->Students->get($id, [
            'contain' => ['Departments.Subjects', 'ClassArms', 'Sparents','States', 'Countries', 'Users', 'Subjects', 'Invoices.Fees',
                'Invoices.Sessions', 'Results.Sessions', 'Results.Semesters', 'Results.Subjects']
        ]);
        
        $this->set('student', $student);
        $this->viewBuilder()->setLayout('backend');
    }

    // parent method for view thier kids
    public function viewmystudent($id = null) {
        $parentcontroller = new SparentsController();
        $parent = $parentcontroller->isparent();
        $student = $this->Students->get($id, [
            'contain' => ['Departments.Subjects', 'Sparents','States', 'Countries', 'Users', 'Subjects', 'Invoices.Fees',
                'Invoices.Sessions', 'Results.Sessions', 'Results.Semesters', 'Results.Subjects']
        ]);
        if($parent->id==null || $parent->id!=$student->sparent->id){
            return $this->redirect(['controller' => 'Sparents', 'action' => 'dashboard']);
        }
        
        // Check if student is suspended
        if($student->studentstatus === 'Suspended') {
            $this->Flash->error(__('This student is currently suspended. Please visit the school to resolve this issue.'));
            return $this->redirect(['controller' => 'Sparents', 'action' => 'dashboard']);
        }
        
        $this->set('student', $student);
        $this->viewBuilder()->setLayout('parentsbackend');
    }

    //method that gets all the courses registered by this student and group them according to semester
    public function getstudentcourses($student_id) {
        $coursereg_Table = TableRegistry::get('Courseregistrations');
        $registeredcourses = $coursereg_Table->find()
                ->contain(['Sessions',  'Semesters', 'Students'])
                ->where(['student_id' => $student_id]);
        // debug(json_encode(  $registeredcourses, JSON_PRETTY_PRINT)); exit;
        return $registeredcourses;
    }

    //admin method for managing applicants
    public function manageapplicants() {
        if ($this->request->is('post')) {
            if (!empty($this->request->getData('startdate') && !empty($this->request->getData('enddate')))) {
                $from = date('Y-m-d', strtotime(date(str_ireplace('/', '-', $this->request->getData('startdate')))));

                $to = date('Y-m-d', strtotime(date(str_ireplace('/', '-', $this->request->getData('enddate')))));
            }

            $department_id = $this->request->getData('department_id');
            $session_id = $this->request->getData('session_id');
            $condition = [];
            if (!empty($session_id)) {
                $condition['Students.session_id'] = $session_id;
            }
            if (!empty($department_id)) {
                $condition['Students.department_id'] = $department_id;
            }
           
            if (!empty($from)) {
                $condition['DATE(joindate) >='] = $from;
            }
            if (!empty($to)) {
                $condition['DATE(joindate) <='] = $to;
            }
            // $condition['YEAR(joindate)'] = date('Y');
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Countries', 'Users', 'Transactions', 'Lgas'])
                    ->where(['status !=' => 'Admitted'])
                    //->where(function (QueryExpression $exp, Query $q) {
//                          $from = date('Y-m-d', strtotime(date($this->request->getData('startdate'))));
//                          return $exp->gte('joindate', $from);
//                      })
//                      ->where(function (QueryExpression $exp, Query $q) {
//                          $to = date('Y-m-d', strtotime(date($this->request->getData('enddate'))));
//                          return $exp->lte('joindate', $to);
//                      })
                    ->andWhere($condition)
                    ->order(['joindate' => 'DESC']);
        } else {
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Countries', 'Users', 'Transactions', 'Lgas'])
                    ->where(['status !=' => 'Admitted', 'DATE(joindate) > DATE(DATE_SUB(NOW(), INTERVAL 365 DAY))'])
                    // ->orWhere(['status' => 'Selected']) 'YEAR(joindate)'=>date('Y')-1,
                    ->order(['joindate' => 'DESC']);
        }
        // debug(json_encode( $students, JSON_PRETTY_PRINT)); exit;
        $sessions = $this->Students->Sessions->find('list')->order(['id' => 'DESC']);
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $this->set(compact('students', 'departments', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for updating students email address
    public function updatestudentschoolemail($id) {
        $student = $this->Students->get($id, [
            'contain' => [ 'Departments', 'States', 'Lgas', 'Countries']
        ]);
        //stude
        //$old_email = $student->email;
        if ($this->request->is(['patch', 'post', 'put'])) {
            //get the new school email address
            $school_email = $this->request->getData('universitymail');
            //update student email to the assigned university email address
            $schoolmail_status = $this->updateschoolmail($student->id, $school_email);
            //check if school mail was created successfully
            if ($schoolmail_status == 1) {

                //upload passport
                $pix = $this->request->getData('passporturls');
                $student_pix = $pix->getClientFilename();
                if (!empty($student_pix)) {

                    $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
                } else {
                    $passport = $student->passporturl;
                }
                $student = $this->Students->patchEntity($student, $this->request->getData());
                $student->passporturl = $passport;
                $student->universitymail = $school_email;
                if ($this->Students->save($student)) {
                    //log activity
                    $usercontroller = new UsersController();

                    $title = "Student School Email Update " . $student->email;
                    $user_id = $this->Auth->user('id');
                    $description = "Updated student email address " . $student->regno;
                    $ip = $this->request->clientIp();
                    $type = "Edit";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                    return $this->redirect(['action' => 'managestudents']);
                }
                $this->Flash->error(__('The student data could not be updated. Please, try again.'));
            } else {
                return $this->redirect(['action' => 'managestudents']);
            }
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 2000]);
        $states = $this->Students->States->find('list', ['limit' => 2000])->where(['country_id' => 160])->order(['name' => 'ASC']);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        // $programes = $this->Students->Programmes->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        // $levels = $this->Students->Levels->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000]);
        // $subjects = $this->Students->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('student', 'departments', 'states', 'countries', 'levels', 'programes', 'lgas'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for viewing applicants
    public function viewapplicant($id = null) {
        $student = $this->Students->get($id, [
            'contain' => ['Departments', 'States', 'Lgas', 'Countries']
        ]);
        //stude
        //$old_email = $student->email;
        if ($this->request->is(['patch', 'post', 'put'])) {
            //get the new school email address
            // $school_email = $this->request->getData('universitymail');
            //update student email to the assigned university email address
            // $schoolmail_status =  $this->updateschoolmail($student->id,$school_email);
            //check if school mail was created successfully
            // if( $schoolmail_status==1){
            //upload passport
            $pix = $this->request->getData('passporturls');
            $student_pix = $pix->getClientFilename();
            if (!empty($student_pix)) {
                $passport = $this->doresizepassport($this->request->getData('passporturls'));
                // $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $passport = $student->passporturl;
            }
            $student = $this->Students->patchEntity($student, $this->request->getData());
            $student->passporturl = $passport;
            // $student->universitymail = $school_email;
            if ($this->Students->save($student)) {
                //get the student regno
                $this->getregno($student->id, $student->department_id);
                //log activity
                $usercontroller = new UsersController();

                $title = "Applicant Admission/Update " . $student->status;
                $user_id = $this->Auth->user('id');
                $description = "Admitted an Applicant " . $student->regno;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                //sends a mail to the student informing him that he has been offered a provissional admission 
                if ($student->status == "Admitted") {
                    $student->admissiondate = date('Y');
                    $this->Students->save($student);
                   // $this->adminlettersender($student->id);
                    // $this->studentselectionmail($student->email, $student->fname, $student->lname);
                    $this->Flash->success(__('The student data has been updated successfully and a provisional admision letter'
                                    . ' sent to them.'));
                        // $this->sendadmissionletter($student);
                   
                }

                return $this->redirect(['action' => 'manageapplicants']);
            }
            $this->Flash->error(__('The student data could not be updated. Please, try again.'));
//        }else{
//          return $this->redirect(['action' => 'viewapplicant', $student->id, $student->fname]);  
//        }
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 2000]);
        $states = $this->Students->States->find('list', ['limit' => 9000])->order(['name' => 'ASC']);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000]);
        // $subjects = $this->Students->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('student', 'departments', 'states', 'countries', 'lgas'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method  for viewl all CDLCE programe students
    public function cdlcestudents() {
        if ($this->request->is('post')) {
            $department_id = $this->request->getData('department_id');
            $programetype_id = $this->request->getData('programetype_id');
            $category_id = $this->request->getData('category_id');
            $level_id = $this->request->getData('level_id');
            $condition = [];
            if (!empty($department_id)) {
                $condition['Students.department_id'] = $department_id;
            }
            if (!empty($level_id)) {
                $condition['Students.level_id'] = $level_id;
            }
            if (!empty($category_id)) {
                $condition['Students.category_id'] = $category_id;
            }
            if (!empty($programetype_id)) {
                $condition['Students.programetype_id'] = $programetype_id;
            }
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Users',                          'Lgas',  'Hostelrooms.Hostels'])
                    ->where(['status' => 'Admitted', 'Students.faculty_id' => 5])
                    ->andWhere($condition)
                    // ->limit(2000)
                    ->order(['joindate' => 'DESC']);
            //  $this->paginate($students);
        } else {
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Users',                          'Lgas',  'Hostelrooms.Hostels'])
                    ->where(['status' => 'Admitted', 'Students.faculty_id' => 5])
                    ->limit(500)
                    ->order(['joindate' => 'DESC']);
        }
        // debug(json_encode($students, JSON_PRETTY_PRINT)); exit;
        $cdlce_deps = [16, 17, 18, 19, 20, 21, 22, 23];
        // $modes = $this->Students->Modes->find('list');
        // $categories = $this->Students->Categories->find('list');
        // $programetypes = $this->Students->Programetypes->find('list');
        $departments = $this->Students->Departments->find('list', ['limit' => 200])
                        ->where(['id IN' => $cdlce_deps])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        $this->set(compact('students', 'programetypes', 'departments', 'levels', 'modes', 'categories'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    //admin method for direct entry
    public function newstudent() {
        // the admin data
        $admin = $this->isAdmin();
        $parentsTable = TableRegistry::get('Sparents');
        $student = $this->Students->newEmptyEntity();
        //  $parent = $parentsTable->newEmptyEntity();

        if ($this->request->is('post')) {


            $userscontroller = new UsersController();

            $parent_id = $this->request->getData('sparent_id');
            try {
               $parent = $parentsTable->get($parent_id);   
             } catch (\Throwable $e) {
                   Log::error($e->getMessage());

        $this->Flash->error(
            'Something went wrong. Please try again.'
        );
             }
          //  $parent = $parentsTable->get($parent_id);
            if(!$parent){
                 $this->Flash->error(__('Unknown Parent, please create parent record first before adding the child.'));

                      return $this->redirect(['action' => 'newstudent']); 
            }
            //upload files
            //upload o level
            // $imagearray = $this->request->getData('olevelresulturls');
            // $name = $imagearray->getClientFilename();
            // if (!empty($name)) {
            //     $olevel = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
            // } else {
            //     $olevel = "";
            // }
                $olevel = "";
            //upload birth cert
            $birth_imagearray = $this->request->getData('birthcerturls');
            $bcert_name = $birth_imagearray->getClientFilename();
            if (!empty($bcert_name)) {
                $bcert = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
            } else {
                $bcert = "";
            }


            //upload other file
            // $other_imagearray = $this->request->getData('othercertss');
            // $other_cert_name = $other_imagearray->getClientFilename();
            // if (!empty($other_cert_name)) {
            //     $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
            // } else {
            //     $other_cert = "";
            // }
                $other_cert = "";

            //upload passport - handle both webcam and file upload
            $passport = "";
            
            // Check if webcam photo was captured
            $passport_imagearray = $this->request->getData('passporturls');
            if (!empty($passport_imagearray)) {
                $passport = $this->getimagefromcam($passport_imagearray, 'student_files/');
            }
            
            // Check if file was uploaded instead
            $passport_file = $this->request->getData('passport_file');
            if (!empty($passport_file) && $passport_file->getClientFilename()) {
                $passport = $this->handlefileupload($passport_file, 'student_files/');
            }
//              if (!empty($passport_imagearray['tmp_name'])) {
//                  $passport = $userscontroller->addimage($passport_imagearray);
//              } else {
//                  $passport = " ";
//              }
            //create student login data
            $email = $this->request->getData('email');
            $fname = $this->request->getData('fname');
            $lname = $this->request->getData('lname');
            $mname = $this->request->getData('mname');
            $userid = $this->getlogindetails($email, $fname, $lname, $mname);
            if (is_numeric($userid)) {
                $student = $this->Students->patchEntity($student, $this->request->getData());
                
                // Debug: Check if class_arm_id was assigned
                if (!empty($student->class_arm_id)) {
                    $this->log("Class arm ID assigned to student: " . $student->class_arm_id, 'debug');
                } else {
                    $this->log("Class arm ID NOT assigned to student", 'debug');
                }
                
                $student->user_id = $userid;
                $student->othercerts = $other_cert;
                $student->passporturl = $passport;
                $student->birthcerturl = $bcert;
                $student->olevelresulturl = $olevel;
                $student->status = "Admitted";
                $student->addedby = $admin->surname . ' On ' . date('D M Y H:i');
                $student->sparent_id = $parent->id;
                
                // Save student using ORM
                $saveResult = $this->Students->save($student);
                
                // Force direct SQL update for sparent_id since ORM doesn't persist it reliably
                if ($saveResult && $student->id) {
                    $connection = \Cake\Datasource\ConnectionManager::get('default');
                    $sql = "UPDATE students SET sparent_id = ? WHERE id = ?";
                    $connection->execute($sql, [$parent->id, $student->id]);
                }
                
                if ($saveResult) {
                    //assign the student a regno
                    $this->getregno($student->id, $student->department_id);
                    //log activity
                    $usercontroller = new UsersController();

                    $title = "Added a new student " . $student->regno;
                    $user_id = $this->Auth->user('id');
                    $description = "Added a student " . $student->fname;
                    $ip = $this->request->clientIp();
                    $type = "Add";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type);

                    //send  a mail to the student
                    // $this->sendwelcomemail($email, md5($email), $fname, $lname);
                    $this->Flash->success(__('The student data has been saved.'));

                    return $this->redirect(['action' => 'summarypage', $student->id, $student->fname]);
                }
                $this->Flash->error(__('The student could not be saved. Please, try again.'));
            } else {
                $this->Flash->error(__('The student user data could not be saved. Please, try again.'));
            }
        }

        $states = $this->Students->States->find('list', ['limit' => 200])->where(['country_id' => 160]);
        $countries = $this->Students->Countries->find('list', ['limit' => 200]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 200])->where(['state_id' => 2647])->order(['name' => 'ASC']);
        $fees = $this->Students->Fees->find('list', ['limit' => 200]);
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        // $programes = $this->Students->Programmes->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $subjects = $this->Students->Subjects->find('list', ['limit' => 200]);
        // Get parents with custom display format (father's name + mother's name)
        $parentsQuery = $parentsTable->find('withFullNames')
            ->order(['fathersname' => 'ASC', 'mothersname' => 'ASC']);
        
        $parents = [];
        foreach ($parentsQuery as $parent) {
            $parents[$parent->id] = $parent->fathersname . ' & ' . $parent->mothersname;
        }
        $this->set(compact('student', 'departments', 'states', 'countries', 'lgas', 'fees', 'subjects', 'parents'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that gets an incomplete application for completeion
    public function getincompleteapplicant() {
        //get and set session data
        $this->getsettings();
        //get fee details
        $fees_Table = TableRegistry::get('Fees');

        $baseUrl = Router::url('/', true);
        if ($this->request->is('post')) {
            $applicant_id = $this->request->getData('application_no');

            $student = $this->Students->find()->where(['application_no' => $applicant_id])
                    ->contain(['Departments', 'States', 'Countries', 'Users',   'Lgas', 'Transactions', 'Faculties'])
                    ->first();
            //check admission mode to determine fee
            $fee_id = 2; //default
            if ($student->mode_id == 1) {
                $fee_id = 2;
            } //UTME
            elseif ($student->mode_id == 2) {
                $fee_id = 22;
            } //Drect entry
            elseif ($student->mode_id == 3 && $student->country_id == 160) {
                $fee_id = 24;
            } //Distance learning NG
            elseif ($student->mode_id == 3 && $student->country_id != 160) {
                $fee_id = 10;
            } //Distance learning oversees
            elseif ($student->mode_id == 4) {
                $fee_id = 28;
            } //TransNation Education
            else {
                $fee_id = 2;
            }  //default
            $fee = $fees_Table->get($fee_id);
            $transaction = $this->checkpaystatus($student->id, $fee_id);
            if (is_object($transaction)) {
                $this->set('student', $student);
                $this->set('transaction', $transaction);
            } elseif ($transaction == 1) {
                $this->Flash->success(__('This application has already completed'));
                return $this->redirect(['action' => 'printapplicationform', $student->id, $student->fname]);
            } elseif ($transaction == 0) {
                //no pending transaction or invoice so create new one
                $invoice_id = $this->creatnewinvoice($student->id, $fee_id, $fee->amount);
                return $this->redirect(['action' => 'generateapplicantpayeeid', $invoice_id, $student->id]);
            }
        }
        // $this->Flash->error(__('Please provide your application ID.'));
        $this->set('baseUrl', $baseUrl);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method that get setting data into session
    public function getsettings() {

        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        return;
    }

    //method that check if a fee has been paid
    public function checkpaystatus($student_id, $fee_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $payment = $transactions_Table->find()->contain(['Fees'])
                ->where(['student_id' => $student_id, 'fee_id' => $fee_id])
                ->first();
        // debug(json_encode(  $payment, JSON_PRETTY_PRINT)); exit;
        if (!empty($payment->id)) {
            if ($payment->paystatus == "completed") {
                return 1;
            } elseif ($payment->paystatus == "initialized") {
                $payment->payref = strtoupper(uniqid(TRANS_REF)) . date('dmHis');
                $payment->transdate = date('Y-m-d H:i');
                $transactions_Table->save($payment);
                return $payment;
            } else { //no payment record found so go and create a new one
                return 0;
            }
        }
        $this->Flash->error(__('No payment record found'));
        return;
    }

    //check if applicant paid application fee
    public function checkPaymentStatus($studentid) {
        $transactions_Table = TableRegistry::get('Transactions');
        $payment = $transactions_Table->find()->contain(['Fees'])
                        ->where(['student_id' => $studentid])->first();
        // debug(json_encode(  $payment, JSON_PRETTY_PRINT)); exit;
        if (!empty($payment->id)) {
            if ($payment->paystatus == "completed") {
                return 1;
            } elseif ($payment->paystatus == "initialized") {
                $payment->payref = strtoupper(uniqid(TRANS_REF)) . date('dmHis');
                $payment->transdate = date('Y-m-d H:i');
                $transactions_Table->save($payment);
                return $payment;
            } else { //no payment record found so go and create a new one
                return 0;
            }
        }
        $this->Flash->error(__('No payment record found'));
        return;
    }

    //the file upload method
    public function handlefileupload($filename, $folder) {
        $attachment = $filename;
        $name = $attachment->getClientFilename();
        $extension = strrchr($name, '.');
        $type = $attachment->getClientMediaType();

        $size = $attachment->getSize();
//        if(($size > 3000000)){
//          $this->Flash->error(__('There was an error uploading your file. Ensure file is <1mb and of right format(word or pdf).  Please, try again.'));
//            return 0;   
//        }
        // echo $type.' '. $size; exit;
        $tmpName = $attachment->getStream()->getMetadata('uri');
        $error = $attachment->getError();
        //  if(empty($tmpName)){}
        if (empty($filename)) {
            $this->Flash->error(__('There was an error uploading your file. Ensure file is <1mb and of right format(word or pdf).  Please, try again.'));
            return 0;
        }
        if ($error != 0) {
            $this->Flash->error(__('There was an error uploading your file. Ensure file is <1mb and of right format(word or pdf).  Please, try again.'));
            return 0;
        }
        $filenametobd = uniqid(date('d_m_y_h_i_s')) . '_' . $name;
        if ((($error == 0) && ($size < 3000000)) && (($type == "image/png") || ($type == "image/jpeg") || ($type == "image/pjpeg") || ($type == "image/x-png") || ($type == "application/pdf"))) {
            $attachment->moveTo($folder . $filenametobd);
            return $filenametobd;
        } else {
            $this->Flash->error(__('There was an error uploading your file. Ensure file is <3mb and of right format(word or pdf).  Please, try again.'));
            return 0;
        }
    }

    //method that actually resizes the passport
    public function doresizepassport($img_file) {

        $dirPath = "student_files/";
        $name = $img_file->getClientFilename();
        $tmpName = $img_file->getStream()->getMetadata('uri');
        $sourceProperties = getimagesize($tmpName);
        $newFileName = uniqid(date('d_m_y_h_i_s')) . '_' . $name;
        $ext = strrchr($name, '.');
        $type = $img_file->getClientMediaType();
        // $ext=pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $imageType = $sourceProperties[2];
        // echo $imageType; echo $type; exit;

        switch ($imageType) {

            case IMAGETYPE_PNG:
                $imageSrc = imagecreatefrompng($tmpName);
                $tmp = $this->imageResize($imageSrc, $sourceProperties[0], $sourceProperties[1]);
                imagepng($tmp, $dirPath . $newFileName);
                return $newFileName;

            case IMAGETYPE_JPEG:
                $imageSrc = imagecreatefromjpeg($tmpName);
                $tmp = $this->imageResize($imageSrc, $sourceProperties[0], $sourceProperties[1]);
                //echo $newFileName; exit;
                imagejpeg($tmp, $dirPath . $newFileName);
                return $newFileName;

            case IMAGETYPE_GIF:
                $imageSrc = imagecreatefromgif($tmpName);
                $tmp = imageResize($imageSrc, $sourceProperties[0], $sourceProperties[1]);
                imagegif($tmp, $dirPath . $newFileName);
                return $newFileName;

            default:
                echo"Invalid Image type. Images MUST be either PNG or JPEG";
                exit;
                break;
        }
    }

    //functionn for deleting a file
    public function deletefile($filename, $folder) {
        // $folder_upload = "img/";
        if (!empty($filename) && file_exists($folder . $filename)) {
            unlink(realpath($folder . $filename));
            // array_map('unlink', glob($folder . $filename));
            return;
        }
        return;
    }

    //method that returns the loggedin admin
    private function isAdmin() {
        $admins_Table = TableRegistry::get('Admins');
        $admin = $admins_Table->find()->where(['user_id' => $this->Auth->user('id')])->first();
        if (!$admin) {
            $this->Flash->error(__('Invalid Access.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'logout', $this->Auth->user('id')]);
        } else {
            return $admin;
        }
        return;
    }

    //method that changes a students to the default student123
    public function resetpassword($user_id) {
        $users_Table = TableRegistry::get('Users');
        $user = $users_Table->get($user_id);
        $admincontroller = new AdminsController();
        $admincontroller->isadmin();
        if ($user) {
            $user->password = "student123";
            $users_Table->save($user);
            $this->resetpasswordalaert($user->username);
            $this->Flash->success(__('The student password has been reset to the default : student123'));
            return $this->redirect(['action' => 'managestudents']);
        } else {
            $this->Flash->error(__('Unknown user data. Please, try again.'));
            return $this->redirect(['action' => 'managestudents']);
        }
        $this->Flash->error(__('Unknown user data or unable to update password. Please, try again.'));

        return $this->redirect(['action' => 'managestudents']);
    }

//update email alert
    public function updatemailalert($username, $old_email, $pass) {
        $message = "<br /> Congratulations! <br /> Your email address has been updated successfully."
                . " Your new school email address/username is now : " . $username . '<br />';
        $message .= "Your Password is: " . $pass . '<br />';
            //call the mailling api
        $userscontroller = new UsersController();
         $userscontroller->sendmailout($username, "Email Reset", $message, "FPNO");
        return;

        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($old_email);
        $email->setBcc(['admission@claretianuniversity.edu.ng', $username, MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('Email Reset');
        if ($email->deliver($message)) {
            $this->Flash->success('Mail has been sent and copied to (' . $username . ')');
        } else {
            $this->Flash->error('Oh!, sorry, We are unable to send mail.');
        }
        return;
    }

//update password alert
    private function resetpasswordalaert($username) {
        $message = "<br /> Congratulations! <br /> Your password has been reset to the default value of : student123.</br />"
                . " Please do change your password to something more secrete and never give it to anyone.";
            //call the mailling api
        $userscontroller = new UsersController();
         $userscontroller->sendmailout($username, "Password Reset", $message, "FPNO");
        return;

        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($username);
        $email->setBcc(['admissions@claretianuniversity.edu.ng', MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('Default Password Reset');
        if ($email->deliver($message)) {
            $this->Flash->success('A confirmation email has been sent to (' . $username . ')');
        } else {
            $this->Flash->error('Oh!, sorry, We are unable to send mail.');
        }
        return;
    }

    /**
     * Edit method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updatestudent($id = null) {
        // Disable FormProtection for this action
        $this->FormProtection->setConfig('validatePost', false);
        
        // Get the student with explicit field selection to ensure sparent_id is loaded
        $student = $this->Students->find()
            ->select(['id', 'sparent_id', 'fname', 'lname', 'regno', 'mname', 'gender', 'phone', 'dob', 'address', 'email', 'country_id', 'state_id', 'lga_id', 'community', 'department_id', 'passporturl', 'othercerts', 'olevelresulturl', 'birthcerturl'])
            ->where(['id' => $id])
            ->first();
        
        if (!$student) {
            $this->Flash->error(__('Student not found.'));
            return $this->redirect(['action' => 'managestudents']);
        }
        
        // Load parent separately to handle missing parent gracefully
        if (!empty($student->sparent_id)) {
            try {
                $sparentsTable = TableRegistry::get('Sparents');
                $parent = $sparentsTable->get($student->sparent_id);
                $student->sparent = $parent;
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                // Parent doesn't exist, set sparent_id to null
                $student->sparent_id = null;
                $student->sparent = null;
            }
        }
        // Check if student has a parent associated
        $hasParent = !empty($student->sparent_id) && !empty($student->sparent);
        
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userscontroller = new UsersController();
            //upload files
            // debug(json_encode($student, JSON_PRETTY_PRINT)); exit;

            // $olevel = $this->request->getData('olevelresulturls');
            // $olevel_filename = $olevel->getClientFilename();
            // if (!empty($olevel_filename)) {

            //     $waec_cert = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
            // } else {
                $waec_cert = $student->olevelresulturl;
            // }


            //upload birth cert
            // $birthcert = $this->request->getData('birthcerturls');
            // $birthcert_filename = $birthcert->getClientFilename();
            // if (!empty($birthcert_filename)) {

            //     $birth_cert = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
            // } else {
                $birth_cert = $student->birthcerturl;
            // }
            //upload other file
            // $other_cert = $this->request->getData('othercertss');
            // $othercert_filename = $other_cert->getClientFilename();
            // if (!empty($othercert_filename)) {

                // $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
            // } else {
                $other_cert = $student->othercerts;
            // }

            // Get passport data before patchEntity to avoid validation issues
            $passport_imagearray = $this->request->getData('passport_data');
            
            // Process passport data
            if (!empty($passport_imagearray)) {
                $passport = $this->getimagefromcam($passport_imagearray, 'student_files/');
            } else {
                $passport = $student->passporturl;
            }
            // Remove passport_data from request data to avoid validation issues
            $formData = $this->request->getData();
            unset($formData['passport_data']);
            
            // Manually assign form data to student entity to bypass patchEntity issues
            // NOTE: Exclude sparent_id from this loop - we'll handle it separately below
            $allowedFields = ['fname', 'lname', 'mname', 'dob', 'phone', 'address', 'email', 'country_id', 'state_id', 'lga_id', 'community', 'department_id', 'regno', 'gender', 'birthcerturls', 'othercertss', 'religion', 'pschools'];
            
            foreach ($allowedFields as $field) {
                if (isset($formData[$field])) {
                    $student->$field = $formData[$field];
                }
            }
            $student->passporturl = $passport;
            $student->othercerts = $other_cert;
            $student->olevelresulturl = $waec_cert;
            $student->birthcerturl = $birth_cert;
             // Handle parent information update/assignment
             $sparentsTable = TableRegistry::get('Sparents');
             $parentSaveResult = true; // Default to true for students without parents
             
             // Get the selected parent ID from form
             $selectedParentId = $this->request->getData('sparent_id');
             
             // Get current parent ID from DB (not from form)
             $currentParentId = !empty($student->sparent_id) ? $student->sparent_id : null;
             
             // Store the intended sparent_id value before save (ORM might revert it)
             $intendedSparentId = null;
             
             if ($hasParent) {
                 // Student has existing parent - check if admin wants to reassign
                 if (!empty($selectedParentId) && $selectedParentId != $currentParentId) {
                     // Admin selected a different parent - reassign
                     $intendedSparentId = (int)$selectedParentId;
                     $student->sparent_id = $intendedSparentId;
                     $student->setDirty('sparent_id', true); // Mark field as dirty to force save
                     $parentSaveResult = true; // No need to save parent, just reassign
                 } else {
                     // Keep current parent (no reassignment) - don't change sparent_id
                     $intendedSparentId = (int)$currentParentId;
                     $student->sparent_id = $intendedSparentId;
                     $parentSaveResult = true;
                 }
             } else {
                 // Student doesn't have parent - check if admin selected an existing parent
                 if (!empty($selectedParentId)) {
                     // Admin selected an existing parent
                     $intendedSparentId = (int)$selectedParentId;
                     $student->sparent_id = $intendedSparentId;
                     $parentSaveResult = true; // No need to save parent, just assign
                 } else {
                     // Admin wants to create a new parent
                     $newParent = $sparentsTable->newEmptyEntity();
                     $newParent->fathersname = $this->request->getData('fathersname');
                     $newParent->mothersname = $this->request->getData('mothersname');
                     $newParent->fatherphone = $this->request->getData('fatherphone');
                     $newParent->motherphone = $this->request->getData('motherphone');
                     $newParent->fathersjob = $this->request->getData('fathersjob');
                     $newParent->mothersjob = $this->request->getData('mothersjob');
                     $newParent->pemailaddress = $this->request->getData('pemailaddress');
                     $newParent->address = $this->request->getData('address');
                     $newParent->status = 'active';
                     
                     // For now, skip user creation and just save parent without user_id
                     // You can implement user creation later if needed
                     $parentSaveResult = $sparentsTable->save($newParent);
                     
                     if ($parentSaveResult) {
                         $intendedSparentId = $newParent->id;
                         $student->sparent_id = $intendedSparentId;
                     }
                 }
             }
            $saveResult = $this->Students->save($student);
            
            // ALWAYS use direct SQL to ensure sparent_id is saved correctly (ORM has issues with this field)
            if ($saveResult && !empty($intendedSparentId)) {
                $connection = ConnectionManager::get('default');
                $result = $connection->execute('SELECT sparent_id FROM students WHERE id = ?', [$student->id])->fetch('assoc');
                
                // Force SQL update to ensure correct value (using intended value, not entity value)
                if ($result['sparent_id'] != $intendedSparentId) {
                    $connection->execute(
                        'UPDATE students SET sparent_id = ? WHERE id = ?',
                        [$intendedSparentId, $student->id]
                    );
                }
            }
             
             if ($parentSaveResult && $saveResult) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Updated a student " . $student->regno;
                $user_id = $this->Auth->user('id');
                $description = "Updated a student " . $student->fname;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The student data has been updated successfully.'));

                return $this->redirect(['action' => 'managestudents']);
            }
            $this->Flash->error(__('The student data could not be updated. Please, try again.'));
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 2000]);
        $states = $this->Students->States->find('list', ['limit' => 2000])->where(['country_id' => 160]);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $fees = $this->Students->Fees->find('list', ['limit' => 2000]);
        // $campuses = $this->Students->Campuses->find('list');
        $subjects = $this->Students->Subjects->find('list', ['limit' => 2000]);
        
        // Get parents list for assignment (only for students without parents)
        $sparentsTable = TableRegistry::get('Sparents');
        $parentsQuery = $sparentsTable->find('withFullNames')
            ->order(['fathersname' => 'ASC', 'mothersname' => 'ASC']);
        
        $parents = [];
        foreach ($parentsQuery as $parent) {
            $parents[$parent->id] = $parent->fathersname . ' & ' . $parent->mothersname;
        }
        
        $this->set(compact('student', 'departments', 'states', 'countries', 'lgas', 'fees', 'subjects', 'parents', 'hasParent'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that ensures that while changing email address, there are no duplications
    public function validateemail($student_id) {
        $student = $this->Students->get($student_id);

        if ($this->request->is('post')) {
            //ensure no duplication of username
            $username = $this->request->getData('email');
            $pass = $this->request->getData('pass');
            $users_Table = TableRegistry::get('Users');
            $userdata = $users_Table->find()->where(['username' => $username])->first();
            if (empty($userdata)) {
                //this email does not exist in the system already, update with it
                $useremail = $users_Table->get($student->user_id);
                $useremail->username = $username;
                $useremail->password = $pass;
                $users_Table->save($useremail);
                //update on students table
                $student->universitymail = $username;
                $this->Students->save($student);
                //send a congratulatory message
                $this->updatemailalert($username, $student->email, $pass);
                $this->Flash->success(__('The email address has been updated.'));
                return $this->redirect(['action' => 'validateemail', $student->id, $student->fname]);
            } else {
                $this->Flash->error(__('Sorry, the email address is already in use. Please, try a different one'));
                return $this->redirect(['action' => 'validateemail', $student->id, $student->fname]);
            }
        }
        $this->set('student', $student);
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for updating email address during admission - adding university mail
    public function updateschoolmail($student_id, $new_school_email, $pass) {
        $student = $this->Students->get($student_id);
        //ensure no duplication of username
        // $username = $this->request->getData('universitymail');
        $users_Table = TableRegistry::get('Users');
        $userdata = $users_Table->find()->where(['username' => $new_school_email])->first();
        if (empty($userdata)) {
            //this email does not exist in the system already, update with it
            $useremail = $users_Table->get($student->user_id);
            $useremail->username = $new_school_email;
            $users_Table->save($useremail);
            //update on students table
            $student->universitymail = $new_school_email;
            $this->Students->save($student);
            //send a congratulatory message
            $this->updatemailalert($student->email, $new_school_email, $pass);
            $this->Flash->success(__('The email address has been updated.'));
            return 1;
        } else {
            $this->Flash->error(__('Sorry, the email address is already in use. Please, try a different one'));
            return 0;
            // return $this->redirect(['action' => 'viewapplicant', $student->id, $student->fname]);
        }
    }

    //admin method for reseting the regnumber back to 0 after each admission year
    public function resetregno() {
        $lastreg_Table = TableRegistry::get('Lastregs');
        $lastregno = $lastreg_Table->get(1);
        $lastregno->lastreg = 0;
        if ($lastreg_Table->save($lastregno)) {
            $this->Flash->success(__('Reg number series has been reset.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'dashboard']);
        }
        $this->Flash->error(__('Unable to reset Reg number series'));
        return $this->redirect(['controller' => 'Users', 'action' => 'dashboard']);
    }

    //funtion that generates a students reg number
    //funtion that generates a students reg number
    private function getregno($student_id, $dept_id) {
        $settings = $this->request->getSession()->read('settings');
       $student = $this->Students->get($student_id);
       $student->regno = $settings->regnoformat.date('Y') . '/' . $student_id;
       //generate the application no
       $student->application_no = $settings->regnoformat. 'APP' . date('Y') . $student_id;
       $this->Students->save($student);
       return;
   }
    // public function getregno($student_id, $dept_id) {
    //     $regyear = ""; //the year to prefix the reg number
    //     $prefix_zeros = "";
    //     $settings = $this->request->getSession()->read('settings');
    //     $department_Table = TableRegistry::get('Departments');
    //     //get the last reg no
    //     $lastreg_Table = TableRegistry::get('Lastregs');
    //     $lastregno = $lastreg_Table->get(1);
    //     $department = $department_Table->get($dept_id);
    //     $student = $this->Students->get($student_id);
    //     $programe = '';
    //     if (date('m') >= 8) {
    //         $regyear = date('Y');
    //     } else {
    //         $regyear = date('Y') - 1;
    //     }
    //     $latestregno = $lastregno->lastreg += 1;

    //     $new_regno = floor(log10($latestregno) + 1);

    //     //check for length of reg number
    //     if ($new_regno == 1) {
    //         $prefix_zeros = "00";
    //         $student->regno = REGNOPREFIX . $regyear . '/' . $prefix_zeros . $latestregno;
    //     } elseif ($new_regno == 2) {
    //         $prefix_zeros = "0";
    //         $student->regno = REGNOPREFIX . $regyear . '/' . $prefix_zeros . $latestregno;
    //     } elseif ($new_regno == 3) {
    //         $prefix_zeros = "0";

    //         $student->regno = REGNOPREFIX . $regyear . '/' . $prefix_zeros . $latestregno;
    //     } else {

    //         $student->regno = REGNOPREFIX . $regyear . '/' . $latestregno;
    //     }
    //     //generate the application no
    //     //  $student->application_no = $settings->application_no_prefix . date('Y') . $student_id;
    //     $student->admissiondate = $regyear;
    //     $this->Students->save($student);
    //     //update last reg table
    //     // $lastregno->lastreg +=1;
    //     $lastregno->student_id = $student_id;
    //     $lastreg_Table->save($lastregno);
    //     return;
    // }

    //admin method for assigning reg number to any student who for any reason did not get it automatically
    public function assignregno($student_id, $dept_id) {

        $student = $this->Students->get($student_id);
        if (empty($student->regno)) {
            //assign regno
            $this->getregno($student_id, $dept_id);
            $this->Flash->success(__('The student has been assigned a registration number.'));
            return $this->redirect(['controller' => 'Students', 'action' => 'managestudents']);
        } else {
            $this->Flash->error(__('Sorry, the student already has a registration number.'));
            return $this->redirect(['controller' => 'Students', 'action' => 'managestudents']);
        }
    }

    //just for a purpose
    public function updatelasreg($id) {
        $lastreg_Table = TableRegistry::get('Lastregs');
        $lastregno = $lastreg_Table->get(1);
        $lastregno->lastreg = $id;
        $lastreg_Table->save($lastregno);
        // debug(json_encode($lastregno, JSON_PRETTY_PRINT));
        // exit;
    }

    //method that creates login details for the student
    private function getlogindetails($email, $fname, $lname, $mname) {
        $users_Table = TableRegistry::get('Users');
        //ensure username does not exit already
        $old_user = $users_Table->find()->where(['username' => $email])->first();

        if (empty($old_user)) {
            $user = $users_Table->newEmptyEntity();
            $user->role_id = 2;
            $user->password = "student123";
            $user->username = $email;
            $user->fname = $fname;
            $user->lname = $lname;
            $user->mname = $mname;
            if ($users_Table->save($user)) {
                return $user->id;
            } else {
                return "Failed";
            }
        } else {
            //username already exits 
            $this->Flash->error(__('Username/Email Already in Use. Please use a different email address'));
            // return $this->redirect(['action' => 'newapplicant']);
            return;

//              $user = $users_Table->newEmptyEntity();
//              $user->role_id = 2;
//              $user->password = "student123";
//              $user->username = $fname . $lname . '@imopoly.net';
//              $user->fname = $fname;
//              $user->lname = $lname;
//              $user->mname = $mname;
//              $users_Table->save($user);
//              return $user->id;
        }
    }

    //method that creates a parent login details
    private function parentlogindata($email, $fname, $lname, $mname) {
        $users_Table = TableRegistry::get('Users');
        $user = $users_Table->newEmptyEntity();
        $user->role_id = 4;
        $user->password = "parent123";
        $user->username = $email;
        $user->fname = $fname;
        $user->lname = $lname;
        $user->mname = $mname;
        if ($users_Table->save($user)) {
            return $user->id;
        } else {
            return "Failed";
        }
    }

    //mail funtion that informs the student that admission has been offered to them
    private function studentselectionmail($emailaddress, $fname, $lname) {
        $settings = $this->request->getSession()->read('settings');
        $message = "<br /> Congratulations! " . $fname . ' ' . $lname . ',<br /><br />' . 'It is our pleasure to inform you '
                . 'that you have been offered a provissional admission into The BENJAMIN UWAJUMOGU STATE COLLEGE OF EDUCATION, IHITTE/UBOMA. <br /><br />'
                . 'Please login to your student portal using your credentials (to be sent in a subsequent mail)'
                . ' to pay the required fees and obtain '
                . 'your registration number.<br /> <br />Congratulations once again.<br /><br />'
                . 'Registrar<br />' . $settings->doa . '<br /><br />BUSCED<br /><br />';
        
         //call the mailling api
        $userscontroller = new UsersController();
         $userscontroller->sendmailout($emailaddress, "Application Summary", $message, "BUSCED");
        return;

        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($emailaddress);
        $email->setBcc(['admission@busced.online', MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('BUSCED Admission Alert');
        if ($email->deliver($message)) {
            $this->Flash->success('A provissional Offer Letter has been sent to (' . $emailaddress . ') with further instructions.');
        } else {
            $this->Flash->error('Oh!, sorry, We are unable to send mail.');
        }
        return;
    }

    //the application method that allows student to apply online
    public function newapplicant() {

        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        $student = $this->Students->newEmptyEntity();
        $parentsTable = TableRegistry::get('Sparents');
        $parent = $parentsTable->newEmptyEntity();
        if ($this->request->is('post')) {
            $userscontroller = new UsersController();

            //create parent login details
            $fathername = $this->request->getData('fathersname');
            $mothername = $this->request->getData('mothersname');
            $pemail = $this->request->getData('pemailaddress');
            $pmname = " ";
            $parentuserid = $this->parentlogindata($pemail, $fathername, $mothername, $pmname);
            if (is_numeric($parentuserid)) {
                $parent = $parentsTable->patchEntity($parent, $this->request->getData());
                $parent->user_id = $parentuserid;
                $parent->pemailaddress = $pemail;
                $parent->fathersname = $fathername;
                $parent->mothersname = $mothername;
                $parent->fatherphone = $this->request->getData('fatherphone');
                $parent->motherphone = $this->request->getData('motherphone');
                $parent->fathersjob = $this->request->getData('fathersjob');
                $parent->mothersjob = $this->request->getData('mothersjob');
                $parent->address = $this->request->getData('address');
                $parentsTable->save($parent);
                // debug(json_encode( $parent , JSON_PRETTY_PRINT)); exit;
            }
            //upload files
            //upload o level
            if ($this->request->getData('olevelresulturls') !== null && !$this->request->getData('olevelresulturls')->getError()) {
                $waec_filename = $this->request->getData('olevelresulturls')->getClientFilename();
                $waec_cert = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
            } else {
                $waec_cert = "";
            }

            //upload birth cert
            if ($this->request->getData('birthcerturls') !== null && !$this->request->getData('birthcerturls')->getError()) {
                $birth_filename = $this->request->getData('birthcerturls')->getClientFilename();
                $birth_cert = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
            } else {
                $birth_cert = "";
            }
            //upload other file
            if ($this->request->getData('othercertss') !== null && !$this->request->getData('othercertss')->getError()) {
                $other_filename = $this->request->getData('othercertss')->getClientFilename();
                $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
            } else {
                $other_cert = "";
            }
            //upload passport
            if ($this->request->getData('passporturls') !== null && !$this->request->getData('passporturls')->getError()) {
                $passport_filename = $this->request->getData('passporturls')->getClientFilename();
                //call the funtion to resize and upload
                $passport = $this->doresizepassport($this->request->getData('passporturls'));
                // $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $passport = "";
            }

            //create login data
            $email = $this->request->getData('email');
            $fname = $this->request->getData('fname');
            $lname = $this->request->getData('lname');
            $mname = $this->request->getData('mname');
            $userid = $this->getlogindetails($email, $fname, $lname, $mname);
            if (is_numeric($userid)) {
                $student = $this->Students->patchEntity($student, $this->request->getData());
                $student->user_id = $userid;
                $student->level_id = 1;
                $student->othercerts = $other_cert;
                $student->passporturl = $passport;
                $student->birthcerturl = $birth_cert;
                $student->olevelresulturl = $waec_cert;
                $student->sparent_id = $parent->id; 
                $student->session_id = $settings->session->id;
                
                $saveResult = $this->Students->save($student);
                
                // If ORM save didn't persist sparent_id, force it with direct SQL
                if ($saveResult && !empty($student->sparent_id)) {
                    $connection = ConnectionManager::get('default');
                    $result = $connection->execute('SELECT sparent_id FROM students WHERE id = ?', [$student->id])->fetch('assoc');
                    
                    // If the sparent_id wasn't saved, force it with direct SQL
                    if ($result['sparent_id'] != $student->sparent_id) {
                        $connection->execute(
                            'UPDATE students SET sparent_id = ? WHERE id = ?',
                            [$student->sparent_id, $student->id]
                        );
                    }
                }
                
                if ($saveResult) {
                    // debug(json_encode( $student, JSON_PRETTY_PRINT)); exit;
                     //get the student regno
                     $this->getregno($student->id, $student->department_id);


                    //assign application number
                    // $this->getapplicationno($student->id);
                    //send application summary to candidate via email
                    // $this->sendapplicationsummary($student->id);

                    $fees_Table = TableRegistry::get('Fees');
                    //select fee base on mode of admission
                    $fee = $fees_Table->get(2);
                    //create invoice for this payment
                    $invoice_id = $this->creatnewinvoice($student->id, $fee->id, $fee->amount);
                    //generate payee id
                    // $this->generateapplicantpayeeid($invoice_id, $student->id);
                    //$url = $transactionController->gotopaystack($email, $student->phone, $name, $fee->amount, $student->id, $fee->id);
                    $this->Flash->success(__('Your application has been saved, please click on the green button below '
                                    . ' to pay the application fee(Note that applications without application fee payment will not be processed).'
                                    . ' You can always check your application status by clicking on (Check Application Status)'
                                    . '  on the application page and entering the application number which we just sent to your email address'));

                    return $this->redirect(['action' => 'generateapplicantpayeeid', $invoice_id, $student->id]);
                }
                $this->Flash->error(__('Sorry, we could not submit your application. Please, try again.'));
            } else {//if unable to create user login data
                $this->Flash->error(__('Sorry, the email address you provided is already in use. Please, try again.'));
            }
        }
        $departments_Table = TableRegistry::get('Departments');
        // $programetypes_Table = TableRegistry::get('Programetypes');
        // $levels_Table = TableRegistry::get('Levels');
        // $faculties_Table = TableRegistry::get('Faculties');
        // $programmes_Table = TableRegistry::get('Programmes');
        // $subjects_Table = TableRegistry::get('Subjects');
        $departments = $departments_Table->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $states = $this->Students->States->find('list', ['limit' => 200])->where(['country_id' => 160]);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000])->where(['state_id' => 2647])->order(['name' => 'ASC']);
        // $fees = $this->Students->Fees->find('list', ['limit' => 200]);
        // $level_ids = [1];
        // $programeids = [1, 2, 3, 4];
        // $modes = $this->Students->Modes->find('list')->where(['id IN' => [1, 2]]);
        // $programetypes = $programetypes_Table->find('list')->where(['id IN' => [1, 2]]);
        // $allowed_faculties = [1, 2, 3, 4,5,6];
        // $levels = $levels_Table->find('list')->where(['id IN' => $level_ids])->order(['name' => 'ASC']);
        // $faculties = $faculties_Table->find('list')->where(['id IN' => $allowed_faculties])->order(['name' => 'ASC']);
        // $programes = $programmes_Table->find('list', ['limit' => 200])->where(['id IN' => $programeids])->order(['name' => 'ASC']);
        // $subjects = $subjects_Table->find('list', ['limit' => 200]);
        $this->set(compact('student', 'departments', 'states', 'countries', 'lgas'));
        $this->set('title', 'New Application');
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //application method for distancle programes
    public function newcdlapplicant() {
        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        $student = $this->Students->newEmptyEntity();
        if ($this->request->is('post')) {
            //   debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $userscontroller = new UsersController();
            $mode_id = $this->request->getData('mode_id');
            //upload files
            //upload o level
            $olevel = $this->request->getData('olevelresulturls');
            $olevel_filename = $olevel->getClientFilename();
            if (!empty($olevel_filename)) {

                $waec_cert = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
            } else {
                $waec_cert = "";
            }
            //upload passport
            $passportfile = $this->request->getData('passporturls');
            $passport_name = $passportfile->getClientFilename();
            if (!empty($passport_name)) {
                $passport = $this->doresizepassport($this->request->getData('passporturls'));
                // $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $passport = "";
            }
            //create login data
            $email = $this->request->getData('email');
            $fname = $this->request->getData('fname');
            $lname = $this->request->getData('lname');
            $mname = $this->request->getData('mname');
            $userid = $this->getlogindetails($email, $fname, $lname, $mname);
            if (is_numeric($userid)) {


                //upload birth cert
//                   $birthcert = $this->request->getData('birthcerturls');
//            $birthcert_filename =  $birthcert->getClientFilename();
//            if (!empty($birthcert_filename)) { 
//               $birth_cert  = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
//            }
//            else{
//           $birth_cert  = "";   
//            }
                //upload other file
//                $other_cert = $this->request->getData('othercertss');
//                $othercert_filename = $other_cert->getClientFilename();
//                if (!empty($othercert_filename)) {
//                    $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
//                } else {
//                    $other_cert = "";
//                }
                //upload land access document
//                $landaccess = $this->request->getData('landaccessurl');
//                $landaccess_file_name = $landaccess->getClientFilename();
//                if (!empty($landaccess_file_name)) {
//
//                    $land_file_name = $this->handlefileupload($this->request->getData('landaccessurl'), 'student_files/');
//                } else {
//                    $land_file_name = "";
//                }
                $student = $this->Students->patchEntity($student, $this->request->getData());
                $student->user_id = $userid;
                $student->landaccessurl = "";
                $student->mode_id = 3;
                $student->previousschool = " ";
                //   $student->othercerts = $other_cert;
                $student->passporturl = $passport;
                $student->category_id = 3;
                $student->olevelresulturl = $waec_cert;
                $student->session_id = $settings->session->id;
                //  debug(json_encode( $student, JSON_PRETTY_PRINT)); exit;
                if ($this->Students->save($student)) {
                    //assign application number
                    $this->getapplicationno($student->id);
                    //send application summary to candidate via email
                    $this->sendapplicationsummary($student->id);

                    $fees_Table = TableRegistry::get('Fees');
                    //check for international applicants
                    if ($student->country_id == 160) {
                        //local candidate
                        $fee = $fees_Table->get(24);
                    } else {
                        //international candidate
                        $fee = $fees_Table->get(10);
                    }
                    //create invoice for this payment
                    $invoice_id = $this->creatnewinvoice($student->id, $fee->id, $fee->amount);
                    //generate payee id
                    // $this->generateapplicantpayeeid($invoice_id, $student->id);
                    //$url = $transactionController->gotopaystack($email, $student->phone, $name, $fee->amount, $student->id, $fee->id);
                    $this->Flash->success(__('Your application has been saved, please click on the green button below '
                                    . ' to pay the application fee(Note that applications without application fee payment will not be processed).'
                                    . ' You can always check your application status by clicking on (Check Application Status)'
                                    . '  on the application page and entering the application number which we just sent to your email address'));

                    return $this->redirect(['action' => 'generateapplicantpayeeid', $invoice_id, $student->id]);
                }
                $this->Flash->error(__('Sorry, we could not submit your application. Please, try again.'));
            } else {//if unable to create user login data
                $this->Flash->error(__('Sorry, the email address you provided is already in use. Please, try again.'));
            }
        }
        $departmentids = [17, 18, 20, 21, 22, 23];
        $departments = $this->Students->Departments->find('list')->where(['id IN' => $departmentids, 'iscdl' => 'Yes'])->order(['name' => 'ASC']);
        $states = $this->Students->States->find('list', ['limit' => 200])->where(['country_id' => 160]);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000])->where(['state_id' => 2647])->order(['name' => 'ASC']);
        //  $fees = $this->Students->Fees->find('list', ['limit' => 200]);
        $level_ids = [1];
        $programeids = [1, 2, 3, 4];
        //$modes = $this->Students->Modes->find('list');
        // $programetypes = $this->Students->Programetypes->find('list')->where(['id' => '3']);

        // $levels = $this->Students->Levels->find('list')->where(['id IN' => $level_ids])->order(['name' => 'ASC']);
        // $faculties = $this->Students->Faculties->find('list', ['limit' => 200])->where(['id' => 5])->order(['name' => 'ASC']);
        $programes = $this->Students->Programmes->find('list', ['limit' => 200])->where(['id IN' => $programeids])->order(['name' => 'ASC']);
        // $subjects = $this->Students->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('student', 'faculties', 'programetypes', 'departments', 'states', 'countries', 'lgas', 'programes'));
        $this->set('title', 'New Application');
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method that assigns application number to new applicants
    public function getapplicationno($student_id) {
        //set session vars
        $settings = $this->request->getSession()->read('settings');
        $student = $this->Students->get($student_id);
        if($student->department_id==39){
          $student->application_no = $settings->application_no_prefix .'IJMB'. date('Y') . $student_id;  
        }else{
        $student->application_no = $settings->application_no_prefix . date('Y') . $student_id;
        }
        $this->Students->save($student);
        return;
    }

    //method that sends application summary to a new applicant
    public function sendapplicationsummary($student_id) {
        $settings = $this->request->getSession()->read('settings');
        //base url
        $baseUrl = Router::url('/', true);
        $url = $baseUrl . 'students/newapplicant';
        $student = $this->Students->get($student_id, ['contain' => ['Departments',  'Faculties', 'Users']]);

        $message = SCHOOL . " Students Application System<br /><br />";

        $message .= "<center>Application Summary</center><br /><br />";
        $message .= "Dear " . $student->fname . ' ' . $student->lname . ',<br /><br /> Your application for admission into ' . SCHOOL .
                ' has been submitted. Please find the summary below : <br /><br />';
        $message .= "Name Of Candidate: " . $student->fname . ' ' . $student->lname . ' ' . $student->mname . '<br />';
        $message .= "Programme Applied for: " . $student->programme->name . '<br />';
        $message .= "School : " . $student->faculty->name . '<br />';
        $message .= "Intending Course Of Study: " . $student->department->name . '<br />';
        $message .= "Department: " . $student->department->name . '<br />';
        $message .= "Session: " . $settings->session->name . '<br />';
        $message .= "Application No: " . $student->application_no . '<br /><br />';
        $message .= "Application date : " . date('D d M, Y') . '<br /><br />';
        $message .= "Note that you can always check your application status by visiting : " . $url . ' click on check application '
                . 'status, enter your application number and click on check status.<br /><br />'
                . 'Please ensure that you pay the compulsary application fee as all applications'
                . ' without application fee payment will not be considered.';

        $message .= "<br /><br />Yours In Service<br /><br />";

        $message .= SCHOOL . "<br />";
         //call the mailling api
        $userscontroller = new UsersController();
         $userscontroller->sendmailout($student->email, "Application Summary", $message, "BUSCED");
        return;

        $email = new Mailer('default');
        //$email->setTemplate('letterlayout', 'letterview');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->user->username);
        $email->setBcc(['admissions@claretianuniversity.edu.ng', 'chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Application Summary');
        if ($email->deliver($message)) {
            $this->Flash->success(__('A summary of your application has been sent to your registered email address : ' . $student->email));
        }
        return;
    }

    //method that sends admission leter to distance learning stdudents
    public function sendAdmissionLetterDistanceL($student_id) {
        $settings = $this->request->getSession()->read('settings');
        $letters_table = TableRegistry::get('Letters');
        //base url
        $baseUrl = Router::url('/', true);
        $url = $baseUrl . 'students/newcdlapplicant';
        $student = $this->Students->get($student_id, ['contain' => ['Departments',  'Faculties', 'Users']]);
        $letter = $letters_table->find()->where(['mode_id' => $student->mode_id])->first();

        $message = "<center>" . SCHOOL . " <br />";
        $message .= "P.M.B 1019 Owerri, Imo State, Nigeria<br />";
        $message .= "Office of the Director of Distance Learning and Continuing Education<br />";
        $message .= "Director: Dr Uchenna B. Amadi-Ihunwo (BaED, MBA, PhD)</center><br />";

        $message .= "Our Ref:" . $student->application_no . "<br />";

        $message .= "Dear " . $student->fname . ' ' . $student->lname . '<br />';
        $message .= "Re: Offer of admission: " . $student->prgramme->name . "in " . $student->department->name . "<br />";

        $message .= $letter->letterbody;

        $message .= SCHOOL . "<br />";

        $email = new Mailer('default');
        //$email->setTemplate('letterlayout', 'letterview');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->user->username);
        $email->setBcc(['admissions@claretianuniversity.edu.ng', 'chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Admission Letter');
        if ($email->deliver($message)) {
            $this->Flash->success(__('An Admission letter has been sent to the registered email address : ' . $student->email));
        }
        return;
    }
    
    
    //method that checks that a student is paying the right fee, tuition fee
    public function confirmfee($facultyid,$levelid, $feeid,$amount){
        $benchmark = 200000;
       // echo $amount; exit;
       // if($amount<$benchmark && $levelid!=4){echo 'yes'; exit;}
        if((($facultyid==1)|| ($facultyid==2) || ($facultyid ==3)) && ($levelid!=4)){
            //should pay new fee
      $tuition_new = [33,34,35,30,32,31,];   
      if(!in_array($feeid,$tuition_new)  && ($amount>$benchmark) ){
          //attempting to pay wrong fee
          return 0; //alert student to see admin before paying
      }
      else{
          return 1; //paying the right fee
      }
      
        }
    }
    
    
    
    

    //the student dashboard function
    public function dashboard() {
        $fees_sTable = TableRegistry::get('Fees');
        $settings = $this->request->getSession()->read('settings');
        $student = $this->Students->find()
                        ->where(['user_id' => $this->Auth->user('id')])
                        ->contain(['Fees', 'Subjects', 'Departments', 'Invoices',
                            'States', 'Departments.Fees', 'Users', 'ClassArms'])->first();

        // Load subjects count for the student's department
        $subjectsCount = 0;
        if ($student && !empty($student->department_id)) {
            $subjectsTable = TableRegistry::get('Subjects');
            $subjectsCount = $subjectsTable->find()
                ->where(['department_id' => $student->department_id])
                ->count();
        }

        //check if this student has updated their profile
        if (empty($student->passporturl)) {
            $this->Flash->error(__('Sorry, you must update your profile to continue. Please ensure you select your state '
                            . 'of origin, current class/level and uplaod a passport. Passport must be less than 1mb and either'
                            . ' jpg, png or jpeg'));
            return $this->redirect(['action' => 'updateprofile']);
        }

        // Get additional data for dashboard
        $assignmentsTable = TableRegistry::get('Assignments');
        $setassignmentsTable = TableRegistry::get('Setassignments');
        
        // Count available assignments
        $availableAssignments = $assignmentsTable->find()
            ->where([
                'student_id' => $student->id,
                'status' => 'available'
            ])
            ->count();
        
        // Count completed assignments
        $completedAssignments = $assignmentsTable->find()
            ->where([
                'student_id' => $student->id,
                'status IN' => ['completed', 'submitted']
            ])
            ->count();
        
        // Get recent assignments
        $recentAssignments = $setassignmentsTable->find()
            ->contain(['Subjects'])
            ->order(['datecreated' => 'DESC'])
            ->limit(5)
            ->all();
        
        // Count paid and unpaid invoices
        $paidInvoices = 0;
        $unpaidInvoices = 0;
        
        if (!empty($student->invoices)) {
            foreach ($student->invoices as $invoice) {
                if ($invoice->paystatus === 'success') {
                    $paidInvoices++;
                } elseif ($invoice->paystatus === 'Unpaid') {
                    $unpaidInvoices++;
                }
            }
        }
        // debug(json_encode(  $student, JSON_PRETTY_PRINT));exit;
        // $coursereg_table = TableRegistry::get('Courseregistrations');
        // $registered_courses = $coursereg_table->find()
        //                 ->contain(['Subjects'])
        //                 ->where([
        //                     'session_id' => $settings->session_id, 'semester_id' => $settings->semester_id,
        //                     'student_id' => $student->id
        //                 ])->first();
        $counter = 0;
        
        foreach ($student->fees as $fee) {
                //check for any fee assigned to this student and if this fee has been paid
            if ($this->checkpayment($student->id, $fee->id) == 0) {
                    //fee has not been paid, check if there is an invoice for it already
               // $is_owing = 'is_owing';
               // $this->request->getSession()->write('is_owing', $is_owing);

                if ($this->checkinvoice($student->id, $fee->id) == 1) {
                        //there is an unpaid invoice, take him to his invoices
                    return $this->redirect(['action' => 'invoices', $student->id]);
                    } else {
                        $counter++;
                        //no invoices, create new one
                    $this->creatnewinvoice($student->id, $fee->id, $fee->amount);
                }
            }
        }
        //check for fees based on class
        foreach ($student->department->fees as $fee) {
           // debug(json_encode( $fee, JSON_PRETTY_PRINT));exit;
            //check for any fee assigned to this student class and if this fee has been paid
            if ($this->checkpayment($student->id, $fee->id) == 0) {
                //fee has not been paid, check if there is an invoice for it already
               // $is_owing = 'is_owing';
               // $this->request->getSession()->write('is_owing', $is_owing);

                if ($this->checkinvoice($student->id, $fee->id) == 1) {
                    //there is an unpaid invoice, take him to his invoices
                    return $this->redirect(['action' => 'invoices', $student->id]);
                } else {
                    $counter++;
                    //no invoices, create new one
                    $this->creatnewinvoice($student->id, $fee->id, $fee->amount);
                }
            }
        }

        if ($counter > 0) { //if new invoice was created, take the student to the invoice
            return $this->redirect(['action' => 'invoices', $student->id, $student->fname]);
        }
        //check if this is an applicant and has paid all the fees and admit the applicant
        if ($student->status == 'Selected') {
            $student->status = 'Admitted';
            $this->Students->save($student);
        }
        $this->set(compact('student', 'subjectsCount', 'availableAssignments', 'completedAssignments', 'recentAssignments', 'paidInvoices', 'unpaidInvoices', 'settings'));
        // $this->set('courses', $registered_courses);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method that checks if a given payment has been made
    public function checkpayment($student_id, $fee_id) {
        $transaction_sTable = TableRegistry::get('Transactions');
        $invoices_sTable = TableRegistry::get('Invoices');
        $current_session = $this->request->getSession()->read('settings');
        
        // Check Transactions table for completed payments
        $payment = $transaction_sTable->find()
                        ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'session_id' => $current_session->session_id,
                            'paystatus' => 'completed'])->first();

        if (!empty($payment->id)) {
            return 1;
        }
        
        // Also check Invoices table for successful payments
        $invoice = $invoices_sTable->find()
                        ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'session_id' => $current_session->session_id,
                            'paystatus' => 'success'])->first();

        if (!empty($invoice->id)) {
            return 1;
        }
        
        return 0;
    }
    
    
    
    //check fee level for student fee payment
    public function checkfeelevel($feeid,$levelid){
         $fees_sTable = TableRegistry::get('FeesLevels');
         $fee =  $fees_sTable->find()->where(['fee_id'=>$feeid,'level_id'=>$levelid])->first();
         if($fee){return $fee->fee_id;} //fee belongs to the level
         else{return " No";} // fee does not belong to the level
    }
    
    

    //check if there is an exisiting invoice for a particular fee
    //method that checks if a given payment has been made
    public function checkinvoice($student_id, $fee_id) {
        $invoices_sTable = TableRegistry::get('Invoices');
        $current_session = $this->request->getSession()->read('settings');
        
        // Check for ANY existing invoice for this student and fee combination (regardless of payment status)
        $payment = $invoices_sTable->find()
                        ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'session_id' => $current_session['session_id']])
                        ->first();

        if (!empty($payment->id)) {
            return 1;
        }
        return 0;
    }

    //method that shows a student all her courses
    public function mycourses() {
        $settings = $this->request->getSession()->read('settings');
        $student = $this->Students->find()
                        ->where(['user_id' => $this->Auth->user('id')])
                        ->contain(['Departments'])
                        ->first();
        $subjects = [];
        if ($student && !empty($student->department_id)) {
            $subjectsTable = TableRegistry::get('Subjects');
            $subjects = $subjectsTable->find()
                ->where(['department_id' => $student->department_id])
                ->order(['name' => 'ASC'])
                ->all();
        }
        $this->set(compact('student','subjects'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }

//method that shows the student his invoices
    public function myinvoices() {
        $student = $this->request->getSession()->read('student');
        $settings = $this->request->getSession()->read('settings');
        $invoices_Table = TableRegistry::get('Invoices');
        $invoices = $invoices_Table->find()->contain(['Fees', 'Sessions'])
                ->where(['student_id' => $student->id])
                ->order(['Invoices.session_id' => 'DESC']);
//        $student = $this->Students->find()
//                         ->contain(['Invoices.Fees', 'Invoices.Sessions', 'Fees'])
//                        ->where(['user_id' => $this->Auth->user('id')])        
//                ->first();
        //   debug(json_encode(  $student, JSON_PRETTY_PRINT));exit;
        $this->set('invoices', $invoices);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method that creates invoices for students
    public function creatnewinvoice($student_id, $fee_id, $amount) {
        //  echo 'yest i got here'; exit;
        $settings = $this->request->getSession()->read('settings');
        //get the invoice table
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->newEmptyEntity();
        $invoice->student_id = $student_id;
        $invoice->fee_id = $fee_id;
        $invoice->amount = $amount;
        $invoice->session_id = $settings->session_id;
        $invoice->invoiceid = INVOICEPREFIX . $fee_id . '/' . $student_id;
        $invoices_Table->save($invoice);
        return $invoice->id;
    }

//method that shows a student all his invoices
    public function invoices($student_id) {
        //ensure this student is loggedin
        $student = $this->isstudent();

        $settings = $this->request->getSession()->read('settings');
        $invoices_Table = TableRegistry::get('Invoices');
        $myinvoices = $invoices_Table->find()->contain(['Fees', 'Sessions'])
                ->where(['student_id' => $student->id, 'session_id' => $settings->session_id]);
        //   debug(json_encode(  $student, JSON_PRETTY_PRINT));exit;
        $this->set('myinvoices', $myinvoices);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method that generates the transaction id(Payee ID) for the student 
    public function generatepayeeid($invoice_id, $student_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id, ['contain' => ['Sessions']]);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',                  'States', 'Countries', 'Lgas', 'Users']]);
        $fee = $fees_Table->get($invoice->fee_id);
        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
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
        //check if student is in year one and check if ict and other fees have been paid
        if ($student->level->id == 1) {
            $status = $this->ictandacceptancefeestatus($student_id);
            $this->set('status', $status);
        }

        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('transaction', $transaction);
        // $this->viewBuilder()->setLayout('backend');
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method that generates payee id for a student
    public function getmypayeeid($invoice_id, $student_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id, ['contain' => ['Sessions']]);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',
                'States', 'Countries', 'Lgas', 'Users']]);
        $fee = $fees_Table->get($invoice->fee_id);
        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
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
        //check if student is in year one and check if ict and other fees have been paid
        // if ($student->level->id == 1) {
        //     $status = $this->ictandacceptancefeestatus($student_id);
        //     $this->set('status', $status);
        // }

        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

//ensure no unpaid transaction id for this invoice
    public function checkpayeeid($fee_id, $invoice_id, $student_id, $session_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()->contain(['Sessions'])->where(['fee_id' => $fee_id, 'invoice_id' => $invoice_id,
                    'student_id' => $student_id, 'session_id' => $session_id])->first();
        // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
        if (is_object($transaction) && $transaction->paystatus == "initialized") {
            $transactioncontroller = new TransactionsController();
            $trx_new_ref = $transactioncontroller->updateref($transaction->payref);
            return $trx_new_ref;
        } elseif (is_object($transaction) && $transaction->paystatus == "completed") {
            return $transaction;
        } else {
            return "none";
        }
        return;
    }

    //admin method for searching students based on admission date
    public function searchreport() {
        if ($this->request->is('post')) {
            // Validate and format start date
            $startDate = $this->request->getData('startdate');
            $from = null;
            if (!empty($startDate)) {
                $startTimestamp = strtotime(str_replace('/', '-', $startDate));
                if ($startTimestamp !== false) {
                    $from = date('Y-m-d', $startTimestamp);
                }
            }

            // Validate and format end date
            $endDate = $this->request->getData('enddate');
            $to = null;
            if (!empty($endDate)) {
                $endTimestamp = strtotime(str_replace('/', '-', $endDate));
                if ($endTimestamp !== false) {
                    $to = date('Y-m-d', $endTimestamp);
                }
            }

            $status = $this->request->getData('status');
            $class_id = $this->request->getData('department_id');
            // $joindate = $this->request->getData('joindate');
            $condition = [];
            if (!empty($status)) {
                $condition['Students.status'] = $status;
            }
            if (!empty($class_id)) {
                $condition['Students.department_id'] = $class_id;
            }
//              if (!empty($joindate)) {
//                  $condition['joindate'] = $joindate;
//              }
            

            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Lgas', 'Users']);
            
            // Add date conditions only if dates are valid
            if (!empty($from)) {
                $students = $students->where(['DATE(joindate) >= ' => $from]);
            }
            if (!empty($to)) {
                $students = $students->andWhere(['DATE(joindate) <= ' => $to]);
            }
            
            $students = $students->andWhere($condition)
                    ->order(['admissiondate' => 'DESC'])
                    ->limit(5000);
        } else {
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Lgas', 'Users'])
                    ->where(['status' => 'Admitted'])
                    ->order(['joindate' => 'DESC'])
                    ->limit(2000);
            //get the base url
        }

        $departments = $this->Students->Departments->find('list')->order(['name' => 'DESC']);
        $this->set(compact('students', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

//testing
    public function testxml($postData) {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';
        $xml .= "<CustomerInformationResponse>";
        $xml .= "<MerchantReference>6405</MerchantReference>";
        $xml .= "<Customers>";
        $xml .= "<Customer>";
        $xml .= "<Status>0</Status>
            <CustReference>111111117</CustReference>
            <CustomerReferenceAlternate></CustomerReferenceAlternate>
            <FirstName>test test</FirstName>
            <LastName></LastName>
            <Email></Email>
            <Phone></Phone>
            <ThirdPartyCode></ThirdPartyCode>
            <Amount>0.00</Amount>
        </Customer>
    </Customers>
</CustomerInformationResponse>";

        if (isset($postData) and $postData != "") { //echo $postData; exit;
            $xml = simplexml_load_string($postData);

            if ($xml->Payments) {
                $xml2 = new DomDocument('1.0', 'utf-8');
                //$xml2->xmlStandalone = false;
                $xml2->formatOutput = true;
                $xml2->encoding = 'utf-8';
                $paynode = $xml2->createElement("PaymentNotificationResponse");
                $payments = $xml2->createElement("Payments");
                foreach ($xml->Payments->Payment as $payment) {
                    $pay = processPaymentNotification($payment, $xml2);

                    $payments->appendChild($pay);
                }
                $paynode->appendChild($payments);
                $xml2->appendChild($paynode);
                header("Content-Length: " . strlen(trim($xml2->saveXML())));
                echo trim($xml2->saveXML());
            } else if ($xml->CustReference) {
                $xml2 = new DomDocument('1.0', 'utf-8');
                $xml2->encoding = 'utf-8';
                $cust = processCustomerInfo($xml, $xml2);
                $CIR = $xml2->createElement("CustomerInformationResponse");
                $merch = $xml2->createElement("MerchantReference", $MerchantReference);
                $customers = $xml2->createElement("Customers");

                $customers->appendChild($cust);

                $CIR->appendChild($merch);
                $CIR->appendChild($customers);
                $xml2->appendChild($CIR);
                header("Content-Length: " . strlen(trim($xml2->saveXML())));
                echo trim($xml2->saveXML());
            } else {
                echo "0";
            }
        }
    }

    //go to paystack for payment
    public function gotopaystack($student_id, $fee_id, $invoice_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $fee = $fees_Table->get($fee_id);
        $invoice = $invoices_Table->get($invoice_id);

        //create invoice
        // $invoice_id = $this->creatnewinvoice($student_id, $fee_id,  $fee->amount);
        $student = $this->Students->get($student_id);
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

        $baseurl = "http://localhost/imsced/";
        $auth = "";
        //account for feeding
        $subaccount4 = 'ACCT_6vg2ks21fg24izr';
        $auth4 = 'sk_live_8c83b87beedc6e7b0020138c94b13762a991dd17';
        //account for accommodation, reg and other fees
        $subaccount3 = 'ACCT_1m4gnaie5gd9jmt';
        $auth3 = 'sk_live_e58bcfe9a3f3f80e9b696cc2d1da2de5331feaed';

        $subaccount2 = 'ACCT_b58j8zfhogydmda';
        $auth2 = 'sk_live_c6cd37f39df62d1643b8a6c854ca82a40667d61e';
        $subaccount1 = 'ACCT_eyec9earijeztxb';
        $auth1 = 'sk_live_bd5ae86597f3fed8a4ad7c013d31c572bc9f7d3f';
        $subacc = 'ACCT_eyec9earijeztxb'; // sub-account code, you get this when you set up a split account.
        $cancel_url = $baseurl . 'cancel/' . $transaction->payref . '/';
        if (($fee_id == 3) || ($fee_id == 55) || ($fee_id == 57) || ($fee_id == 63) || ($fee_id == 64) || ($fee_id == 65) || ($fee_id == 68) || ($fee_id == 67)) {
            //account for pre-admission fees, clinicals and transfer fee, accreditation fee and mattress fee  - UBA
            $subacc = $subaccount2;
            $auth = $auth2;
        } elseif (($fee_id == 13) || ($fee_id == 17) || ($fee_id == 29)) { //account for accommodation fee, reg fee and other fees  - Eco Bank
            $subacc = $subaccount3;
            $auth = $auth3;
        } elseif (($fee_id == 19) || ($fee_id == 40) || ($fee_id == 52) || ($fee_id == 53) || ($fee_id == 54) || ($fee_id == 66)) {
            //account for feeding fee  and transcript fee   - first bank
            $subacc = $subaccount4;
            $auth = $auth4;
        } else { //account for the rest of the fees, application, SUG,   - Zenith bank
            $subacc = $subaccount1;
            $auth = $auth1;
        }
        //handle split
        $split_to_cun = 0;
        if (($fee_id == 5) || ($fee_id == 4)) {
            $split_to_cun = 0;
        } else {
            $split_to_cun = ($fee->amount - 500);
        }
        //put auth code insession for usein transactions controller for validation
        $this->request->getSession()->write('dauth', $auth);

        //base url
        $baseUrl = Router::url('/', true);
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
                'callback_url' => $baseUrl . 'students/paymentverification/' . $transaction->payref,
                // 'callback_url' => 'http://localhost/nerp/students/paymentverification/' . $transaction->payref,
                'amount' => $fee->amount . '00',
                'email' => $student->email,
                'name' => $name,
                'subaccount' => $subacc,
                'phone' => $student->phone,
                'last_name' => $student->lname,
                'bearer' => 'subaccount',
                'reference' => $transaction->payref,
                'transaction_charge' => $split_to_cun . '00',
                'metadata' => json_encode([
                    'cancel_action' => $cancel_url,
                    'name' => $name,
                    'fname' => $student->fname,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'transaction_id' => $transaction->id,
                    'student_id' => $student_id,
                    'payee_id' => $transaction->payref,
                    'invoice_id' => $invoice_id,
                ]),
            ]),
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $auth,
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
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
        //  header('location : '.$tranx->data->authorization_url);
        //return $tranx->data->authorization_url;
        return $this->redirect($tranx->data->authorization_url);
    }

    //verify payment and assign value
    public function paymentverification($ref) {
        // echo $ref; exit;
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()->where(['payref' => $ref])->first();
        $auth = "";
        //account for feeding
        $subaccount4 = 'ACCT_6vg2ks21fg24izr';
        $auth4 = 'sk_live_8c83b87beedc6e7b0020138c94b13762a991dd17';
        //account for accommodation, reg and other fees
        $subaccount3 = 'ACCT_1m4gnaie5gd9jmt';
        $auth3 = 'sk_live_e58bcfe9a3f3f80e9b696cc2d1da2de5331feaed';

        $subaccount2 = 'ACCT_b58j8zfhogydmda';
        $auth2 = 'sk_live_c6cd37f39df62d1643b8a6c854ca82a40667d61e';
        $subaccount1 = 'ACCT_eyec9earijeztxb';
        $auth1 = 'sk_live_bd5ae86597f3fed8a4ad7c013d31c572bc9f7d3f';
        $subacc = 'ACCT_eyec9earijeztxb'; // sub-account code, you get this when you set up a split account.
        // $fee_id = $transaction->fee_id;

        if (($transaction->fee_id == 3) || ($transaction->fee_id == 55) || ($transaction->fee_id == 57) || ($transaction->fee_id == 63) || ($transaction->fee_id == 64) || ($transaction->fee_id == 65) || ($transaction->fee_id == 67) || ($transaction->fee_id == 68)) {
            //account for pre-admission fees, clinicals and transfer fee, accreditation fee, medical and mattress fee - fidelity bank
            $subacc = $subaccount2;
            $auth = $auth2;
        } elseif (($transaction->fee_id == 13) || ($transaction->fee_id == 17) || ($transaction->fee_id == 29)) {
            //account for accommodation fee, reg fee and other fees
            $subacc = $subaccount3;
            $auth = $auth3;
        } elseif (($transaction->fee_id == 19) || ($transaction->fee_id == 40) || ($transaction->fee_id == 52) || ($transaction->fee_id == 53) || ($fee_id == 54) || ($fee_id == 66)) {
//account for feeding fee and transcript
            $subacc = $subaccount4;
            $auth = $auth4;
        }


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($ref),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . $auth,
                "cache-control: no-cache"
            ],
        ));

        //sk_test_7d5d515418c31cf203abbe3f753b1487b7d2a5e2

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

        // debug($tranx); exit;

        $trans_id = $tranx->data->metadata->transaction_id;
        $email = $tranx->data->metadata->email;
        $name = $tranx->data->metadata->name;
        $invoice_id = $tranx->data->metadata->invoice_id;
        //update transaction record
        // $transaction = $transactions_Table->get($trans_id);
        $transaction->status = $tranx->status;
        $transaction->transdate = date('Y-m-d H:i');
        $transaction->amount = $tranx->data->amount / 100;
        $transaction->paystatus = 'completed';
        $transaction->gresponse = $tranx->data->status;
        $transactions_Table->save($transaction);
        //check if this is acceptance fee and assign reg number
//        if($transaction->fee_id==3 || $transaction->fee_id==21 || $transaction->fee_id==25 ){
//            $student = $this->Students->get($transaction->student_id);
//            $this->getregno($transaction->student_id, $student->department_id);
//            
//        }
        // update invoice
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($invoice_id);
        $invoice->paystatus = $tranx->data->status;
        $invoice->payday = date('d M Y H:i a');
        $invoices_Table->save($invoice);
        //send payment alert via email
        // $this->payconfirmationmail($email, $name, $transaction->amount, $transaction->student_id, $transaction->payref);
        //check if this is feeding fee and update table for the mobile app
        if ($transaction->fee_id == 19 || $transaction->fee_id == 40) {
            $this->updatefeedingfee($transaction->student_id, $transaction->amount);
        }
        $this->Flash->success('Your payment was successful.');
        return $this->redirect(['action' => 'invoices', $tranx->data->metadata->student_id]);
    }

    //mailing method for payment receipt
    public function payconfirmationmail($studentemail, $name, $amount, $student_id, $ref) {
        $students_table = TableRegistry::get('Students');
        $student = $students_table->get($student_id, ['contain' => ['Departments']]);

        $message = " Hello " . $name . ' ' . ',<br />' . SCHOOL . ' has recieved your payment'
                . '<br />';
        // . ' Do remember that you can always use your application number to check your admission status any time. <br />Please find details below your payment details: <br />';
        $message .= '<br />Payment Ref : ' . $ref;
        $message .= '<br />Department : ' . $student->department->name;
        $message .= '<br />Registration No : ' . $student->regno;
        $message .= '<br /> Payment  Date : ' . date('D, d M Y');
        $message .= '<br /> Amount : ₦' . number_format($amount);

        $message .= '<br /><br />'
                . 'Kind Regards,<br />'
                . SCHOOL . ' <br />';

        // $statusmsg = "";
        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($studentemail);
        $email->setBcc(['payments@claretianuniversity.edu.ng', 'chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Student Payment Receipt - CUN');
        $email->deliver($message);
        return;
    }

    //method that updates the cafcredit table for student feeding fee payment
    private function updatefeedingfee($studentid, $amount) {
        $cafcredit_Table = TableRegistry::get('Cafcredit');
        $student = $this->Students->get($studentid);
        $cafcredit = $cafcredit_Table->newEmptyEntity();
        $cafcredit->maricnum = $student->regno;
        $cafcredit->amount = $amount;
        $cafcredit_Table->save($cafcredit);
        return;
    }

    //student method for viewing their profile
    public function viewprofile() {
        //check fee payment before proceeding
        $users_controller = New UsersController();
        $this_student = $this->request->getSession()->read('student');
        $paid_fees = $users_controller->checkfees($this_student->id);
        if ($paid_fees < 4) {
            $this->Flash->error(__('Sorry, you still have some outstanding fees, you need to pay up'
                            . ' else you wont be able to access your results'));
            return $this->redirect(['controller' => 'Students', 'action' => 'dashboard']);
        }
        $student = $this->Students->find()
                        ->where(['user_id' => $this->Auth->user('id')])
                        ->contain(['Departments.Subjects', 'States', 'Countries', 'Users', 'Subjects', 'Invoices.Fees',
                            'Invoices.Sessions', 'Lgas', 'Results.Sessions', 'Results.Semesters', 'Results.Subjects'
                            , 'Hostelrooms.Hostels'])->first();
        //get student registered courses
        $courses = $this->getstudentcourses($student->id);
        $this->set('courses', $courses);
        $this->set('student', $student);
        //debug(json_encode($student , JSON_PRETTY_PRINT)); exit;
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //student method for updating their profile
    public function updateprofile() {
        $student = $this->Students->find()
                ->select(['id', 'fname', 'lname', 'mname', 'dob', 'phone', 'address', 'email', 'country_id', 'state_id', 'lga_id', 'community', 'department_id', 'regno', 'gender', 'birthcerturl', 'othercerts', 'olevelresulturl', 'passporturl', 'studentstatus', 'user_id', 'sparent_id'])
                ->where(['Students.user_id' => $this->Auth->user('id')])
                ->contain(['Users', 'Departments', 'States', 'Countries', 'Lgas', 'Sparents'])
                ->first();
        
        // Manually load parent data if sparent_id exists but sparent object is not loaded
        if (!empty($student->sparent_id) && !isset($student->sparent)) {
            $sparentsTable = TableRegistry::get('Sparents');
            try {
                $student->sparent = $sparentsTable->get($student->sparent_id);
            } catch (\Exception $e) {
                // Parent not found, continue without parent data
            }
        }
        //debug(json_encode($student , JSON_PRETTY_PRINT)); exit;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userscontroller = new UsersController();
            //upload files
            //upload o level
//            $imagearray = $this->request->getData('olevelresulturls');
//            if (!empty($imagearray['tmp_name'])) {
//                $waec_cert = $userscontroller->addimage($imagearray);
//            } else {
//                $waec_cert = $student->olevelresulturl;
//            }
            //upload birth cert
//            $birth_imagearray = $this->request->getData('birthcerturls');
//            if (!empty($birth_imagearray['tmp_name'])) {
//                $birth_cert = $userscontroller->addimage($birth_imagearray);
//            } else {
//                $birth_cert = $student->birthcerturl;
//            }
            //upload other file
//            $other_imagearray = $this->request->getData('othercertss');
//            if (!empty($other_imagearray['tmp_name'])) {
//                $other_cert = $userscontroller->addimage($other_imagearray);
//            } else {
//                $other_cert = $student->othercerts;
//            }
            //upload other file
            $passport_imagearray = $this->request->getData('passporturls');
            $name = $passport_imagearray->getClientFilename();
            if (!empty($name)) {

                $photo = $this->doresizepassport($this->request->getData('passporturls'));
                // $photo = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $photo = $student->passporturl;
            }


            $student = $this->Students->patchEntity($student, $this->request->getData());
            $student->studentstatus = "Active";
            if (!empty($photo)) {
                $student->passporturl = $photo;
            }
            
            // Update parent information if student has a parent
            $parentSaveResult = true;
            if (!empty($student->sparent_id)) {
                $sparentsTable = TableRegistry::get('Sparents');
                $parent = $sparentsTable->get($student->sparent_id);
                
                // Update parent fields from form data
                $parent->fathersname = $this->request->getData('fathersname');
                $parent->mothersname = $this->request->getData('mothersname');
                $parent->fatherphone = $this->request->getData('fatherphone');
                $parent->motherphone = $this->request->getData('motherphone');
                $parent->fathersjob = $this->request->getData('fathersjob');
                $parent->mothersjob = $this->request->getData('mothersjob');
                
                $parentSaveResult = $sparentsTable->save($parent);
            }
            if ($this->Students->save($student) && $parentSaveResult) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Student Profile Update " . $student->regno;
                $user_id = $this->Auth->user('id');
                $description = "Profile Update " . $student->regno;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('Your data has been updated successfully.'));

                return $this->redirect(['action' => 'viewprofile']);
            }
            $this->Flash->error(__('Profile data could not be updated. Please, try again.'));
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 2000]);
        $states = $this->Students->States->find('list', ['limit' => 2000])->where(['country_id' => 160])->order(['name' => 'ASC']);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $fees = $this->Students->Fees->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $subjects = $this->Students->Subjects->find('list', ['limit' => 2000]);
        // $levels = $this->Students->Levels->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        // $programes = $this->Students->Programmes->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $this->set(compact('student', 'departments', 'states', 'countries', 'fees', 'subjects', 'lgas'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //function that returns the states on the drop down
    public function getstates($country_id) {
        $statestable = TableRegistry::get('States');
        $states = $statestable->find('list')
                ->where(['country_id' => $country_id]);
        $this->set(compact('states'));
        //debug(json_encode($states , JSON_PRETTY_PRINT)); exit;
    }

    //gets states based on countries on transcript request
    public function liststates($countryid) {
        $statestable = TableRegistry::get('States');
        $states = $statestable->find('list')
                ->where(['country_id' => $countryid]);
        $this->set(compact('states'));
    }

    //admin method for bulk import of students
    public function newimportstudents() {
        if ($this->request->is(['patch', 'post', 'put'])) {

            $message = "";
            $department_id = $this->request->getData('department_id');
            $faculty_id = $this->request->getData('faculty_id');
            $level_id = $this->request->getData('level_id');
            $programe_id = $this->request->getData('programme_id');
            $upload_file = $this->request->getData('students');
            $name = $upload_file->getClientFilename();
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $tmpName = $upload_file->getStream()->getMetadata('uri');
            $type = $upload_file->getClientMediaType();
            $error = $upload_file->getError();

            $allowedext = ['csv', 'xlsx'];
            if ($error != 0) {
                $this->Flash->error(__('Sorry, there is a problem with the file. Please check and try again'));

                return $this->redirect(['action' => 'importstudents']);
            }
            if (!in_array($ext, $allowedext)) {
                $this->Flash->error(__('Sorry, only csv or xlsx files can be uploaded.'));

                return $this->redirect(['action' => 'importstudents']);
            } else {
                // echo $type.'-'.$ext.'-'.$error; exit;
                $helper = new Helper\Sample();
                debug($helper);
                $spreadsheet = IOFactory::load($tmpName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $count = 0;
                $old = 0;
                $inserted = 0;
                foreach ($sheetData as $data) {
                    $count++;

                    if ($count > 1) {
                        //  debug(json_encode($data, JSON_PRETTY_PRINT)); exit;
                        
                        // Validate required fields are present and not empty
                        if (empty($data['A']) || empty($data['B']) || empty($data['E']) || empty($data['J'])) {
                            continue; // Skip this row if required fields are missing
                        }
                        
                        $department = $this->Students->Departments->get($department_id);
                        $faculty = $this->Students->Faculties->get($faculty_id);
                        // $level = $this->Students->Levels->get($level_id);
                        // $programe = $this->Students->Programmes->get($programe_id);
                        //  echo strtolower(trim($department->name)).' | '.strtolower(trim($data['D'])); exit;
                        if ((strtolower(trim($department->name)) == strtolower(trim($data['D']))) && (strtolower(trim($faculty->name)) == strtolower(trim($data['L'])))
                        ) {

                            // check if student exists in the database already.
                            $oldstudent = $this->Students->find()->where(['regno' => $data['J']])->first();
                            //  debug(json_encode($oldstudent, JSON_PRETTY_PRINT)); exit;
                            if (empty($oldstudent)) {
                                //create login data for the student
                                $user_id = $this->getlogindetails($data['E'], $data['A'], $data['B'], $data['K']);
                                if (!is_numeric($user_id)) {
                                    $this->Flash->error(__('Sorry, there is a problem with the file. Unable to create user data. Please check and try again'));

                                    return $this->redirect(['action' => 'importstudents']);
                                }
                                //get admision date
                                //$admindate = explode('/', $data['J']);
                                // $admin_date = $admindate[0];
                                //echo  $admindate[0]; exit;
                                //create a new student object
                                $student = $this->Students->newEmptyEntity();
                                $student->regno = $data['J'];
                                $student->fname = $data['A'];
                                $student->lname = $data['B'];
                                $student->mname = $data['K'];
                                $student->status = 'Admitted';
                                $student->gender = $data['I'];
                                $student->dob = $data['C'];
                                $student->country_id = 160;
                                $student->state_id = 2648;
                                $student->department_id = $department_id;
                                $student->email = $data['E'];
                                $student->address = $data['F'];
                                $student->phone = $data['G'];
                                $student->programme_id = $programe_id;
                                $student->faculty_id = $faculty_id;
                                $student->mode_id = 1;
                                $student->level_id = $level_id;
                                //  $student->jambregno = $data['N'];
                                //$student->community = $data['O'];
                                $student->admissiondate = date('D d M, Y h:i a');
                                $student->user_id = $user_id;
                                //  debug(json_encode($student, JSON_PRETTY_PRINT)); exit;
                                //save the student
                                $this->Students->save($student);

                                $inserted++;
                            } else {
                                $old++;
                                $message = $old++ . ' Student(s) could not be uploaded because their regno already exists';
                            }
                        } else {
                            $this->Flash->error(__('Sorry, the selected department or faculty, didn\'t match that in the csv file you are uploading...'));

                            return $this->redirect(['action' => 'importstudents']);
                        }

                        // debug(json_encode($data['F'], JSON_PRETTY_PRINT)); exit;
                    }
                }
                if ($inserted > 0) {
                    $this->Flash->success(__($inserted . ' Students have been uploaded successfully. ' . $message));
                } else {
                    $this->Flash->warning(__('No students were imported. ' . $message));
                }
                
                // Debug: Log the import results
                $this->log('Import Results - Inserted: ' . $inserted . ', Message: ' . $message, 'info');

                return $this->redirect(['action' => 'importstudents']);
            }
        }

        // $faculties = $this->Students->Faculties->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $states = $this->Students->States->find('list', ['limit' => 2000]);
        $countries = $this->Students->Countries->find('list', ['limit' => 200]);
        // $levels = $this->Students->Levels->find('list', ['limit' => 10]);
        // $programes = $this->Students->Programmes->find('list', ['limit' => 200])->order(['name' => 'Asc']);
        $this->set(compact('states', 'departments', 'countries'));

        $this->viewBuilder()->setLayout('backend');
    }



    public function importstudents() {
        if ($this->request->is(['patch', 'post', 'put'])) {

            $message = "";
            $department_id = $this->request->getData('department_id');
            $class_arm_id = $this->request->getData('class_arm_id');
            $upload_file = $this->request->getData('students');
            $name = $upload_file->getClientFilename();
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $tmpName = $upload_file->getStream()->getMetadata('uri');
            $type = $upload_file->getClientMediaType();
            $error = $upload_file->getError();

            $allowedext = ['csv', 'xlsx'];
            if ($error != 0) {
                $this->Flash->error(__('Sorry, there is a problem with the file. Please check and try again'));

                return $this->redirect(['action' => 'importstudents']);
            }
            if (!in_array($ext, $allowedext)) {
                $this->Flash->error(__('Sorry, only csv or xlsx files can be uploaded.'));

                return $this->redirect(['action' => 'importstudents']);
            } else {
                $helper = new Helper\Sample();
                // debug($helper);
                $spreadsheet = IOFactory::load($tmpName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $count = 0;
                $old = 0;
                $inserted = 0;
                foreach ($sheetData as $data) {
                    $count++;

                    if ($count > 1) {
                        //  debug(json_encode($data, JSON_PRETTY_PRINT)); exit;
                        
                        // Validate required fields are present and not empty
                        if (empty($data['A']) || empty($data['B']) || empty($data['E']) || empty($data['J'])) {
                            continue; // Skip this row if required fields are missing
                        }
                        
                        $department = $this->Students->Departments->get($department_id);
                        //  echo strtolower(trim($department->name)).' | '.strtolower(trim($data['D'])); exit;
                        if ((strtolower(trim($department->name)) == strtolower(trim($data['D']))) 
                        ) {

                            // check if student exists in the database already.
                            $oldstudent = $this->Students->find()->where(['regno' => $data['J']])->first();
                            //  debug(json_encode($oldstudent, JSON_PRETTY_PRINT)); exit;
                            if (empty($oldstudent)) {
                                //create login data for the student
                                $user_id = $this->getlogindetails($data['E'], $data['A'], $data['B'], ' ');
                                if (!is_numeric($user_id)) {
                                    $this->Flash->error(__('Sorry, there is a problem with the file. Unable to create user data. Please check and try again'));

                                    return $this->redirect(['action' => 'importstudents']);
                                }
                                
                                $student = $this->Students->newEmptyEntity();
                                $student->regno = $data['J'];
                                $student->fname = $data['A'];
                                $student->lname = $data['B'];
                                $student->status = 'Admitted';
                                $student->gender = $data['I'];
                                $student->dob = $data['C'];
                                $student->country_id = 160;
                                $student->state_id = 2648;
                                $student->department_id = $department_id;
                                $student->class_arm_id = !empty($class_arm_id) ? $class_arm_id : null;
                                $student->email = $data['E'];
                                $student->address = $data['F'];
                                $student->phone = $data['G'];
                                $student->admissiondate = $data['H'];
                                $student->user_id = $user_id;
                                //save the student
                                $this->Students->save($student);
                                $inserted++;
                            } else {
                                $old++;
                                $message = $old++ . ' Student(s) could not be uploaded because their regno already exists';
                            }
                        } else {
                            $this->Flash->error(__('Sorry, the selected department, didn\'t match that in the csv file you are uploading...'));

                            return $this->redirect(['action' => 'importstudents']);
                        }

                        // debug(json_encode($data['F'], JSON_PRETTY_PRINT)); exit;
                    }
                }
                if ($inserted > 0) {
                    $this->Flash->success(__($inserted . ' Students have been uploaded successfully. ' . $message));
                } else {
                    $this->Flash->warning(__('No students were imported. ' . $message));
                }
                
                // Debug: Log the import results
                $this->log('Import Results - Inserted: ' . $inserted . ', Message: ' . $message, 'info');

                return $this->redirect(['action' => 'importstudents']);
            }
        }

        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        $states = $this->Students->States->find('list', ['limit' => 200]);
        $countries = $this->Students->Countries->find('list', ['limit' => 200]);

        $this->set(compact('states', 'departments', 'countries'));

        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            //delete userrecords
            $user = $this->Students->Users->get($student->user_id);
            $this->Students->Users->delete($user);
            //log activity
            $usercontroller = new UsersController();

            $title = "Deleted a student " . $student->regno . '-' . $student->fname . '-' . $student->lname;
            $user_id = $this->Auth->user('id');
            $description = "Deleted a student " . $student->application_no;
            $ip = $this->request->clientIp();
            $type = "Delete";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'managestudents']);
    }

    //admin method for deleteing an applican
    public function deleteapplicant($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            //delete userrecords
            $user = $this->Students->Users->get($student->user_id);
            $this->Students->Users->delete($user);
            //log activity
            $usercontroller = new UsersController();

            $title = "Deleted an Applicant " . $student->application_no;
            $user_id = $this->Auth->user('id');
            $description = "Deleted an applicant " . $student->fname . ' ' . $student->lname;
            $ip = $this->request->clientIp();
            $type = "Delete";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The applicant has been deleted.'));
        } else {
            $this->Flash->error(__('The applicant could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageapplicants']);
    }

    //method that downloads the student data format
    public function downloadformat() {
        $url = Router::url('/', true);
        $ext = pathinfo("students_format.xlsx", PATHINFO_EXTENSION);
        // echo  basename($pathtofile."cvs/students_format.xlsx"); exit;
        $filename = "students_format.xlsx";
        header('Content-Type: ' . $ext);
        header('Content-Length: ' . filesize("cvs/students_format.xlsx"));
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header("Cache-control: private");
        readfile("cvs/students_format.xlsx");
        return;
    }

    //admin method for getting students in a department
    public function getstudentsindept($deptid) {
        $students = $this->Students->find()
                ->contain(['Departments'])
                ->where(['department_id' => $deptid, 'status' => 'Admitted']);

        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        $this->set('students', $students);
        $this->set('departments', $departments);
    }

    //student method for checking their application status
    public function checkstatus($application_id) {
        $applicant = $this->Students->find()->where(['application_no' => $application_id])->first();
        // debug(json_encode($applicant, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('applicant'));
    }

    //admin method for sending a message to students
    public function newmessagetostudents() {

        if ($this->request->is('post')) {

            $student_ids = $this->request->getData('student._ids');
            $dept_id = $this->request->getData('department_id');
            $subject = $this->request->getData('subject');
            $message = $this->request->getData('message');
            $count = 0;
            //check if we are sending to all students in the selected department
            if (!empty($dept_id && empty($student_ids))) {
                $students = $this->Students->find()->where(['department_id' => $dept_id]);
                foreach ($students as $student) {
                    $greeting = 'Hello ' . $student->fname . ' ' . $student->lname . '<br />';

                    $message .= $greeting;
                    $message .= '<br /><br />';
                    $this->messagetostudents($student->email, $subject, $message);
                    $count++;
                }
            } elseif (!empty($dept_id && !empty($student_ids))) {
                //we are sending to selected students in a selected department
                foreach ($student_ids as $id) {
                    $student = $this->Students->get($id);
                    $greeting = 'Hello ' . $student->fname . ' ' . $student->lname . '<br />';

                    $message .= $greeting;
                    $message .= '<br /><br />';
                    $this->messagetostudents($student->email, $subject, $message);
                    $count++;
                    // echo $id; exit;
                }
            }
            //log activity
            $usercontroller = new UsersController();

            $title = "Sent a mail to some students ";
            $user_id = $this->Auth->user('id');
            $description = "Sent mail to a total of" . $count . " students ";
            $ip = $this->request->clientIp();
            $type = "Add";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('Message has been sent to ' . $count . ' students'));
            return $this->redirect(['action' => 'newmessagetostudents']);
            //debug(json_encode( $student_ids, JSON_PRETTY_PRINT)); exit;  
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        $students = $this->Students->find('list')->where(['status' => 'Admitted']);
        $this->set(compact('students', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method that sends a message to selected students
    private function messagetostudents($emailaddress, $subject, $message) {

        $message .= '<br /><br />'
                . 'Kind Regards,<br />'
                . SCHOOL . ' <br />';

        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($emailaddress);
        $email->setBcc(['admissions@claretianuniversity.edu.ng']);
        $email->setEmailFormat('html');
        $email->setSubject($subject);
        $email->deliver($message);
        return;
    }

    //student method for contacting admin
    public function contactadmin() {
        if ($this->request->is('post')) {
            $subject = $this->request->getData('subject');
            $message = $this->request->getData('message');
            //get admin email from session
            $settings = $this->request->getSession()->read('settings');
            //call the mailling function

            if ($this->messagetostudents($settings->email, $subject, $message)) {
                // debug(json_encode($settings, JSON_PRETTY_PRINT)); exit; 
                $this->Flash->success(__('Message has been sent to admin'));
                return $this->redirect(['action' => 'messagetoadmin']);
            } else {
                $this->Flash->error(__('Sorry, unable to send message, please try again'));
                return $this->redirect(['action' => 'messagetoadmin']);
            }
        }

        $this->viewBuilder()->setLayout('backend');
    }

    //students method for contacting their teacher
    public function contactlecturer() {
        $teachers_Table = TableRegistry::get('Teachers');
        $student = $this->Students->find()->where(['user_id' => $this->Auth->user('id')])->first();

        if ($this->request->is('post')) {
            $subject = $this->request->getData('subject');
            $message = $this->request->getData('message');
            $teacher_id = $this->request->getData('teacher_id');
            if (!empty($teacher_id)) {
                $teacher = $teachers_Table->get($teacher_id, ['contain' => ['Users']]);
                //call the mailing function
                if ($this->messagetostudents($teacher->user->username, $subject, $message)) {
                    $this->Flash->success(__('Message has been sent to ' . $teacher->firstname . ' ' . $teacher->lastname));
                    return $this->redirect(['action' => 'contactlecturer']);
                } else {
                    $this->Flash->error(__('Sorry, unable to send message. Please try again'));
                    return $this->redirect(['action' => 'contactlecturer']);
                }
            }
        }
        $teachers = $teachers_Table->find('list')->where(['department_id' => $student->department_id]);
        $this->set(compact('teachers'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that populates the dropdown for admin to send email to students
    public function getstudentsformail($deptid) {
        $students = $this->Students->find('list')->where(['department_id' => $deptid]);
        $this->set(compact('students'));
    }

    //admin method for promoting students
    public function promotestudents() {
        $students = $this->Students->find()
                ->contain(['Departments'])
                ->where(['status' => 'Admitted'])
                ->order(['joindate' => 'DESC']);
        if ($this->request->is('post')) {
            $deptid = $this->request->getData('department_id');
            $targetClassArmId = $this->request->getData('target_class_arm_id');
            

            $count = 0;
            // echo $level_id; exit;
            // debug(json_encode( $this->request->getData('studentids'), JSON_PRETTY_PRINT)); exit;
            //ensure at least a student is selected
            if (!empty($this->request->getData('studentids'))) {
                foreach ($this->request->getData('studentids') as $student_id) {
                    if (is_numeric($student_id)) {
                        $student = $this->Students->get($student_id);
                        $student->department_id = $deptid;
                        $student->class_arm_id = $targetClassArmId; // Set the target class arm
                        
                        if ($this->Students->save($student)) {
                            $count++;
                        }
                    }
                }
                
                // Get department and class arm names for success message
                $department = $this->Students->Departments->get($deptid);
                $targetClassArm = null;
                if (!empty($targetClassArmId)) {
                    $targetClassArm = $this->Students->ClassArms->get($targetClassArmId);
                }
                
                $targetName = $department->name;
                if (!empty($targetClassArm)) {
                    $targetName .= ' - ' . $targetClassArm->arm_name;
                }
                
                $this->Flash->success(__($count . ' Students have been promoted to ' . $targetName));
                return $this->redirect(['action' => 'getstudentsforpromotion']);
            } else {
                $this->Flash->error(__(' Unable to promote student. It seems like you did not select any student after all'));
                return $this->redirect(['action' => 'getstudentsforpromotion']);
            }
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        // $this->set(compact('students', 'levels'));
        $this->set(compact('students', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method that gets the students to be promoted
    public function getstudentstopromote($deptid) {
        $students = $this->Students->find()
                ->contain(['Departments'])
                ->where(['department_id' => $deptid, 'status' => 'Admitted']);

        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        $this->set(compact('students', 'levels'));
        $this->set('departments', $departments);
    }

    //student method for viewing their course materials
    public function coursematerials() {
        //ensure this is a student
        $student = $this->isstudent();
        $coursematerials_Table = TableRegistry::get('Coursematerials');
        $materials = $coursematerials_Table->find()
                ->contain(['Teachers', 'Subjects', 'Departments'])
                ->where(['Coursematerials.department_id' => $student->department->id]);
        $this->set('materials', $materials);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method that ensure this person is a student
    public function isstudent() {

        $student = $this->Students->find()
                ->contain(['Departments', 'Subjects'])
                ->where(['user_id' => $this->Auth->user('id')])
                ->first();
        if (!$student) { //this is not a valid student
            $this->Flash->error(__('Sorry, invalid access'));

            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        } else {
            return $student;
        }
    }

    //student method for dowloading course materials
//
//    public function downloadmaterial($id) {
//        //ensure this is a student
//        $student = $this->isstudent();
//        $coursematerials_Table = TableRegistry::get('Coursematerials');
//        $coursematerial = $coursematerials_Table->get($id);
//        $ext = pathinfo($coursematerial->fileurl, PATHINFO_EXTENSION);
//        // debug(json_encode($coursematerial, JSON_PRETTY_PRINT)); exit;
//        //  exit;
//        //  if(is_file("coursematerials/" . $coursematerial->fileurl)){echo "coursematerials/" . $coursematerial->fileurl; exit;}
//        header('Content-Type: ' . $ext);
//        header('Content-Length: ' . filesize("coursematerials/" . $coursematerial->fileurl));
//        header('Content-Disposition: attachment;filename="' . $coursematerial->fileurl . '"');
//        header("Cache-control: private");
//        header('Content-Transfer-Encoding', 'binary');
//        header('Expires', 0);
//        header('Cache-Control', 'no-cache');
//        header('Pragma', 'public');
//        header('X-Pad', 'avoid browser bug');

//        readfile("coursematerials/" . $coursematerial->fileurl);
//        return;
//    }

    
     //student method for dowloading course materials

    public function downloadmaterial($id) {
        //ensure this is a student
        $student = $this->isstudent();
        $resources_Table = TableRegistry::get('Eresources');
        $coursematerial = $resources_Table->get($id);
        
        $ext = pathinfo($coursematerial->filenameurl, PATHINFO_EXTENSION);
        $filename = $coursematerial->filenameurl;
        header('Content-Type: ' . $ext);
        header('Content-Length: ' . filesize("eresources/".$coursematerial->filenameurl));
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header("Cache-control: private");
        readfile("eresources/".$coursematerial->filenameurl);
        return;
        
    }

    
    
    //method that gets the countries in a selected continent
    public function getcountries($continent_id) {
        $continents_Table = TableRegistry::get('Continents');
        $countries = $continents_Table->get('list')->where(['continent_id' => $continent_id]);
        $this->set('countries', $countries);
    }

    //student method for requesting for transcript
    public function requesttrnscript() {
        //ensure this is a student
        $student = $this->isstudent();

        $trequest_Table = TableRegistry::get('Trequests');
        $continents_Table = TableRegistry::get('Continents');
        $trequest = $trequest_Table->newEmptyEntity();
        $continent_costs = $continents_Table->find();
        if ($this->request->is('post')) {
            // debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $continentid = $this->request->getData('continent_id');
            $continent = $continents_Table->get($continentid);
            $trequest = $trequest_Table->patchEntity($trequest, $this->request->getData());
            $trequest->amount = $continent->cost;
            $trequest->student_id = $student->id;
            $trequest->fee_id = 54;
            if ($trequest_Table->save($trequest)) {
                //created invoice
                // $invoice_id = $this->creatnewinvoice($student->id, 54, $continent->cost);
                //proceed to payment gateway for payment
                // $transactionController = new TransactionsController();
                $this->Flash->success(__('Success, your transcript request has been submitted and would be processed within the next ten days as soon as we confirm your payment'));
                // return $this->redirect(['action' => 'generatetranscriptpayeeid', $invoice_id, $student->id]);
                return $this->redirect(['action' => 'myinvoices']);
                // $url = $this->gotopaystack($incoice_id, $student->id);
                // $this->Flash->success(__('Success, your transcript order has been submitted and would be processed within the next ten days'));
                // return $this->redirect($url);
            } else {
                $this->Flash->error(__('Sorry, unable to submit order. Please try again'));
                // return $this->redirect(['action' => 'myinvoices']);    
            }
        }

        $continents = $trequest_Table->Continents->find('list', ['limit' => 200]);
        $countries = $trequest_Table->Countries->find('list', ['limit' => 200]);
        $states = $trequest_Table->States->find('list', ['limit' => 200]);
        $couriers = $trequest_Table->Couriers->find('list', ['limit' => 200]);
        $this->set(compact('trequest', 'continents', 'countries', 'states', 'couriers', 'continent_costs'));
        //  $this->set('continent_costs', $continent_costs);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //creates a transaction record
    public function createtransaction($student_id, $fee_id, $rrr, $amount, $order_id, $new_hash, $invoice_id) {
        //get session data
        $settings = $this->request->getSession()->read('settings');
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->newEmptyEntity();
        $transaction->payref = $rrr;
        $transaction->amount = $amount;
        $transaction->student_id = $student_id;
        $transaction->paystatus = "initialized";
        $transaction->session_id = $settings->session_id;
        $transaction->semester_id = $settings->semester_id;
        $transaction->gresponse = $order_id; //the order id
        $transaction->fee_id = $fee_id;
        $transaction->invoice_id = $invoice_id;
        // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
        if ($transactions_Table->save($transaction)) {
            $this->Flash->success(__(' You can either pay online using the button below or copy the Transaction ID and proceed to the bank to make the '
                            . 'Payment'));
            return $this->redirect(['action' => 'gotoremita', $transaction->gresponse, $new_hash]);
        } else {
            $this->Flash->error(__(' Sorry, unable to initiate transaction.Please try again'));
            return $this->redirect(['action' => 'gotoremita', $transaction->gresponse, $new_hash]);
        }
    }

    //student method for verifying payment made at the bank using RRR
    public function verifypay() {
        if ($this->request->is('post')) {
            // $machantid = MERCHANTID;
            $rrr = $this->request->getData('rrr');
            $apiHash = hash('SHA512', $rrr . APIKEY . MERCHANTID);
            $resp = file_get_contents("https://remitademo.net/remita/ecomm/" . MERCHANTID . "/$rrr/$apiHash/status.reg");
            //$url = "https://remitademo.net/remita/ecomm/".MERCHANTID."/$rrr/$apiHash/status.reg";  
            // debug(json_encode($resp, JSON_PRETTY_PRINT)); exit;
            $data = explode(',', $resp);
            $message = explode('"', $data[6]); //get status
            $payday = explode('"', $data[4]); // check for pay date
            $datepaid = explode('"', $payday[3]); //get date of pay
            // echo $datepaid[0]; exit;
            //  echo $message[3];
            if ($message[3] == 01) {
                $transactions_Table = TableRegistry::get('Transactions');
                $invoices_Table = TableRegistry::get('Invoices');
                $transaction = $transactions_Table->find()->where(['payref' => $rrr])->first();

                if ($transaction) {
                    if ($transaction->paystatus == "completed") {
                        $this->Flash->error(__('This payment had already been verified'));
                        return $this->redirect(['action' => 'verifypay']);
                    }
                    $transaction->paystatus = "completed";
                    $transactions_Table->save($transaction);
                    $invoice = $invoices_Table->get($transaction->invoice_id);
                    $invoice->paystatus = "success";
                    $invoice->payday = $datepaid[0];
                    $invoices_Table->save($invoice);
                    //email receipt
                    $transactioncontroller = new TransactionController();
                    $transactioncontroller->transactionconfirmationmail($invoice->amount, $invoice->student_id, $invoice->fee_id);

                    $this->Flash->success(__('Congratulations, your payment has been verified'));
                } else {
                    $this->Flash->error(__('Sorry, unknown payment details. Please check the RRR code and try again'));
                }
            } else {
                $this->Flash->error(__('Sorry, we could not verify this transaction. Please check the RRR code and try again'));
            }
        }
        $this->viewBuilder()->setLayout('backend');
    }

    //method that handles the real payment by taking the user to remita
    public function gotoremita($order_id, $new_hash) {
        $user = $this->request->getSession()->read('usersinfo');
        //base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $fees_Table = TableRegistry::get('Fees');
        $students_Table = TableRegistry::get('Students');
        $transaction = $transactions_Table->find()->where(['gresponse' => $order_id])->first();
        $fee = $fees_Table->get($transaction->fee_id);
        $student = $students_Table->get($transaction->student_id);
        $responseurl = $baseUrl . 'Transactions/confirmremitapay/' . $transaction->payref;
        $this->set('transaction', $transaction);
        $this->set('responseurl', $responseurl);
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('new_hash', $new_hash);
        if ($user['role_id'] == 2 || $user['role_id'] == 5) {
            $this->viewBuilder()->setLayout('studentsbackend');
        } else {
            $this->viewBuilder()->setLayout('webland');
        }
    }

    //method that populates the student dropdown when admin is assigning him a book in the library
    public function departmentstudents($deptid) {
        $students_Table = TableRegistry::get('Students');
        $students = $students_Table->find('list')
                ->where(['department_id' => $deptid,
                        //'status'=>'Admitted'
                ])
                ->order(['fname' => 'DESC']);
        // debug(json_encode( $students, JSON_PRETTY_PRINT)); exit;

        $this->set('students', $students);
    }

    //method that shows a student profile when borrowing a book
    public function showstudent($id) {
        $student = $this->Students->get($id, ['contain' => ['Departments']]);
        $this->set('student', $student);
    }

    //method that shows the student book he has taken from the library
    public function borrowedbooks() {
        //ensure this is a student 
        $student = $this->isstudent();
        $Borrowedbooks_Table = TableRegistry::get('Borrowedbooks');
        $borrowedbooks = $Borrowedbooks_Table->find()
                ->contain(['Books'])
                ->where(['student_id' => $student->id])
                ->order(['date' => 'DESC']);
        $this->set('borrowedbooks', $borrowedbooks);
        $this->viewBuilder()->setLayout('backend');
    }

    //method that generates student id card
    public function getidcard($id) {
        $student = $this->Students->get($id, [
            'contain' => ['Departments', 'States', 'Countries', 'Users', 'Lgas',  'Faculties', 'Levels']
        ]);
        $this->set('student', $student);
        //set the regno to be used for generating the qrcode
        $this->set('regno', $student->regno);
        $this->viewBuilder()->setLayout('backend');
    }

    //method that generates QRCode using the students reg no(not in use)
    private function generateqrcode($regno) {
        $this->set('regno', $regno);
    }

//initiate remita payment
    public function initiateremitapostjson($invoice_id, $student_id) {
        //base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id);
        $name = $student->fname . ' ' . $student->lname;
        // debug(json_encode($student, JSON_PRETTY_PRINT)); exit;
        $totalAmount = $invoice->amount;
        $timesammp = DATE("dmyHis");
        $orderID = $timesammp;
        $payerName = $name;
        $payerEmail = $student->email;
        $payerPhone = $student->phone;
        $responseurl = $baseUrl . 'transactions/paymentverification/' . $orderID;
        $hash_string = MERCHANTID . SERVICETYPEID . $orderID . $totalAmount . APIKEY;
        $hash = hash('sha512', $hash_string);
        $itemtimestamp = $timesammp;
        //The JSON data.
        $content = '{"serviceTypeId":"' . SERVICETYPEID . '"' . "," . '
              "amount":"' . $totalAmount . '"' . "," . '
              "hash":"' . $hash . '"' . "," . '
              "orderId":"' . $orderID . '"' . "," . '
              "payerName":"' . $payerName . '"' . "," . '
              "payerEmail":"' . $payerEmail . '"' . ",
              " . '"payerPhone":"' . $payerPhone . '"}';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => GATEWAYURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_HTTPHEADER => array(
                "Authorization: remitaConsumerKey=" . MERCHANTID . ",remitaConsumerToken=$hash",
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $json_response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }

        // echo $json_response.'<br />';
        // echo GATEWAYRRRPAYMENTURL.'<br />';
        // echo GATEWAYURL.'<br />';

        $jsonData = substr($json_response, 7, -1);
        //  debug(json_encode($json_response, JSON_PRETTY_PRINT)); exit;
        $response = json_decode($jsonData, true);
        // var_dump($response);
        $statuscode = $response['statuscode'];
        $statusMsg = $response['status'];
        if ($statuscode == '025') {
            $rrr = trim($response['RRR']);

            $new_hash_string = MERCHANTID . $rrr . APIKEY;
            $new_hash = hash('sha512', $new_hash_string);
            $fee_id = $invoice->fee_id;
            // echo $rrr.' , '.$orderID; exit;
            $this->createtransaction($student_id, $fee_id, $rrr, $invoice->amount, $orderID, $new_hash, $invoice->id);
        }
    }

    //get Remita split invoice
    public function getsplitinvoice($invoice_id, $student_id) {
        //  curl --location 'https://remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit' \
//--header 'Content-Type: application/json' \
//--header 'Authorization: remitaConsumerKey=DEMOMDA1234,remitaConsumerToken=' \
        //base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id);
        $name = $student->fname . ' ' . $student->lname;
        // debug(json_encode($student, JSON_PRETTY_PRINT)); exit;
        $totalAmount = $invoice->amount;
        $amount_to_CUN = $totalAmount - 500;
        $timesammp = DATE("dmyHis");
        $orderID = $timesammp;
        $payerName = $name;
        $payerEmail = $student->email;
        $payerPhone = $student->phone;
        $responseurl = $baseUrl . 'transactions/paymentverification/' . $orderID;
        $hash_string = MERCHANTID . SERVICETYPEID . $orderID . $totalAmount . APIKEY;
        $hash = hash('sha512', $hash_string);
        $itemtimestamp = $timesammp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => GATEWAYURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "serviceTypeId": "' . SERVICETYPEID . '",
    "amount": "' . $totalAmount . '",
    "orderId": "' . $orderID . '",
    "payerName": "' . $payerName . '",
    "payerEmail": "' . $payerEmail . '",
    "payerPhone": "' . $payerPhone . '",
    "lineItems":[
       {
          "lineItemsId":"itemid1",
          "beneficiaryName":"CLARETIAN UNIVERSITY OF NIGERIA",
          "beneficiaryAccount":"1219829163",
          "bankCode":"058",
          "beneficiaryAmount":"' . $amount_to_CUN . '",
          "deductFeeFrom":"1"
       },
       {
          "lineItemsId":"itemid2",
          "beneficiaryName":"NETPRO INTL NIG LTD-ACCT 2",
          "beneficiaryAccount":"1019186840",
          "bankCode":"058",
          "beneficiaryAmount":"200",
          "deductFeeFrom":"0"
       }
    ]
}',
            CURLOPT_HTTPHEADER => array(
                "Authorization: remitaConsumerKey=" . MERCHANTID . ",remitaConsumerToken=$hash",
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $json_response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }

        //echo $response;
        // echo $json_response.'<br />';
        // echo GATEWAYRRRPAYMENTURL.'<br />';
        // echo GATEWAYURL.'<br />';

        $jsonData = substr($json_response, 7, -1);
        debug(json_encode($json_response, JSON_PRETTY_PRINT));
        exit;
        $response = json_decode($jsonData, true);
        // var_dump($response);
        $statuscode = $response['statuscode'];
        $statusMsg = $response['status'];
        if ($statuscode == '025') {
            $rrr = trim($response['RRR']);

            $new_hash_string = MERCHANTID . $rrr . APIKEY;
            $new_hash = hash('sha512', $new_hash_string);
            $fee_id = $invoice->fee_id;
            // echo $rrr.' , '.$orderID; exit;
            $this->createtransaction($student_id, $fee_id, $rrr, $invoice->amount, $orderID, $new_hash, $invoice->id);
        }
    }

    //get remita split invoice
    public function remitasplit($invoice_id, $student_id) {
         $baseUrl = Router::url('/', true);
         $netpro_split = 0; $cun_split = 0; $account = ""; $bank_code = "057"; 
          
         $frist_bank = [19,40,62]; $firstbank_code = "011";
         $eco_bank = [29,17,13]; $ecobank_code = "050";
         $fidelity_bank = [3]; $fidelity_code = "070";
         $zenith_bank = [16,8,31,32,30,36,39,70,71,69,38,37,6,1,54,23,55,33,34,35,18,12,7,9,49,52,53,66,67,57,22,2
             ,63,64,65,68]; $zenith_code = "057";
         $uba_code = "033";
        
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
         $fees_Table = TableRegistry::get('Fees');
       
        $invoice = $invoices_Table->get($invoice_id);
        $fee = $fees_Table->get($invoice->fee_id);
        //check fee and select destination bank
        if(in_array($fee->id, $zenith_bank)){  //tuition going to zenit bank
         $bank_code =  $zenith_code;  $netpro_split = 200;  $cun_split = ($fee->amount)-200;
         $account = "1219829163";
        }else if(in_array($fee->id, $frist_bank)){  //fees going to first bank
          $bank_code =   $firstbank_code; $netpro_split = 200; $cun_split = ($fee->amount)-200;
          $account = "2035182780";
        } elseif(in_array($fee->id, $eco_bank)){ //fees going to eco bank
            $bank_code = $ecobank_code; $netpro_split = 200; $cun_split = ($fee->amount)-200;
            $account = "2350078101";
        }elseif(in_array($fee->id, $fidelity_bank)){ // fees goin to fidelity
          $bank_code = $fidelity_code;  $netpro_split = 200; $cun_split = ($fee->amount)-200;
          $account = "2035182780";
        }
         //check for ICT fee
         if($fee->id == 5){  //ICT fee
             return $this->redirect(['action' => 'getictrrr',$invoice_id, $student_id]);
             //$this->getictrrr($invoice_id, $student_id);
          // $bank_code =  $uba_code;  
          //$netpro_split = $invoice->amount-100; $cun_split = 100;    
         }else{
          $netpro_split = 200; $cun_split = ($invoice->amount)-200;   
         }
        $student = $this->Students->get($student_id,['contain'=>['Users']]);
        $name = $student->fname . ' ' . $student->lname;
        // debug(json_encode($student, JSON_PRETTY_PRINT)); exit;
        $totalAmount = $invoice->amount;
        //$amount_to_CUN = $totalAmount - 500;
        $timesammp = DATE("dmyHis");
        $orderID = $timesammp;
        $payerName = $name;
        $payerEmail = $student->user->username;
        $payerPhone = $student->phone;
        $merch_id = MERCHANTID;
        $serviceid = $fee->remitaitemcode;
        $orderid =  uniqid(DATE("dmyHis"));
        $apikey = APIKEY;
        $hash_string = $merch_id .$serviceid. $orderid . $totalAmount . $apikey;
        $hash = hash('sha512', $hash_string);
        $curl = curl_init();
      // echo $bank_code .' - '.$account; exit;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "serviceTypeId": "' . $serviceid . '",
    "amount": "'.$totalAmount.'",
    "orderId": "' . $orderid . '",
    "payerName": "'.$payerName.' ",
    "payerEmail": "'.$payerEmail.'",
    "payerPhone": "'.$payerPhone.'",
    "lineItems":[
       {
          "lineItemsId":"itemid1",
          "beneficiaryName":"Claretian University of Nigeria",
          "beneficiaryAccount":'.$account.',
          "bankCode":'.$bank_code.',
          "beneficiaryAmount":'.$cun_split.',
          "deductFeeFrom":"1"
       },
       {
          "lineItemsId":"itemid2",
          "beneficiaryName":"NETPRO INTL NIG LTD-ACCT 2",
          "beneficiaryAccount":"1019186840",
          "bankCode":"033",
          "beneficiaryAmount": '.$netpro_split.',
          "deductFeeFrom":"0"
       }
    ]
}',
            CURLOPT_HTTPHEADER => array(
                "Authorization: remitaConsumerKey=" . $merch_id . ",remitaConsumerToken=$hash",
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));
 //echo  $netpro_split.'-' .$cun_split.'-'.$totalAmount; exit;
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }
//debug(json_encode($response, JSON_PRETTY_PRINT));
        curl_close($curl);
       $jsonData = substr($response, 7, -1);

        $returned_response = json_decode($jsonData, true);
        // var_dump($response); first UBA account  1019186840 
        $statuscode = $returned_response['statuscode'];
        $statusMsg = $returned_response['status'];
        if ($statuscode == '025') {
            $rrr = trim($returned_response['RRR']);

            $new_hash_string = $merch_id . $rrr . $apikey;
            $new_hash = hash('sha512', $new_hash_string);
            $fee_id = $invoice->fee_id;
            // echo $rrr.' , '.$orderID; exit;
            $this->createtransaction($student_id, $fee_id, $rrr, $invoice->amount, $orderID, $new_hash, $invoice->id);
        }
    }
    
    
    
    //method for generating RRR for ICT Netpro fee without split
    public function getictrrr($invoiceid, $student_id){
        $fees_Table = TableRegistry::get('Fees');
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($invoiceid);
        $fee = $fees_Table->get($invoice->fee_id);
      $student = $this->Students->get($student_id,['contain'=>['Users']]);
        $name = $student->fname . ' ' . $student->lname;
        // debug(json_encode($student, JSON_PRETTY_PRINT)); exit;
        $totalAmount = $invoice->amount;
        //$amount_to_CUN = $totalAmount - 500;
        $timesammp = DATE("dmyHis");
        $orderID = $timesammp;
        $payerName = $name;
        $payerEmail = $student->user->username;
        $payerPhone = $student->phone;
        $merch_id = MERCHANTID;
        $serviceid = $fee->remitaitemcode;
        $orderid =  uniqid(DATE("dmyHis"));
        $apikey = APIKEY;
        $hash_string = $merch_id .$serviceid. $orderid . $totalAmount . $apikey;
        $hash = hash('sha512', $hash_string);
        $curl = curl_init();
      // echo $bank_code .' - '.$account; exit;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "serviceTypeId": "' . $serviceid . '",
    "amount": "'.$totalAmount.'",
    "orderId": "' . $orderid . '",
    "payerName": "'.$payerName.' ",
    "payerEmail": "'.$payerEmail.'",
    "payerPhone": "'.$payerPhone.'"
}',
            CURLOPT_HTTPHEADER => array(
                "Authorization: remitaConsumerKey=" . $merch_id . ",remitaConsumerToken=$hash",
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }
//debug(json_encode($response, JSON_PRETTY_PRINT));
        curl_close($curl);
       $jsonData = substr($response, 7, -1);

        $returned_response = json_decode($jsonData, true);
        // var_dump($response); first UBA account  1019186840 
        $statuscode = $returned_response['statuscode'];
        $statusMsg = $returned_response['status'];
        if ($statuscode == '025') {
            $rrr = trim($returned_response['RRR']);

            $new_hash_string = $merch_id . $rrr . $apikey;
            $new_hash = hash('sha512', $new_hash_string);
            $fee_id = $invoice->fee_id;
            // echo $rrr.' , '.$orderID; exit;
            $this->createtransaction($student_id, $fee_id, $rrr, $invoice->amount, $orderID, $new_hash, $invoice->id);
        }  
    }
    

    //admin method that checks and or generates invoices for a chosen student
    public function getstudentinvoices($id) {
        $fees_sTable = TableRegistry::get('Fees');
        $student = $this->Students->get($id, ['contain' => ['Departments.Fees']]);
        // debug(json_encode($student, JSON_PRETTY_PRINT));exit;
        $this->set('student', $student);
        $counter = 0;
        $imostate_id = 2663;
        $nd1_level_id = 1;
        $hnd1_level_id = 3;
        $regular_id = 1;
        $science_faculties = [1, 2, 4];
        $arts_faculty = [3];
        $fee_ids_year_1_science = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 20, 21];
        $fee_ids_year_1_arts = [3, 4, 5, 1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 20, 21];
        $fee_ids_other_years_science = [5, 6, 7, 11, 15, 16, 17, 18];
        $fee_ids_other_years_arts = [5, 1, 7, 11, 15, 16, 17, 18];

        //check and assign fee to cdl students
        if ($student) {
            //year2 cdl (diploma)
            foreach ($student->department->fees as $fee) {
                //get the fee
                $thisfee = $fees_sTable->get($fee->id);
                //check for any fee assigned to this student and if this fee has been paid
                if ($this->checkpayment($student->id, $thisfee->id) == 0) {
                    //fee has not been paid, check if there is an invoice for it already
                    $is_owing = 'is_owing';
                    $this->request->getSession()->write('is_owing', $is_owing);

                    if ($this->checkinvoice($student->id, $thisfee->id) == 1) {
                        //there is an unpaid invoice, take him to his invoices
                        return $this->redirect(['action' => 'invoices', $student->id, $student->fname]);
                    } else {
                        $counter++;
                        //no invoices, create new one
                        $this->creatnewinvoice($student->id, $thisfee->id, $thisfee->amount);
                    }
                }
            }
        }

        // //verify student is in year 1 and a science student and assign fee
        // elseif (($student->level_id == 1) && ( ($student->faculty_id == 1) || ($student->faculty_id == 2) || ($student->faculty_id == 4))) {
        //     //this is a year 1 science student
        //     foreach ($fee_ids_year_1_science as $fee) {
        //         //get the fee
        //         $thisfee = $fees_sTable->get($fee);
        //         //check for any fee assigned to this student and if this fee has been paid
        //         if ($this->checkpayment($student->id, $thisfee->id) == 0) {
        //             //fee has not been paid, check if there is an invoice for it already
        //             $is_owing = 'is_owing';
        //             $this->request->getSession()->write('is_owing', $is_owing);

        //             if ($this->checkinvoice($student->id, $thisfee->id) == 1) {
        //                 //there is an unpaid invoice, take him to his invoices
        //                 return $this->redirect(['action' => 'studentinvoices', $student->id, $student->fname]);
        //             } else {
        //                 $counter++;
        //                 //no invoices, create new one
        //                 $this->creatnewinvoice($student->id, $thisfee->id, $thisfee->amount);
        //             }
        //         }
        //     }
        // }
        // //verify student is in year 1 and arts student
        // elseif (($student->level_id == 1) && ($student->faculty_id == 3)) {
        //     //this is a year 1 arts student
        //     foreach ($fee_ids_year_1_arts as $fee) {
        //         //get the fee
        //         $thisfee = $fees_sTable->get($fee);
        //         //check for any fee assigned to this student and if this fee has been paid
        //         if ($this->checkpayment($student->id, $thisfee->id) == 0) {
        //             //fee has not been paid, check if there is an invoice for it already
        //             $is_owing = 'is_owing';
        //             $this->request->getSession()->write('is_owing', $is_owing);

        //             if ($this->checkinvoice($student->id, $thisfee->id) == 1) {
        //                 //there is an unpaid invoice, take him to his invoices
        //                 return $this->redirect(['action' => 'studentinvoices', $student->id, $student->fname]);
        //             } else {
        //                 $counter++;
        //                 //no invoices, create new one
        //                 $this->creatnewinvoice($student->id, $thisfee->id, $thisfee->amount);
        //             }
        //         }
        //     }
        // }

        // //verify student is not in year 1 but a science student
        // elseif (($student->level_id != 1) && ( ($student->faculty_id == 1) || ($student->faculty_id == 2) || ($student->faculty_id == 4))) {
        //     //this is other years science student
        //     foreach ($fee_ids_other_years_science as $fee) {
        //         //get the fee
        //         $thisfee = $fees_sTable->get($fee);
        //         //check for any fee assigned to this student and if this fee has been paid
        //         if ($this->checkpayment($student->id, $thisfee->id) == 0) {
        //             //fee has not been paid, check if there is an invoice for it already
        //             $is_owing = 'is_owing';
        //             $this->request->getSession()->write('is_owing', $is_owing);

        //             if ($this->checkinvoice($student->id, $thisfee->id) == 1) {
        //                 //there is an unpaid invoice, take him to his invoices
        //                 return $this->redirect(['action' => 'studentinvoices', $student->id, $student->fname]);
        //             } else {
        //                 $counter++;
        //                 //no invoices, create new one
        //                 $this->creatnewinvoice($student->id, $thisfee->id, $thisfee->amount);
        //             }
        //         }
        //     }
        // }
        // //verify student is not in year 1 and arts student
        // elseif (($student->level_id != 1) && ($student->faculty_id == 3)) {
        //     //this is a year 1 arts student
        //     foreach ($fee_ids_other_years_arts as $fee) {
        //         //get the fee
        //         $thisfee = $fees_sTable->get($fee);
        //         //check for any fee assigned to this student and if this fee has been paid
        //         if ($this->checkpayment($student->id, $thisfee->id) == 0) {
        //             //fee has not been paid, check if there is an invoice for it already
        //             $is_owing = 'is_owing';
        //             $this->request->getSession()->write('is_owing', $is_owing);

        //             if ($this->checkinvoice($student->id, $thisfee->id) == 1) {
        //                 //there is an unpaid invoice, take him to his invoices
        //                 return $this->redirect(['action' => 'studentinvoices', $student->id, $student->fname]);
        //             } else {
        //                 $counter++;
        //                 //no invoices, create new one
        //                 $this->creatnewinvoice($student->id, $thisfee->id, $thisfee->amount);
        //             }
        //         }
        //     }
        // }

        return $this->redirect(['action' => 'studentinvoices', $student->id, $student->fname]);
        // $this->set('student', $student);
        $this->viewBuilder()->setLayout('backend');
    }
//admin method that shows a student's invoices
    public function studentinvoices($student_id) {
        //ensure this admin is loggedin
        $admincontroller = new AdminsController();
        $admin = $admincontroller->isadmin();
        //get the student
        $student = $this->Students->get($student_id);
        //get the invoice table
        $invoices_Table = TableRegistry::get('Invoices');
        $studentinvoices = $invoices_Table->find()
                ->contain(['Fees', 'Sessions', 'Students'])
                ->where(['student_id' => $student_id,
                //'paystatus'=>'Unpaid'
        ]);
        //debug(json_encode( $myinvoices, JSON_PRETTY_PRINT));exit;
        $this->set('invoices', $studentinvoices);
        $this->set('student', $student);
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for selecteing a stydent for fee assignments
    public function assignfee() {
        $departments_Table = TableRegistry::get('Departments');
        $fees_Table = TableRegistry::get('Fees');
        $sessions_Table = TableRegistry::get('Sessions');
        if ($this->request->is('post')) {
            $dept_id = $this->request->getData('department_id');
            $students = $this->Students->find()->contain(['States', 'Departments'])
                    ->where(['Students.department_id' => $dept_id, 'Students.status' => 'Admitted'])
                    ->order(['fname' => 'DESC']);
        } else {
            $students = $this->Students->find()->contain(['States', 'Departments'])
                            ->where(['Students.status' => 'Admitted'])
                            ->order(['fname' => 'DESC'])->limit(200);
        }
        $departments = $departments_Table->find('list')->order(['name' => 'DESC']);
        //   $students = $this->Students->find('list')->order(['fname'=>'DESC'])->limit(200);
        $fees = $fees_Table->find('list')->order(['name' => 'DESC']);
        $sessions = $sessions_Table->find('list')->order(['name' => 'DESC']);
        $this->set(compact('students', 'fees', 'sessions', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method that actually doe s the fee assignment
    public function feeassignment($student_id) {
        $student = $this->Students->get($student_id);
        $fees_Table = TableRegistry::get('Fees');
        $sessions_Table = TableRegistry::get('Sessions');
        if ($this->request->is('post')) {
            $fee_id = $this->request->getData('fee_id');
            $session_id = $this->request->getData('session_id');
            $fee = $fees_Table->get($fee_id);
            $invoice_id = $this->createinvoiceforstudent($student_id, $fee_id, $fee->amount, $session_id);
            if (is_numeric($invoice_id)) {
                $this->Flash->success(__('The fee ' . $fee->name . ' has been assigned to the student'));
                return $this->redirect(['action' => 'studentinvoices', $student_id, '$student->fname']);
            } else {
                $this->Flash->success(__('Sorry, unable to assign fee'));
            }
        }
        $this->set('student', $student);
        $fees = $fees_Table->find('list')->order(['name' => 'DESC']);
        $sessions = $sessions_Table->find('list')->order(['name' => 'DESC']);
        $this->set(compact('fees', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
    }

//method that creates an invoice and assigns a fee to a student manually
    public function createinvoiceforstudent($student_id, $fee_id, $amount, $session_id) {

        //  echo 'yest i got here'; exit;
        $settings = $this->request->getSession()->read('settings');
        //get the invoice table
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->newEmptyEntity();
        $invoice->student_id = $student_id;
        $invoice->fee_id = $fee_id;
        $invoice->amount = $amount;
        $invoice->session_id = $session_id;
        $invoice->invoiceid = TRANS_REF . $fee_id . 'INV' . $student_id;
        $invoices_Table->save($invoice);
        return $invoice->id;
    }

    //sends a welcome mail to the newly added student
    public function sendwelcomemail($username, $key, $fname, $lname) {
        //base url
        $baseUrl = Router::url('/', true);
        $message = "Hello, " . $fname . ' ' . $lname . " <br /> a student account has been created for you in "
                . "the University of Agriculture and Environmental Sciences Student Information System"
                . ". please click on the "
                . "below link to choose a password<br /><br />.";

        $message .= "  <a href='" . $baseUrl . "users/changepassword/" . $key . "'>Change Password </a> <br />or <br />copy the link below and paste on your browser,then click go : ";

        $message .= $baseUrl . "users/changepassword/" . $key;

        $message .= '<br /><br />'
                . 'Kind Regards,<br />'
                . SCHOOL . ' <br />';

        // $statusmsg = "";
        $email = new Mailer('default');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($username);
        $email->setBcc(['admissions@claretianuniversity.edu.ng']);
        $email->setEmailFormat('html');
        $email->setSubject('Welcome To CUN');
        if ($email->deliver($message)) {
            $this->Flash->success(__('A verification mail has been sent to ' . $username . ' They should check their inbox/spam folder and click on the link to change their password.'));
        } else {
            $this->Flash->error(__('Sorry, unable to send mail. Please try again.'));
        }
        return;
    }

//get the list of departments in a chosen faculty
    public function getdapts($id) {
        $departments_Table = TableRegistry::get('Departments');
        $departments = $departments_Table->find('list')
                        ->where(['faculty_id' => $id, 'iscdl' => 'No'])->order(['name' => 'ASC']);
        $this->set('departments', $departments);
    }

    //get the list of departments in a chosen faculty
    public function getTneDapts($id) {
        $departments_Table = TableRegistry::get('Departments');
        $departments = $departments_Table->find('list')
                        ->where(['faculty_id' => $id, 'iscdl' => 'TNE Brazil'])->order(['name' => 'ASC']);
        $this->set('departments', $departments);
    }

    //method that gets all departments available for CDLP
    public function getdaptsforcdl($id) {
        $departments_Table = TableRegistry::get('Departments');
        $agric = [16, 19];
        $departments = $departments_Table->find('list')
                        ->where(['id NOT IN' => $agric, 'faculty_id' => $id, 'iscdl' => 'Yes'])->order(['name' => 'ASC']);
        $this->set('departments', $departments);
    }

//returns lga in a chosen state
    public function getlgas($state_id) {
        $lgas_Table = TableRegistry::get('Lgas');
        $lgas = $lgas_Table->find('list')->where(['state_id' => $state_id])->order(['name' => 'ASC']);
        $this->set('lgas', $lgas);
    }

    //get programes during application
    public function getprogrames($dept_id) {
        $departments_programes_Table = TableRegistry::get('DepartmentsProgrammes');
        $sprogrames = $departments_programes_Table->find()->where(['department_id' => $dept_id]);
        $progids = [];
        foreach ($sprogrames as $programe) {
            array_push($progids, $programe->programme_id);
        }
        $programes_Table = TableRegistry::get('Programmes');
        $programmes = $programes_Table->find('list')->where(['id IN' => $progids]);
        $this->set('programmes', $programmes);
    }

    //method for TNE Brazile application
    public function newtnebapplication() {

        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        $student = $this->Students->newEmptyEntity();
        if ($this->request->is('post')) {
            //   debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $admin_letter = $this->request->getData('jamb_admin_letters');
            $jamb_notice_letter = $this->request->getData('jamb_notifications');
            $userscontroller = new UsersController();
            //create login data
            $email = $this->request->getData('email');
            $fname = $this->request->getData('fname');
            $lname = $this->request->getData('lname');
            $mname = $this->request->getData('mname');
            $userid = $this->getlogindetails($email, $fname, $lname, $mname);
            if (is_numeric($userid)) {
                //upload files
                //upload o level
                $olevel = $this->request->getData('olevelresulturls');
                $olevel_filename = $olevel->getClientFilename();
                if (!empty($olevel_filename)) {

                    $waec_cert = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
                } else {
                    $waec_cert = "";
                }

                //handle adminssion letter upload
                $letter = $admin_letter->getClientFilename();
                if (!empty($letter)) {
                    $jamb_admin_letter = $this->handlefileupload($this->request->getData('jamb_admin_letters'), 'student_files/');
                } else {
                    $jamb_admin_letter = "";
                }
                //handle jamb notification file upload
                $jamb_notification = $jamb_notice_letter->getClientFilename();
                if (!empty($jamb_notification)) {
                    $jamb_notice = $this->handlefileupload($this->request->getData('jamb_notifications'), 'student_files/');
                } else {
                    $jamb_notice = "";
                }
                //upload birth cert
                $birthcert = $this->request->getData('birthcerturls');
                $birthcert_filename = $birthcert->getClientFilename();
                if (!empty($birthcert_filename)) {

                    $birth_cert = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
                } else {
                    $birth_cert = "";
                }
                //upload other file
                $other_cert = $this->request->getData('othercertss');
                $othercert_filename = $other_cert->getClientFilename();
                if (!empty($othercert_filename)) {

                    $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
                } else {
                    $other_cert = "";
                }

                //upload passport
                $passportfile = $this->request->getData('passporturls');
                $passport_name = $passportfile->getClientFilename();
                if (!empty($passport_name)) {
                    //call the funtion to resize and upload
                    $passport = $this->doresizepassport($this->request->getData('passporturls'));
                    // $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
                } else {
                    $passport = "";
                }
                //upload jamb result
                $jambresultfile = $this->request->getUploadedFile('jambresults');
                $jambresult_name = $jambresultfile->getClientFilename();
                if (!empty($jambresult_name)) {
                    $jambresult = $this->handlefileupload($this->request->getData('jambresults'), 'student_files/');
                } else {
                    $jambresult = "";
                }
                $student = $this->Students->patchEntity($student, $this->request->getData());
                $student->user_id = $userid;
                $student->category_id = 1;
                $student->mode_id = 4;
                $student->previousschool = $this->request->getData('nin');
                $student->othercerts = $other_cert;
                $student->passporturl = $passport;
                $student->birthcerturl = $birth_cert;
                $student->olevelresulturl = $waec_cert;
                $student->jamb_notification = $jamb_notice;
                $student->jamb_admin_letter = $jamb_admin_letter;
                $student->jambresult = $jambresult;
                //  debug(json_encode( $student, JSON_PRETTY_PRINT)); exit;
                if ($this->Students->save($student)) {
                    //assign application number
                    //  $this->getapplicationno($student->id);
                    //send application summary to candidate via email
                    //    $this->sendapplicationsummary($student->id);

                    $fees_Table = TableRegistry::get('Fees');
                    $fee = $fees_Table->get(28);

                    //create invoice for this payment
                    $invoice_id = $this->creatnewinvoice($student->id, $fee->id, $fee->amount);
                    //generate payee id
                    // $this->generateapplicantpayeeid($invoice_id, $student->id);
                    //$url = $transactionController->gotopaystack($email, $student->phone, $name, $fee->amount, $student->id, $fee->id);
                    $this->Flash->success(__('Your application has been saved, please click on the green button below '
                                    . ' to pay the application fee(Note that applications without application fee payment will not be processed).'
                                    . ' You can always check your application status by clicking on (Check Application Status)'
                                    . '  on the application page and entering the application number which we just sent to your email address'));

                    return $this->redirect(['action' => 'generateapplicantpayeeid', $invoice_id, $student->id]);
                }
                $this->Flash->error(__('Sorry, we could not submit your application. Please, try again.'));
            } else {//if unable to create user login data
                $this->Flash->error(__('Sorry, the email address you provided is already in use. Please, try again.'));
            }
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->where(['iscdl' => 'TNE Brazil'])->order(['name' => 'ASC']);
        $states = $this->Students->States->find('list', ['limit' => 200])->where(['country_id' => 160]);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000])->where(['state_id' => 2647])->order(['name' => 'ASC']);
        $fees = $this->Students->Fees->find('list', ['limit' => 200]);
        $level_ids = [1];
        $programeids = [61, 62, 63, 64, 65];
        // $modes = $this->Students->Modes->find('list')->where(['id IN' => [1, 2]]);
        // $programetypes = $this->Students->Programetypes->find('list')->where(['id IN' => [1, 2]]);

        $levels = $this->Students->Levels->find('list')->where(['id IN' => $level_ids])->order(['name' => 'ASC']);
        // $faculties = $this->Students->Faculties->find('list')->where(['id' => 7]);
        $programes = $this->Students->Programmes->find('list')->where(['id IN' => $programeids])->order(['name' => 'ASC']);
        $this->set(compact('student', 'levels', 'programetypes', 'departments', 'states', 'countries', 'lgas', 'fees', 'faculties', 'programes', 'modes'));
        $this->set('title', 'New Application');
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //the news page
    public function news() {

        $this->viewBuilder()->setLayout('webland');
    }

//the alumnai page
    public function alumai() {
        $this->viewBuilder()->setLayout('webland');
    }

    //the academics page
    public function academics() {
        $this->viewBuilder()->setLayout('webland');
    }

    //the library page 
    public function library() {
        $this->viewBuilder()->setLayout('webland');
    }

    //the about us page 
    public function aboutus() {
        $this->viewBuilder()->setLayout('webland');
    }

    //the admission page
    public function admission() {
        $this->viewBuilder()->setLayout('webland');
    }

    //testing method for new capture from camera
    public function newcapture() {


        $this->viewBuilder()->setLayout('cam');
    }

    //method that uploads image directly from the webcam
    public function getimagefromcam($image_array, $folder) {
        // Check if image_array is null or empty
        if (empty($image_array) || is_null($image_array)) {
            return ""; // Return empty string if no image provided
        }
        
        $img = $image_array;
        $folderPath = $folder;

        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . time() . '.png';

        $file = $folderPath . $fileName;
        file_put_contents($file, $image_base64);
        return $fileName;
    }

    //method that allows that student to print their acceptance letter
    public function getacceptanceletter() {
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $conditions = $admisionconditions_Table->get(1);
        $student = $this->Students->find()->contain(['Departments',  'Programmes'])
                        ->where(['status' => 'Admitted', 'user_id' => $this->Auth->user('id')])->first();
        //check for ict and acceptance fee payment
        $this->checkictandacceptancefee($student->id);
        if ($student) {
            $this->set('student', $student);
            $this->set('conditions', $conditions);
        } else {
            $this->Flash->error(__(' Sorry, unknown student data'));
        }


        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //students method for printing admission letter
    public function getadmissionletter() {
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $conditions = $admisionconditions_Table->get(3);
        $student = $this->Students->find()->contain(['Departments',  'Programmes'])
                        ->where(['status' => 'Admitted', 'user_id' => $this->Auth->user('id')])->first();
        //check for ict and acceptance fee payment
        // $this->checkictandacceptancefee($student->id);
        if ($student) {
            $this->set('student', $student);
            $this->set('conditions', $conditions);
        } else {
            $this->Flash->error(__(' Sorry, unknown student data'));
        }


        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method that allows that student to print their acceptance letter
    public function printacceptanceletter($id) {
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $conditions = $admisionconditions_Table->get(1);
        $student = $this->Students->find()->contain(['Departments',  'Programmes'])
                        ->where(['status' => 'Admitted', 'Students.id' => $id])->first();
        $this->set('student', $student);
        $this->set('conditions', $conditions);
        $this->viewBuilder()->setLayout('backend');
    }

    //method that sends provissional admission letter to the new student
    public function sendadmissionletter_old($student) {
        $settings = $this->request->getSession()->read('settings');
        $url = 'https://portal.claretianuniversity.edu.ng/students/getacceptanceletter/' . $student->id;

        $message = "Provissional Letter<br /><br />";
        $message .= "The Office Of The Registrar<br />";
        $message .= "The Vice Chancelor: " . $settings->rector . '<br />';
        $message .= "The Registrar: " . $settings->registrar . '<br />';
        $message .= "Our Ref: " . $student->regno . '<br />';
        $message .= "Date: " . date('D d M, Y') . '<br />';
        $message .= "<center>Offer Of Provisional  Admission</center><br />";
        $message .= "Name Of Candidate: " . $student->fname . ' ' . $student->lname . ' ' . $student->mname . '<br />';
        $message .= "Programme: " . $student->programe->name . '<br />';
        $message .= "Course Of Study: " . $student->department->name . '<br />';
        $message .= "Department: " . $student->department->name . '<br />';
        $message .= "Session: " . $settings->session->name . '<br /><br />';
        $message .= "1. I am pleased to inform you that you have been offered provisional admission into " . SCHOOL . "<br />";
        $message .= "2. This offer is conditional upon the confirmation of your qualifications as listed by you in the application form of which you possess as at when the offer of admission was made<br />";
        $message .= "3. At the registration, you will be required to produce the original copies of your certificates or any other acceptable evidence of qualification"
                . ",on which this offer of admission has been based<br />";
        $message .= "4. If at any time after admission, it is obsrved that you do not have any of the qualifications which you have claimed"
                . " to have obtianed,you will be required to withdraw from the university<br />";
        $message .= "5. You should complete the attached acceptance form in five copies indicating whether you accept or reject"
                . " the offer, four copies should be forwarded to the registrar(Admissions) while the other should be retained by you"
                . " and should be produced on demand<br />";
        $message .= "6. In the absence of any response from you at the commencement of lectures, the intitution would assume"
                . " the you are not interested in the offer and may proceed to fill your place<br />";
        $message .= "7. Before the commencement of registration, you will be required to undergo a medical examination, which "
                . " should be in the school medical centre<br />";
        $message .= "8. You are required to pay the Tuition Fee and other fees/charges within two weeks from the beginning of the "
                . "academic session<br />";
        $message .= "9. We hope that you will be able to take advantage of this offer of provisional admission<br />";
        $message .= "10. Accept our hearty CONGRATULATIONS<br /><br />";

        $message .= "Please login to the student portal using your username(email/new university email address: " . $student->universitymail . ") and a default password of student123 "
                . " to generate TRANSACTION REFs/invoices for all the required fees and proceed to the bank to make payment(or pay online using your ATM card if you wish) after which you will print your Acceptance Letter directly from the system.</a>";
        $message .= "<br />Yours Sincerly<br /><br />";

        $message .= " <br />";
        $message .= "Registrar";
        $email = new Mailer('default');
        //$email->setTemplate('letterlayout', 'letterview');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->email);
        $email->setBcc(['admission@claretianuniversity.edu.ng', 'chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Provisional Admission');
        $email->deliver($message);
        return;
    }

    //method that sends provissional admission letter to the new student
    public function sendadmissionletter($student) {
        //  $settings = $this->request->getSession()->read('settings');
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $provisional_admision_letter = $admisionconditions_Table->get(3);

        $message = "Dear " . $student->fname . ' ' . $student->lname . ' ' . $student->mname;
        $message .= $provisional_admision_letter->conditiond;
        
         //call the mailling api
        $userscontroller = new UsersController();
         $userscontroller->sendmailout($student->email, "Admission Letter", $message, "BUSCED");
        return;

        $email = new Mailer('default');
        //$email->setTemplate('letterlayout', 'letterview');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->email);
        $email->setBcc(['admission@busced.online', MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('Provisional Admission');
        $email->deliver($message);
        return;
    }

    //admin method for re-sending admission letters
    public function sendadminletter($student_id) {
        // $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $students_Table = TableRegistry::get('Students');
        // $provisional_admision_letter = $admisionconditions_Table->get(3);
        $student = $students_Table->get($student_id);

        $message = "Dear " . $student->fname . ' ' . $student->lname . ' ' . $student->mname;
        // $message .= $provisional_admision_letter->conditiond; chineduokoye785@gmail.com

        $admission_letter = "<br /><br />With the completion of the first phase of the screening exercise,
            I am pleased to offer you provisional admission into the Claretian University of Nigeria.
            <br /><br />The University will resume on Wednesday, the 4th of November 2022.
            Please find your Registration number, and Login details below: 
           <br /> Please guard these details well, and do not share it with anyone else. 
           <br /> You can now proceed with making payment of fees.
           <br />Registration Number : " . $student->regno . "<br />
           <br />username/school email address :" . $student->universitymail . "<br />
           <br />default password : student123
           <br /> Use the link below:
           <br /> Do remember to change your password to something secrete and personal to you
           <br /><br /> www.portal.claretianuniversity.edu.ng (log in with your username and password)
           <br /><br /> The second phase of the screening exercise will commence on the 15th of November. You will be required to present the original copies of the credentials you sent online, as well as this letter to the registrar's office on this day.
           <br /> <br />Congratulations and good luck<br /><br />
________________________________
    
            <br />Rev Fr Dr John Ezenwankwo, CMF
            <br />Registrar<br />";
        $message .= $admission_letter;
        $email = new Mailer('default');
        //$email->setTemplate('letterlayout', 'letterview');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->email);
        $email->setBcc(['admission@claretianuniversity.edu.ng', MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('Provisional Admission');
        if ($email->deliver($message)) {
            $this->Flash->success(__(' Admission letter sent'));
            return $this->redirect(['action' => 'managestudents']);
        }
        return $this->redirect(['action' => 'managestudents']);
    }

    //students method for printing their admission letter
    public function printadmissionletter() {
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $admissionletter = $admisionconditions_Table->get(3);
        $student = $this->Students->find()->contain(['Departments',  'Programmes'])
                        ->where(['status' => 'Admitted', 'user_id' => $this->Auth->user('id')])->first();
        //check for ict and acceptance fee payment
        $this->checkictandacceptancefee($student->id);
        $this->set('student', $student);
        $this->set('letter', $admissionletter);
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for printing admin letter for students
    public function printadminletter($studentid) {
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $conditions = $admisionconditions_Table->get(3);
        $student = $this->Students->find()->contain(['Departments',  'Programmes'])
                        ->where(['status' => 'Admitted', 'Students.id' => $studentid])->first();
        $this->set('student', $student);
        $this->set('conditions', $conditions);
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method that sends admission letters during admission
    public function adminlettersender($student_id) {
        // $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $students_Table = TableRegistry::get('Students');
        // $provisional_admision_letter = $admisionconditions_Table->get(3);
        $student = $students_Table->get($student_id);

        $message = "Dear " . $student->fname . ' ' . $student->lname . ' ' . $student->mname;
        // $message .= $provisional_admision_letter->conditiond; chineduokoye785@gmail.com

        $admission_letter = "<br /><br />With the completion of the first phase of the screening exercise,
            I am pleased to offer you provisional admission into the Claretian University of Nigeria.
            <br /><br />The University will resume on Wednesday, the 4th of November 2022.
            Please find your Registration number, and Login details below: 
           <br /> Please guard these details well, and do not share it with anyone else. 
           <br /> You can now proceed with making payment of fees.
           <br />Registration Number : " . $student->regno . "<br />
           <br />username/school email address :" . $student->universitymail . "<br />
           <br />default password : student123
           <br /> Use the link below:
           <br /> Do remember to change your password to something for secrete and personal to you
           <br /><br /> www.portal.claretianuniversity.edu.ng (log in with your username and password)
           <br /><br /> The second phase of the screening exercise will commence on the 15th of November. You will be required to present the original copies of the credentials you sent online, as well as this letter to the registrar's office on this day.
           <br /> <br />Congratulations and good luck<br /><br />.
________________________________
    
            <br />Rev Fr Dr John Ezenwankwo, CMF
            <br />Registrar<br />";
        $message .= $admission_letter;
        $email = new Mailer('default');
        //$email->setTemplate('letterlayout', 'letterview');
        $email->setFrom([SENDMAIL => SCHOOL]);
        $email->setTo($student->email);
        $email->setBcc(['admission@claretianuniversity.edu.ng', MCC]);
        $email->setEmailFormat('html');
        $email->setSubject('Provisional Admission');
        if ($email->deliver($message)) {
            $this->Flash->success(__(' Admission letter sent'));
            return; // $this->redirect(['action' => 'managestudents']);
        }
        $this->Flash->error(__(' Sorry, unable to send Admission letter'));
        return; // $this->redirect(['action' => 'managestudents']);
    }

    //method that generates the transaction id(Payee ID) for the applicants 
    public function generateapplicantpayeeid($invoice_id, $student_id) {
        //set the base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id, ['contain' => ['Departments', 'Users', 'States', 'Lgas', 'Countries']]);
        $fee = $fees_Table->get($invoice->fee_id);

        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
        if ($transaction == "none") {
            $transaction = $transactions_Table->newEmptyEntity();
            $transaction->student_id = $student_id;
            $transaction->fee_id = $invoice->fee_id;
            $transaction->session_id = $settings->session_id;
            $transaction->gresponse = 'initialized';
            $transaction->invoice_id = $invoice->id;
            $transaction->amount = $invoice->amount;
            $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
            $transaction->paystatus = 'initialized';
            //debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $transactions_Table->save($transaction);
        }
        //debug(json_encode(  $transaction, JSON_PRETTY_PRINT)); exit;    
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('baseUrl', $baseUrl);
        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method for generating transcript payment invoice when loggedin
    public function generatetranscriptpayeeid($invoice_id, $student_id) {
        //set the base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',                  'Users', 'States', 'Lgas', 'Countries', 'Faculties']]);
        $fee = $fees_Table->get($invoice->fee_id);

        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
        if ($transaction == "none") {
            $transaction = $transactions_Table->newEmptyEntity();
            $transaction->student_id = $student_id;
            $transaction->fee_id = $invoice->fee_id;
            $transaction->session_id = $settings->session_id;
            $transaction->gresponse = 'initialized';
            $transaction->invoice_id = $invoice->id;
            $transaction->amount = $invoice->amount;
            $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
            $transaction->paystatus = 'initialized';
            //debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $transactions_Table->save($transaction);
        }
        //debug(json_encode(  $transaction, JSON_PRETTY_PRINT)); exit;    
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('baseUrl', $baseUrl);
        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //method to generate transcript fee payment invoice when not loggedin
    public function gettranscriptinvoice($invoice_id, $student_id) {
        //set the base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',                  'Users', 'States', 'Lgas', 'Countries', 'Faculties']]);
        $fee = $fees_Table->get($invoice->fee_id);

        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
        if ($transaction == "none") {
            $transaction = $transactions_Table->newEmptyEntity();
            $transaction->student_id = $student_id;
            $transaction->fee_id = $invoice->fee_id;
            $transaction->session_id = $settings->session_id;
            $transaction->gresponse = 'initialized';
            $transaction->invoice_id = $invoice->id;
            $transaction->amount = $invoice->amount;
            $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
            $transaction->paystatus = 'initialized';
            //debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $transactions_Table->save($transaction);
        }
        //debug(json_encode(  $transaction, JSON_PRETTY_PRINT)); exit;    
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('baseUrl', $baseUrl);
        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method for requesting transcript without login
    public function transcript() {

        if ($this->request->is('post')) {

            $regno = $this->request->getData('regno');
            $student = $this->Students->find()->where(['regno' => $regno])
                            ->contain([  'Departments', 'States', 'Lgas', 'Countries'])->first();
            if (!empty($student->id)) {
                return $this->redirect(['action' => 'requesttranscript', $student->id, $student->fname]);
            } else {
                $this->Flash->error('Unknown student data. Record not found');
            }
        }


        $this->viewBuilder()->setLayout('loginlayout');
    }

    //student method for actually requestion transcript without login
    public function requesttranscript($student_id, $fname) {
        $trequest_Table = TableRegistry::get('Trequests');
        $continents_Table = TableRegistry::get('Continents');
        $trequest = $trequest_Table->newEmptyEntity();
        $continent_costs = $continents_Table->find();
        if ($this->request->is('post')) {
            // debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $continentid = $this->request->getData('continent_id');
            $continent = $continents_Table->get($continentid);
            $trequest = $trequest_Table->patchEntity($trequest, $this->request->getData());
            $trequest->amount = $continent->cost;
            $trequest->student_id = $student_id;
            $trequest->fee_id = 23;
            //  debug(json_encode( $trequest, JSON_PRETTY_PRINT)); exit;
            if ($trequest_Table->save($trequest)) {
                //created invoice
                $invoice_id = $this->creatnewinvoice($student_id, 23, $continent->cost);
                //proceed to payment gateway for payment
                // $transactionController = new TransactionsController();
                $this->Flash->success(__('Success, your transcript request has been submitted and would be processed within the next ten days as soon as we confirm your payment'));
                return $this->redirect(['action' => 'gettranscriptinvoice', $invoice_id, $student_id]);

                // $url = $this->gotopaystack($incoice_id, $student->id);
                // $this->Flash->success(__('Success, your transcript order has been submitted and would be processed within the next ten days'));
                // return $this->redirect($url);
            } else {
                $this->Flash->error(__('Sorry, unable to submit order. Please try again'));
                // return $this->redirect(['action' => 'myinvoices']);    
            }
        }

        $continents = $trequest_Table->Continents->find('list', ['limit' => 200]);
        $countries = $trequest_Table->Countries->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $states = $trequest_Table->States->find('list', ['limit' => 5000])->order(['name' => 'ASC']);
        $couriers = $trequest_Table->Couriers->find('list', ['limit' => 200]);
        $this->set(compact('trequest', 'students', 'continents', 'countries', 'states', 'couriers', 'continent_costs '));
        $this->set('continent_costs', $continent_costs);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //admin method for getting an applicant's payeeid
    public function getapplicantpayeeid($studentid) {
        //set the base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->find()->contain(['Students.Departments', 'Fees',
                           'Students.States', 'Students.Lgas', 'Students.Countries', 'Students.Users'])
                        ->where(['student_id' => $studentid, 'fee_id' => 2])->first();

        if (!empty($invoice)) {

            if ($invoice->paystatus == "success") {
                $this->Flash->success('Application Fee payment has already been completed');
                return $this->redirect(['action' => 'manageapplicants']);
            } else { //call a function that generates the payee id
                //initialize the transaction before going to interswitch
                $settings = $this->request->getSession()->read('settings');
                //check for unpaid transaction id
                $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $studentid, $settings->session_id);
                if ($transaction == "none") {
                    $transaction = $transactions_Table->newEmptyEntity();
                    $transaction->student_id = $studentid;
                    $transaction->fee_id = $invoice->fee_id;
                    $transaction->session_id = $settings->session_id;
                    $transaction->gresponse = 'initialized';
                    $transaction->invoice_id = $invoice->id;
                    $transaction->amount = $invoice->amount;
                    $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
                    $transaction->paystatus = 'initialized';
                    //  debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
                    $transactions_Table->save($transaction);
                }
                $this->set('transaction', $transaction);
                $this->set('student', $invoice->student);
                $this->set('fee', $invoice->fee);
            }
            // return $this->redirect(['action' => 'manageapplicants']); 
        } else {
            $this->Flash->error('Application Fee invoice not found');
            return $this->redirect(['action' => 'manageapplicants']);
        }
        $this->set('baseUrl', $baseUrl);
        $this->viewBuilder()->setLayout('backend');
    }

    //method for managing alumni
    public function getalumnai() {
        if ($this->request->is('post')) {
            $department_id = $this->request->getData('department_id');
            $condition = [];
            if (!empty($department_id)) {
                $condition['Students.department_id'] = $department_id;
            }
            $alumni = $this->Students->find()
                    ->contain(['Departments', 'States', 'Countries', 'Users',   'Lgas'])
                    ->where(['status' => 'Admitted', 'Students.level_id' => 5])
                    ->andWhere($condition)
                    ->limit(2000)
                    ->order(['joindate' => 'DESC']);
        } else {
            $alumni = $this->Students->find()
                    ->contain(['Departments', 'States', 'Countries', 'Users',   'Lgas'])
                    ->where(['status' => 'Admitted', 'Students.level_id' => 5])
                    ->limit(2000)
                    ->order(['joindate' => 'DESC']);
        }
        $this->paginate($alumni);

        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        $this->set(compact('alumni', 'departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //page that explains to the student how to pay fees
    public function howtopayfees() {
        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        $admisionconditions_Table = TableRegistry::get('Admisionconditions');
        $howtopayfee = $admisionconditions_Table->get(2);
        $this->set('howtopayfee', $howtopayfee);

        $this->viewBuilder()->setLayout('loginlayout');
    }

    //admin method for updating applicant's details when there is a mistake
    public function updateapplicantdata($id) {

        $student = $this->Students->get($id, [
            'contain' => ['Departments', 'States', 'Lgas', 'Countries']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            //upload passport
            $pix = $this->request->getData('passporturls');
            $student_pix = $pix->getClientFilename();
            if (!empty($student_pix)) {

                $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $passport = $student->passporturl;
            }
            //update username
            $username = $this->request->getData('email');
            $user_id = $this->request->getData('user_id');
            $this->resetapplicantemail($student->user_id, $username);
            $student = $this->Students->patchEntity($student, $this->request->getData());
            $student->passporturl = $passport;
            if ($this->Students->save($student)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Applicant data Update " . $student->fname . ' ' . $student->lname;
                $user_id = $this->Auth->user('id');
                $description = "Updated an Applicant " . $student->application_no;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The candidate data has been updated successfully.'));
                return $this->redirect(['action' => 'manageapplicants']);
            }
            $this->Flash->error(__('The candidate data could not be updated. Please, try again.'));
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 2000]);
        $states = $this->Students->States->find('list', ['limit' => 2000])->where(['country_id' => 160])->order(['name' => 'ASC']);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000]);
        $this->set(compact('student', 'departments', 'states', 'countries', 'lgas'));
        $this->viewBuilder()->setLayout('backend');
    }

//method that updates email address for an applicant
    private function resetapplicantemail($user_id, $username) {
        $users_Table = TableRegistry::get('Users');
        //search and ensure new email is not in use by another student
        $useraccount = $users_Table->find()->where(['username' => $username])->first();
        if (empty($useraccount->id)) {
            //update student with new email
            $account_to_update = $users_Table->get($user_id);
            $account_to_update->username = $username;
            $users_Table->save($account_to_update);
            $this->Flash->success(__('The candidate email address has been updated to :' . $username));
            return;
        } elseif ($useraccount->id == $user_id) {
            //same owner so do nothing
            return;
        } else {
            $this->Flash->error(__('The chosen email address ' . $username . ' is already in use by another candidate'));
            return;
        }
    }

    //method that validates a student using his reg number called by js
    public function validatestudent($regno) {
        echo $regno;
        exit;
        $student = $this->Students->find()->contain(['Departments'])->where(['regno' => $dregno])->first();

        $this->set(compact('student'));

        //    $this->viewBuilder()->setLayout('login');
    }

    //method that gets a photo card for the student
    public function summarypage($student_id) {
        $student = $this->Students->get($student_id, ['contain' => ['Departments',
                'States', 'Lgas', 'Users', 'Sparents', 'ClassArms']]);
        //check if student is in year one and check if ict and other fees have been paid
        // if (($student->level->id == 1) || ($student->level->id == 3)) {
        //     $status = $this->ictandacceptancefeestatus($student_id);
        //     $this->set('status', $status);
        // }
        $this->set('student', $student);
        $this->viewBuilder()->setLayout('backend');
    }

    //check if student has paid ict fee and acceptance fee
    public function checkictandacceptancefee($studentid) {
        $feeids = [5];
        $settings = $this->request->getSession()->read('settings');
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()->where(['fee_id IN' => $feeids,
                    'student_id' => $studentid, 'session_id' => $settings->session_id, 'paystatus' => 'completed'])->count();

        if ($transaction < 1) {
            $this->Flash->error('Sorry, you have to complete ICT fee payment before you can print your '
                    . 'Admission and acceptance letters.');
            return $this->redirect(['action' => 'dashboard']);
        } else {
            return true;
        }
        return;
    }

    //method that hides a students reg no till ict and other fess are paid
    public function ictandacceptancefeestatus($student_id) {
        $feeids = [3, 5];
        $settings = $this->request->getSession()->read('settings');
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()->where(['fee_id IN' => $feeids,
                    'student_id' => $student_id, 'session_id' => $settings->session_id, 'paystatus' => 'completed'])->count();

        if ($transaction < 2) {
            //unpaid
            return 0;
        } else {
            //paid
            return 1;
        }
        return;
    }

    //method that validates the student with their regno, the form
    public function verifystudent() {
        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        if ($this->request->is('post')) {
            $regno = $this->request->getData('regno');
            $student = $this->Students->find()->contain(['Departments', 'Faculties'])
                            ->where(['regno' => $regno, 'status' => 'Admitted'])->first();

            $this->set(compact('student'));
        }
        $this->set('title', 'Student Verification');
        $this->viewBuilder()->setLayout('login');
    }

    //admin method for promoting students
    public function getstudentsforpromotion() {


        if ($this->request->is('post')) {
            // $level_id = $this->request->getData('slevel_id');
            $deptid = $this->request->getData('department_id');
            $classArmId = $this->request->getData('class_arm_id');
            $conditions = [];
            if (!empty($deptid)) {
                $conditions['Students.department_id'] = $deptid;
            }
            if (!empty($classArmId)) {
                $conditions['Students.class_arm_id'] = $classArmId;
            }
            // if (!empty($level_id)) {
            //     $conditions['Students.level_id'] = $level_id;
            // }

            //search for students
            $students = $this->Students->find()
                    ->contain(['Departments', 'ClassArms'])
                    ->where(['Students.status' => 'Admitted'])
                    ->where($conditions)
                    ->order(['joindate' => 'DESC']);
            
            
            $this->set(compact('students'));
        }


        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        $this->set(compact('departments'));
        $this->viewBuilder()->setLayout('backend');
    }

    //applicants method for printing their application summary
    public function applicationsummary($student_id) {

        $student = $this->Students->get($student_id, ['contain' => ['Departments', 'States', 'Lgas', 'Users']]);
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->find()->where(['student_id' => $student_id, 'fee_id' => 2])->first();
        $this->set('invoice', $invoice);
        $this->set('student', $student);
        $this->set('title', 'Application Summary');
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //page that shows the applicant the requirements and guides him on how to apply
    public function applicationguide() {


        $this->set('title', 'Application Guide');
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //admin method for getting current students
    public function getcurrentstudents() {

        $currentstudents = $this->Students->find()
                ->contain(['Departments', 'States', 'Users',                      'Lgas'])
                ->where(['admissiondate >=' => date('Y') - 4]);
        $this->set('currentstudents', $currentstudents);
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        $this->set(compact('students', 'departments', 'levels'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that generates payment invoice for students to pay all fees
    public function getinvoiceforfees($studentid, $feeid) {
        //get and set session data
        $this->getsettings();
        //get fee details
        $fees_Table = TableRegistry::get('Fees');
        $settings = $this->request->getSession()->read('settings');

        $fee = $fees_Table->get($feeid);
        $student = $this->Students->get($studentid, ['contain' => ['Departments', 'States', 'Countries', 'Users',   'Lgas', 'Faculties']]);

        //check payment
        $transaction = $this->checkpaystatus($student->id, $fee->id);
        if (is_object($transaction)) {
            $this->set('student', $student);
            $this->set('transaction', $transaction);
        } elseif ($transaction == 1) {
            $this->Flash->success(__('This fee payment has already been completed'));
            return $this->redirect(['action' => 'getreceipt', $student->id, $fee->id, $settings->session_id, $student->fname]);
        } elseif ($transaction == 0) {
            //no pending transaction or invoice so create new one
            $invoice_id = $this->creatnewinvoice($studentid, $fee->id, $fee->amount);
            return $this->redirect(['action' => 'payfees', $student->id, $fee->id, $invoice_id]);
        }

        // $this->Flash->error(__('Please provide your application ID.'));
        // $this->viewBuilder()->setLayout('loginlayout');  //get and set session data
        // $this->viewBuilder()->setLayout('loginlayout');
    }

//shows the candidate their receipt
    public function getreceipt($studentid, $feeid, $sessionid) {
        $transactions_Table = TableRegistry::get('Transactions');
        $transaction = $transactions_Table->find()
                        ->contain(['Students', 'Students.Programmes', 'Students.Faculties',
                            'Students.Users', 'Students.Departments', 'Students.Levels', 'Students.States',
                            'Students.Lgas', 'Fees'])
                        ->where(['Transactions.student_id' => $studentid, 'fee_id' => $feeid, 'session_id' => $sessionid])->first();
        $this->set(compact('transaction'));
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //display invoice for the candidate to either pay online or go to bank
    public function paymentinvoice($invoiceid, $studentid) {


        $this->viewBuilder()->setLayout('loginlayout');
    }

//method that displays fees to applicants
    public function identifycandidate() {

        $fees_Table = TableRegistry::get('Fees');
        if ($this->request->is('post')) {
            $applicant_id = $this->request->getData('application_no');
            $fee_id = $this->request->getData('fee_id');
            $student = $this->Students->find()->where(['application_no' => $applicant_id])
                    ->contain(['Departments', 'States', 'Countries', 'Users',   'Lgas', 'Transactions', 'Faculties'])
                    ->first();
            if (!empty($student->id)) {
//display all fees for them to select and get invoice

                $this->getinvoiceforfees($student->id, $fee_id);
            } else {
                $this->Flash->error(__('Unknown candidate. Please check your application ID and try again.'));
            }
        }
        // $this->Flash->error(__('Please provide your application ID.'));

        $fees = $fees_Table->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        $this->set('fees', $fees);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //go to paystack for payment
    public function gotopaystackotherfees($student_id, $transaction_id) {
        $transactions_Table = TableRegistry::get('Transactions');

        $student = $this->Students->get($student_id);
        $name = $student->fname . ' ' . $student->lname;
        $transaction = $transactions_Table->get($transaction_id);

        //initialize the transaction before going to paystack
//        $settings = $this->request->getSession()->read('settings');
//        $transaction = $transactions_Table->newEmptyEntity();
//        $transaction->student_id = $student_id;
//        $transaction->fee_id = $fee_id;
//        $transaction->session_id = $settings->session_id;
//        $transaction->gresponse = 'initialized';
//        $transaction->amount = $fee->amount;
//        $transaction->payref = strtoupper(uniqid(TRANS_REF)) . date('dmHis');
//        $transaction->paystatus = 'initialized';
//        $transaction->invoice_id = $invoice_id;
//        $transaction->pgateway = "PayStack";
//        // debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
//        $transactions_Table->save($transaction);

        $baseurl = "https://portal.claretianuniversity.edu.ng/";

        $subacc = 'ACCT_eyec9earijeztxb'; // sub-account code, you get this when you set up a split account.
        $cancel_url = $baseurl . 'cancel/' . $transaction->payref . '/';
        //handle split
        $split_to_cun = 0;
        if ($transaction->fee_id == 5) {
            $split_to_cun = 0;
        } else {
            $split_to_cun = ($transaction->amount - 500);
        }

        //base url
        $baseUrl = Router::url('/', true);
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
                'callback_url' => $baseUrl . 'students/paymentverificationotherfees/' . $transaction->payref,
                'amount' => $transaction->amount . '00',
                'email' => $student->email,
                'name' => $name,
                'subaccount' => $subacc,
                'phone' => $student->phone,
                'last_name' => $student->lname,
                'bearer' => 'subaccount',
                'reference' => $transaction->payref,
                'transaction_charge' => $split_to_cun . '00',
                'metadata' => json_encode([
                    'cancel_action' => $cancel_url,
                    'name' => $name,
                    'fname' => $student->fname,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'transaction_id' => $transaction->id,
                    'student_id' => $student_id,
                    'payee_id' => $transaction->payref,
                    'invoice_id' => $transaction->invoice_id,
                ]),
            ]),
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer sk_live_bd5ae86597f3fed8a4ad7c013d31c572bc9f7d3f",
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
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
        //  header('location : '.$tranx->data->authorization_url);
        //return $tranx->data->authorization_url;
        return $this->redirect($tranx->data->authorization_url);
    }

    public function paymentverificationotherfees($ref) {
        // echo $ref; exit;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($ref),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer sk_live_bd5ae86597f3fed8a4ad7c013d31c572bc9f7d3f",
                "cache-control: no-cache"
            ],
        ));

        //sk_test_7d5d515418c31cf203abbe3f753b1487b7d2a5e2

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

        // debug($tranx); exit;
        $transactions_Table = TableRegistry::get('Transactions');
        $trans_id = $tranx->data->metadata->transaction_id;
        $email = $tranx->data->metadata->email;
        $name = $tranx->data->metadata->name;
        $invoice_id = $tranx->data->metadata->invoice_id;
        //update transaction record
        $transaction = $transactions_Table->get($trans_id);
        $transaction->status = $tranx->status;
        $transaction->transdate = date('Y-m-d H:i');
        $transaction->amount = $tranx->data->amount / 100;
        $transaction->paystatus = 'completed';
        $transaction->gresponse = $tranx->data->status;
        $transaction->pgateway = "PayStack";
        $transactions_Table->save($transaction);
        // update invoice
        $invoices_Table = TableRegistry::get('Invoices');
        $invoice = $invoices_Table->get($invoice_id);
        $invoice->paystatus = $tranx->data->status;
        $invoice->payday = date('d M Y H:i a');
        $invoices_Table->save($invoice);
        //send payment alert via email
        $this->payconfirmationmail($email, $name, $transaction->amount, $transaction->student_id, $transaction->payref);

        $this->Flash->success('Your payment was successful.');
        return $this->redirect(['action' => 'getreceipt', $tranx->data->metadata->student_id, $transaction->fee_id, $transaction->session_id]);
    }

    //admin method for assigning school email address to students
    public function studentswithoutemail() {

        $students = $this->Students->find()->where(['universitymail IS' => Null, 'status' => 'Admitted'])
                ->contain(['Departments', 'States', 'Countries', 'Users',  'Hostelrooms.Hostels',
 'Lgas', 'Faculties']);
        $this->set('students', $students);
        // debug(json_encode($students, JSON_PRETTY_PRINT)); exit; 
        $this->viewBuilder()->setLayout('backend');
    }

    //shows the candidate their invoice for payment
    public function payfees($student_id, $fee_id, $invoice_id) {
        //set the base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',                  'Users', 'States', 'Lgas', 'Countries', 'Faculties']]);
        $fee = $fees_Table->get($invoice->fee_id);

        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $transaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
        if ($transaction == "none") {
            $transaction = $transactions_Table->newEmptyEntity();
            $transaction->student_id = $student_id;
            $transaction->fee_id = $invoice->fee_id;
            $transaction->session_id = $settings->session_id;
            $transaction->gresponse = 'initialized';
            $transaction->invoice_id = $invoice->id;
            $transaction->amount = $invoice->amount;
            $transaction->payref = strtoupper(uniqid(TRANS_REF) . date('dmyHis'));
            $transaction->paystatus = 'initialized';
            //debug(json_encode($transaction, JSON_PRETTY_PRINT)); exit;
            $transactions_Table->save($transaction);
        }
        //debug(json_encode(  $transaction, JSON_PRETTY_PRINT)); exit;    
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('baseUrl', $baseUrl);
        $this->set('transaction', $transaction);
        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method that gets an appplicantd data and allows him to update some fields
    public function getmydata() {
        //get and set session data
        $this->getsettings();
        if ($this->request->is(['post'])) {
            $applicant_id = $this->request->getData('application_no');
            // echo $applicant_id; exit;
            $applicant = $this->Students->find()
                            ->contain([  'Faculties',  'Departments', 'States', 'Lgas', 'Countries'])
                            ->where(['application_no' => $applicant_id, 'status' => 'Applied'])->first();
            if (!empty($applicant->id)) {
                $this->Flash->success(__('All files for upload must be less than 1mb and either jpg,jpeg or pdf.'));
                return $this->redirect(['action' => 'updatemydata', $applicant->id, $applicant->fname]);
            } else {
                $this->Flash->error(__('Candidate not found. please check the application number and try again'));
                return $this->redirect(['action' => 'getmydata']);
            }
        }

        $this->viewBuilder()->setLayout('loginlayout');
    }

    //method for candidates to update their application and files
    public function updatemydata($id) {
        $student = $this->Students->get($id, [
            'contain' => [  'Faculties',  'Departments', 'States', 'Lgas', 'Countries']
        ]);

        //get and set session data
        $this->getsettings();
        if ($this->request->is(['patch', 'post', 'put'])) {
            //upload passport
            $pix = $this->request->getData('passporturls');
            $admin_letter = $this->request->getData('jamb_admin_letters');
            $jamb_notice_letter = $this->request->getData('jamb_notifications');
            $student_pix = $pix->getClientFilename();
            if (!empty($student_pix)) {
                $this->deletefile($student->passporturl, 'student_files/');
                $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $passport = $student->passporturl;
            }
            //handle adminssion letter upload
            $letter = $admin_letter->getClientFilename();
            if (!empty($letter)) {
                $this->deletefile($student->jamb_admin_letter, 'student_files/');
                $jamb_admin_letter = $this->handlefileupload($this->request->getData('jamb_admin_letters'), 'student_files/');
            } else {
                $jamb_admin_letter = $student->jamb_admin_letter;
            }
            //handle jamb notification file upload
            $jamb_notification = $jamb_notice_letter->getClientFilename();
            if (!empty($jamb_notification)) {
                $this->deletefile($student->jamb_notification, 'student_files/');
                $jamb_notice = $this->handlefileupload($this->request->getData('jamb_notifications'), 'student_files/');
            } else {
                $jamb_notice = $student->jamb_notification;
            }

            //upload o level
            $olevel = $this->request->getData('olevelresulturls');
            $olevel_filename = $olevel->getClientFilename();
            if (!empty($olevel_filename)) {
                $this->deletefile($student->olevelresulturl, 'student_files/');
                $waec_cert = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
            } else {
                $waec_cert = $student->olevelresulturl;
            }
            //upload birth cert
            $birthcert = $this->request->getData('birthcerturls');
            $birthcert_filename = $birthcert->getClientFilename();
            if (!empty($birthcert_filename)) {
                $this->deletefile($student->birthcerturl, 'student_files/');
                $birth_cert = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
            } else {
                $birth_cert = $student->birthcerturl;
            }
            //upload other file
            $other_cert = $this->request->getData('othercertss');
            $othercert_filename = $other_cert->getClientFilename();
            if (!empty($othercert_filename)) {
                $this->deletefile($student->othercerts, 'student_files/');
                $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
            } else {
                $other_cert = $student->othercerts;
            }
            //upload jamb result
            $jambresultfile = $this->request->getUploadedFile('jambresults');
            $jambresult_name = $jambresultfile->getClientFilename();
            if (!empty($jambresult_name)) {
                $jambresult = $this->handlefileupload($this->request->getData('jambresults'), 'student_files/');
            } else {
                $jambresult = "";
            }

            $student = $this->Students->patchEntity($student, $this->request->getData());
            $student->passporturl = $passport;
            $student->othercerts = $other_cert;
            $student->jamb_notification = $jamb_notice;
            $student->birthcerturl = $birth_cert;
            $student->jamb_admin_letter = $jamb_admin_letter;
            $student->olevelresulturl = $waec_cert;
            $student->jambresult = $jambresult;
            if ($this->Students->save($student)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Applicant Self-data Update " . $student->fname . ' ' . $student->lname;
                $user_id = $this->Auth->user('id');
                $description = "Applicant Update" . $student->application_no;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('Your data has been updated successfully.'));
                return $this->redirect(['action' => 'getmydata']);
            }
            $this->Flash->error(__('The candidate data could not be updated. Please, try again.'));
        }

        $this->set(compact('student'));

        $this->viewBuilder()->setLayout('loginlayout');
    }

    //admin method for generating student ids
    public function generateids() {
        if ($this->request->is('post')) {
            $department_id = $this->request->getData('department_id');
            $programetype_id = $this->request->getData('programetype_id');
            $category_id = $this->request->getData('category_id');
            $level_id = $this->request->getData('level_id');
            $condition = [];
            if (!empty($department_id)) {
                $condition['Students.department_id'] = $department_id;
            }
            if (!empty($level_id)) {
                $condition['Students.level_id'] = $level_id;
            }
            if (!empty($category_id)) {
                $condition['Students.category_id'] = $category_id;
            }
            if (!empty($programetype_id)) {
                $condition['Students.programetype_id'] = $programetype_id;
            }
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Users',                          'Lgas',  'Hostelrooms.Hostels', 'Faculties'])
                    ->where(['status' => 'Admitted'])
                    ->andWhere($condition)
                    // ->limit(2000)
                    ->order(['joindate' => 'DESC']);
            //  $this->paginate($students);
        } else {
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Users',                          'Lgas',  'Hostelrooms.Hostels', 'Faculties'])
                    ->where(['status' => 'Admitted', 'Students.level_id !=' => 5])
                    ->limit(500)
                    ->order(['joindate' => 'DESC']);
        }
        // debug(json_encode($students, JSON_PRETTY_PRINT)); exit;
        // $modes = $this->Students->Modes->find('list');
        // $categories = $this->Students->Categories->find('list');
        // $programetypes = $this->Students->Programetypes->find('list');
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'DESC']);
        // $levels = $this->Students->Levels->find('list');
        $this->set(compact('students', 'programetypes', 'departments', 'levels', 'modes', 'categories'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for suspending a students account
    public function suspendstudent($studentid) {

        $student = $this->Students->get($studentid);
        $student->studentstatus = "Suspended";
        $this->Students->save($student);
        $this->Flash->success(__('The student account has been suspended and can no longer login to the portal'));
        //log activity
        $usercontroller = new UsersController();

        $title = "Student account suspension " . $student->fname . ' ' . $student->lname;
        $user_id = $this->Auth->user('id');
        $description = "Suspended a student: " . $student->regno;
        $ip = $this->request->clientIp();
        $type = "Edit";
        $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
        return $this->redirect(['action' => 'managestudents']);
    }

    //admin method for unsuspending a students account
    public function unsuspendstudent($studentid) {

        $student = $this->Students->get($studentid);
        $student->studentstatus = "Active";
        $this->Students->save($student);
        $this->Flash->success(__('The account has been reactivated and can now login to the portal.'));
        //log activity
        $usercontroller = new UsersController();

        $title = "Student account Unsuspension " . $student->fname . ' ' . $student->lname;
        $user_id = $this->Auth->user('id');
        $description = "Activated a student account after suspension: " . $student->regno;
        $ip = $this->request->clientIp();
        $type = "Edit";
        $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
        return $this->redirect(['action' => 'managestudents']);
    }

    //method that resizes an image before uploading it
    public function imageResize($imageResourceId, $width, $height) {
        $targetWidth = 150;
        $targetHeight = 150;
        $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        return $targetLayer;
    }

    //function that creates thumbnails for students passport
    function makeThumbnail($sourcefile, $endfile, $thumbwidth = 100, $thumbheight = 100, $quality = 80) {
        // Takes the sourcefile (path/to/image.jpg) and makes a thumbnail from it
        // and places it at endfile (path/to/thumb.jpg).
        // Load image and get image size.
        $img = imagecreatefromjpeg($sourcefile);
        $width = imagesx($img);
        $height = imagesy($img);

        if ($width > $height) {
            $newwidth = $thumbwidth;
            $divisor = $width / $thumbwidth;
            $newheight = floor($height / $divisor);
        } else {
            $newheight = $thumbheight;
            $divisor = $height / $thumbheight;
            $newwidth = floor($width / $divisor);
        }

        // Create a new temporary image.
        $tmpimg = imagecreatetruecolor($newwidth, $newheight);

        // Copy and resize old image into new image.
        imagecopyresampled($tmpimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        // Save thumbnail into a file.
        imagejpeg($tmpimg, $endfile, $quality);

        // release the memory
        imagedestroy($tmpimg);
        imagedestroy($img);
    }

    //admin method for removing a candidate
    public function removecandidate() {
        $students = $this->Students->find()->order(['id' => 'DESC'])->limit(100);
        $this->set('students', $students);

        $this->viewBuilder()->setLayout('backend');
    }

    //just testing my kotlin app api
    public function getStudentWithRegNo($id) {
        $student = $this->Students->get($id);
        // ->contain(['Departments', 'States', 'Users',          //     'Lgas',  'Hostelrooms.Hostels'])
        //   ->where(['regno' => $regno]);
        return $this->response->withType("application/json")->withStringBody(json_encode($student));
    }

//student method for online payment with interswitch
    public function interswitchwebpay($student_id, $fee_id, $invoice_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id, ['contain' => ['Sessions']]);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',                  'States', 'Countries', 'Lgas', 'Users', 'Faculties']]);
        $fee = $fees_Table->get($fee_id);
        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $old_transaction = $this->checkpayeeid($fee_id, $invoice_id, $student_id, $settings->session_id);
        if ($old_transaction == "none") {
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
        } else {
            $transaction = $transactions_Table->find()
                    ->where(['student_id' => $student_id, 'fee_id' => $fee_id, 'invoice_id' => $invoice_id])
                    ->first();
        }
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('transaction', $transaction);
        // $this->viewBuilder()->setLayout('backend');
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    //admin method for generating payment invoice for sudents
    public function admingeneratepayeeid($invoice_id, $student_id) {
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $invoice = $invoices_Table->get($invoice_id, ['contain' => ['Sessions']]);
        $student = $this->Students->get($student_id, ['contain' => ['Departments',                  'States', 'Countries', 'Lgas', 'Users']]);
        $fee = $fees_Table->get($invoice->fee_id);
        //initialize the transaction before going to interswitch
        $settings = $this->request->getSession()->read('settings');
        //check for unpaid transaction id
        $oldtransaction = $this->checkpayeeid($invoice->fee_id, $invoice->id, $student_id, $settings->session_id);
        if ($oldtransaction == "none") {
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
            $newtransaction = $transactions_Table->get($transaction->id, ['contain' => ['Sessions']]);
        }
        //check if student is in year one and check if ict and other fees have been paid
//        if ($student->level->id == 1) {
//            $status = $this->ictandacceptancefeestatus($student_id);
//            $this->set('status', $status);
//        }

        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('transaction', $newtransaction);
        $this->viewBuilder()->setLayout('backend');
    }

    //credo //payment with credo API
    public function gotocredo($student_id, $fee_id, $invoice_id) {

# STEP 1 -> PAYMENT INITIALIZATION
        $transactions_Table = TableRegistry::get('Transactions');
        $invoices_Table = TableRegistry::get('Invoices');
        $fees_Table = TableRegistry::get('Fees');
        $transaction = $transactions_Table->newEmptyEntity();
        $fee = $fees_Table->get($fee_id);
        $invoice = $invoices_Table->get($invoice_id);
        $student = $this->Students->get($student_id);
        $name = $student->fname . ' ' . $student->lname;
        //initialize the transaction before going to paystack
        $settings = $this->request->getSession()->read('settings');

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
        $amount = $invoice->amount;
        $demoBaseUrl = 'https://api.credodemo.com/transaction';
# live Base url = https://api.credocentral.com
        $publishableKey = '0PUB0214cW880hsk1hrmjLM6nT2l6vYR'; //'0PUB0312poF5J29cruYr0ZO1tcAh6V0J';
        $ref = $transaction->payref;
        $data = array(
            "amount" => $amount * 100, # amount is in kobo
            "bearer" => 1, # who bears the fee; 0 = customer, 1 = merchant
            "callbackUrl" => "https://portal.busced.edu.ng/transactions/verifycredo/.{$ref}", # set this to your receipt URL as the case may be
            "channels" => array("card", "bank"),
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
            "serviceCode" => $fee->itemcode # If you have dynamic settlement service setup
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
            // there was an error contacting the Paystack API
            die('Curl returned error: ' . $err);
        }
        curl_close($ex_transaction);

# STEP 2 -> REDIRECT CUSTOMER TO CREDO GATEWAY FOR PAYMENT

        $responseData = json_decode($response);
       // debug(json_encode($responseData, JSON_PRETTY_PRINT));
       // exit;
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

    //verification page for credo payment
    public function verifycredo($ref) {
        $demoBaseUrl = 'https://api.credodemo.com/transaction/' . $ref . '/verify';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.credodemo.com/transaction/' . $ref . '/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: 0PRI0214x8QLukyy5A02dW76mRW78kHM",
//                "cache-control: no-cache"
            ],
        ));

        //sk_test_7d5d515418c31cf203abbe3f753b1487b7d2a5e2

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            // there was an error contacting the Paystack API
            die('Curl returned error: ' . $err);
        }

        $tranx = json_decode($response);
        debug(json_encode($response, JSON_PRETTY_PRINT));
        exit;
        if (!$tranx->status) {
            // there was an error from the API
            die('API returned error: ' . $tranx->message);
        }
        //ensure payment was successful
        if ($tranx->status != 200) {
            $this->Flash->error('Sorry, the payment was not successful, please try again: ' . $tranx->message);
            return $this->redirect(['controller' => 'Students', 'action' => 'myinvoices']);
        }
    }

    //used to inestigate something
    public function findmissing($id) {
        $student = $this->Students->get($id);
        debug(json_encode($student, JSON_PRETTY_PRINT));
        exit;
    }
    
    
    
    
    //admin page for view special candidates like on scholarship, etc
    public function spcanidates(){
        
        if ($this->request->is('post')) {
            $category = $this->request->getData('category');
      
            
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Users',                          'Lgas',  'Hostelrooms.Hostels'])
                    ->where(['status' => 'Admitted','isclaretian'=>$category])
                    ->order(['joindate' => 'DESC']);
            //  $this->paginate($students);
        } else {
            $students = $this->Students->find()
                    ->contain(['Departments', 'States', 'Users',                          'Lgas',  'Hostelrooms.Hostels'])
                    ->where(['status' => 'Admitted', 'isclaretian' => 'Yes'])
                    ->limit(500)
                    ->order(['joindate' => 'DESC']);
        }
        // debug(json_encode($students, JSON_PRETTY_PRINT)); exit;
        
        $this->set(compact('students'));
        $this->viewBuilder()->setLayout('backend');   
        
        
        
    }
    
    
    //students method for viewing and participating in an election
    public function viewelections(){
        
      $candidates_Table = TableRegistry::get('Candidates');  //get the candidates model 
         $settings =    $this->request->getSession()->read('settings');
        $candidates = $candidates_Table->find()->contain(['Students', 'Positions', 'Sessions','Votes'])
                ->where(['Candidates.session_id'=>$settings->session_id]);

        $this->set(compact('candidates'));
        
        
          $this->viewBuilder()->setLayout('studentsbackend'); 
    }
    
    
    //students method for voting in an election
    public function votecandidate($candidate_id,$position_id){
     $votes_Table = TableRegistry::get('Votes'); 
      $candidates_Table = TableRegistry::get('Candidates'); 
      $positions_Table = TableRegistry::get('Positions'); 
      $settings =    $this->request->getSession()->read('settings');
     $student =   $this->request->getSession()->read('student');
     //check voting time
     $position = $positions_Table->get($position_id);
//     date_default_timezone_set("Africa/Lagos");
//     // Get the current time.
//    $time = FrozenTime::now();
//    // check stated voting time
//    $votetime = new FrozenTime($position->votingends, 'Africa/Lagos');
    //ensure voting time has not passed
     if(strtotime($position->votingends)< time()){
       $this->Flash->error(__('Voting has ended'));
           return $this->redirect(['controller' => 'Students', 'action' => 'viewelections']);
     }
    // echo strtotime($position->votingends).' - '.time(); exit;
     //ensure student has not voted for this poisitio already
     $voted = $votes_Table->find()->where(['position_id'=>$position_id,'student_id'=> $student->id])->first();
     if(empty($voted->id)){ //proceed to vote 
      $newvote =  $votes_Table->newEmptyEntity();
      $newvote->position_id = $position_id;
      $newvote->student_id = $student->id;
      $newvote->candidate_id = $candidate_id;
       $newvote->vote = 1;
       if($votes_Table->save($newvote)){
           //update candidates total votes
       $candidate = $candidates_Table->find()->where(['Candidates.id'=>$newvote->candidate_id,'position_id'=>$position_id,
           'session_id'=>$settings->session_id])->first(); 
        $candidate->totalvotes+=1;
        $candidates_Table->save($candidate);  
            $this->Flash->success(__('Your vote has been saved'));
           return $this->redirect(['controller' => 'Students', 'action' => 'viewelections']);
       } else{
         
           $this->Flash->error(__('Unable to save vote. Please try again'));
           return $this->redirect(['controller' => 'Students', 'action' => 'viewelections']);
       }
     }else{
      $this->Flash->error(__('Sorry, you have already voted for this position'));
           return $this->redirect(['controller' => 'Students', 'action' => 'viewelections']);   
     }
        
        
    }
    
    
    //handlie payment for CUN online
    public function getpayonline($amount, $serviceid,$name, $phone, $email){
        $baseUrl = Router::url('/', true);
         $account = "2035182780";  //CUN first bank
          $uba_code = "033";
          $firstbank_code = "011";
          $CUN_SPLIT = $amount-5000;
          $netpro_split = 5000;
        
       $timesammp = DATE("dmyHis");
        $orderID = $timesammp;
        $payerName = $name;
        $payerEmail = $email;
        $payerPhone = $phone;
        $merch_id = MERCHANTID;
        //$serviceid = $serviceid;
        $orderid =  uniqid(DATE("dmyHis"));
        $apikey = APIKEY;
        $hash_string = $merch_id .$serviceid. $orderid . $amount . $apikey;
        $hash = hash('sha512', $hash_string);
        $curl = curl_init();
      // echo $bank_code .' - '.$account; exit;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "serviceTypeId": "' . $serviceid . '",
    "amount": "'.$amount.'",
    "orderId": "' . $orderid . '",
    "payerName": "'.$payerName.' ",
    "payerEmail": "'.$payerEmail.'",
    "payerPhone": "'.$payerPhone.'",
    "lineItems":[
       {
          "lineItemsId":"itemid1",
          "beneficiaryName":"Claretian University of Nigeria",
          "beneficiaryAccount":'.$account.',
          "bankCode":'.$firstbank_code.',
          "beneficiaryAmount":'.$CUN_SPLIT.',
          "deductFeeFrom":"1"
       },
       {
          "lineItemsId":"itemid2",
          "beneficiaryName":"NETPRO INTL NIG LTD-ACCT 2",
          "beneficiaryAccount":"1019186840",
          "bankCode":"033",
          "beneficiaryAmount": '.$netpro_split.',
          "deductFeeFrom":"0"
       }
    ]
}',
            CURLOPT_HTTPHEADER => array(
                "Authorization: remitaConsumerKey=" . $merch_id . ",remitaConsumerToken=$hash",
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));
 //echo  $netpro_split.'-' .$cun_split.'-'.$totalAmount; exit;
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            debug(json_encode($err, JSON_PRETTY_PRINT));
            exit;
        }
//debug(json_encode($response, JSON_PRETTY_PRINT));
        curl_close($curl);
       $jsonData = substr($response, 7, -1);

        $returned_response = json_decode($jsonData, true);
        // var_dump($response); first UBA account  1019186840 
        $statuscode = $returned_response['statuscode'];
        $statusMsg = $returned_response['status'];
        if ($statuscode == '025') {
            $rrr = trim($returned_response['RRR']);

            $new_hash_string = $merch_id . $rrr . $apikey;
            $new_hash = hash('sha512', $new_hash_string);
            $responseurl = $baseUrl . 'Transactions/confirmremitapay/' . $rrr;
            //return $this->redirect([ 'action' =>  'gotoremitacunonline', $orderid, $new_hash,$rrr]);  
           // $this->createtransaction($student_id, $fee_id, $rrr, $invoice->amount, $orderID, $new_hash, $invoice->id);
        }  
        $this->set(compact('name','amount','phone','email','rrr','new_hash','responseurl'));
        $this->viewBuilder()->setLayout('loginlayout');
        
        
    }
    
    
    //CUN ONLINE ACTUAL PAYMENT PAGE
    public function gotoremitacunonline($orderid, $hash){
            //base url
        $baseUrl = Router::url('/', true);
        $transactions_Table = TableRegistry::get('Transactions');
        $fees_Table = TableRegistry::get('Fees');
        $students_Table = TableRegistry::get('Students');
        $transaction = $transactions_Table->find()->where(['gresponse' => $orderid])->first();
        $fee = $fees_Table->get($transaction->fee_id);
        $student = $students_Table->get($transaction->student_id);
        $responseurl = $baseUrl . 'Transactions/confirmremitapaycunonline/' . $transaction->payref;
        $this->set('transaction', $transaction);
        $this->set('responseurl', $responseurl);
        $this->set('student', $student);
        $this->set('fee', $fee);
        $this->set('new_hash', $hash);
        if ($user['role_id'] == 2 || $user['role_id'] == 5) {
            $this->viewBuilder()->setLayout('studentsbackend');
        } else {
            $this->viewBuilder()->setLayout('loginlayout');
        }   
        
    }

    
    
     //the application method that allows student to apply online
    public function ijmbapplicantion() {

        $settings_Table = TableRegistry::get('Settings');
        $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
        $this->request->getSession()->write('settings', $settings);
        $student = $this->Students->newEmptyEntity();
        if ($this->request->is('post')) {
            //   debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
            
            $userscontroller = new UsersController();
           
            //upload files
            //upload o level
            $olevel = $this->request->getData('olevelresulturls');
            $olevel_filename = $olevel->getClientFilename();
            if (!empty($olevel_filename)) {

                $waec_cert = $this->handlefileupload($this->request->getData('olevelresulturls'), 'student_files/');
            } else {
                $waec_cert = "";
            }

            //upload birth cert
            $birthcert = $this->request->getData('birthcerturls');
            $birthcert_filename = $birthcert->getClientFilename();
            if (!empty($birthcert_filename)) {

                $birth_cert = $this->handlefileupload($this->request->getData('birthcerturls'), 'student_files/');
            } else {
                $birth_cert = "";
            }
            //upload other file
            $other_cert = $this->request->getData('othercertss');
            $othercert_filename = $other_cert->getClientFilename();
            if (!empty($othercert_filename)) {

                $other_cert = $this->handlefileupload($this->request->getData('othercertss'), 'student_files/');
            } else {
                $other_cert = "";
            }
            //upload passport
            $passportfile = $this->request->getData('passporturls');
            $passport_name = $passportfile->getClientFilename();
            if (!empty($passport_name)) {
                //call the funtion to resize and upload
                $passport = $this->doresizepassport($this->request->getData('passporturls'));
                // $passport = $this->handlefileupload($this->request->getData('passporturls'), 'student_files/');
            } else {
                $passport = "";
            }
            //create login data
            $email = $this->request->getData('email');
            $fname = $this->request->getData('fname');
            $lname = $this->request->getData('lname');
            $mname = $this->request->getData('mname');
            $userid = $this->getlogindetails($email, $fname, $lname, $mname);
            if (is_numeric($userid)) {
                $student = $this->Students->patchEntity($student, $this->request->getData());
                $student->user_id = $userid;
                $student->category_id = 1;
                $student->previousschool = $this->request->getData('nin');
                $student->othercerts = $other_cert;
                $student->passporturl = $passport;
                $student->birthcerturl = $birth_cert;
                $student->olevelresulturl = $waec_cert;
                $student->mode_id = 5;
                $student->level_id = 7;
                $student->department_id = 39;
                $student->faculty_id = 8;
                $student->programe_id = 77;
                $student->programme_id = 77;
                $student->session_id = $settings->session->id;
              //  debug(json_encode( $student, JSON_PRETTY_PRINT)); exit;
                if ($this->Students->save($student)) {
                    //assign application number
                    $this->getapplicationno($student->id);
                    //send application summary to candidate via email
                    //$this->sendapplicationsummary($student->id);

                    $fees_Table = TableRegistry::get('Fees');            
                        $fee = $fees_Table->get(22);  
                    //create invoice for this payment
                    $invoice_id = $this->creatnewinvoice($student->id, $fee->id, $fee->amount);
                    //generate payee id
                    // $this->generateapplicantpayeeid($invoice_id, $student->id);
                    //$url = $transactionController->gotopaystack($email, $student->phone, $name, $fee->amount, $student->id, $fee->id);
                    $this->Flash->success(__('Your application has been saved, please click on the green button below '
                                    . ' to pay the application fee(Note that applications without application fee payment will not be processed).'
                                    . ' You can always check your application status by clicking on (Check Application Status)'
                                    . '  on the application page and entering the application number which we just sent to your email address'));

                    return $this->redirect(['action' => 'generateapplicantpayeeid', $invoice_id, $student->id]);
                }
                $this->Flash->error(__('Sorry, we could not submit your application. Please, try again.'));
            } else {//if unable to create user login data
                $this->Flash->error(__('Sorry, the email address you provided is already in use. Please, try again.'));
            }
        }
        $departments = $this->Students->Departments->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $states = $this->Students->States->find('list', ['limit' => 200])->where(['country_id' => 160]);
        $countries = $this->Students->Countries->find('list', ['limit' => 2000]);
        $lgas = $this->Students->Lgas->find('list', ['limit' => 2000])->where(['state_id' => 2647])->order(['name' => 'ASC']);
       // $fees = $this->Students->Fees->find('list', ['limit' => 200]);
      //  $level_ids = [1];
        $programeids = [1, 2, 3, 4];
        // $modes = $this->Students->Modes->find('list')->where(['id IN' => [1, 2]]);
        // $programetypes = $this->Students->Programetypes->find('list')->where(['id IN' => [4]]);
        $allowed_faculties = [1, 2, 3, 4];
      //  $levels = $this->Students->Levels->find('list')->where(['id IN' => $level_ids])->order(['name' => 'ASC']);
        // $faculties = $this->Students->Faculties->find('list')->where(['id IN' => $allowed_faculties])->order(['name' => 'ASC']);
        $programes = $this->Students->Programmes->find('list', ['limit' => 200])->order(['name' => 'ASC']);
      //  $subjects = $this->Students->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('student', 'programetypes', 'departments', 'states', 'countries', 'lgas', 'faculties', 'programes'));
        $this->set('title', 'New IJMB Application');
        $this->viewBuilder()->setLayout('loginlayout');
    }
    
    
    
    //students method for finding resources
    public function findresources(){
         $resources_table = TableRegistry::get('Eresources');
              if ($this->request->is(['patch', 'post', 'put'])) {
         
         $deptid =  $this->request->getData('department_id');  
        $author =  $this->request->getData('author'); 
         $title =  $this->request->getData('title');
         $condition = [];
            if (!empty($author)) {
                $condition['Eresources.author LIKE'] = $author;
            }
      if(!empty($deptid)){
       $condition['Eresources.department_id'] = $deptid;   
      }
       if(!empty($title)){
       $condition['Eresources.title LIKE'] = $title;   
      }
         $eresources = $resources_table->find()->contain(['Departments'])
                  ->where($condition);
              //  ->Where(['details LIKE' => '%' . $search_param . '%'])
         
               
           }else{
         $eresources = $resources_table->find()->contain(['Departments'])->order(['Eresources.id'=>'DESC'])->limit(12);
         
           }
        $departments_Table = TableRegistry::get('Departments');
        //$eresources = $this->paginate($this->Eresources);

        $departments = $departments_Table->find('list', ['limit' => 200])->all();
        $this->set(compact('eresources','departments'));
        
   $this->viewBuilder()->setLayout('studentsbackend');     
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
        $student = $this->Students->get($student_id);
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
                'callback_url' => $baseUrl . 'students/paymentverificationtest/' . $transaction->payref,
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
                "authorization: Bearer sk_test_02417f044c8402c28651bdbf63c95109662c4014",
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        //sk_test_3643e4d436f451b2818fc018700c09bf94dba11a
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
                "authorization: Bearer sk_test_02417f044c8402c28651bdbf63c95109662c4014",
                "cache-control: no-cache"
            ],
        ));

        //sk_live_65b10dd930c5c67ca10d7d832211d10d40ed40e5 


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
        return $this->redirect(['action' => 'invoices', $tranx->data->metadata->student_id]);
    }
    
    

    
    // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        $this->Auth->allow(['newapplicant', 'requesttranscript', 'transcript', 'getstates', 'checkstatus', 'index', 'admission', 'getdapts',
            'academics', 'news', 'applicationguide', 'alumai', 'academics', 'aboutus','ijmbapplicantion', 'getlgas', 'printapplicationform',
            'getacceptanceletter', 'howtopayfees', 'generateapplicantpayeeid', 'creatnewinvoice',
            'validatestudent', 'verifystudent', 'applicationsummary', 'getprogrames', 'identifycandidate',
            'getincompleteapplicant', 'newcdlapplicant', 'checkpaystatus', 'getdaptsforcdl', 'getsettings',
            'generatetranscriptpayeeid', 'gettranscriptinvoice', 'getinvoiceforfees', 'getreceipt',
            'paymentinvoice', 'paymentverificationotherfees', 'gotopaystackotherfees', 'payfees'
            , 'updatemydata', 'getmydata', 'getStudentWithRegNo', 'newtnebapplication','getpayonline']);

        $actions = ['newstudent', 'newcdlapplicant', 'newtnebapplication', 'promotestudents', 'updatestudent', 'managestudents', 'importstudents', 'getstudentsforpromotion'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
        }
    }
}