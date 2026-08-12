<?php
declare(strict_types=1);

namespace App\Controller;
 use Cake\Event\EventInterface;
 use Cake\ORM\TableRegistry;

/**
 * Setassignments Controller
 *
 * @property \App\Model\Table\SetassignmentsTable $Setassignments
 * @method \App\Model\Entity\Setassignment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SetassignmentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
          $teacher =  $this->request->getSession()->read('teacher');
        $this->paginate = [
            'contain' => ['Subjects.Departments', 'Teachers', 'Semesters'], 'where'=>['teacher_id'=>$teacher->id]
        ];
        $setassignments = $this->paginate($this->Setassignments);
        
        // Get question counts for each setassignment
        $questionsTable = TableRegistry::get('Questions');
        $questionCounts = [];
        foreach ($setassignments as $setassignment) {
            $questionCounts[$setassignment->id] = $questionsTable->find()
                ->where(['setassignment_id' => $setassignment->id])
                ->count();
        }
         

        $this->set(compact('setassignments', 'questionCounts'));
         $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * View method
     *
     * @param string|null $id Setassignment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $setassignment = $this->Setassignments->get($id, [
            'contain' => ['Subjects.Departments', 'Teachers', 'Semesters'],
        ]);
        
        // Get questions for this assignment
        $questionsTable = TableRegistry::get('Questions');
        $questions = $questionsTable->find()
            ->contain(['QuestionOptions'])
            ->where(['setassignment_id' => $id])
            ->order(['order_number' => 'ASC'])
            ->all();
        
        // Count student submissions for this assignment
        $assignmentsTable = TableRegistry::get('Assignments');
        $submissionCount = $assignmentsTable->find()->where(['setassignment_id' => $id])->count();

        $this->set(compact('setassignment', 'questions', 'submissionCount'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addassignment()
    {
        $teacher =  $this->request->getSession()->read('teacher');
         $settings = $this->request->getSession()->read('settings');
        $setassignment = $this->Setassignments->newEmptyEntity();
        if ($this->request->is('post')) {
            $setassignment = $this->Setassignments->patchEntity($setassignment, $this->request->getData());
            $setassignment->teacher_id = $teacher->id;
            $setassignment->semester_id = $settings->semester_id;
            $setassignment->test_type = 'cbt_test'; // Set default to CBT test
            $setassignment->datecreated = date('Y-m-d H:i:s'); // Set creation date
            
            if ($this->Setassignments->save($setassignment)) {
                $this->Flash->success(__('The test has been created successfully. Now add questions.'));

                return $this->redirect(['action' => 'managequestions', $setassignment->id]);
            }
            
            // Show validation errors if save fails
            if ($setassignment->hasErrors()) {
                $this->Flash->error(__('Please correct the errors below.'));
            } else {
                $this->Flash->error(__('The test could not be created. Please, try again.'));
            }
        }
        $teacherscontroller = new TeachersController();
        $subids = $teacherscontroller->getteachercourses();
        
        // Load subjects with department information
        if (empty($subids)) {
            // No subjects assigned to teacher, return empty result
            $subjectsQuery = $this->Setassignments->Subjects->find()
                ->contain(['Departments'])
                ->where(['Subjects.id' => 0]) // This will return no results
                ->order(['Subjects.name' => 'ASC']);
        } else {
            $subjectsQuery = $this->Setassignments->Subjects->find()
                ->contain(['Departments'])
                ->where(['Subjects.id IN' => $subids])
                ->order(['Subjects.name' => 'ASC']);
        }
        
        // Create a custom list with subject name and class
        $subjects = [];
        foreach ($subjectsQuery as $subject) {
            if (isset($subject->department) && !empty($subject->department->name)) {
                $subjects[$subject->id] = $subject->name . ' (' . $subject->department->name . ')';
            } else {
                $subjects[$subject->id] = $subject->name;
            }
        }
        $teachers = $this->Setassignments->Teachers->find('list', ['limit' => 200])->all();
        $semesters = $this->Setassignments->Semesters->find('list', ['limit' => 200])->all();
        $this->set(compact('setassignment', 'subjects', 'teachers', 'semesters'));
         $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Setassignment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editassignment($id = null)
    {
        $setassignment = $this->Setassignments->get($id, [
            'contain' => ['Subjects.Departments'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $setassignment = $this->Setassignments->patchEntity($setassignment, $this->request->getData());
            if ($this->Setassignments->save($setassignment)) {
                $this->Flash->success(__('The test has been updated successfully.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The test could not be updated. Please, try again.'));
        }
        
        // Format dates for datetime-local inputs
        if ($setassignment->opendate) {
            // Convert date to datetime-local format (YYYY-MM-DDTHH:MM)
            if (is_string($setassignment->opendate)) {
                // If it's a string date, add default time
                $setassignment->opendate = $setassignment->opendate . 'T00:00';
            } else {
                // If it's a DateTime object, format it properly
                $setassignment->opendate = $setassignment->opendate->format('Y-m-d\TH:i');
            }
        }
        
        if ($setassignment->closedate) {
            // Convert date to datetime-local format (YYYY-MM-DDTHH:MM)
            if (is_string($setassignment->closedate)) {
                // If it's a string date, add default time (end of day)
                $setassignment->closedate = $setassignment->closedate . 'T23:59';
            } else {
                // If it's a DateTime object, format it properly
                $setassignment->closedate = $setassignment->closedate->format('Y-m-d\TH:i');
            }
        }
        
        // Only show the current assignment's subject, not all teacher subjects
        $subjects = [$setassignment->subject->id => $setassignment->subject->name . ' (' . $setassignment->subject->department->name . ')'];
        $teachers = $this->Setassignments->Teachers->find('list', ['limit' => 200])->all();
        $semesters = $this->Setassignments->Semesters->find('list', ['limit' => 200])->all();
        $this->set(compact('setassignment', 'subjects', 'teachers', 'semesters'));
          $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Manage Questions for a test
     *
     * @param string|null $id Setassignment id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function managequestions($id = null)
    {
        $setassignment = $this->Setassignments->get($id, [
            'contain' => ['Subjects.Departments', 'Teachers', 'Semesters'],
        ]);
        
        // Get existing questions
        $questionsTable = TableRegistry::get('Questions');
        $questions = $questionsTable->find()
            ->contain(['QuestionOptions'])
            ->where(['setassignment_id' => $id])
            ->order(['order_number' => 'ASC'])
            ->all();
        
        $this->set(compact('setassignment', 'questions'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Add Question to a test
     *
     * @param string|null $id Setassignment id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function addquestion($id = null)
    {
        $setassignment = $this->Setassignments->get($id, [
            'contain' => ['Subjects.Departments'],
        ]);
        
        // Check if assignment has reached question limit
        $questionsTable = TableRegistry::get('Questions');
        $currentQuestionCount = $questionsTable->find()
            ->where(['setassignment_id' => $id])
            ->count();
            
        if ($currentQuestionCount >= $setassignment->total_questions) {
            $this->Flash->error(__('You have reached the maximum number of questions (' . $setassignment->total_questions . ') for this assignment.'));
            return $this->redirect(['action' => 'managequestions', $id]);
        }
        
        if ($this->request->is('post')) {
            $question = $questionsTable->newEmptyEntity();
            
            $data = $this->request->getData();
            
            // Debug: Log the submitted data
            error_log('=== ADD QUESTION DEBUG ===');
            error_log('POST Data: ' . print_r($data, true));
            
            $question = $questionsTable->patchEntity($question, $data);
            $question->setassignment_id = $id;
            
            if ($questionsTable->save($question)) {
                error_log('Question saved successfully with ID: ' . $question->id);
                
                // If multiple choice, save options
                if ($data['question_type'] === 'multiple_choice' && isset($data['options'])) {
                    error_log('Processing multiple choice options: ' . print_r($data['options'], true));
                    error_log('Correct option index: ' . $data['correct_option']);
                    
                    $optionsTable = TableRegistry::get('QuestionOptions');
                    $optionsSaved = 0;
                    
                    foreach ($data['options'] as $index => $optionText) {
                        if (!empty($optionText)) {
                            $option = $optionsTable->newEmptyEntity();
                            $option->question_id = $question->id;
                            $option->option_text = $optionText;
                            $option->is_correct = ($data['correct_option'] == $index);
                            $option->order_number = $index + 1;
                            
                            if ($optionsTable->save($option)) {
                                $optionsSaved++;
                                error_log("Option saved: '{$optionText}' (Correct: " . ($option->is_correct ? 'Yes' : 'No') . ")");
                            } else {
                                error_log("Failed to save option: '{$optionText}'");
                            }
                        }
                    }
                    
                    error_log("Total options saved: {$optionsSaved}");
                } else {
                    error_log('Not multiple choice or no options provided');
                }
                
                error_log('=== ADD QUESTION DEBUG END ===');
                
                $this->Flash->success(__('Question added successfully.'));
                return $this->redirect(['action' => 'managequestions', $id]);
            }
            
            error_log('Failed to save question: ' . print_r($question->getErrors(), true));
            error_log('=== ADD QUESTION DEBUG END ===');
            
            $this->Flash->error(__('Question could not be added. Please, try again.'));
        }
        
        $this->set(compact('setassignment'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Edit Question
     *
     * @param string|null $id Question id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function editquestion($id = null)
    {
        $questionsTable = TableRegistry::get('Questions');
        $question = $questionsTable->get($id, [
            'contain' => ['QuestionOptions', 'Setassignments.Subjects.Departments'],
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            
            // Debug: Log the submitted data
            error_log('=== EDIT QUESTION DEBUG ===');
            error_log('POST Data: ' . print_r($data, true));
            
            $question = $questionsTable->patchEntity($question, $data);
            
            // Debug: Check for validation errors
            if ($question->hasErrors()) {
                error_log('Validation errors: ' . print_r($question->getErrors(), true));
            }
            
            if ($questionsTable->save($question)) {
                // Update options for multiple choice
                if ($data['question_type'] === 'multiple_choice' && isset($data['options'])) {
                    error_log('Processing multiple choice options: ' . print_r($data['options'], true));
                    error_log('Correct option index: ' . $data['correct_option']);
                    
                    $optionsTable = TableRegistry::get('QuestionOptions');
                    // Delete existing options
                    $optionsTable->deleteAll(['question_id' => $question->id]);
                    
                    // Add new options
                    $optionsSaved = 0;
                    foreach ($data['options'] as $optionId => $optionText) {
                        if (!empty($optionText)) {
                            $option = $optionsTable->newEmptyEntity();
                            $option->question_id = $question->id;
                            $option->option_text = $optionText;
                            $option->is_correct = ($data['correct_option'] == $optionId);
                            $option->order_number = $optionsSaved + 1;
                            
                            if ($optionsTable->save($option)) {
                                $optionsSaved++;
                                error_log("Option saved: '{$optionText}' (Correct: " . ($option->is_correct ? 'Yes' : 'No') . ")");
                            } else {
                                error_log("Failed to save option: '{$optionText}'");
                            }
                        }
                    }
                    
                    error_log("Total options saved: {$optionsSaved}");
                } else {
                    error_log('Not multiple choice or no options provided');
                }
                
                error_log('=== EDIT QUESTION DEBUG END ===');
                $this->Flash->success(__('Question updated successfully.'));
                return $this->redirect(['action' => 'managequestions', $question->setassignment_id]);
            }
            
            error_log('Failed to save question: ' . print_r($question->getErrors(), true));
            error_log('=== EDIT QUESTION DEBUG END ===');
            $this->Flash->error(__('Question could not be updated. Please, try again.'));
        }
        
        $setassignment = $question->setassignment;
        $this->set(compact('question', 'setassignment'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Delete Question
     *
     * @param string|null $id Question id.
     * @return \Cake\Http\Response|null|void Redirects to managequestions.
     */
    public function deletequestion($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $questionsTable = TableRegistry::get('Questions');
        $question = $questionsTable->get($id);
        
        if ($questionsTable->delete($question)) {
            $this->Flash->success(__('Question deleted successfully.'));
        } else {
            $this->Flash->error(__('Question could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'managequestions', $question->setassignment_id]);
    }

    /**
     * View Student Submissions
     *
     * @param string|null $id Setassignment id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function viewsubmissions($id = null)
    {
        $setassignment = $this->Setassignments->get($id, [
            'contain' => ['Subjects.Departments'],
        ]);
        
        // Get all student submissions
        $assignmentsTable = TableRegistry::get('Assignments');
        $submissions = $assignmentsTable->find()
            ->contain(['Students', 'Subjects', 'StudentAnswers.Questions.QuestionOptions'])
            ->where(['setassignment_id' => $id])
            ->all();
        

        
        $this->set(compact('setassignment', 'submissions'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Grade Student Submission
     *
     * @param string|null $id Assignment id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function gradesubmission($id = null)
    {
        $assignmentsTable = TableRegistry::get('Assignments');
        $submission = $assignmentsTable->get($id, [
            'contain' => ['Students.ClassArms', 'Students.Departments', 'StudentAnswers.Questions.QuestionOptions', 'Setassignments.Subjects.Departments'],
        ]);
        
        // Check if submission is already graded - only block POST requests
        if ($this->request->is(['patch', 'post', 'put']) && isset($submission->graded_at) && !empty($submission->graded_at)) {
            $this->Flash->warning(__('This submission has already been graded on ' . $submission->graded_at->format('d M Y, H:i') . '. Grading is locked to prevent changes.'));
            return $this->redirect(['action' => 'gradesubmission', $submission->id]);
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            

            
            // Get the StudentAnswers table to update individual answer scores
            $studentAnswersTable = TableRegistry::get('StudentAnswers');
            
            // Calculate total score and update individual answer scores
            $totalScore = 0;
            $totalQuestions = 0;
            
            foreach ($submission->student_answers as $answer) {
                $totalQuestions++;
                
                if ($answer->question->question_type === 'multiple_choice') {
                    // Auto-grade multiple choice questions
                    foreach ($answer->question->question_options as $option) {
                        if ($option->id == $answer->selected_option_id && $option->is_correct) {
                            $totalScore += $answer->question->points;
                            $answer->theory_score = $answer->question->points; // Store score in theory_score field
                            break;
                        }
                    }
                } else {
                    // Handle theory question grades from teacher input
                    $theoryScoreKey = 'theory_score_' . $answer->id;
                    if (isset($data[$theoryScoreKey])) {
                        $theoryScore = (int)$data[$theoryScoreKey];
                        $totalScore += $theoryScore;
                        $answer->theory_score = $theoryScore;
                        
                        // Save the individual answer score
                        $studentAnswersTable->save($answer);
                    } else {
                        // If no score provided, default to 0
                        $answer->theory_score = 0;
                        $studentAnswersTable->save($answer);
                    }
                }
            }
            
            // Save the main submission with total score
            $submission->total_score = $totalScore;
            $submission->teacher_comments = $data['teacher_comments'] ?? '';
            $submission->graded_at = date('Y-m-d H:i:s');
            
            if ($assignmentsTable->save($submission)) {
                $this->Flash->success(__('Submission graded successfully. Total Score: ' . $totalScore . '/' . $totalQuestions));
                return $this->redirect(['action' => 'viewsubmissions', $submission->setassignment_id]);
            }
            $this->Flash->error(__('Could not save grade. Please, try again.'));
        }
        
        $this->set(compact('submission'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Setassignment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $setassignment = $this->Setassignments->get($id);
        if ($this->Setassignments->delete($setassignment)) {
            $this->Flash->success(__('The test has been deleted.'));
        } else {
            $this->Flash->error(__('The test could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * Get question count for a setassignment
     *
     * @param int $setassignmentId The setassignment ID
     * @return int The number of questions
     */
    public function getQuestionCount($setassignmentId)
    {
        $questionsTable = TableRegistry::get('Questions');
        return $questionsTable->find()->where(['setassignment_id' => $setassignmentId])->count();
    }

    // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        $this->Auth->allow(['newapplicant']);
        $actions = ['addassignment','editassignment', 'addquestion', 'editquestion', 'gradesubmission'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
        }
    }
}
