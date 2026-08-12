<?php

declare(strict_types = 1);

namespace App\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper;
use Cake\ORM\TableRegistry;
/**
 * Examquestions Controller
 *
 * @property \App\Model\Table\ExamquestionsTable $Examquestions
 * @method \App\Model\Entity\Examquestion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ExamquestionsController extends AppController {

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index() {
//        $this->paginate = [
//            'contain' => ['Subjects', 'Admins', 'Exams', 'Departments', 'Levels', 'Faculties', 'Exams.Semesters', 'Exams.Sessions'],
//        ];
        $examquestions = $this->Examquestions->find()
                ->contain(['Subjects', 'Admins', 'Exams', 'Departments', 'Levels', 'Faculties', 'Exams.Semesters', 'Exams.Sessions'])
                ->distinct(['subject_id']);
       // $examquestions = $this->paginate($this->Examquestions);
//debug(json_encode( $examquestions, JSON_PRETTY_PRINT)); exit;
        $subjects = $this->Examquestions->Subjects->find('list', ['limit' => 500])->order(['name' => 'ASC']);
        $faculties = $this->Examquestions->Faculties->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $exams = $this->Examquestions->Exams->find('list', ['limit' => 200]);
        $departments = $this->Examquestions->Departments->find('list', ['limit' => 200])->order(['name' => 'ASC']);
        $levels = $this->Examquestions->Levels->find('list', ['limit' => 200])->where(['id !=' => 5])->order(['name' => 'ASC']);
        $this->set(compact('examquestions', 'subjects', 'faculties', 'exams', 'departments', 'levels'));
        // $this->set(compact('examquestions'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Examquestion id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($exam_id, $level_id, $subject_id,$id) {
        $examquestions =  $this->Examquestions->find()
                ->contain(['Subjects', 'Admins', 'Exams.Semesters','Exams.Sessions','Faculties', 'Departments', 'Levels'])
                ->where(['exam_id'=>$exam_id,'subject_id'=>$subject_id,'Examquestions.level_id'=>$level_id]);
       $exam = $this->Examquestions->get($id,['contain'=>['Exams.Semesters','Exams.Sessions','Departments','Levels']]);
       $this->set('exam',$exam);
        $this->set(compact('examquestions'));
        $this->viewBuilder()->setLayout('backend');
    }
    
    
    
    //student method for writing the exam
    public function myquestions($exam_id, $level_id, $subject_id,$id){
        $examquestions =  $this->Examquestions->find()
                ->contain(['Subjects', 'Admins', 'Exams.Semesters','Exams.Sessions','Faculties', 'Departments', 'Levels'])
                ->where(['exam_id'=>$exam_id,'subject_id'=>$subject_id,'Examquestions.level_id'=>$level_id]);
       $exam = $this->Examquestions->get($id,['contain'=>['Exams.Semesters','Exams.Sessions','Departments','Levels']]);
       $this->set('exam',$exam);
        $this->set(compact('examquestions'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    






    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function uploadquestions() {
        //ensure this is an admin
        $admincontroller = new AdminsController();
        $admin = $admincontroller->isadmin();
        $examquestion = $this->Examquestions->newEmptyEntity();
        if ($this->request->is('post')) {
            $message = " ";
            $department_id = $this->request->getData('department_id');
            $faculty_id = $this->request->getData('faculty_id');
            $upload_file = $this->request->getData('questions');
            $level_id = $this->request->getData('level_id');
            $exam_id = $this->request->getData('exam_id');
            $course_id = $this->request->getData('subject_id');
            $name = $upload_file->getClientFilename();
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $tmpName = $upload_file->getStream()->getMetadata('uri');
            $type = $upload_file->getClientMediaType();
            $error = $upload_file->getError();
            $current_session = $this->request->getSession()->read('settings');
            $allowedext = ['csv', 'xlsx'];
            if ($error != 0) {
                $this->Flash->error(__('Sorry, there is a problem with the file. Please check and try again'));

                return $this->redirect(['action' => 'index']);
            }
            if (!in_array($ext, $allowedext)) {
                $this->Flash->error(__('Sorry, only csv or xlsx files can be uploaded.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $helper = new Helper\Sample();
                debug($helper);
                $spreadsheet = IOFactory::load($tmpName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                $count = 1;
                $old = 0;
                $inserted = 0;
                foreach ($sheetData as $data) {
                    $count++;
                    if ($count > 2) {
                         $subject = $this->Examquestions->Subjects->get($course_id);
                        $department = $this->Examquestions->Departments->get($department_id);
                        $faculty = $this->Examquestions->Faculties->get($faculty_id);
                        $level = $this->Examquestions->Levels->get($level_id);

//               echo strtolower($this->clean($level->name)) .' '. strtolower($this->clean($data['J'])).'<br />';
//              echo strtolower(trim($department->name)) .' '. strtolower(trim($data['I'])).'<br />';
//             echo  strtolower(trim($faculty->name)) .' '. strtolower(trim($data['H'])).'<br />';
//             echo strtolower(trim($subject->subjectcode)) .' '. strtolower(trim($data['K'])).'<br />';
//            exit;

                        if ((strtolower(trim($department->name)) == strtolower(trim($data['I']))) && (strtolower($this->clean($level->name)) == strtolower($this->clean($data['J']))) && (strtolower(trim($faculty->name)) == strtolower(trim($data['H'])))
                        ) {
                           
                            //  debug(json_encode($oldstudent, JSON_PRETTY_PRINT)); exit; 5182.50
                            //create a new question object
                            $question = $this->Examquestions->newEmptyEntity();
                            $question->subject_id = $course_id;
                            $question->department_id = $department->id;
                            $question->faculty_id = $faculty->id;
                            $question->question = $data['A'];
                            $question->op1 = $data['B'];
                            $question->op2 = $data['C'];
                            $question->op3 = $data['D'];
                            $question->op4 = $data['E'];
                            $question->correctans = $data['F'];
                            $question->mark = $data['G'];
                            $question->exam_id = $exam_id;
                            $question->level_id = $level_id;
                            $question->admin_id = $admin->id;
                           // debug(json_encode($question, JSON_PRETTY_PRINT));
                           // exit;
                            //save the question
                            $this->Examquestions->save($question);
                            $inserted++;
                        } else {
                            $this->Flash->error(__('Sorry, the selected department or faculty or level didn\'t match that in the csv file you are uploading...'));

                            return $this->redirect(['action' => 'index']);
                        }

                        // debug(json_encode($data['F'], JSON_PRETTY_PRINT)); exit;
                    }
                }
                $this->Flash->success(__($inserted . ' Questions have been uploaded successfully. ' . $message));

                return $this->redirect(['action' => 'index']);
            }
        }
        $subjects = $this->Examquestions->Subjects->find('list', ['limit' => 200]);
        // $admins = $this->Examquestions->Admins->find('list', ['limit' => 200]);
        $exams = $this->Examquestions->Exams->find('list', ['limit' => 200]);
        $departments = $this->Examquestions->Departments->find('list', ['limit' => 200]);
        $levels = $this->Examquestions->Levels->find('list', ['limit' => 200]);
        $this->set(compact('examquestion', 'subjects', 'exams', 'departments', 'levels'));
        $this->viewBuilder()->setLayout('backend');
    }

    //method that removes special characters from a string
    public function clean($string) {
        $clean_string = str_replace(', ', ' ', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $clean_string); // Removes special chars.
    }

    /**
     * Edit method
     *
     * @param string|null $id Examquestion id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $examquestion = $this->Examquestions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examquestion = $this->Examquestions->patchEntity($examquestion, $this->request->getData());
            if ($this->Examquestions->save($examquestion)) {
                $this->Flash->success(__('The examquestion has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The examquestion could not be saved. Please, try again.'));
        }
        $subjects = $this->Examquestions->Subjects->find('list', ['limit' => 200]);
        $admins = $this->Examquestions->Admins->find('list', ['limit' => 200]);
        $exams = $this->Examquestions->Exams->find('list', ['limit' => 200]);
        $departments = $this->Examquestions->Departments->find('list', ['limit' => 200]);
        $levels = $this->Examquestions->Levels->find('list', ['limit' => 200]);
        $this->set(compact('examquestion', 'subjects', 'admins', 'exams', 'departments', 'levels'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Examquestion id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $examquestion = $this->Examquestions->get($id);
        if ($this->Examquestions->delete($examquestion)) {
            $this->Flash->success(__('The question has been deleted.'));
        } else {
            $this->Flash->error(__('The question could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    //method for deleting an entire exam question
    public function bulkdelete($exam_id,$subject_id,$level_id){
        $this->request->allowMethod(['post', 'delete']);
       $examquestions =  $this->Examquestions->find()
                ->where(['exam_id'=>$exam_id,'subject_id'=>$subject_id,'Examquestions.level_id'=>$level_id]);
        if ($this->Examquestions->deleteMany($examquestions)) {
            $this->Flash->success(__('The questions has been deleted.'));
        } else {
            $this->Flash->error(__('The questions could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
