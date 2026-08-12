<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Quizquestions Controller
 *
 * @property \App\Model\Table\QuizquestionsTable $Quizquestions
 * @method \App\Model\Entity\Quizquestion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class QuizquestionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Quizzes'],
        ];
        $quizquestions = $this->paginate($this->Quizquestions);

        $this->set(compact('quizquestions'));
    }

    /**
     * View method
     *
     * @param string|null $id Quizquestion id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $quizquestion = $this->Quizquestions->get($id, [
            'contain' => ['Quizzes', 'Attemptedquizzes'],
        ]);

        $this->set(compact('quizquestion'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $quizquestion = $this->Quizquestions->newEmptyEntity();
        if ($this->request->is('post')) {
            $quizquestion = $this->Quizquestions->patchEntity($quizquestion, $this->request->getData());
            if ($this->Quizquestions->save($quizquestion)) {
                $this->Flash->success(__('The quizquestion has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quizquestion could not be saved. Please, try again.'));
        }
        $quizzes = $this->Quizquestions->Quizzes->find('list', ['limit' => 200]);
        $this->set(compact('quizquestion', 'quizzes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Quizquestion id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $quizquestion = $this->Quizquestions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $quizquestion = $this->Quizquestions->patchEntity($quizquestion, $this->request->getData());
            if ($this->Quizquestions->save($quizquestion)) {
                $this->Flash->success(__('The quizquestion has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The quizquestion could not be saved. Please, try again.'));
        }
        $quizzes = $this->Quizquestions->Quizzes->find('list', ['limit' => 200]);
        $this->set(compact('quizquestion', 'quizzes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Quizquestion id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $quizquestion = $this->Quizquestions->get($id);
        if ($this->Quizquestions->delete($quizquestion)) {
            $this->Flash->success(__('The quizquestion has been deleted.'));
        } else {
            $this->Flash->error(__('The quizquestion could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
