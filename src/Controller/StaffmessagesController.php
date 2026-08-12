<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Staffmessages Controller
 *
 * @property \App\Model\Table\StaffmessagesTable $Staffmessages
 * @method \App\Model\Entity\Staffmessage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StaffmessagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Teachers', 'Users'],
        ];
        $staffmessages = $this->paginate($this->Staffmessages);

        $this->set(compact('staffmessages'));
    }

    /**
     * View method
     *
     * @param string|null $id Staffmessage id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $staffmessage = $this->Staffmessages->get($id, [
            'contain' => ['Teachers', 'Users'],
        ]);

        $this->set(compact('staffmessage'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $staffmessage = $this->Staffmessages->newEmptyEntity();
        if ($this->request->is('post')) {
            $staffmessage = $this->Staffmessages->patchEntity($staffmessage, $this->request->getData());
            if ($this->Staffmessages->save($staffmessage)) {
                $this->Flash->success(__('The staffmessage has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staffmessage could not be saved. Please, try again.'));
        }
        $teachers = $this->Staffmessages->Teachers->find('list', ['limit' => 200]);
        $users = $this->Staffmessages->Users->find('list', ['limit' => 200]);
        $this->set(compact('staffmessage', 'teachers', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Staffmessage id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $staffmessage = $this->Staffmessages->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffmessage = $this->Staffmessages->patchEntity($staffmessage, $this->request->getData());
            if ($this->Staffmessages->save($staffmessage)) {
                $this->Flash->success(__('The staffmessage has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staffmessage could not be saved. Please, try again.'));
        }
        $teachers = $this->Staffmessages->Teachers->find('list', ['limit' => 200]);
        $users = $this->Staffmessages->Users->find('list', ['limit' => 200]);
        $this->set(compact('staffmessage', 'teachers', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Staffmessage id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staffmessage = $this->Staffmessages->get($id);
        if ($this->Staffmessages->delete($staffmessage)) {
            $this->Flash->success(__('The staffmessage has been deleted.'));
        } else {
            $this->Flash->error(__('The staffmessage could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
