<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Borrowedbooks Controller
 *
 * @property \App\Model\Table\BorrowedbooksTable $Borrowedbooks
 * @method \App\Model\Entity\Borrowedbook[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BorrowedbooksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Books'],
        ];
        $borrowedbooks = $this->paginate($this->Borrowedbooks);

        $this->set(compact('borrowedbooks'));
    }

    /**
     * View method
     *
     * @param string|null $id Borrowedbook id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $borrowedbook = $this->Borrowedbooks->get($id, [
            'contain' => ['Students', 'Books'],
        ]);

        $this->set(compact('borrowedbook'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $borrowedbook = $this->Borrowedbooks->newEmptyEntity();
        if ($this->request->is('post')) {
            $borrowedbook = $this->Borrowedbooks->patchEntity($borrowedbook, $this->request->getData());
            if ($this->Borrowedbooks->save($borrowedbook)) {
                $this->Flash->success(__('The borrowedbook has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The borrowedbook could not be saved. Please, try again.'));
        }
        $students = $this->Borrowedbooks->Students->find('list', ['limit' => 200]);
        $books = $this->Borrowedbooks->Books->find('list', ['limit' => 200]);
        $this->set(compact('borrowedbook', 'students', 'books'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Borrowedbook id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $borrowedbook = $this->Borrowedbooks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $borrowedbook = $this->Borrowedbooks->patchEntity($borrowedbook, $this->request->getData());
            if ($this->Borrowedbooks->save($borrowedbook)) {
                $this->Flash->success(__('The borrowedbook has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The borrowedbook could not be saved. Please, try again.'));
        }
        $students = $this->Borrowedbooks->Students->find('list', ['limit' => 200]);
        $books = $this->Borrowedbooks->Books->find('list', ['limit' => 200]);
        $this->set(compact('borrowedbook', 'students', 'books'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Borrowedbook id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $borrowedbook = $this->Borrowedbooks->get($id);
        if ($this->Borrowedbooks->delete($borrowedbook)) {
            $this->Flash->success(__('The borrowedbook has been deleted.'));
        } else {
            $this->Flash->error(__('The borrowedbook could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
