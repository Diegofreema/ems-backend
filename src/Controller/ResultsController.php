<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper;
use App\Controller\AppController;
use \Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Results Controller
 *
 * @property \App\Model\Table\ResultsTable $Results
 *
 * @method \App\Model\Entity\Result[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ResultsController extends AppController {

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('FormProtection');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function manageresults() {
         $sets_table = TableRegistry::get('Sets');
        //if this was a search
        if ($this->request->is('post')) {
            $departments_table = TableRegistry::get('Departments');
           
            $department_id = $this->request->getData('department_id');
            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');
          
            $student_id = $this->request->getData('student_id');
            //get a student in the chosen level and use for filtering carry over
           // $set = $sets_table->get($set_id);
             $this->set('semester_id', $semester_id);
          
         // debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;

            $conditions = []; $carry_over = [];
            if (!empty($department_id)) {
                $conditions['Results.department_id'] = $department_id;
               // $carry_over['Results.department_id'] = $department_id;
                $deptmt = $departments_table->get($department_id);
                $this->set('deptmt', $deptmt);
            }
            
            if (!empty($course_id)) {
                $conditions['Results.subject_id'] = $course_id;
               // $carry_over['Results.subject_id'] = $course_id;
            }
            if (!empty($student_id)) {
                $conditions['Results.student_id'] = $student_id;
              //  $carry_over['Results.subject_id'] = $course_id;
            }
            if (!empty($session_id)) {
                $conditions['Results.session_id'] = $session_id;
               // $carry_over['Results.session_id'] = $session_id;
            }
            if (!empty($semester_id)) {
                $conditions['Results.semester_id'] = $semester_id;
               // $carry_over['Results.semester_id'] = $semester_id;
            }


            
            $courses = $this->Results->find()
                    ->distinct(['subject_id'])
                    ->contain(['Subjects', 'Departments', 'Sessions', 'Semesters'])
                    ->where($conditions)
                     ->where(['Results.iscarryover'=>'no'])
                    ->order(['Results.subject_id' => 'DESC']);
            //get the total unit for the previous semester
            // $past_tnp = $this->gettotalunitpast($semester_id, $session_id,$department_id);
            //  $this->set('past_tnp', $past_tnp);
         


            $results = $this->Results->find()
                    ->contain(['Students.ClassArms', 'Departments', 'Subjects', 'Semesters', 'Sessions', 'Users'])
                    ->where($conditions);

            $this->set('results', $results);
            // $this->set('dresults', $dresults);
            $this->set('courses', $courses);
        } 
        
        // else { //if this was not a search
        //     $results = $this->Results->find()
        //             ->contain(['Students', 'Departments', 'Subjects', 'Semesters', 'Sessions', 'Users'])
        //             ->limit(50);

        //     // $results = $this->paginate($this->Results);

        //     $this->set(compact('results'));
        // }

        $departments = $this->Results->Departments->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $students = $this->Results->Students->find('list', ['limit' => 2000]);
        $this->set(compact('students', 'departments', 'subjects', 'semesters', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
    }
    
    
    
    
    //admin method for managing nursing results due to their different calculations methods
       public function nursingresults() {
         $sets_table = TableRegistry::get('Sets');
        //if this was a search
        if ($this->request->is('post')) {
            $departments_table = TableRegistry::get('Departments');
           
            $department_id = $this->request->getData('department_id');
            $faculty_id = $this->request->getData('faculty_id');
            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');
          
            $student_id = $this->request->getData('student_id');
            $level_id = $this->request->getData('level_id');
            //get a student in the chosen level and use for filtering carry over
           // $set = $sets_table->get($set_id);
             $this->set('semester_id', $semester_id);
          
         // debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;

            $conditions = []; $carry_over = [];
            if (!empty($department_id)) {
                $conditions['Results.department_id'] = $department_id;
               // $carry_over['Results.department_id'] = $department_id;
                $deptmt = $departments_table->get($department_id);
                $this->set('deptmt', $deptmt);
            }
            if (!empty($faculty_id)) {
                $conditions['Results.faculty_id'] = $faculty_id;
              // $carry_over['Results.faculty_id'] = $faculty_id;
            }
            if (!empty($course_id)) {
                $conditions['Results.subject_id'] = $course_id;
               // $carry_over['Results.subject_id'] = $course_id;
            }
            if (!empty($student_id)) {
                $conditions['Results.student_id'] = $student_id;
              //  $carry_over['Results.subject_id'] = $course_id;
            }
            if (!empty($session_id)) {
                $conditions['Results.session_id'] = $session_id;
               // $carry_over['Results.session_id'] = $session_id;
            }
            if (!empty($semester_id)) {
                $conditions['Results.semester_id'] = $semester_id;
               // $carry_over['Results.semester_id'] = $semester_id;
            }
            if (!empty($level_id)) {
                $conditions['Results.level_id'] = $level_id;
                $this->request->getSession()->write('dlevelid', $level_id);
            }

            
            $courses = $this->Results->find()
                    ->distinct(['subject_id'])
                    ->contain(['Subjects', 'Departments', 'Levels', 'Faculties', 'Sessions', 'Semesters'])
                    ->where($conditions)
                     ->where(['Results.iscarryover'=>'no'])
                    ->order(['Results.subject_id' => 'DESC']);
            //get the total unit for the previous semester
            $past_tnp = $this->gettotalunitpast($semester_id, $level_id, $session_id,$department_id);
             $this->set('past_tnp', $past_tnp);
         $carryover_courses = $this->Results->find()
                    ->distinct(['subject_id'])
                    ->contain(['Subjects', 'Departments', 'Levels', 'Faculties', 'Sessions', 'Semesters'])
                    ->where($conditions)
                 ->where(['Results.iscarryover'=>'yes'])
                    ->order(['Results.subject_id' => 'DESC']);

            // $students_Table = TableRegistry::get('Students');
            $dstudents = $this->Results->find()->contain(['Students'])->distinct(['Students.id'])
                     ->where($conditions)
                    ->where([//'Results.level_id' => $level_id, 'Results.department_id' => $department_id,
                        'Students.status' => 'Admitted'])
                    ->order(['Students.fname' => 'DESC']);
             $carryover_students = $this->Results->find()->contain(['Students'])->distinct(['Students.id'])
                    ->where(['Results.level_id' => $level_id, 'Results.department_id' => $department_id,
                        'Students.status' => 'Admitted', 'Results.semester_id'=>$semester_id, 'Results.session_id' => $session_id,'Results.iscarryover'=>'yes'])
                    ->order(['Students.fname' => 'DESC']);

//            $dstudents = $students_Table->find()
//                    //->distinct(['Students.id'])
//                   // ->contain(['Results'])
//                    ->where(['level_id'=>$level_id,'department_id'=>$department_id,'status'=>'Admitted'])
//                    ->order(['Students.fname'=>'DESC']);
            $this->set('dstudents', $dstudents);
            $this->set('carryover_students', $carryover_students);
            $this->set('carryover_courses', $carryover_courses);
            //  debug(json_encode( $students, JSON_PRETTY_PRINT)); exit;


            $results = $this->Results->find()
                    ->contain(['Students.ClassArms', 'Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions', 'Users', 'Levels'])
                    ->where($conditions);

            $this->set('results', $results);
            // $this->set('dresults', $dresults);
            $this->set('courses', $courses);
        } else { //if this was not a search
            $results = $this->Results->find()
                    ->contain(['Students.ClassArms', 'Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions', 'Users', 'Levels'])
                    ->limit(50);

            // $results = $this->paginate($this->Results);

            $this->set(compact('results'));
        }

        $faculties = $this->Results->Faculties->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $departments = $this->Results->Departments->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $levels = $this->Results->Levels->find('list', ['limit' => 9])
                ->order(['name' => 'ASC']);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $students = $this->Results->Students->find('list', ['limit' => 2000]);
        $this->set(compact('results', 'levels','students', 'faculties', 'departments', 'subjects', 'semesters', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
    }

    
    
    //method that gets the total unit for previous semester on the manage results page for calculating past result
    public function gettotalunitpast($semesterid, $levelid, $sessionid,$depatmentid){
         //retriev chosen level from session
     $chosen_levelid =  $levelid; $past_tnp = 0;
         $lev = 0; $semid = 0; $sessid = 0;
         if($semesterid == 2){
         $semid = 1; $lev = $chosen_levelid; $sessid =  $sessionid;
         }else{ $lev = $chosen_levelid-1; $semid = 2; $sessid =  $sessionid-1; }
     $courses = $this->Results->find()->contain(['Subjects'])
              ->distinct(['subject_id'])
             ->where(['Results.department_id'=>$depatmentid,'iscarryover'=>'no',
         'Results.level_id'=>$lev,'Results.semester_id'=>$semid,'session_id'=>$sessid])
             ->order(['Results.subject_id'=>'DESC'])->all();
     foreach($courses as $subject){
         $past_tnp += $subject->subject->creditload;
     }
    // debug(json_encode(  $sresult, JSON_PRETTY_PRINT)); exit;
     return  $past_tnp;
        
    }
    
    
    
    /**
     * View method
     *
     * @param string|null $id Result id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $result = $this->Results->get($id, [
            'contain' => ['Students', 'Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions', 'Users', 'Levels']
        ]);

        $this->set('result', $result);
        $this->viewBuilder()->setLayout('backend');
    }

    //admin and teacher method for result bulk upload
    public function uploadresults() {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $department_id = $this->request->getData('department_id');
            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');

            // Validate that all required fields are selected
            if (empty($department_id) || empty($session_id) || empty($semester_id) || empty($course_id)) {
                $this->Flash->error(__('Please select Class, Subject, Term, and Session before uploading.'));
                return $this->redirect(['action' => 'uploadresults']);
            }

            $filename = $this->request->getData('result');
            
            // Check if file was uploaded
            if (!$filename || $filename->getError() !== 0) {
                $this->Flash->error(__('Please select a CSV or Excel file to upload.'));
                return $this->redirect(['action' => 'uploadresults']);
            }
            $name = $filename->getClientFilename();
            $tmpName = $filename->getStream()->getMetadata('uri');
            $type = $filename->getClientMediaType();
            $error = $filename->getError();
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            // echo $ext; exit;
            $allowedext = ['csv', 'xlsx'];
            if ($error != 0) {
                $this->Flash->error(__('Sorry, there is a problem with the file,only csv or xlsx files can be uploaded. Please check and try again'));

                return $this->redirect(['action' => 'uploadresults']);
            }
            if (!in_array($ext, $allowedext)) {
                $this->Flash->error(__('Sorry, only csv or xlsx files can be uploaded.'));

                return $this->redirect(['action' => 'uploadresults']);
            } else {
                $helper = new Helper\Sample();
                //  debug($helper);
                $spreadsheet = IOFactory::load($tmpName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                  $count = 0;
                  $inserted = 0;
                  $duplicate_results = 0;
                  $unknown_students = 0;
                  $skipped_rows = 0;
                  
                  foreach ($sheetData as $data) {

                      $count++;

                      if ($count > 1) { 
                          // Detect CSV format and process accordingly
                          // Format 1 (Migration): A=RegNo, B=Total, C=Grade, D=Remark, E=Subject, F=Department, G=Semester, H=Session, I=CA, J=Score
                          // Format 2 (Teacher): A=RegNo, B=CA, C=1st Exam, D=2nd Exam, E=3rd Exam
                          
                          // Skip empty rows (no regno)
                          if (empty($data['A']) || trim($data['A']) == '') {
                              continue;
                          }
                         
                          $isFormat1 = !empty($data['A']) && isset($data['I']) && isset($data['J']); // Migration format has columns I and J
                          $isFormat2 = !empty($data['A']) && isset($data['B']) && isset($data['C']) && isset($data['D']) && isset($data['E']); // Teacher format - use isset() to allow 0 values
                          
                          if ($isFormat1 || $isFormat2) {

                              //get the student and ensure no double result
                              $student =  $this->Results->Students->find()->where(['regno' => trim($data['A'])])->first();
 
                              if ($student) {

                                  $has_result =  $this->Results->find()->where(['regno' => $data['A'],
                                      'department_id' => $department_id, 'subject_id' => $course_id, 'semester_id' => $semester_id, 'session_id' => $session_id]);
                                  
                                  // Check if there's an existing result that's not rejected
                                  $existing_result = $has_result->first();
                                  if (empty($existing_result) || (isset($existing_result->approval_status) && $existing_result->approval_status == 'rejected')) {
                                      // If there's a rejected result, update it; otherwise create new
                                      if ($existing_result && isset($existing_result->approval_status) && $existing_result->approval_status == 'rejected') {
                                          $result = $existing_result; // Update existing rejected result
                                      } else {
                                          $result = $this->Results->newEmptyEntity(); // Create new result
                                      }
                                   // echo $data['A']; exit;   
                                      $result->regno = $data['A'];
                                      $result->student_id = $student->id;
                                      $result->faculty_id = null; 
                                      $result->department_id = $department_id;
                                      $result->class_arm_id = $student->class_arm_id; // Use student's class_arm_id
                                      $result->level_id = null; 
                                      $result->semester_id = $semester_id;
                                      $result->subject_id = $course_id;
                                      $result->session_id = $session_id;
                                      $result->user_id = $this->Auth->user('id');
                                      
                                      if ($isFormat1) {
                                          // Migration CSV format: A=RegNo, B=Total, C=Grade, D=Remark, I=CA, J=Score
                                          $result->ca = isset($data['I']) ? (float)$data['I'] : 0;
                                          $result->score = isset($data['J']) ? (float)$data['J'] : 0;
                                          $result->total = isset($data['B']) ? (float)$data['B'] : 0;
                                          $result->grade = isset($data['C']) && $data['C'] !== null ? $data['C'] : '';
                                          $result->remark = isset($data['D']) && $data['D'] !== null ? $data['D'] : 'Uploaded';
                                          $result->approval_status = 'approved'; // Auto-approve admin uploads
                                      } else {
                                          // Teacher format: A=RegNo, B=CA, C=1st Exam, D=2nd Exam, E=3rd Exam
                                          $result->ca = isset($data['B']) ? (float)$data['B'] : 0;
                                          $result->first_exam = isset($data['C']) ? (float)$data['C'] : 0;
                                          $result->second_exam = isset($data['D']) ? (float)$data['D'] : 0;
                                          $result->third_exam = isset($data['E']) ? (float)$data['E'] : 0;
                                          
                                          // Calculate total
                                          $result->total = $result->ca + $result->first_exam + $result->second_exam + $result->third_exam;
                                          $result->score = $result->first_exam + $result->second_exam + $result->third_exam;
                                          
                                          // Calculate grade
                                          if ($result->total >= 70) {
                                              $result->grade = "A";
                                          } elseif ($result->total >= 60) {
                                              $result->grade = "B";
                                          } elseif ($result->total >= 50) {
                                              $result->grade = "C";
                                          } elseif ($result->total >= 45) {
                                              $result->grade = "D";
                                          } elseif ($result->total >= 40) {
                                              $result->grade = "E";
                                          } else {
                                              $result->grade = "F";
                                          }
                                          $result->approval_status = 'pending'; // Require approval for teacher format
                                      }
                                      
                                       if ($this->Results->save($result)) {
                                           $inserted++;
                                       } else {
                                           // Log validation errors
                                           $errors = $result->getErrors();
                                           debug('Save failed for student: ' . $data['A']);
                                           debug($errors);
                                       }
                                  } else {
                                      $duplicate_results++;
                                  }
                              } else {
                                  $unknown_students++;
                              }
                          } else {
                              // Row has incomplete data
                              $skipped_rows++;
                          }
                      }
                  }
                  
                //log activity
                $usercontroller = new UsersController();

                $title = "Result Bulk Upload ";
                $user_id = $this->Auth->user('id');
                $description = "Uploaded " . $inserted . ' results';
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                
                // Build detailed flash message
                $message = $inserted . ' Result(s) uploaded successfully!';
                if ($duplicate_results > 0) {
                    $message .= ' | Duplicates: ' . $duplicate_results;
                }
                if ($unknown_students > 0) {
                    $message .= ' | Unknown students: ' . $unknown_students;
                }
                if ($skipped_rows > 0) {
                    $message .= ' | Skipped rows (incomplete data): ' . $skipped_rows;
                }
                
                if ($inserted > 0) {
                    $this->Flash->success(__($message));
                } else {
                    $this->Flash->error(__('No results were uploaded. Please check your CSV file format. Expected format: Column A=RegNo, B=CA, C=1st Exam, D=2nd Exam, E=3rd Exam'));
                }

                return $this->redirect(['action' => 'uploadresults']);
            }
        }

        // $faculties = $this->Results->Faculties->find('list', ['limit' => 200])
        //         ->order(['name' => 'ASC']);
        $departments = $this->Results->Departments->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        
        // Start with empty subjects - will be loaded via AJAX when class is selected
        $subjects = ['' => 'Select Class First'];

        // $levels = $this->Results->Levels->find('list', ['limit' => 200])->where(['id !=' => 5])->order(['name' => 'ASC']);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 20]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $this->viewBuilder()->setLayout('backend');
        $this->set(compact('departments', 'subjects', 'semesters', 'sessions'));
    }

    //admin result upload based on courses
    public function uploadcourseresults() {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $faculty_id = $this->request->getData('faculty_id');
            //  $department_id = $this->request->getData('department_id');
            $level_id = $this->request->getData('level_id');
            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');

            $filename = $this->request->getData('result');
            $name = $filename->getClientFilename();
            $tmpName = $filename->getStream()->getMetadata('uri');
            $type = $filename->getClientMediaType();
            $error = $filename->getError();
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            // echo $ext; exit;
            $allowedext = ['csv', 'xlsx'];
            if ($error != 0) {
                $this->Flash->error(__('Sorry, there is a problem with the file,only csv or xlsx files can be uploaded. Please check and try again'));

                return $this->redirect(['action' => 'uploadcourseresults']);
            }
            if (!in_array($ext, $allowedext)) {
                $this->Flash->error(__('Sorry, only csv or xlsx files can be uploaded.'));

                return $this->redirect(['action' => 'uploadcourseresults']);
            } else {
                $helper = new Helper\Sample();
                //  debug($helper);
                $spreadsheet = IOFactory::load($tmpName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $count = 0;
                $inserted = 0;
                $duplicate_results = 0;
                $unknown_students = 0;
                //note students with old results
                $regnos = "";
                foreach ($sheetData as $data) {

                    $count++;

                    if ($count > 1) {
                        
                        //   $level = $this->Results->Levels->get($level_id);
                        //   $department = $this->Results->Departments->get($department_id);
                        //   $semester = $this->Results->Semesters->get($semester_id);
                        //  $course = $this->Results->Subjects->get($course_id);
                        //  $session = $this->Results->Sessions->get($session_id);
                        $dept_name = trim($data['G']);
                        $student_dept = $this->Results->Departments->find()->where(['name' => $dept_name])->first();
                        
                        $subjectname = trim($data['F']);
                        $student_subject = $this->Results->Subjects->find()
                                        ->where(['name' => $subjectname, 'department_id' => $student_dept->id,
                                            'semester_id' => $semester_id, 'level_id' => $level_id])->first();
                         //debug(json_encode( $student_subject, JSON_PRETTY_PRINT)); exit;

//               echo strtolower(trim($semester->name)) .' '. strtolower(trim($data['H'])).'<br />';
//              echo strtolower(trim($department->name)) .' '. strtolower(trim($data['G'])).'<br />';
//             echo  strtolower(trim($level->name)).' '. strtolower(trim($data['J'])).'<br />';
//             echo  strtolower(trim($course->name)) .' '. strtolower(trim($data['F'])).'<br />';
//            echo   strtolower(trim($session->name)) .' '. strtolower(trim($data['I'])).'<br />';
//            exit;
                        if ((!empty($student_dept->id)) && (!empty($student_subject->id))) {

                            //get the student and ensure no double result
                            //  debug(json_encode( $data, JSON_PRETTY_PRINT)); exit;
                            $student = $this->Results->Students->find()->where(['regno' => $data['A']])->first();
                            //ensure no result for this course already

                            if ($student) {
                                //note students with old results
                                $has_result = $this->Results->find()->where(['regno' => $data['A'],
                                            'department_id' => $student_dept->id, 'subject_id' =>  $student_subject->id, 'semester_id' => $semester_id, 'session_id' => $session_id])->first();

                                if (empty($has_result) && !empty($data['A'])) {

                                    //create a new result for this student
                                    $result = $this->Results->newEmptyEntity();
                                    $result->regno = $data['A'];
                                    $result->level_id = $level_id;
                                    $result->student_id = $student->id;
                                    $result->faculty_id = $student->faculty_id;
                                    // $result->ca = $data['C'];
                                    $result->total = $data['B'] + $data['C'];
                                    $result->department_id = $student_dept->id;
                                    $result->ca = $data['C'];
                                    $result->semester_id = $semester_id;
                                    $result->subject_id = $student_subject->id;
                                    $result->session_id = $session_id;
                                    $result->creditload = $student_subject->creditload;
                                    $result->score = $data['B'];
                                    $result->grade = $this->getgrade($data['B'] + $data['C']);
                                    $result->user_id = $this->Auth->user('id');
                                    //  debug(json_encode($result, JSON_PRETTY_PRINT)); exit;
                                    $this->Results->save($result);
                                    $inserted++;
                                } else {
                                    $duplicate_results++;
                                    $regnos .= '-' . $has_result->regno;
                                    $this->Flash->error('Total results uploaded : ' . $inserted . ' Some results failed to upload because selected data didnt match provided data. Please ensure the right department,'
                                            . 'course, session and faculty was selected. Duplicate results found : ' . $duplicate_results . ' (' . $regnos . ')'
                                            . ' Unknown students : ' . $unknown_students);
                                    return $this->redirect(['action' => 'uploadcourseresults']);
                                }
                            } else {
                                $unknown_students++;
                            }
                        } else {
                            $this->Flash->error('Total results uploaded : ' . $inserted . ' Some results failed to upload because selected data didnt match provided data. Please ensure the right department,'
                                    . 'course, session and faculty was selected. Duplicate results found : ' . $duplicate_results . ' (' . $regnos . ')'
                                    . ' Unknown students : ' . $unknown_students);
                            return $this->redirect(['action' => 'uploadcourseresults']);
                        }
                    }
                }
                //log activity
                $usercontroller = new UsersController();

                $title = "Result Bulk Upload ";
                $user_id = $this->Auth->user('id');
                $description = "Uploaded " . $inserted . ' results';
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__($inserted . ' Result(s) have been uploaded successfully. Duplicates found : ' . $duplicate_results . ' (' . $regnos . ')' . ' Unknown students : ' . $unknown_students));

                return $this->redirect(['action' => 'uploadcourseresults']);
            }
        }

        $faculties = $this->Results->Faculties->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $departments = $this->Results->Departments->find('list', ['limit' => 200])
                ->order(['name' => 'ASC']);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 700])
                ->order(['id' => 'ASC']);

        $levels = $this->Results->Levels->find('list', ['limit' => 200])->where(['id !=' => 5])->order(['name' => 'ASC']);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 20]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $this->viewBuilder()->setLayout('backend');
        $this->set(compact('faculties', 'departments', 'subjects', 'semesters', 'sessions', 'levels'));
    }

    //method that gets a subject and return the credit unit for result upload
    public function getcreditunit($subjectid) {
        $subjects_table = TableRegistry::get('Subjects');
        $subject = $subjects_table->get($subjectid);
        return $subject->creditload;
    }

    //method that returns grade based on the score
    public function getgrade($total) {
        $grade = "";
        if ($total >= 70) {
            $grade = "A";
        } elseif ($total >= 60) {
            $grade = "B";
        } elseif ($total >= 50) {
            $grade = "C";
        } elseif ($total >= 45) {
            $grade = "D";
        } elseif ($total >= 40) {
            $grade = "E";
        } elseif ($total <= 39) {
            $grade = "F";
        }

        return $grade;
    }

    //method that downloads the result file format 
    public function downloadformat() {
        // Download the new CSV format
        $filename = "result_format_new.csv";
        $filepath = "cvs/result_format_new.csv";
        
        // Check if file exists
        if (!file_exists($filepath)) {
            $this->Flash->error(__('Result format file not found. Please contact administrator.'));
            return $this->redirect(['controller' => 'Teachers', 'action' => 'uploadresults']);
        }
        
        // Use CakePHP response handling
        $this->response = $this->response->withType('csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Cache-Control', 'private');
        
        // Read and output file content
        $fileContent = file_get_contents($filepath);
        $this->response = $this->response->withStringBody($fileContent);
        
        return $this->response;
    }

    //admin method for generating transcript
    public function gettranscript($student_id) {
        //check for any pending carry over
        $check_standing = $this->checkstanding($student_id);
        //get last result session for graduation year
        $graduation_years = $this->getlastresultsession($student_id);
        $students_table = TableRegistry::get('Students');
        $student = $students_table->get($student_id, ['contain' => ['Departments', 'Programmes', 'Users', 'Countries', 'States', 'Faculties']]);
        // debug(json_encode(  $student, JSON_PRETTY_PRINT)); exit; 


        $year1 = $this->Results->find()->contain(['Subjects', 'Sessions'])->where(['student_id' => $student_id, 'Results.level_id' => 1]);
        $year2 = $this->Results->find()->contain(['Subjects', 'Sessions'])->where(['student_id' => $student_id, 'Results.level_id' => 2]);
        $year3 = $this->Results->find()->contain(['Subjects', 'Sessions'])->where(['student_id' => $student_id, 'Results.level_id' => 3]);
        $year4 = $this->Results->find()->contain(['Subjects', 'Sessions'])->where(['student_id' => $student_id, 'Results.level_id' => 4]);
        // debug(json_encode( $year3, JSON_PRETTY_PRINT)); exit; 
        $this->set(compact('year1', 'year2', 'year3', 'year4', 'student', 'graduation_years'));
        $this->viewBuilder()->setLayout('backend');
    }

    //admin method that check if a student is cleared for transcript
    public function checkstanding($student_id) {
        $check_standing = $this->Results->find()->where(['student_id' => $student_id, 'grade' => "F"]);

        if (!empty($check_standing)) {

            //check if carryover has been written
            foreach ($check_standing as $result) {
                $this->verifycarryover($result->student_id, $result->subject_id, $result->session_id);
            }
        }
    }

    //admin method that checks if a carryover has been passed by the student
    private function verifycarryover($student_id, $subject_id, $failed_session_id) {
        $passed_carryover = $this->Results->find()->where(['student_id' => $student_id,
            'grade !=' => "F", 'subject_id' => $subject_id, 'session_id !=' => $failed_session_id]);

        if (empty($passed_carryover->toArray())) {
            //debug(json_encode(  $passed_carryover, JSON_PRETTY_PRINT)); exit;
            //has some uncleared carryover
            $this->Flash->error(__('This student is not on clear standings. Has some CARRYOVER COURSES UNCLEARED.'));
            return $this->redirect(['controller' => 'Admins', 'action' => 'managetranscriptorders']);
        } else {
            return;
        }
    }

    //admin method that returns the graduation year based on last entered result session
    public function getlastresultsession($student_id) {
        $last_result = $this->Results->find()->contain(['Sessions'])
                        ->where(['student_id' => $student_id])
                        ->order(['session_id' => 'DESC'])
                        ->limit(1)->last();

        $last_session = $last_result->session->name;
        $syears = explode("/", $last_session);
        return $syears[0];
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $result = $this->Results->newEmptyEntity();
        if ($this->request->is('post')) {
            $result = $this->Results->patchEntity($result, $this->request->getData());
            if ($this->Results->save($result)) {
                $this->Flash->success(__('The result has been saved.'));

                return $this->redirect(['action' => 'manageresults']);
            }
            $this->Flash->error(__('The result could not be saved. Please, try again.'));
        }
        $students = $this->Results->Students->find('list', ['limit' => 200]);
        $faculties = $this->Results->Faculties->find('list', ['limit' => 200]);
        $departments = $this->Results->Departments->find('list', ['limit' => 200]);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 2000])->order(['name' => 'ASC']);;
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $users = $this->Results->Users->find('list', ['limit' => 200]);
        $this->set(compact('result', 'students', 'faculties', 'departments', 'subjects', 'semesters', 'sessions', 'users'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Result id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updateresult($id = null) {
        $result = $this->Results->get($id, [
            'contain' => ['Students', 'Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ca = $this->request->getData('ca');
            $score = $this->request->getData('score');
            $total = $ca + $score;
            if ($total >= 70) {
                $grade = "A";
            } elseif ($total >= 60) {
                $grade = "B";
            } elseif ($total >= 50) {
                $grade = "C";
            } elseif ($total >= 45) {
                $grade = "D";
            } elseif ($total >= 40) {
                $grade = "E";
            } elseif ($total <= 39) {
                $grade = "F";
            }
            $result = $this->Results->patchEntity($result, $this->request->getData());
            $result->total = $total;
            $result->grade = $grade;
            if ($this->Results->save($result)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Updated a Result ";
                $user_id = $this->Auth->user('id');
                $description = "Updated a result " . $result->id;
                $ip = $this->request->clientIp();
                $type = "Update";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The result has been updated.'));

                return $this->redirect(['action' => 'manageresults']);
            }
            $this->Flash->error(__('The result could not be saved. Please, try again.'));
        }
        $levels = $this->Results->Levels->find('list', ['limit' => 20]);
        $students = $this->Results->Students->find('list', ['limit' => 200]);
        $faculties = $this->Results->Faculties->find('list', ['limit' => 200]);
        $departments = $this->Results->Departments->find('list', ['limit' => 200]);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 2000])->order(['name' => 'ASC']);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $users = $this->Results->Users->find('list', ['limit' => 200]);
        $this->set(compact('result','levels', 'students', 'faculties', 'departments', 'subjects', 'semesters', 'sessions', 'users'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that gets a list of all departments in a faculty and puts them in a dropdown
    public function getdepartments($faculty_id) {
        $this->viewBuilder()->setLayout('ajax');
        $departments = $this->Results->Departments->find('list', ['limit' => 200])
                ->where(['faculty_id' => $faculty_id])
                ->order(['name' => 'ASC']);
        $this->set(compact('departments'));
    }

    //method that gets a list of all subjects in a department and puts them in a dropdown
    public function getsubjectsbydepartment($department_id) {
        $this->viewBuilder()->setLayout('ajax');
        $subjects = $this->Results->Subjects->find('list', ['limit' => 900])
                ->where(['department_id' => $department_id])
                ->order(['name' => 'ASC']);
        $this->set(compact('subjects'));
    }

    /**
     * Examination Sheet method - Generate examination sheet for admin
     *
     * @return \Cake\Http\Response|void
     */
    public function examinationsheet() {

        $students = [];
        $subjects = [];
        $examinationData = [];
        $classInfo = null;

        if ($this->request->is('post')) {
            $department_id = $this->request->getData('department_id');
            $class_arm_id = $this->request->getData('class_arm_id');
            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');

            // Build conditions
            $conditions = [];
            if (!empty($department_id)) {
                $conditions['Results.department_id'] = $department_id;
            }
            if (!empty($class_arm_id)) {
                $conditions['Results.class_arm_id'] = $class_arm_id;
            }
            if (!empty($session_id)) {
                $conditions['Results.session_id'] = $session_id;
            }
            if (!empty($semester_id)) {
                $conditions['Results.semester_id'] = $semester_id;
            }

            // Get students in the selected criteria
            $students = $this->Results->Students->find()
                ->contain(['ClassArms', 'Departments'])
                ->where([
                    'Students.department_id' => $department_id,
                    'Students.class_arm_id' => $class_arm_id,
                    'Students.status' => 'Admitted'
                ])
                ->order(['Students.fname' => 'ASC', 'Students.lname' => 'ASC'])
                ->all();

            // Get subjects for this department/session/semester
            $subjects = $this->Results->Subjects->find()
                ->where(['Subjects.department_id' => $department_id])
                ->order(['Subjects.id' => 'ASC'])
                ->all();

            // Get class information
            if (!empty($department_id)) {
                $classInfo = $this->Results->Departments->get($department_id);
            }

            // Get examination data for each student and subject
            foreach ($students as $student) {
                $studentResults = [];
                $grandTotal = 0;
                $subjectsWithResults = 0;

                foreach ($subjects as $subject) {
                    $result = $this->Results->find()
                        ->where([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'session_id' => $session_id,
                            'semester_id' => $semester_id
                        ])
                        ->first();

                    if ($result) {
                        $ca = $result->ca ?? 0;
                        $first_exam = $result->first_exam ?? 0;
                        $second_exam = $result->second_exam ?? 0;
                        $third_exam = $result->third_exam ?? 0;
                        // Calculate total from all exam components
                        $subjectTotal = $ca + $first_exam + $second_exam + $third_exam;
                        
                        // Only add to grand total if subject has results
                        if ($subjectTotal > 0) {
                            $grandTotal += $subjectTotal;
                            $subjectsWithResults++;
                        }

                        $studentResults[] = [
                            'subject' => $subject,
                            'ca' => $ca,
                            'first_exam' => $first_exam,
                            'second_exam' => $second_exam,
                            'third_exam' => $third_exam,
                            'total' => $subjectTotal
                        ];
                    } else {
                        $studentResults[] = [
                            'subject' => $subject,
                            'ca' => 0,
                            'first_exam' => 0,
                            'second_exam' => 0,
                            'third_exam' => 0,
                            'total' => 0
                        ];
                    }
                }

                // Calculate average: GRAND TOTAL ÷ Number of subjects with results
                $average = $subjectsWithResults > 0 ? round($grandTotal / $subjectsWithResults, 2) : 0;
                $examinationData[] = [
                    'student' => $student,
                    'results' => $studentResults,
                    'total' => $grandTotal, // GRAND TOTAL = Sum of all subject totals
                    'average' => $average,   // AVERAGE = GRAND TOTAL ÷ subjects with results
                    'position' => 0 // Will be calculated after sorting
                ];
            }

            // Separate students with results from those without
            $studentsWithResults = [];
            $studentsWithoutResults = [];
            
            foreach ($examinationData as $data) {
                if ($data['total'] > 0) {
                    $studentsWithResults[] = $data;
                } else {
                    $studentsWithoutResults[] = $data;
                }
            }
            
            // Sort students with results by total score (descending) and assign positions
            usort($studentsWithResults, function($a, $b) {
                return $b['total'] - $a['total'];
            });
            
            foreach ($studentsWithResults as $index => $data) {
                $studentsWithResults[$index]['position'] = $index + 1;
            }
            
            // Students without results get no position
            foreach ($studentsWithoutResults as $index => $data) {
                $studentsWithoutResults[$index]['position'] = null;
            }
            
            // Combine both groups and sort by student name for display
            $examinationData = array_merge($studentsWithResults, $studentsWithoutResults);
            usort($examinationData, function($a, $b) {
                return strcmp($a['student']->fname, $b['student']->fname);
            });
        }

        // Get dropdown data
        $departments = $this->Results->Departments->find('list', ['limit' => 200])
            ->order(['name' => 'ASC']);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);

        // Get class arms if department is selected
        $classArms = [];
        if ($this->request->is('post') && !empty($this->request->getData('department_id'))) {
            $classArms = $this->Results->ClassArms->find()
                ->contain(['Departments'])
                ->where(['ClassArms.department_id' => $this->request->getData('department_id')])
                ->order(['ClassArms.arm_name' => 'ASC'])
                ->all();
        }

        $this->set(compact('students', 'subjects', 'examinationData', 'classInfo', 'departments', 'sessions', 'semesters', 'classArms'));
        $this->viewBuilder()->setLayout('backend');
    }

    //student method for checking their results
    public function myresults() {
        //disable results viewing
       // $this->Flash->error(__('Sorry, result viewing has been temporarily disabled'));
       //return $this->redirect(['controller'=>'Students','action' => 'dashboard']);
       $student = $this->Results->Students->find()->contain(['Departments', 'ClassArms'])
                        ->where(['user_id' => $this->Auth->user('id')])->first();
        //check for special candidates before checking fee payment
//       if(($student->isclaretian =='No')){
//           //this is not a claretian or special candidate so verify fee payment
//        $this->checkfeepaymentbeforeresult();   
//       }
        if ($this->request->is('post')) {

            $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');
            $level_id = $this->request->getData('level_id');
            $conditions = [];
            if (!empty($semester_id)) {
                $conditions['Results.semester_id'] = $semester_id;
            }
            if (!empty($course_id)) {
                $conditions['Results.subject_id'] = $course_id;
            }
            if (!empty($session_id)) {
                $conditions['Results.session_id'] = $session_id;
            }
            if (!empty($level_id)) {
                $conditions['Results.level_id'] = $level_id;
            }
            $results = $this->Results->find()
                    ->contain(['Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions'])
                    ->where(['Results.regno' => $student->regno])
                    ->where($conditions)
                    ->where(['Results.approval_status' => 'approved']); // Only show approved results
            //debug(json_encode($conditions, JSON_PRETTY_PRINT)); exit;
            $this->set('results', $results);
        // } else {
        //     $results = $this->Results->find()
        //             ->contain(['Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions'])
        //             ->where(['student_id' => $student->id]);

        //     //debug(json_encode($conditions, JSON_PRETTY_PRINT)); exit;
        //     $this->set('results', $results);
        }
       

        $subjects = $this->Results->Subjects->find('list', ['limit' => 200]);
        $levels = $this->Results->Levels->find('list', ['limit' => 200])->where(['id !=' => 5])->order(['name' => 'ASC']);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $this->set(compact('subjects', 'semesters', 'sessions', 'student', 'levels'));

        $this->viewBuilder()->setLayout('studentsbackend');
    }

    public function downloadPdf($studentId = null, $sessionId = null, $semesterId = null) {
        // Get student information
        $student = $this->Results->Students->find()->contain(['Departments', 'ClassArms'])
                        ->where(['Students.id' => $studentId])->first();
        
        if (!$student) {
            $this->Flash->error(__('Student not found'));
            return $this->redirect(['action' => 'myresults']);
        }

        // Get approved results for this student
        $conditions = [];
        if (!empty($semesterId)) {
            $conditions['Results.semester_id'] = $semesterId;
        }
        if (!empty($sessionId)) {
            $conditions['Results.session_id'] = $sessionId;
        }

        $results = $this->Results->find()
                ->contain(['Faculties', 'Departments', 'Subjects', 'Semesters', 'Sessions'])
                ->where(['Results.regno' => $student->regno])
                ->where($conditions)
                ->where(['Results.approval_status' => 'approved'])
                ->all();

        if ($results->isEmpty()) {
            $this->Flash->error(__('No approved results found for this student'));
            return $this->redirect(['action' => 'myresults']);
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

    public function printResult($studentId = null, $sessionId = null, $semesterId = null) {
        // Get student data
        $student = $this->Results->Students->find()->contain(['Departments', 'ClassArms'])
                        ->where(['Students.id' => $studentId])->first();
        
        if (!$student) {
            $this->Flash->error('Student not found.');
            return $this->redirect(['action' => 'myresults']);
        }

        // Build where conditions dynamically to handle null values
        $whereConditions = [
            'Results.student_id' => $studentId,
            'Results.approval_status' => 'approved'
        ];
        
        if (!empty($sessionId)) {
            $whereConditions['Results.session_id'] = $sessionId;
        }
        
        if (!empty($semesterId)) {
            $whereConditions['Results.semester_id'] = $semesterId;
        }

        // Get approved results for the student
        $results = $this->Results->find()
            ->contain(['Subjects', 'Departments', 'Semesters', 'Sessions'])
            ->where($whereConditions)
            ->all();

        if ($results->isEmpty()) {
            $this->Flash->error('No approved results found for this student.');
            return $this->redirect(['action' => 'myresults']);
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

        $this->set(compact('results', 'student', 'settings', 'total_marks', 'subjects'));
        $this->viewBuilder()->setLayout('print');
    }

    
    //this method was used to correct a result given to the wrong student by using the wrong regno
    public function correctresult(){
     
        //get all the result for the session 2022/2023
        $results = $this->Results->find()
                    ->where(['regno' => "CUN2021/0033",'session_id'=>5]);
       // debug(json_encode( $results, JSON_PRETTY_PRINT)); exit;
        
        foreach($results as $result){
            //echo $result->regno.'<br />'.$result->department_id; exit;
         $result->faculty_id = 3;
         $result->department_id = 10;
         $result->regno = "CUN2021/0033";
         $result->student_id = 89;
         $this->Results->save($result);
            
        }
       exit; 
    }
    
    
    
    //ENsure student has paid at least 3 different fees for the session before they can see their results
    public function checkfeepaymentbeforeresult(){
        //disable results viewing
//        $this->Flash->error(__('Sorry, you can not view your results now because you have outstanding fees to pay.'
//                . ' Please check back when you have cleared your fees'));
//       return $this->redirect(['controller'=>'Students','action' => 'dashboard']);
        
        $studentscontroller = New StudentsController();
     $student =   $studentscontroller->isstudent();
              $transactions_table = TableRegistry::get('Transactions');
              $session = $this->request->getSession()->read('settings');
              //$past_session_id = $session->session_id - 1;
     //check payment
     $payment =  $transactions_table->find()
             ->where(['student_id'=>$student->id,'session_id'=>$session->session_id,'paystatus'=>'completed'])
             ->count();
     if(  $payment>=4){
         return;
     }
     else{
         $this->Flash->error(__('Sorry, you need to pay up your fees before you can access your results.'));
       return $this->redirect(['controller'=>'Students','action' => 'dashboard']);  
     }
        
    }
    
    
    //method that checks that a result is approved before students can see them
    public function checkifapproved($sessionid,$semesterid){
        $approvedresults_table = TableRegistry::get('Approvedresults'); 
        $status = $approvedresults_table->find(['session_id'=>$sessionid,
            'semester_id'=>$semesterid,'status'=>'Approved'])->first();
        if(!empty($status->id)){
            return;
        }
        else{
        $this->Flash->error(__('Sorry, the result you are looking for is currently unavailable.'));
       return $this->redirect(['controller'=>'Students','action' => 'dashboard']);  
     }
    }
    
    
    //calculate CGPA
    public function calculateCGPA($regnumb) {
        //$results_table = TableRegistry::get('Results');
        $courses_table = TableRegistry::get('Subjects');
        $constants_table = TableRegistry::get('Constants');
        $total = 0;
        $totalUnits = 0;
        $results = $this->Results->find()->where(['regnumb' => $regnumb]);
        $l = 0;

        //  debug(json_encode( $results, JSON_PRETTY_PRINT)); exit;
        foreach ($results as $result) {
            $credit_unit = $courses_table->get($result->course_id);
            $grade_point_quality = $constants_table->find()->where(['name' => $result->grade])->first();
            $course_point = $grade_point_quality->value * $credit_unit->creditload;
            $total += $course_point;
            $totalUnits += $credit_unit->creditload;
            $l++;
        }
        return number_format($total / $totalUnits, 2);
    }

    /**
     * Delete method
     *
     * @param string|null $id Result id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $result = $this->Results->get($id);
        if ($this->Results->delete($result)) {
            //log activity
            $usercontroller = new UsersController();

            $title = "Deleted a Result ";
            $user_id = $this->Auth->user('id');
            $description = "Deleted a result " . $result->id;
            $ip = $this->request->clientIp();
            $type = "Delete";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The result has been deleted.'));
        } else {
            $this->Flash->error(__('The result could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageresults']);
    }
    
    
    //admin method for removing results uploaded by mistake
    public function removeresult(){
       if ($this->request->is('post')) {
           $count = 0;
     $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');
      $dept_id = $this->request->getData('department_id');
      $results = $this->Results->find()->where(['semester_id'=>$semester_id,
          'subject_id'=>$course_id,'department_id'=>$dept_id,'session_id'=>$session_id]);
    // debug(json_encode( $results, JSON_PRETTY_PRINT)); exit;
      foreach($results as $result){
       $this->Results->delete($result); 
                $count++;
      }
      
      $this->Flash->success(__($count .' results have been deleted'));
       }
       
        $departments = $this->Results->Departments->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 4000]);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        // $levels = $this->Results->Levels->find('list', ['limit' => 200]);
        $this->set(compact('departments', 'subjects', 'semesters', 'sessions'));
        $this->viewBuilder()->setLayout('backend');
        
       // return $this->redirect(['action' => 'removeresult']);
    }
    
    

    //method that gets the students in a given department
    public function getdaepts($faculty_id) {
        $departments_table = TableRegistry::get('Departments');
        $departments = $departments_table->find('list')
                ->where(['faculty_id' => $faculty_id])
                ->order(['name' => 'ASC']);
        $this->set('departments', $departments);
    }

    //returns the students in a selected department during result search
    public function studentsindept($dept_id) {
        $students_table = TableRegistry::get('Students');
        $students = $students_table->find('list')
                ->where(['department_id' => $dept_id])
                ->order(['fname' => 'ASC']);
        $this->set('students', $students);
    }

    //admin method for managing and generating transcripts
    public function managetranscript() {

        $students_table = TableRegistry::get('Students');
        $departments_table = TableRegistry::get('Levels');

        if ($this->request->is('post')) {

            $dept_id = $this->request->getData('department_id');
            $level_id = $this->request->getData('level_id');
            $conditions = [];
            if (!empty($dept_id)) {
                $conditions['Students.department_id'] = $dept_id;
            }
            if (!empty($level_id)) {
                $conditions['Students.level_id'] = $level_id;
            }
            $students = $students_table->find()
                    ->contain(['Departments'])
                    ->where($conditions);
            //debug(json_encode($conditions, JSON_PRETTY_PRINT)); exit;
            $this->set('students', $students);
        } else {
            $students = $students_table->find()
                    ->contain(['Departments'])
                    ->where(['Students.level_id' => 4]);

            //debug(json_encode($conditions, JSON_PRETTY_PRINT)); exit;
            $this->set('students', $students);
        }


        $levels = $students_table->Levels->find('list', ['limit' => 20])->order(['name' => 'ASC']);
        $departments = $students_table->Departments->find('list', ['limit' => 200]);

        $this->set(compact('departments'));

        $this->viewBuilder()->setLayout('backend');
    }

    //admin method for getting all the results of a student
    public function getallresults($student_id, $name) {
        $students_table = TableRegistry::get('Students');
        $student = $students_table->get($student_id, ['contain' => ['Faculties', 'Departments', 'Levels']]);
        $results = $this->Results->find()
                ->contain(['Subjects', 'Semesters', 'Sessions', 'Levels'])
                ->where(['student_id' => $student->id])
                ->order(['Results.session_id' => 'ASC']);
        //debug(json_encode($conditions, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('results', 'student'));
        $this->viewBuilder()->setLayout('backend');
    }
    
    
    //a function to update the credit unit of a result already uploaded
    public function updateunit(){
        if ($this->request->is('post')) {
      $count = 0;
     $session_id = $this->request->getData('session_id');
            $semester_id = $this->request->getData('semester_id');
            $course_id = $this->request->getData('subject_id');
            $level_id = $this->request->getData('level_id');
      $dept_id = $this->request->getData('department_id');
       $unit = $this->request->getData('unit');
      $results = $this->Results->find()->where(['semester_id'=>$semester_id,
          'subject_id'=>$course_id,'level_id'=>$level_id,'department_id'=>$dept_id,'session_id'=>$session_id]);
    // debug(json_encode( $results, JSON_PRETTY_PRINT)); exit;
      foreach($results as $result){
       $result->creditload =    $unit;
       $this->Results->save($result); 
                $count++;
      }
      
      $this->Flash->success(__($count .' results have been updated unit'));
       }
       
        $departments = $this->Results->Departments->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $subjects = $this->Results->Subjects->find('list', ['limit' => 4000]);
        $semesters = $this->Results->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Results->Sessions->find('list', ['limit' => 200]);
        $levels = $this->Results->Levels->find('list', ['limit' => 200]);
        $this->set(compact('departments', 'subjects', 'semesters', 'sessions', 'levels'));

         $this->viewBuilder()->setLayout('backend');
    }
    
    /**
     * Admin method to view pending results for approval (grouped by subject/class)
     */
    public function pendingApproval() {
        
        // Get pending results grouped by subject, class, term, and session
        $pendingBatches = $this->Results->find()
            ->contain(['Students', 'Subjects', 'Departments', 'Semesters', 'Sessions', 'Users', 'ClassArms'])
            ->where(['Results.approval_status' => 'pending'])
            ->order(['Results.uploaddate' => 'DESC'])
            ->all();
            
        // Group results by batch (subject + class + term + session)
        $groupedBatches = [];
        foreach ($pendingBatches as $result) {
            $batchKey = $result->subject_id . '_' . $result->department_id . '_' . $result->semester_id . '_' . $result->session_id;
            
            if (!isset($groupedBatches[$batchKey])) {
                $groupedBatches[$batchKey] = [
                    'subject' => $result->subject,
                    'department' => $result->department,
                    'class_arm' => $result->class_arm,
                    'semester' => $result->semester,
                    'session' => $result->session,
                    'uploaded_by' => $result->user,
                    'upload_date' => $result->uploaddate,
                    'student_count' => 0,
                    'results' => []
                ];
            }
            
            $groupedBatches[$batchKey]['results'][] = $result;
            $groupedBatches[$batchKey]['student_count']++;
        }
            
        $this->set('groupedBatches', $groupedBatches);
        $this->viewBuilder()->setLayout('backend');
    }
    
    /**
     * Admin method to view batch details for approval
     */
    public function viewBatch($subjectId = null, $departmentId = null, $semesterId = null, $sessionId = null) {
        
        $batchResults = $this->Results->find()
            ->contain(['Students', 'Subjects', 'Departments', 'Semesters', 'Sessions', 'Users', 'ClassArms'])
            ->where([
                'Results.subject_id' => $subjectId,
                'Results.department_id' => $departmentId,
                'Results.semester_id' => $semesterId,
                'Results.session_id' => $sessionId,
                'Results.approval_status' => 'pending'
            ])
            ->order(['Students.fname' => 'ASC'])
            ->all(); // Add ->all() to ensure we get all results
            
        // Get subject and department details for the view
        $subject = null;
        $department = null;
        $semester = null;
        $session = null;
        
        if ($batchResults->count() > 0) {
            $firstResult = $batchResults->first();
            $subject = $firstResult->subject;
            $department = $firstResult->department;
            $semester = $firstResult->semester;
            $session = $firstResult->session;
        }
        
        $this->set('batchResults', $batchResults);
        $this->set('subject', $subject);
        $this->set('department', $department);
        $this->set('semester', $semester);
        $this->set('session', $session);
        $this->set('subjectId', $subjectId);
        $this->set('departmentId', $departmentId);
        $this->set('semesterId', $semesterId);
        $this->set('sessionId', $sessionId);
        $this->viewBuilder()->setLayout('backend');
    }
    
    /**
     * Admin method to approve a batch of results
     */
    public function approveBatch($subjectId = null, $departmentId = null, $semesterId = null, $sessionId = null) {
        
        $batchResults = $this->Results->find()
            ->where([
                'Results.subject_id' => $subjectId,
                'Results.department_id' => $departmentId,
                'Results.semester_id' => $semesterId,
                'Results.session_id' => $sessionId,
                'Results.approval_status' => 'pending'
            ]);
            
        $approvedCount = 0;
        foreach ($batchResults as $result) {
            $result->approval_status = 'approved';
            $result->approved_by = $this->Auth->user('id');
            $result->approved_at = new \Cake\I18n\FrozenTime();
            
            if ($this->Results->save($result)) {
                $approvedCount++;
            }
        }
        
        if ($approvedCount > 0) {
            $this->Flash->success(__($approvedCount . ' results have been approved successfully.'));
        } else {
            $this->Flash->error(__('Unable to approve results. Please try again.'));
        }
        
        return $this->redirect(['action' => 'pendingApproval']);
    }
    
    /**
     * Admin method to reject a batch of results
     */
    public function rejectBatch($subjectId = null, $departmentId = null, $semesterId = null, $sessionId = null) {
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rejectionReason = $this->request->getData('rejection_reason');
            
            $batchResults = $this->Results->find()
                ->where([
                    'Results.subject_id' => $subjectId,
                    'Results.department_id' => $departmentId,
                    'Results.semester_id' => $semesterId,
                    'Results.session_id' => $sessionId,
                    'Results.approval_status' => 'pending'
                ]);
                
            $rejectedCount = 0;
            foreach ($batchResults as $result) {
                $result->approval_status = 'rejected';
                $result->approved_by = $this->Auth->user('id');
                $result->approved_at = new \Cake\I18n\FrozenTime();
                $result->rejection_reason = $rejectionReason;
                
                if ($this->Results->save($result)) {
                    $rejectedCount++;
                }
            }
            
            if ($rejectedCount > 0) {
                $this->Flash->success(__($rejectedCount . ' results have been rejected successfully.'));
                return $this->redirect(['action' => 'pendingApproval']);
            } else {
                $this->Flash->error(__('Unable to reject results. Please try again.'));
            }
        }
        
        $batchResults = $this->Results->find()
            ->contain(['Students', 'Subjects', 'Departments', 'Semesters', 'Sessions', 'Users', 'ClassArms'])
            ->where([
                'Results.subject_id' => $subjectId,
                'Results.department_id' => $departmentId,
                'Results.semester_id' => $semesterId,
                'Results.session_id' => $sessionId,
                'Results.approval_status' => 'pending'
            ])
            ->order(['Students.fname' => 'ASC'])
            ->all();
            
        $this->set('batchResults', $batchResults);
        $this->set('subjectId', $subjectId);
        $this->set('departmentId', $departmentId);
        $this->set('semesterId', $semesterId);
        $this->set('sessionId', $sessionId);
        $this->viewBuilder()->setLayout('backend');
    }
    
    /**
     * Admin method to reject a result
     */
    public function rejectResult($id = null) {
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $result = $this->Results->get($id);
            $result->approval_status = 'rejected';
            $result->approved_by = $this->Auth->user('id');
            $result->approved_at = new \Cake\I18n\FrozenTime();
            $result->rejection_reason = $this->request->getData('rejection_reason');
            
            if ($this->Results->save($result)) {
                $this->Flash->success(__('Result has been rejected successfully.'));
                return $this->redirect(['action' => 'pendingApproval']);
            } else {
                $this->Flash->error(__('Unable to reject result. Please try again.'));
            }
        }
        
        $result = $this->Results->get($id, [
            'contain' => ['Students', 'Subjects', 'Departments', 'Semesters', 'Sessions', 'Users']
        ]);
        
        $this->set('result', $result);
        $this->viewBuilder()->setLayout('backend');
    }
    
    
      // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
     

        $actions = ['uploadcourseresults', 'uploadresults', 'examinationsheet'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
        }
    }

}
