<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Attemptedquizzes Controller
 *
 * @property \App\Model\Table\AttemptedquizzesTable $Attemptedquizzes
 * @method \App\Model\Entity\Attemptedquiz[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttemptedquizzesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Quizquestions', 'Students'],
        ];
        $attemptedquizzes = $this->paginate($this->Attemptedquizzes);

        $this->set(compact('attemptedquizzes'));
    }

    /**
     * View method
     *
     * @param string|null $id Attemptedquiz id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $attemptedquiz = $this->Attemptedquizzes->get($id, [
            'contain' => ['Quizquestions', 'Students'],
        ]);

        $this->set(compact('attemptedquiz'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $attemptedquiz = $this->Attemptedquizzes->newEmptyEntity();
        if ($this->request->is('post')) {
            $attemptedquiz = $this->Attemptedquizzes->patchEntity($attemptedquiz, $this->request->getData());
            if ($this->Attemptedquizzes->save($attemptedquiz)) {
                $this->Flash->success(__('The attemptedquiz has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attemptedquiz could not be saved. Please, try again.'));
        }
        $quizquestions = $this->Attemptedquizzes->Quizquestions->find('list', ['limit' => 200]);
        $students = $this->Attemptedquizzes->Students->find('list', ['limit' => 200]);
        $this->set(compact('attemptedquiz', 'quizquestions', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Attemptedquiz id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $attemptedquiz = $this->Attemptedquizzes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $attemptedquiz = $this->Attemptedquizzes->patchEntity($attemptedquiz, $this->request->getData());
            if ($this->Attemptedquizzes->save($attemptedquiz)) {
                $this->Flash->success(__('The attemptedquiz has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attemptedquiz could not be saved. Please, try again.'));
        }
        $quizquestions = $this->Attemptedquizzes->Quizquestions->find('list', ['limit' => 200]);
        $students = $this->Attemptedquizzes->Students->find('list', ['limit' => 200]);
        $this->set(compact('attemptedquiz', 'quizquestions', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Attemptedquiz id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attemptedquiz = $this->Attemptedquizzes->get($id);
        if ($this->Attemptedquizzes->delete($attemptedquiz)) {
            $this->Flash->success(__('The attemptedquiz has been deleted.'));
        } else {
            $this->Flash->error(__('The attemptedquiz could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
