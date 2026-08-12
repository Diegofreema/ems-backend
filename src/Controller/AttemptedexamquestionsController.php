<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Attemptedexamquestions Controller
 *
 * @property \App\Model\Table\AttemptedexamquestionsTable $Attemptedexamquestions
 * @method \App\Model\Entity\Attemptedexamquestion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttemptedexamquestionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Examquestions', 'Students'],
        ];
        $attemptedexamquestions = $this->paginate($this->Attemptedexamquestions);

        $this->set(compact('attemptedexamquestions'));
    }

    /**
     * View method
     *
     * @param string|null $id Attemptedexamquestion id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $attemptedexamquestion = $this->Attemptedexamquestions->get($id, [
            'contain' => ['Examquestions', 'Students'],
        ]);

        $this->set(compact('attemptedexamquestion'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $attemptedexamquestion = $this->Attemptedexamquestions->newEmptyEntity();
        if ($this->request->is('post')) {
            $attemptedexamquestion = $this->Attemptedexamquestions->patchEntity($attemptedexamquestion, $this->request->getData());
            if ($this->Attemptedexamquestions->save($attemptedexamquestion)) {
                $this->Flash->success(__('The attemptedexamquestion has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attemptedexamquestion could not be saved. Please, try again.'));
        }
        $examquestions = $this->Attemptedexamquestions->Examquestions->find('list', ['limit' => 200]);
        $students = $this->Attemptedexamquestions->Students->find('list', ['limit' => 200]);
        $this->set(compact('attemptedexamquestion', 'examquestions', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Attemptedexamquestion id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $attemptedexamquestion = $this->Attemptedexamquestions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $attemptedexamquestion = $this->Attemptedexamquestions->patchEntity($attemptedexamquestion, $this->request->getData());
            if ($this->Attemptedexamquestions->save($attemptedexamquestion)) {
                $this->Flash->success(__('The attemptedexamquestion has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The attemptedexamquestion could not be saved. Please, try again.'));
        }
        $examquestions = $this->Attemptedexamquestions->Examquestions->find('list', ['limit' => 200]);
        $students = $this->Attemptedexamquestions->Students->find('list', ['limit' => 200]);
        $this->set(compact('attemptedexamquestion', 'examquestions', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Attemptedexamquestion id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attemptedexamquestion = $this->Attemptedexamquestions->get($id);
        if ($this->Attemptedexamquestions->delete($attemptedexamquestion)) {
            $this->Flash->success(__('The attemptedexamquestion has been deleted.'));
        } else {
            $this->Flash->error(__('The attemptedexamquestion could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
