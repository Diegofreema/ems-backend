<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Programes Controller
 *
 * @property \App\Model\Table\ProgramesTable $Programes
 *
 * @method \App\Model\Entity\Programe[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProgramesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function manageprogrames()
    {
        $programes_Table = TableRegistry::get('Programmes');
//        $this->paginate = [
//            'contain' => ['Departments']
//        ];
        $programes = $programes_Table->find();

        $this->set(compact('programes'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Programe id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewprogrames($id = null)
    {
        $programes_Table = TableRegistry::get('Programmes');
        $programe = $programes_Table->get($id, [
          //  'contain' => ['Departments','Departments.Faculties']
        ]);

        $this->set('programe', $programe);
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newprograme()
    {
         $programes_Table = TableRegistry::get('Programmes');
        $programe = $programes_Table->newEmptyEntity();
        if ($this->request->is('post')) {
            $programe = $programes_Table->patchEntity($programe, $this->request->getData());
            if ($programes_Table->save($programe)) {
                 //log activity
                $usercontroller = new UsersController();
               
                 $title = "Added a new programe ".$programe->id;
                $user_id = $this->Auth->user('id');
                $description = "Created new programe " . $programe->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The programe has been saved.'));

                return $this->redirect(['action' => 'manageprogrames']);
            }
            $this->Flash->error(__('The programe could not be saved. Please, try again.'));
        }
        $departments = $programes_Table->Departments->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $this->set(compact('programe', 'departments'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Programe id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updateprograme($id = null)
    {
         $programes_Table = TableRegistry::get('Programmes');
        $programe =  $programes_Table->get($id, [
           // 'contain' => ['Departments']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programe =  $programes_Table->patchEntity($programe, $this->request->getData());
            if ( $programes_Table->save($programe)) {
                 //log activity
                $usercontroller = new UsersController();
               
                 $title = "Updated a programe ".$programe->id;
                $user_id = $this->Auth->user('id');
                $description = "Updated a programme " . $programe->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The programe has been updated.'));

                return $this->redirect(['action' => 'manageprogrames']);
            }
            $this->Flash->error(__('The programe could not be saved. Please, try again.'));
        }
       // $departments = $this->Programes->Departments->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $this->set(compact('programe'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Programe id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $programes_Table = TableRegistry::get('Programmes');
        $this->request->allowMethod(['post', 'delete']);
        $programe = $programes_Table->get($id);
        if ($programes_Table->delete($programe)) {
             //log activity
                $usercontroller = new UsersController();
               
                 $title = "Deleted a programe ".$programe->id;
                $user_id = $this->Auth->user('id');
                $description = "Deleted a programme " . $programe->name;
                $ip = $this->request->clientIp();
                $type = "Delete";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The programe has been deleted.'));
        } else {
            $this->Flash->error(__('The programe could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageprogrames']);
    }
}
