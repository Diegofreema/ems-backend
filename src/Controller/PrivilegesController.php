<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Privileges Controller
 *
 * @property \App\Model\Table\PrivilegesTable $Privileges
 *
 * @method \App\Model\Entity\Privilege[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PrivilegesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $admincontroller = new AdminsController();
         $admincontroller->isadmin();
         
        $privileges = $this->paginate($this->Privileges);

        $this->set(compact('privileges'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Privilege id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewprivilege($id = null)
    {
        $privilege = $this->Privileges->get($id, [
            'contain' => ['Admins.Users.Roles']
        ]);

        $this->set('privilege', $privilege);
         $this->viewBuilder()->setLayout('backend');
    }

    
    //method that check for admin privileges before he performs an action
    public function hasprivilege($privilege_id){
        //get admin data from session
     $admindata =    $this->request->getSession()->read('admin');
     $admin = $this->Privileges->Admins->get($admindata->id,['contain'=>['Privileges']]);
     $privileges = [];
    // debug(json_encode($admindata->privileges, JSON_PRETTY_PRINT)); exit;
     foreach ($admin->privileges as $privilege){
         array_push($privileges, $privilege->id);
     }
     if(in_array($privilege_id, $privileges)){
         return 1;
     }
     else{
         $this->Flash->error(__('Invalid privilege : You do not have the privilege to perform that action'));

                return 0;
     }
       return false; 
    }

    









    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newprivilege()
    {
        $privilege = $this->Privileges->newEmptyEntity();
        if ($this->request->is('post')) {
            $privilege = $this->Privileges->patchEntity($privilege, $this->request->getData());
            if ($this->Privileges->save($privilege)) {
                 //log activity
                  $usercontroller = new UsersController();

                  $title = "created new privilege " . $privilege->name;
                  $user_id = $this->Auth->user('id');
                  $description = "created new privilege ";
                  $ip = $this->request->clientIp();
                  $type = "Add";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The privilege has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The privilege could not be saved. Please, try again.'));
        }
        $admins = $this->Privileges->Admins->find('list', ['limit' => 200]);
        $this->set(compact('privilege', 'admins'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Privilege id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editprivilege($id = null)
    {
        $privilege = $this->Privileges->get($id, [
            'contain' => ['Admins']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $privilege = $this->Privileges->patchEntity($privilege, $this->request->getData());
            if ($this->Privileges->save($privilege)) {
                 //log activity
                  $usercontroller = new UsersController();

                  $title = "updated a privilege " . $privilege->name;
                  $user_id = $this->Auth->user('id');
                  $description = "updated a privilege";
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The privilege has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The privilege could not be saved. Please, try again.'));
        }
        $admins = $this->Privileges->Admins->find('list', ['limit' => 200]);
        $this->set(compact('privilege', 'admins'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Privilege id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {   //check privilege
        $this->request->allowMethod(['post', 'delete']);
        $privilege = $this->Privileges->get($id);
        if ($this->Privileges->delete($privilege)) {
            //log activity
                  $usercontroller = new UsersController();

                  $title = "deleted a privilege " . $privilege->name;
                  $user_id = $this->Auth->user('id');
                  $description = "deleted a privilege";
                  $ip = $this->request->clientIp();
                  $type = "delete";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The privilege has been deleted.'));
        } else {
            $this->Flash->error(__('The privilege could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
