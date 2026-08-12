<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Constants Controller
 *
 * @property \App\Model\Table\ConstantsTable $Constants
 * @method \App\Model\Entity\Constant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ConstantsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $constants = $this->paginate($this->Constants);

        $this->set(compact('constants'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Constant id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $constant = $this->Constants->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('constant'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function newgradepoint()
    {
        $constant = $this->Constants->newEmptyEntity();
        if ($this->request->is('post')) {
            $constant = $this->Constants->patchEntity($constant, $this->request->getData());
            if ($this->Constants->save($constant)) {
                //log activity
                    $usercontroller = new UsersController();

                    $title = "Added New Grade Point " . $constant->value;
                    $user_id = $this->Auth->user('id');
                    $username = $this->Auth->user('username');
                    $description =  $username." Added a Grade point " . $constant->name;
                    $ip = $this->request->clientIp();
                    $type = "Add";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The grade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The grade could not be saved. Please, try again.'));
        }
        $this->set(compact('constant'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Constant id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editgradepoint($id = null)
    {
        $constant = $this->Constants->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $constant = $this->Constants->patchEntity($constant, $this->request->getData());
            if ($this->Constants->save($constant)) {
                //log activity
                    $usercontroller = new UsersController();

                    $title = "Grade Point Update " . $constant->value;
                    $user_id = $this->Auth->user('id');
                    $username = $this->Auth->user('username');
                    $description =  $username." Updated a Grade point " . $constant->name;
                    $ip = $this->request->clientIp();
                    $type = "Edit";
                    $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The grade has been saved.'));

                return $this->redirect(['action' => 'index']);
                
            }
            $this->Flash->error(__('The grade could not be saved. Please, try again.'));
        }
        $this->set(compact('constant'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Constant id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $constant = $this->Constants->get($id);
        if ($this->Constants->delete($constant)) {
            $this->Flash->success(__('The grade has been deleted.'));
        } else {
            $this->Flash->error(__('The grade could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
