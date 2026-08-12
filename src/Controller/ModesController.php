<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Modes Controller
 *
 * @property \App\Model\Table\ModesTable $Modes
 * @method \App\Model\Entity\Mode[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ModesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $modes = $this->paginate($this->Modes);

        $this->set(compact('modes'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Mode id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mode = $this->Modes->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('mode'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mode = $this->Modes->newEmptyEntity();
        if ($this->request->is('post')) {
            $mode = $this->Modes->patchEntity($mode, $this->request->getData());
            if ($this->Modes->save($mode)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Added new admission  mode " . $mode->name;
                $user_id = $this->Auth->user('id');
                $description = "Added new admission  mode" .  $mode->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The mode has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The mode could not be saved. Please, try again.'));
        }
        $this->set(compact('mode'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Mode id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mode = $this->Modes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mode = $this->Modes->patchEntity($mode, $this->request->getData());
            if ($this->Modes->save($mode)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Updated admission  mode " . $mode->name;
                $user_id = $this->Auth->user('id');
                $description = "Updated admission  mode" .  $mode->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The mode has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The mode could not be saved. Please, try again.'));
        }
        $this->set(compact('mode'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Mode id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mode = $this->Modes->get($id);
        if ($this->Modes->delete($mode)) {
             //log activity
                $usercontroller = new UsersController();

                $title = "Deleted an admission  mode " . $mode->name;
                $user_id = $this->Auth->user('id');
                $description = "Deleted an admission  mode" .  $mode->name;
                $ip = $this->request->clientIp();
                $type = "Delete";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The mode has been deleted.'));
        } else {
            $this->Flash->error(__('The mode could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
