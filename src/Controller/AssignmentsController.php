<?php
declare(strict_types=1);

namespace App\Controller;
 use Cake\ORM\TableRegistry;
 use Cake\Event\EventInterface;

/**
 * Assignments Controller
 *
 * @property \App\Model\Table\AssignmentsTable $Assignments
 * @method \App\Model\Entity\Assignment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AssignmentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Subjects', 'Students', 'Sessions'],
        ];
        $assignments = $this->paginate($this->Assignments);

        $this->set(compact('assignments'));
    }

    /**
     * View method
     *
     * @param string|null $id Assignment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
           //get table that holds the assignments
          $setassignments_Table = TableRegistry::get('Setassignments');
          $settings = $this->request->getSession()->read('settings');
        $assignment =  $setassignments_Table->get($id, [
            'contain' => ['Subjects'],
        ]);
        //check and retrieve a response if already submitted
              $student = $this->request->getSession()->read('student');
        $answer = $this->Assignments->find()->where(['subject_id'=>$assignment->subject_id,
          'student_id'=>$student->id,'session_id'=> $settings->session_id,'setassignment_id'=>$id])->first();
       // debug(json_encode(  $answer , JSON_PRETTY_PRINT)); exit;

        $this->set(compact('assignment','answer'));
           $this->viewBuilder()->setLayout('studentsbackend');
    }
    
    
    //method that shows the student all assignments in a registered course
    public function checkassignments($subid){
        //get table that holds the assignments
          $setassignments_Table = TableRegistry::get('Setassignments');
          $settings = $this->request->getSession()->read('settings');
        $assignment =  $setassignments_Table->find()->contain(['Subjects'])
                ->where(['Setassignments.subject_id'=>$subid,'Setassignments.semester_id'=>  $settings->semester_id]);  
        
         $this->set(compact('assignment'));
           $this->viewBuilder()->setLayout('studentsbackend');
    }
    
    //method that shows the student all assignments across all registered courses
    public function myassignments(){
        //get table that holds the assignments
        $setassignments_Table = TableRegistry::get('Setassignments');
        $settings = $this->request->getSession()->read('settings');
        $student = $this->request->getSession()->read('student');
        
        // Get subjects for the student's department
        $subjects_Table = TableRegistry::get('Subjects');
        $departmentSubjects = $subjects_Table->find()
            ->where(['department_id' => $student->department_id])
            ->extract('id')
            ->toArray();
        
        if (!empty($departmentSubjects)) {
            $assignments = $setassignments_Table->find()
                ->contain(['Subjects', 'Teachers'])
                ->where([
                    'Setassignments.subject_id IN' => $departmentSubjects,
                    'Setassignments.semester_id' => $settings->semester_id
                ])
                ->order(['Setassignments.datecreated' => 'DESC'])
                ->all();
            
            // Check submission status for each assignment
            $assignments_Table = TableRegistry::get('Assignments');
            foreach ($assignments as $assignment) {
                $submitted = $assignments_Table->find()
                    ->where([
                        'subject_id' => $assignment->subject_id,
                        'student_id' => $student->id,
                        'session_id' => $settings->session_id,
                        'setassignment_id' => $assignment->id
                    ])
                    ->first();
                
                // Check if assignment is actually completed/submitted
                if (!empty($submitted)) {
                    $assignment->submitted = ($submitted->status === 'completed' || $submitted->status === 'submitted');
                    $assignment->submission_data = $submitted;
                    $assignment->assignment_status = $submitted->status; // Store the actual status
                } else {
                    $assignment->submitted = false;
                    $assignment->submission_data = null;
                    $assignment->assignment_status = 'available';
                }
                
                // Get question count for CBT tests
                if ($assignment->test_type === 'cbt_test') {
                    $questionsTable = TableRegistry::get('Questions');
                    $assignment->question_count = $questionsTable->find()
                        ->where(['setassignment_id' => $assignment->id])
                        ->count();
                }
            }
        } else {
            $assignments = [];
        }
        
        $this->set(compact('assignments'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }
    

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function submitassignment($subid,$setassignid)
    {
        $assignment = $this->Assignments->newEmptyEntity();
        // Load the assignment question for quick read on the submit page
        $setassignments_Table = TableRegistry::get('Setassignments');
        $setassignment = $setassignments_Table->get($setassignid, [
            'contain' => ['Subjects', 'Teachers']
        ]);
        if ($this->request->is('post')) {
             $settings = $this->request->getSession()->read('settings');
             $student = $this->request->getSession()->read('student');
            $assignment = $this->Assignments->patchEntity($assignment, $this->request->getData());
            $assignment->session_id =   $settings->session_id;
             $assignment->status = "submitted";
              $assignment->setassignment_id = $setassignid;
             $assignment->student_id = $student->id;
            if ($this->Assignments->save($assignment)) {
                $this->Flash->success(__('The assignment has been saved.'));

                return $this->redirect(['action' => 'myassignments']);
            }
            $this->Flash->error(__('The assignment could not be saved. Please, try again.'));
        }
        $subjects = $this->Assignments->Subjects->find('list', ['limit' => 200])->where(['Subjects.id IN'=>$subid])->all();
       // $students = $this->Assignments->Students->find('list', ['limit' => 200])->all();
        $sessions = $this->Assignments->Sessions->find('list', ['limit' => 200])->all();
        $this->set(compact('assignment', 'subjects',  'sessions', 'setassignment'));
          $this->viewBuilder()->setLayout('studentsbackend');
    }
    
    
    //lecturers method for viewing all submissions for a given assignment
    public function viewrespones($setassgnmtid){
      $assignments = $this->Assignments->find()->contain(['Students','Subjects'])->where(['setassignment_id'=>$setassgnmtid])
              ->order(['Assignments.id'=>'ASC']);
      $this->set(compact('assignments'));  
      $this->viewBuilder()->setLayout('teachersbackend');   
    }
    
    
    //lecturers method for viewingdetails of an assignment
    public function viewres($assignmentid){
      $assignment = $this->Assignments->get($assignmentid, ['contain' => ['Subjects', 'Students']]); 
      $setassgnmtid = $assignment->setassignment_id;
      $this->set(compact('assignment','setassgnmtid')); 
     $this->viewBuilder()->setLayout('teachersbackend');     
    }
    
    /**
     * Start CBT Test - Show test instructions and start the test
     */
    public function startcbt($setassignmentId = null) {
        $setassignmentsTable = TableRegistry::get('Setassignments');
        $student = $this->request->getSession()->read('student');
        $settings = $this->request->getSession()->read('settings');
        
        // Get the test details
        $test = $setassignmentsTable->get($setassignmentId, [
            'contain' => ['Subjects', 'Teachers']
        ]);
        
        // Check if student has already taken this test
        $existingAssignment = $this->Assignments->find()
            ->where([
                'setassignment_id' => $setassignmentId,
                'student_id' => $student->id,
                'session_id' => $settings->session_id
            ])
            ->first();
        
        if ($existingAssignment) {
            $this->Flash->error(__('You have already taken this test.'));
            return $this->redirect(['action' => 'myassignments']);
        }
        
        // Check if test is open
        $now = new \DateTime();
        // Adjust for timezone difference (add 1 hour to match local time)
        $now->add(new \DateInterval('PT1H'));
        if (!empty($test->opendate)) {
            // Handle both FrozenDate/FrozenTime objects and string dates
            if ($test->opendate instanceof \Cake\I18n\FrozenDate || 
                $test->opendate instanceof \Cake\I18n\FrozenTime) {
                $openDate = $test->opendate->format('Y-m-d H:i:s');
            } else {
                $openDate = $test->opendate;
            }
            
            
            // Fix timezone issue: Convert both to same timezone for comparison
            $openDateTime = new \DateTime($openDate);
            $openDateTime->setTimezone($now->getTimezone());
            
            if ($now < $openDateTime) {
                $this->Flash->error(__('This test is not yet open.'));
                return $this->redirect(['action' => 'myassignments']);
            }
        }
        
        if (!empty($test->closedate)) {
            // Handle both FrozenDate/FrozenTime objects and string dates
            if ($test->closedate instanceof \Cake\I18n\FrozenDate || 
                $test->closedate instanceof \Cake\I18n\FrozenTime) {
                $closeDate = $test->closedate->format('Y-m-d H:i:s');
            } else {
                $closeDate = $test->closedate;
            }
            if ($now > new \DateTime($closeDate)) {
                $this->Flash->error(__('This test has closed.'));
                return $this->redirect(['action' => 'myassignments']);
            }
        }
        
        // Get question count
        $questionsTable = TableRegistry::get('Questions');
        $questionCount = $questionsTable->find()
            ->where(['setassignment_id' => $setassignmentId])
            ->count();
        
        if ($questionCount == 0) {
            $this->Flash->error(__('This test has no questions yet.'));
            return $this->redirect(['action' => 'myassignments']);
        }
        
        $this->set(compact('test', 'questionCount'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }
    
    /**
     * Take CBT Test - The actual test interface
     */
    public function takecbt($setassignmentId = null) {
        $setassignmentsTable = TableRegistry::get('Setassignments');
        $student = $this->request->getSession()->read('student');
        $settings = $this->request->getSession()->read('settings');
        
        // Get the test details
        $test = $setassignmentsTable->get($setassignmentId, [
            'contain' => ['Subjects', 'Teachers']
        ]);
        
        // Check if student has already taken this test
        $existingAssignment = $this->Assignments->find()
            ->where([
                'setassignment_id' => $setassignmentId,
                'student_id' => $student->id,
                'session_id' => $settings->session_id
            ])
            ->first();
        
        if ($existingAssignment) {
            $this->Flash->error(__('You have already taken this test.'));
            return $this->redirect(['action' => 'myassignments']);
        }
        
        // Check if test is open/closed
        $now = new \DateTime();
        // Adjust for timezone difference (add 1 hour to match local time)
        $now->add(new \DateInterval('PT1H'));
        if (!empty($test->opendate)) {
            // Handle both FrozenDate/FrozenTime objects and string dates
            if ($test->opendate instanceof \Cake\I18n\FrozenDate || 
                $test->opendate instanceof \Cake\I18n\FrozenTime) {
                $openDate = $test->opendate->format('Y-m-d H:i:s');
            } else {
                $openDate = $test->opendate;
            }
            
            
            // Fix timezone issue: Convert both to same timezone for comparison
            $openDateTime = new \DateTime($openDate);
            $openDateTime->setTimezone($now->getTimezone());
            
            if ($now < $openDateTime) {
                $this->Flash->error(__('This test is not yet open.'));
                return $this->redirect(['action' => 'myassignments']);
            }
        }
        
        if (!empty($test->closedate)) {
            // Handle both FrozenDate/FrozenTime objects and string dates
            if ($test->closedate instanceof \Cake\I18n\FrozenDate || 
                $test->closedate instanceof \Cake\I18n\FrozenTime) {
                $closeDate = $test->closedate->format('Y-m-d H:i:s');
            } else {
                $closeDate = $test->closedate;
            }
            if ($now > new \DateTime($closeDate)) {
                $this->Flash->error(__('This test has closed.'));
                return $this->redirect(['action' => 'myassignments']);
            }
        }
        
        // Get questions with options
        $questionsTable = TableRegistry::get('Questions');
        $questions = $questionsTable->find()
            ->contain(['QuestionOptions'])
            ->where(['setassignment_id' => $setassignmentId])
            ->order(['order_number' => 'ASC'])
            ->all();
        
        if ($this->request->is('post')) {
            // // Debug: Log the submitted data
            // error_log('=== CBT TEST SUBMISSION DEBUG ===');
            // error_log('POST Data: ' . print_r($this->request->getData(), true));
            // error_log('Raw POST: ' . print_r($_POST, true));
            // error_log('Request Method: ' . $this->request->getMethod());
            // error_log('Content Type: ' . $this->request->getHeaderLine('Content-Type'));
            
            // Create new assignment record
            $assignment = $this->Assignments->newEmptyEntity();
            $assignment->subject_id = $test->subject_id;
            $assignment->student_id = $student->id;
            $assignment->session_id = $settings->session_id;
            $assignment->setassignment_id = $setassignmentId;
            $assignment->status = 'submitted';
            // Use JavaScript-calculated timing for accurate duration
            $data = $this->request->getData();
            
            if (!empty($data['actual_start_time'])) {
                // Convert ISO string to MySQL format
                $startTime = new \DateTime($data['actual_start_time']);
                $assignment->start_time = $startTime->format('Y-m-d H:i:s');
            } else {
                // Fallback: set start time to 5 minutes ago
                $assignment->start_time = date('Y-m-d H:i:s', time() - 300);
            }
            
            $assignment->end_time = date('Y-m-d H:i:s');
            
            if ($this->Assignments->save($assignment)) {
                // error_log('Assignment saved with ID: ' . $assignment->id);
                
                // Save student answers
                $studentAnswersTable = TableRegistry::get('StudentAnswers');
                $data = $this->request->getData();
                $answersSaved = 0;
                
                foreach ($questions as $question) {
                    // error_log("Processing question {$question->id}:");
                    // error_log("  - Question type: {$question->question_type}");
                    // error_log("  - Data available: " . (isset($data['answers'][$question->id]) ? 'Yes' : 'No'));
                    
                    if (isset($data['answers'][$question->id])) {
                        $answerValue = $data['answers'][$question->id];
                        // error_log("  - Answer value: '{$answerValue}'");
                        
                        $studentAnswer = $studentAnswersTable->newEmptyEntity();
                        $studentAnswer->assignment_id = $assignment->id;
                        $studentAnswer->question_id = $question->id;
                        
                        // error_log("  - StudentAnswer entity before save: " . print_r($studentAnswer, true));
                        
                        if ($question->question_type === 'multiple_choice') {
                            $studentAnswer->selected_option_id = $answerValue;
                            // error_log("  - Saving multiple choice answer: selected_option_id = {$answerValue}");
                        } else {
                            $studentAnswer->theory_answer = $answerValue;
                            // error_log("  - Saving theory answer: " . substr($answerValue, 0, 50) . '...');
                        }
                        
                        if ($studentAnswersTable->save($studentAnswer)) {
                            $answersSaved++;
                            // error_log("  - Answer saved successfully");
                        } else {
                            // error_log("  - Failed to save answer: " . print_r($studentAnswer->getErrors(), true));
                        }
                    } else {
                        // error_log("  - No answer found for question {$question->id}");
                    }
                }
                
                // error_log("Total answers saved: {$answersSaved}");
                // error_log('=== CBT TEST SUBMISSION DEBUG END ===');
                
                $this->Flash->success(__('Test submitted successfully!'));
                return $this->redirect(['action' => 'viewcbtresult', $assignment->id]);
            } else {
                // error_log('Failed to save assignment: ' . print_r($assignment->getErrors(), true));
                // error_log('=== CBT TEST SUBMISSION DEBUG END ===');
                $this->Flash->error(__('Failed to submit test. Please try again.'));
            }
        }
        
        $this->set(compact('test', 'questions'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }
    
    /**
     * View CBT Test Result
     */
    public function viewcbtresult($assignmentId = null) {
        $assignment = $this->Assignments->get($assignmentId, [
            'contain' => ['Subjects', 'Setassignments']
        ]);
        
        $student = $this->request->getSession()->read('student');
        
        // Verify this assignment belongs to the current student
        if ($assignment->student_id != $student->id) {
            $this->Flash->error(__('Access denied.'));
            return $this->redirect(['action' => 'myassignments']);
        }
        
        // Get student answers and check if already graded
        $studentAnswersTable = TableRegistry::get('StudentAnswers');
        $studentAnswers = $studentAnswersTable->find()
            ->contain(['Questions.QuestionOptions'])
            ->where(['assignment_id' => $assignmentId])
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
        
        // Calculate duration properly handling CakePHP date objects
        $duration = 'Not recorded';
        if ($assignment->start_time && $assignment->end_time) {
            error_log("Calculating duration...");
            
            $startTime = $assignment->start_time instanceof \Cake\I18n\FrozenTime ? 
                        $assignment->start_time->format('Y-m-d H:i:s') : 
                        $assignment->start_time;
            $endTime = $assignment->end_time instanceof \Cake\I18n\FrozenTime ? 
                      $assignment->end_time->format('Y-m-d H:i:s') : 
                      $assignment->end_time;
            
            error_log("Start time (formatted): {$startTime}");
            error_log("End time (formatted): {$endTime}");
            
            $start = new \DateTime($startTime);
            $end = new \DateTime($endTime);
            $diff = $start->diff($end);
            $duration = $diff->format('%H:%I:%S');
            
            error_log("Calculated duration: {$duration}");
        } else {
            error_log("Missing start_time or end_time for duration calculation");
        }
        
        $this->set(compact('assignment', 'studentAnswers', 'totalQuestions', 'correctAnswers', 'totalScore', 'percentage', 'duration'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }
    
    

    /**
     * Edit method
     *
     * @param string|null $id Assignment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $assignment = $this->Assignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $assignment = $this->Assignments->patchEntity($assignment, $this->request->getData());
            if ($this->Assignments->save($assignment)) {
                $this->Flash->success(__('The assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The assignment could not be saved. Please, try again.'));
        }
        $subjects = $this->Assignments->Subjects->find('list', ['limit' => 200])->all();
        $students = $this->Assignments->Students->find('list', ['limit' => 200])->all();
        $sessions = $this->Assignments->Sessions->find('list', ['limit' => 200])->all();
        $this->set(compact('assignment', 'subjects', 'students', 'sessions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Assignment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $assignment = $this->Assignments->get($id);
        if ($this->Assignments->delete($assignment)) {
            $this->Flash->success(__('The assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
          // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        $this->Auth->allow(['newapplicant']);
        $actions = ['submitassignment','editassignment','startcbt','takecbt','viewcbtresult'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
        }
    }
}
