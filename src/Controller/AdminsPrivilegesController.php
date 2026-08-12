<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * AdminsPrivileges Controller
 *
 * @property \App\Model\Table\AdminsPrivilegesTable $AdminsPrivileges
 * @method \App\Model\Entity\AdminsPrivilege[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdminsPrivilegesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Admins', 'Privileges'],
        ];
        $adminsPrivileges = $this->paginate($this->AdminsPrivileges);

        $this->set(compact('adminsPrivileges'));
    }

    /**
     * View method
     *
     * @param string|null $id Admins Privilege id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $adminsPrivilege = $this->AdminsPrivileges->get($id, [
            'contain' => ['Admins', 'Privileges'],
        ]);

        $this->set(compact('adminsPrivilege'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $adminsPrivilege = $this->AdminsPrivileges->newEmptyEntity();
        if ($this->request->is('post')) {
            $adminsPrivilege = $this->AdminsPrivileges->patchEntity($adminsPrivilege, $this->request->getData());
            if ($this->AdminsPrivileges->save($adminsPrivilege)) {
                $this->Flash->success(__('The admins privilege has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The admins privilege could not be saved. Please, try again.'));
        }
        $admins = $this->AdminsPrivileges->Admins->find('list', ['limit' => 200]);
        $privileges = $this->AdminsPrivileges->Privileges->find('list', ['limit' => 200]);
        $this->set(compact('adminsPrivilege', 'admins', 'privileges'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Admins Privilege id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $adminsPrivilege = $this->AdminsPrivileges->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $adminsPrivilege = $this->AdminsPrivileges->patchEntity($adminsPrivilege, $this->request->getData());
            if ($this->AdminsPrivileges->save($adminsPrivilege)) {
                $this->Flash->success(__('The admins privilege has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The admins privilege could not be saved. Please, try again.'));
        }
        $admins = $this->AdminsPrivileges->Admins->find('list', ['limit' => 200]);
        $privileges = $this->AdminsPrivileges->Privileges->find('list', ['limit' => 200]);
        $this->set(compact('adminsPrivilege', 'admins', 'privileges'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Admins Privilege id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $adminsPrivilege = $this->AdminsPrivileges->get($id);
        if ($this->AdminsPrivileges->delete($adminsPrivilege)) {
            $this->Flash->success(__('The admins privilege has been deleted.'));
        } else {
            $this->Flash->error(__('The admins privilege could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
