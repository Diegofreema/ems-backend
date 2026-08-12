<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Faculties Controller
 *
 * @property \App\Model\Table\FacultiesTable $Faculties
 *
 * @method \App\Model\Entity\Faculty[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FacultiesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function managefaculties()
    {
        $faculties = $this->paginate($this->Faculties);

        $this->set(compact('faculties'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Faculty id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewfaculty($id = null)
    {
        $faculty = $this->Faculties->get($id, [
            'contain' => ['Departments']
        ]);

        $this->set('faculty', $faculty);
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newfaculty()
    {
        $faculty = $this->Faculties->newEmptyEntity();
        if ($this->request->is('post')) {
            $faculty = $this->Faculties->patchEntity($faculty, $this->request->getData());
            if ($this->Faculties->save($faculty)) {
                 //log activity
                $usercontroller = new UsersController();
               
                 $title = "Added a new Faculty ".$faculty->name;
                $user_id = $this->Auth->user('id');
                $description = "Created a new Faculty " . $faculty->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The faculty has been saved.'));

                return $this->redirect(['action' => 'managefaculties']);
            }
            $this->Flash->error(__('The faculty could not be saved. Please, try again.'));
        }
        $this->set(compact('faculty'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Faculty id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updatefaculty($id = null)
    {
        $faculty = $this->Faculties->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $faculty = $this->Faculties->patchEntity($faculty, $this->request->getData());
            if ($this->Faculties->save($faculty)) {
                 //log activity
                $usercontroller = new UsersController();
               
                 $title = "Updated a department ".$faculty->name;
                $user_id = $this->Auth->user('id');
                $description = "Updated a Faculty " . $faculty->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The faculty has been updated.'));

                return $this->redirect(['action' => 'managefaculties']);
            }
            $this->Flash->error(__('The faculty could not be saved. Please, try again.'));
        }
        $this->set(compact('faculty'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Faculty id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $faculty = $this->Faculties->get($id);
        if ($this->Faculties->delete($faculty)) {
            //log activity
                $usercontroller = new UsersController();
               
                 $title = "Deleted a faculty ".$id;
                $user_id = $this->Auth->user('id');
                $description = "Deleted a faculty " . $faculty->name;
                $ip = $this->request->clientIp();
                $type = "Delete";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type); 
            $this->Flash->success(__('The faculty has been deleted.'));
        } else {
            $this->Flash->error(__('The faculty could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'managefaculties']);
    }
}
