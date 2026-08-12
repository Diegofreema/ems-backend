<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Quizzes Controller
 *
 * @property \App\Model\Table\QuizzesTable $Quizzes
 * @method \App\Model\Entity\Quiz[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QuizzesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Faculties', 'Departments', 'Semesters', 'Sessions', 'Subjects'],
        ];
        $quizzes = $this->paginate($this->Quizzes);

        $this->set(compact('quizzes'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Quiz id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $quiz = $this->Quizzes->get($id, [
            'contain' => ['Faculties', 'Departments', 'Semesters', 'Sessions', 'Subjects', 'Quizquestions'],
        ]);

        $this->set(compact('quiz'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $quiz = $this->Quizzes->newEmptyEntity();
        if ($this->request->is('post')) {
            $quiz = $this->Quizzes->patchEntity($quiz, $this->request->getData());
            if ($this->Quizzes->save($quiz)) {
                $this->Flash->success(__('The quiz has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quiz could not be saved. Please, try again.'));
        }
        $faculties = $this->Quizzes->Faculties->find('list', ['limit' => 200]);
        $departments = $this->Quizzes->Departments->find('list', ['limit' => 200]);
        $semesters = $this->Quizzes->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Quizzes->Sessions->find('list', ['limit' => 200]);
        $subjects = $this->Quizzes->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('quiz', 'faculties', 'departments', 'semesters', 'sessions', 'subjects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Quiz id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $quiz = $this->Quizzes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $quiz = $this->Quizzes->patchEntity($quiz, $this->request->getData());
            if ($this->Quizzes->save($quiz)) {
                $this->Flash->success(__('The quiz has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quiz could not be saved. Please, try again.'));
        }
        $faculties = $this->Quizzes->Faculties->find('list', ['limit' => 200]);
        $departments = $this->Quizzes->Departments->find('list', ['limit' => 200]);
        $semesters = $this->Quizzes->Semesters->find('list', ['limit' => 200]);
        $sessions = $this->Quizzes->Sessions->find('list', ['limit' => 200]);
        $subjects = $this->Quizzes->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('quiz', 'faculties', 'departments', 'semesters', 'sessions', 'subjects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Quiz id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $quiz = $this->Quizzes->get($id);
        if ($this->Quizzes->delete($quiz)) {
            $this->Flash->success(__('The quiz has been deleted.'));
        } else {
            $this->Flash->error(__('The quiz could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
