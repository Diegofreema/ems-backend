<?php
declare(strict_types = 1);

  namespace App\Controller;

 use Cake\Mailer\Mailer;
use Cake\Event\EventInterface;
  use Cake\ORM\TableRegistry;
  use App\Controller\AppController;

  /**
   * Courseregistrations Controller
   *
   * @property \App\Model\Table\CourseregistrationsTable $Courseregistrations
   *
   * @method \App\Model\Entity\Courseregistration[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
   */
  class CourseregistrationsController extends AppController {

      /**
       * Index method
       *
       * @return \Cake\Http\Response|void
       */
      public function index() {
          $this->paginate = [
              'contain' => ['Students', 'Sessions', 'Semesters', 'Levels']
          ];
          $courseregistrations = $this->paginate($this->Courseregistrations);

          $this->set(compact('courseregistrations'));
      }

      /**
       * View method
       *
       * @param string|null $id Courseregistration id.
       * @return \Cake\Http\Response|void
       * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
       */
      public function view($id = null) {
          $courseregistration = $this->Courseregistrations->get($id, [
              'contain' => ['Students', 'Sessions', 'Semesters', 'Levels', 'Subjects']
          ]);

          $this->set('courseregistration', $courseregistration);
      }

      /**
       * Add method
       *
       * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
       */
      public function register() {
          //get the current session
          $sesion = $this->request->getSession()->read('settings');
          //get the course registration_subjects table
          $coursereg_students_Table = TableRegistry::get('CourseregistrationsSubjects');
          $course_assignments = TableRegistry::get('Courseassignments'); 
          $departments_table = TableRegistry::get('Departments'); 
          //ensure this is a valid student
          $student = $this->isstudent();
           //check if this guy has already registered
              $this->checkcourseregistraion($student->id,$student->level_id); 
              
//              $assigned_courses =  $course_assignments->find()
//                       ->contain(['Subjects'])
//                      ->where(['department_id'=> $student->department_id,'level_id'=>$student->level_id,'semester_id'=> $sesion->semester_id])
//                      ->first();
//            $this->set('assigned_courses',$assigned_courses);
         
          $courseregistration = $this->Courseregistrations->newEmptyEntity();
          if ($this->request->is('post')) {
             
              //check if there is a carry over course
               $selected_courses = $this->request->getData('subjects._ids');
               $carryover = $this->checkCarryOver($student->id,$selected_courses);
               if($carryover==0){ //there is a carry over, check for payment
                 $payment = $this->checkCarryOverFee($student->id); 
                 if($payment==0){
                     //already paid so continue registeration
                     
                 }
                 else{ //has to pay for carry over courses
                   $this->Flash->error(__('Sorry, because you are registering carryover course(s),'
                           . 'you have to pay course registration fee of 5500. Please click on Course '
                           . 'registration fee below and pay online using ATM card'));

                  return $this->redirect(['controller'=>'Students','action' => 'myinvoices']);  
                 }
               }
           
//          if(empty($carry_over_courses)){
//              $this->registerfreshcourses($student->id, $assigned_courses, $student->level_id);
//          }
//          else{
      
              $courseregistration = $this->Courseregistrations->patchEntity($courseregistration, $this->request->getData());
              $courseregistration->student_id = $student->id;
              $courseregistration->session_id = $sesion->session_id;
             // debug(json_encode($courseregistration, JSON_PRETTY_PRINT)); exit;
              if ($this->Courseregistrations->save($courseregistration)) {
                  //save the main courses asign to his dept
//                  foreach ($selected_courses as $subject) {
//                      echo $subject->id; exit;
//                      $coursereg = $coursereg_students_Table->newEmptyEntity();
//                      $coursereg->courseregistration_id = $courseregistration->id;
//                      $coursereg->subject_id = $subject->id;
//
//                      $coursereg_students_Table->save($coursereg);
//                  }

                  $this->Flash->success(__('Your course registration was succesful.'));

                  return $this->redirect(['action' => 'registeredcourses']);
              }
          
              $this->Flash->error(__('The course registration could not be saved. Please, try again.'));
         // }
          }
         // $students = $this->Courseregistrations->Students->find('list', ['limit' => 200])->where(['id' => $student->id]);
        //  $sessions = $this->Courseregistrations->Sessions->find('list', ['limit' => 10])->order(['name'=>'DESC']);
          $semesters = $this->Courseregistrations->Semesters->find('list', ['limit' => 2]);
          $levels = $this->Courseregistrations->Levels->find('list', ['limit' => 4])->order(['id'=>'ASC']);
          $subjects =  $course_assignments->Subjects->find('list', ['limit' => 200]);
         $departments =  $departments_table->find('list')->order(['name'=>'DESC']);
          // $subjects = $this->Courseregistrations->Subjects->find('list', ['limit' => 90])->where(['department_id' => $student->department_id]);
          $this->set(compact('courseregistration', 'student', 'departments', 'semesters', 'levels', 'subjects'));
        // $this->set('session_id', $sesion->session_id);
          $this->viewBuilder()->setLayout('studentsbackend');
      }

      
      
      //method that populates courses based on students department
      public function getdeptcourses($semester_id,$deptid,$levelid){
          //$student = $this->isstudent();
        $course_table = TableRegistry::get('Subjects');
      // $course_assignments = TableRegistry::get('CourseassignmentsSubjects');
        $subjects =  $course_table->find('list')
                ->where(['department_id' => $deptid,'level_id'=>$levelid,
                    'semester_id'=>$semester_id])
                ->order(['name'=>'DESC']);
       
        $this->set(compact('subjects'));
          
      }
      
      
      
      //method that ensures the student has not registered courses already for the current semester
      private function checkcourseregistraion($student_id,$level_id){
           //get the current session
          $sesion = $this->request->getSession()->read('settings');
          $coursereg =  $this->Courseregistrations->find()
                  ->where(['student_id'=>$student_id,'level_id'=>$level_id,'session_id'=>$sesion->session_id,
                      'semester_id'=>$sesion->semester_id])->first();
          if(!empty($coursereg)){
              $this->Flash->error(__('You have already registered your courses for this semester. View and print below'));
           return $this->redirect(['action' => 'registeredcourses']);
          }
          return;
          
      }








      //student method for registering courses without carry over
      private function registerfreshcourses($student_id, $department, $level_id) {
          //get the current session
          $sesion = $this->request->getSession()->read('settings');
          $coursereg_students_Table = TableRegistry::get('CourseregistrationsSubjects');
          $courseregistration = $this->Courseregistrations->newEmptyEntity();
          $courseregistration->student_id = $student_id;
          $courseregistration->session_id = $sesion->session_id;
          $courseregistration->semester_id = $sesion->semester_id;
          $courseregistration->level_id = $level_id;
         //  debug(json_encode($department, JSON_PRETTY_PRINT)); exit;
          $this->Courseregistrations->save($courseregistration);
          //save the main courses asign to his dept
          foreach ($department->subjects as $subject) {
              $coursereg = $coursereg_students_Table->newEmptyEntity();
              $coursereg->courseregistration_id = $courseregistration->id;
              $coursereg->subject_id = $subject->id;

              $coursereg_students_Table->save($coursereg);
             
          }
           $this->Flash->success(__('Your course registration was succesful.'));
           return $this->redirect(['action' => 'registeredcourses']);
      }

      //method that dispalys all courses registered by this student
      public function registeredcourses() {
          //ensure this is a valid student
          $student = $this->isstudent();
          $registeredcourses = $this->Courseregistrations->find()
                  ->contain(['Sessions', 'Levels', 'Semesters'])
                  ->where(['student_id' => $student->id]);
          $this->set(compact('registeredcourses', 'student'));
          $this->viewBuilder()->setLayout('studentsbackend');
      }

      //method that shows a teacher the students that registered for his course
      public function coursestudents() {
          $course_subjects_table = TableRegistry::get('CourseregistrationsSubjects');
          //get this teacher
          $teacherscontroller = new TeachersController();
          $teacher = $teacherscontroller->isteacher();
          $teacher_subjects = [];
          foreach ($teacher->subjects as $subject) {
              array_push($teacher_subjects, $subject->id);
          }

          $courseregistrations = $course_subjects_table->find()
                  ->contain(['Courseregistrations.Sessions', 'Courseregistrations.Semesters',
                      'Courseregistrations.Subjects', 'Courseregistrations.Students'])
                  ->where(['subject_id IN ' => $teacher_subjects])
                  ->distinct(['subject_id', 'Courseregistration_id'])
                  ->order(['Courseregistrations.date_created' => 'ASC']);
          // debug(json_encode($courseregistrations, JSON_PRETTY_PRINT)); exit;
          $this->set('teacher_subjects', $teacher_subjects);
          $this->set('courseregistrations', $courseregistrations);
          $this->viewBuilder()->setLayout('adminbackend');
      }

      //show the student that registered for this course
      public function viewregisteredstudent($id) {
          //get the current session
          $sesion = $this->request->getSession()->read('settings');
          $coursereg_students_Table = TableRegistry::get('Courseregistrations_Subjects');
          $registrations = $coursereg_students_Table->find()
                  ->contain(['Courseregistrations.Students', 'Courseregistrations.Levels',
                      'Courseregistrations.Sessions', 'Courseregistrations.Semesters'])
                  ->where(['subject_id' => $id, 'Courseregistrations.session_id' => $sesion->session_id,
              'Courseregistrations.semester_id' => $sesion->semester_id]);
//        $courseregistration = $this->Courseregistrations->get($id, [
//            'contain' => ['Students', 'Sessions', 'Semesters', 'Levels']
//        ]);
          // debug(json_encode( $sesion->semester_id, JSON_PRETTY_PRINT)); exit;
          $this->set('courseregistration', $registrations);
          $this->viewBuilder()->setLayout('adminbackend');
      }

      //shows the student his registered courses for the semester https://www.youtube.com/watch?v=j957SvQHd1c
      public function viewcourses($id) {
          //ensure this is a valid student
          $student = $this->isstudent();
          $courseregistration = $this->Courseregistrations->get($id, [
              'contain' => ['Sessions', 'Semesters', 'Levels', 'Subjects']
          ]);

          $this->set('courseregistration', $courseregistration);
          $this->set('student', $student);

          $this->viewBuilder()->setLayout('studentsbackend');
      }

      //method that ensure this person is a student
      private function isstudent() {
          $students_Table = TableRegistry::get('Students');
          $student = $students_Table->find()
                  ->contain(['Departments', 'Levels'])
                  ->where(['user_id' => $this->Auth->user('id')])->all()
                  ->last();
          if (!$student) { //this is not a valid student
              $this->Flash->error(__('Sorry, invalid access'));

              return $this->redirect(['action' => 'index']);
          } else {
              return $student;
          }
      }

      /**
       * Edit method
       *
       * @param string|null $id Courseregistration id.
       * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
       * @throws \Cake\Network\Exception\NotFoundException When record not found.
       */
      public function edit($id = null) {
          $courseregistration = $this->Courseregistrations->get($id, [
              'contain' => ['Subjects.Departments','Subjects.Teachers','Subjects.Semesters']
          ]);
            $student = $this->isstudent();
        // debug(json_encode($courseregistration, JSON_PRETTY_PRINT)); exit;
            //ensure this course registration belongs to this student
            if($courseregistration->student_id != $student->id){
             $this->Flash->error(__('Invalid access.'));

                  return $this->redirect(['controller'=>'Users','action' => 'login']);   
            }
          
           $departments_table = TableRegistry::get('Departments');
              $course_assignments = TableRegistry::get('Courseassignments'); 
              //get the current session
          $sesion = $this->request->getSession()->read('settings');
          if ($this->request->is(['patch', 'post', 'put'])) {
              $chosen_courses = $this->request->getData('subjects._ids');
              if(!empty( $chosen_courses) && $this->checkMaxUnit($chosen_courses, $student->department_id)!=0){
           $couses_added =   $this->addcourses($chosen_courses,$id); 
           $this->Flash->success(__('The course registration has been updated, '. $couses_added.' Courses added'));
            return $this->redirect(['action' => 'edit',$courseregistration->id,$courseregistration->session_id]);
              }
              else{ //the student has selected more credit unit than 35, so dont register
                $this->Flash->error(__('Sorry, you have selected more courses than your credit unit for '
                        . 'the semester allows, kindly remove some courses and try again.'));

                 return $this->redirect(['action' => 'edit',$courseregistration->id,$courseregistration->session_id]);   
                  
              }
              
        
             // $courseregistration = $this->Courseregistrations->patchEntity($courseregistration, $this->request->getData());
//              if ($this->Courseregistrations->save($courseregistration)) {
//                  $this->Flash->success(__('The course registration has been updated.'));
//
//                  return $this->redirect(['action' => 'edit',$courseregistration->id,$courseregistration->session_id]);
//              }
              $this->Flash->error(__('The course registration could not be updated. Please, try again.'));
          }
         $sessions = $this->Courseregistrations->Sessions->find('list', ['limit' => 10])->order(['name'=>'DESC']);
          $semesters = $this->Courseregistrations->Semesters->find('list', ['limit' => 2]);
          $levels = $this->Courseregistrations->Levels->find('list', ['limit' => 4])->order(['id'=>'ASC']);
          $subjects =  $course_assignments->Subjects->find('list', ['limit' => 200]);
         $departments =  $departments_table->find('list')->order(['name'=>'ASC']);
          // $subjects = $this->Courseregistrations->Subjects->find('list', ['limit' => 90])->where(['department_id' => $student->department_id]);
          $this->set(compact('courseregistration','sessions', 'student', 'departments', 'semesters', 'levels', 'subjects'));
         $this->set('session_id', $sesion->session_id);
           $this->viewBuilder()->setLayout('studentsbackend');
          }
          
          
          
          //method that adds courses chosen by the student
          private function addcourses($courseids,$coursereg_id){
             $coursereg_subject_table =  TableRegistry::get('CourseregistrationsSubjects'); 
              $count = 0;
            if (!empty($courseids)) {
                foreach ($courseids as $course_id) {
                    if (is_numeric($course_id)) {
                        //check that course has not been registered before
                        If($this->checkCourse($coursereg_id,$course_id)==1){
                        $coursereg =  $coursereg_subject_table->newEmptyEntity();
                      $coursereg->courseregistration_id = $coursereg_id;
                      $coursereg->subject_id = $course_id;
                      $coursereg_subject_table->save($coursereg);

                        $count++;
                        //echo "value : " . $value . '<br/>';    
                    }
                    }
                }
            }
            return $count;
           
          }
          
          
          //check that a particular course has not been registred by this student
          private function checkCourse($coursereg_id,$course_id){
                $coursereg_subject_table =  TableRegistry::get('CourseregistrationsSubjects'); 
                $registered_course = $coursereg_subject_table->find()
                        ->where(['courseregistration_id'=>$coursereg_id,'subject_id'=>$course_id])->first();
                if(!empty($registered_course->subject_id)){
                    return 0; //already registered, do not register the course again
                }else{ //register course
                    return 1;
                    
                }
              
          }
          
          
        //method that checks for carryover by checking already registered courses 
        public function checkCarryOver($studentid,$courseids){
                $coursereg_subject_table =  TableRegistry::get('CourseregistrationsSubjects'); 
                $registered_courses = $coursereg_subject_table->find()->contain(['Courseregistrations'])
                        ->where(['Courseregistrations.student_id'=>$studentid,'subject_id IN'=>$courseids])->first();
                if(!empty($registered_courses->subject_id)){
                    return 0; //already registered, this is carry over and has to pay
                }else{ //register course
                    return 1;
                    
                }
            
        }

        
        //method that checks if the student has paid for carryover courses for the current session
        public function checkCarryOverFee($student_id){
             $session = $this->request->getSession()->read('settings');
             $transactions_table =  TableRegistry::get('Transactions'); 
             $fees_table =  TableRegistry::get('Fees'); 
             $invoices_table =  TableRegistry::get('Invoices');
             $transaction =  $transactions_table->find()->contain(['Students'])
                     ->where(['student_id'=>$student_id,'fee_id'=>12,'Transactions.session_id'=>$session->session_id])->last(); 
             if(!empty($transaction->id) && ($transaction->paystatus=="completed")){
                 //payment already made
                 return 0;
                 
             }elseif(!empty($transaction->id) && ($transaction->paystatus!="completed")){
                 //has invoice but yet to pay
                 return 1;
             }
             else{
                    $fee =  $fees_table->get(12);
                 //no payment record so create transaction data
                 //create invoice
                $invoice = $invoices_table->newEmptyEntity();
                $invoice->student_id = $student_id;
                $invoice->fee_id = $fee->id;
                $invoice->amount = $fee->amount;
                $invoice->session_id = $session->session_id;
                $invoice->invoiceid = "IMOPOLY/" . $fee->id . '/' . $student_id;
                $invoices_table->save($invoice);
                 
              //create transaction data
                $transaction =  $transactions_table->newEmptyEntity();
                $transaction->payref = strtoupper(uniqid('ImoPoly')) . date('dmHis');
                $transaction->transdate = date('Y-m-d H:i');
               $transaction->amount =  $fee->amount;
                $transaction->student_id = $student_id;
                $transaction->paystatus = "initialized";
                $transaction->session_id = $session->session_id;
               // $transaction->semester_id = $session->semester_id;
                $transaction->gresponse = "initialized"; //the order id
                $transaction->fee_id = $fee->id;
                $transaction->invoice_id = $invoice->id;
                 $transactions_table->save($transaction);
                 return 1;
                
             }
             
            
        }





        //check that max unit is not exceeded
          private function checkMaxUnit($selectedcourses,$dept_id){
           $reg_unit =   $this->request->getSession()->read('creditload_registered');
        
          $subject_table =  TableRegistry::get('Subjects'); 
           $departments_table =  TableRegistry::get('Departments');
          $chosen_units = 0;
           foreach ($selectedcourses as $course_id) {
                    if (is_numeric($course_id)) {
                        
                      $course =  $subject_table->get($course_id);
                      $chosen_units +=$course->creditload;    
                    }
                      
          
          }
          // echo $chosen_units; exit;
          $department = $departments_table->get($dept_id);
           $max_unit =  $department->maxunit;
          $total_unit = $chosen_units;
          if( $total_unit > $max_unit){ 
            // echo 'max '.$max_unit.' - '.$total_unit; exit;  
              return 0;}else{return 1;}
          
         // return $reg_unit+$chosen_units;
          }







          /**
       * Delete method
       *
       * @param string|null $id Courseregistration id.
       * @return \Cake\Http\Response|null Redirects to index.
       * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
       */
      public function delete($id = null) {
          $this->request->allowMethod(['post', 'delete']);
          $courseregistration = $this->Courseregistrations->get($id);
          if ($this->Courseregistrations->delete($courseregistration)) {
              $this->Flash->success(__('The course registration data has been deleted.'));
              $this->courseregdeletealert( $courseregistration->student_id);
          } else {
              $this->Flash->error(__('The course registration data could not be deleted. Please, try again.'));
          }

          return $this->redirect(['controller'=>'Admins','action' => 'viewregisteredstudents']);
      }
      
      
      
      //student method for deleting registered courses
        public function deletecourses($id = null) {
          $this->request->allowMethod(['post', 'delete']);
          $courseregistration = $this->Courseregistrations->get($id);
          if ($this->Courseregistrations->delete($courseregistration)) {
              $this->Flash->success(__('The course registration data has been deleted.'));
             // $this->courseregdeletealert( $courseregistration->student_id);
          } else {
              $this->Flash->error(__('The course registration data could not be deleted. Please, try again.'));
          }

          return $this->redirect(['controller'=>'Courseregistrations','action' => 'register']);
      }
      
      
      
      //method that sends a mail to the student when their course registration is deleted
      //update email alert
    private function courseregdeletealert($student_id) {
        //get student data
         $students_Table = TableRegistry::get('Students');
          $student =  $students_Table->get($student_id);
        $message = "<br />Hello! ".$student->fname.' '.$student->lname ." <br /> We wish to inform you that your Course registration was not approved."
                . " This is because you registered more courses that your total credit "
                . "load for the semester. Kindly find our your total credit load for this"
                . " semester and register the appropriate number of courses. <br />";

        $email = new Mailer('default');
        $email->setFrom(['supportfess@imopoly.net' => SCHOOL]);
        $email->setTo($student->email);
        $email->setBcc(['chukwudi.aniegboka@netpro.africa']);
        $email->setEmailFormat('html');
        $email->setSubject('Course Registration Deleted');
        if ($email->deliver($message)) {
            $this->Flash->success('Email with appropriate instructions have been sent to (' . $student->email . ')');
        } else {
            $this->Flash->error('Oh!, sorry, We are unable to send mail.');
        }
        return;
    }
    
    
    //method that populates courses based on department
    public function getcoursesindept($deptid){
        $course_table = TableRegistry::get('Subjects');
        $subjects =  $course_table->find('list')
                ->where(['department_id' => $deptid])
                ->order(['name'=>'DESC']);
        $this->set(compact('subjects'));
        
    }
    
    //method that gets courses based on department and level
    public function getdeptcoursesindeptandlevel($deptid,$levelid){
      $course_table = TableRegistry::get('Subjects');
        $subjects =  $course_table->find('list')
                ->where(['department_id' => $deptid,'level_id'=>$levelid])
                ->order(['name'=>'DESC']);
        $this->set(compact('subjects'));  
    }

  }
  