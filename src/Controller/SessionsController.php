<?php
declare(strict_types=1);

namespace App\Controller;
 use Cake\Mailer\Email;
  use Cake\Event\Event;
  use Cake\ORM\TableRegistry;
use App\Controller\AppController;

/**
 * Sessions Controller
 *
 * @property \App\Model\Table\SessionsTable $Sessions
 *
 * @method \App\Model\Entity\Session[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SessionsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function managesessions()
    {
        $this->paginate = [
            'contain' => ['Users']
        ];
        $sessions = $this->paginate($this->Sessions);

        $this->set(compact('sessions'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Session id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $session = $this->Sessions->get($id, [
            'contain' => ['Users', 'Settings', 'Transactions']
        ]);

        $this->set('session', $session);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newsession()
    {
        $session = $this->Sessions->newEmptyEntity();
        if ($this->request->is('post')) {
            $session = $this->Sessions->patchEntity($session, $this->request->getData());
            $session->user_id = $this->Auth->user('id');
            if ($this->Sessions->save($session)) {
                //log activity
                $usercontroller = new UsersController();
               
                 $title = "Added a New Session ". $session->name;
                $user_id = $this->Auth->user('id');
                $description = "Created new session " . $session->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The session has been saved.'));

                return $this->redirect(['action' => 'managesessions']);
            }
            $this->Flash->error(__('The session could not be saved. Please, try again.'));
        }
       // $users = $this->Sessions->Users->find('list', ['limit' => 200]);
        $this->set(compact('session'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Session id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updatesession($id = null)
    {
        $session = $this->Sessions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $session = $this->Sessions->patchEntity($session, $this->request->getData());
            if ($this->Sessions->save($session)) {
                 //log activity
                $usercontroller = new UsersController();
               
                 $title = "Updated a session ".$session->name;
                $user_id = $this->Auth->user('id');
                $description = "Updated a Session " . $session->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The session has been saved.'));

                return $this->redirect(['action' => 'managesessions']);
            }
            $this->Flash->error(__('The session could not be saved. Please, try again.'));
        }
       // $users = $this->Sessions->Users->find('list', ['limit' => 200]);
        $this->set(compact('session'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Session id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $session = $this->Sessions->get($id);
        if ($this->Sessions->delete($session)) {
             //log activity
                $usercontroller = new UsersController();
               
                 $title = "Deleted a session ".$session->name;
                $user_id = $this->Auth->user('id');
                $description = "Deleted a Session " . $session->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The session has been deleted.'));
        } else {
            $this->Flash->error(__('The session could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'managesessions']);
    }
}
