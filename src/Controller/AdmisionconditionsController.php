<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Admisionconditions Controller
 *
 * @property \App\Model\Table\AdmisionconditionsTable $Admisionconditions
 *
 * @method \App\Model\Entity\Admisioncondition[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdmisionconditionsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users']
        ];
        $admisionconditions = $this->paginate($this->Admisionconditions);

        $this->set(compact('admisionconditions'));
    }

    /**
     * View method
     *
     * @param string|null $id Admisioncondition id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $admisioncondition = $this->Admisionconditions->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('admisioncondition', $admisioncondition);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addcondition()
    {
        $admisioncondition = $this->Admisionconditions->newEmptyEntity();
        if ($this->request->is('post')) {
            $admisioncondition = $this->Admisionconditions->patchEntity($admisioncondition, $this->request->getData());
           $admisioncondition->user_id = $this->Auth->user('id');
            if ($this->Admisionconditions->save($admisioncondition)) {
                //log activity
                      $usercontroller = new UsersController();

                      $title = "Updated Admision Conditions ";
                      $user_id = $this->Auth->user('id');
                      $description = "Created new department ";
                      $ip = $this->request->clientIp();
                      $type = "Edit";
                      $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The admision condition has been saved.'));

                return $this->redirect(['action' => 'editcondition',$admisioncondition->id]);
            }
            $this->Flash->error(__('The admision condition could not be saved. Please, try again.'));
        }
        $users = $this->Admisionconditions->Users->find('list', ['limit' => 200]);
        $this->set(compact('admisioncondition', 'users'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Admisioncondition id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editcondition($id = null)
    {
        $admisioncondition = $this->Admisionconditions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $admisioncondition = $this->Admisionconditions->patchEntity($admisioncondition, $this->request->getData());
           $admisioncondition->user_id = $this->Auth->user('id');
            if ($this->Admisionconditions->save($admisioncondition)) {
               //log activity
                      $usercontroller = new UsersController();

                      $title = "Updated Admision Conditions ";
                      $user_id = $this->Auth->user('id');
                      $description = "Created new department ";
                      $ip = $this->request->clientIp();
                      $type = "Edit";
                      $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The update has been successful.'));
                return $this->redirect(['action' => 'editcondition',$id]);
            }
            $this->Flash->error(__('The admisioncondition could not be saved. Please, try again.'));
        }
        $users = $this->Admisionconditions->Users->find('list', ['limit' => 200]);
        $this->set(compact('admisioncondition', 'users'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Admisioncondition id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $admisioncondition = $this->Admisionconditions->get($id);
        if ($this->Admisionconditions->delete($admisioncondition)) {
            $this->Flash->success(__('The admisioncondition has been deleted.'));
        } else {
            $this->Flash->error(__('The admisioncondition could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
