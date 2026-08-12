<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;
/**
 * Exams Controller
 *
 * @property \App\Model\Table\ExamsTable $Exams
 * @method \App\Model\Entity\Exam[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ExamsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Semesters', 'Sessions', 'Admins'],
        ];
        $exams = $this->paginate($this->Exams);
         $semesters = $this->Exams->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Exams->Sessions->find('list', ['limit' => 200]);

        $this->set(compact('exams','semesters','sessions'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    //method that shows the student their avaiable exams
    public function myexams(){
          $settings = $this->request->getSession()->read('settings');
       $myexam =  $this->Exams->find()
               ->contain(['Semesters','Sessions','Examquestions'])
              // ->distinct(['Examquestions.subject_id'])
               ->where(['session_id'=>$settings->session_id,'semester_id'=>$settings->semester_id]);
       //debug(json_encode( $myexam, JSON_PRETTY_PRINT)); exit;
        $this->set('myexams', $myexam);
        $this->viewBuilder()->setLayout('studentsbackend');    
    }

    
//method that shows a student the list of his exam/courses
    public function mycourses($exam_id){
         $settings = $this->request->getSession()->read('settings');
     $examquestions_Table = TableRegistry::get('Examquestions');
     $registeredcourses = $this->checkcourseregistration();
     if(is_array($registeredcourses)){
      $examcourses =   $examquestions_Table->find()
                ->contain(['Subjects', 'Exams.Semesters','Exams.Sessions','Faculties', 'Departments', 'Levels'])
                ->where(['exam_id'=>$exam_id,'subject_id IN'=>$registeredcourses])
               ->distinct(['subject_id']);  
       $this->set('examcourses', $examcourses);  
     }
     else{
          $this->Flash->error(__('No courses found, ensure you have registered your courses for this semester.'));
           return $this->redirect(['action' => 'myexams']);
     }
        
        $this->viewBuilder()->setLayout('studentsbackend');    
    }

    
    //method that returns all the ids of the courses registered by the student
    public function getregisteredcourses(){
        
    }

    

//method that checks if the student has registered his courses
    public function checkcourseregistration(){
         $courseregistration_Table = TableRegistry::get('Courseregistrations');
         $settings = $this->request->getSession()->read('settings');
        //get student data
        $studentcontroller = new StudentsController();
         $student =  $studentcontroller->isstudent();
          $courseregistration =   $courseregistration_Table->find()
                  ->contain(['Subjects'])
                  ->where(['student_id'=> $student->id,'level_id'=>$student->level_id,
                      'semester_id'=>  $settings->semester_id,'session_id'=>$settings->session_id])
                  ->first();
     
          if(is_object($courseregistration)){                
              $course_ids = [];
              foreach ($courseregistration->subjects as $subject){
                  array_push($course_ids, $subject->id); 
              }
             //debug(json_encode($course_ids, JSON_PRETTY_PRINT)); exit;
              return $course_ids;
          }
          return 0;
                  
              
    }

        /**
     * View method
     *
     * @param string|null $id Exam id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $exam = $this->Exams->get($id, [
            'contain' => ['Subjects', 'Departments', 'Faculties', 'Semesters', 'Sessions', 'Admins'],
        ]);

        $this->set(compact('exam'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function createexam()
    {
        $exam = $this->Exams->newEmptyEntity();
        if ($this->request->is('post')) {
            //get admin
            $admincontroller = new AdminsController();
          $admin =  $admincontroller->isadmin();
            $exam = $this->Exams->patchEntity($exam, $this->request->getData());
            $exam->admin_id =  $admin->id;
          //  debug(json_encode($exam , JSON_PRETTY_PRINT)); exit;
            if ($this->Exams->save($exam)) {
                $this->Flash->success(__('The exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam could not be saved. Please, try again.'));
        }
       // $subjects = $this->Exams->Subjects->find('list', ['limit' => 200]);
      //  $departments = $this->Exams->Departments->find('list', ['limit' => 200]);
       // $faculties = $this->Exams->Faculties->find('list', ['limit' => 200]);
        $semesters = $this->Exams->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Exams->Sessions->find('list', ['limit' => 200]);
      //  $admins = $this->Exams->Admins->find('list', ['limit' => 200]);
       // $this->set(compact('exam', 'departments', 'faculties', 'semesters', 'sessions', 'admins'));
    $this->viewBuilder()->setLayout('backend');
        
        
            }

    /**
     * Edit method
     *
     * @param string|null $id Exam id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function updateexam($id = null)
    {
        $exam = $this->Exams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $exam = $this->Exams->patchEntity($exam, $this->request->getData());
            if ($this->Exams->save($exam)) {
                $this->Flash->success(__('The exam has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exam could not be saved. Please, try again.'));
        }
        
        $semesters = $this->Exams->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Exams->Sessions->find('list', ['limit' => 200]);
       
        $this->set(compact('exam', 'semesters', 'sessions', 'admins'));
           $this->viewBuilder()->setLayout('backend');
        }

    /**
     * Delete method
     *
     * @param string|null $id Exam id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $exam = $this->Exams->get($id);
        if ($this->Exams->delete($exam)) {
            $this->Flash->success(__('The exam has been deleted.'));
        } else {
            $this->Flash->error(__('The exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
