<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Userlogins Controller
 *
 * @property \App\Model\Table\UserloginsTable $Userlogins
 * @method \App\Model\Entity\Userlogin[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserloginsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $userlogins = $this->paginate($this->Userlogins);

        $this->set(compact('userlogins'));
    }

    /**
     * View method
     *
     * @param string|null $id Userlogin id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userlogin = $this->Userlogins->get($id, [
            'contain' => ['Users'],
        ]);

        $this->set(compact('userlogin'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userlogin = $this->Userlogins->newEmptyEntity();
        if ($this->request->is('post')) {
            $userlogin = $this->Userlogins->patchEntity($userlogin, $this->request->getData());
            if ($this->Userlogins->save($userlogin)) {
                $this->Flash->success(__('The userlogin has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The userlogin could not be saved. Please, try again.'));
        }
        $users = $this->Userlogins->Users->find('list', ['limit' => 200]);
        $this->set(compact('userlogin', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Userlogin id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userlogin = $this->Userlogins->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userlogin = $this->Userlogins->patchEntity($userlogin, $this->request->getData());
            if ($this->Userlogins->save($userlogin)) {
                $this->Flash->success(__('The userlogin has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The userlogin could not be saved. Please, try again.'));
        }
        $users = $this->Userlogins->Users->find('list', ['limit' => 200]);
        $this->set(compact('userlogin', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Userlogin id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userlogin = $this->Userlogins->get($id);
        if ($this->Userlogins->delete($userlogin)) {
            $this->Flash->success(__('The userlogin has been deleted.'));
        } else {
            $this->Flash->error(__('The userlogin could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
