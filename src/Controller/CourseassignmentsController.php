<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Courseassignments Controller
 *
 * @property \App\Model\Table\CourseassignmentsTable $Courseassignments
 *
 * @method \App\Model\Entity\Courseassignment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseassignmentsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
//        $this->paginate = [
//            'contain' => ['Departments', 'Semesters', 'Levels', 'Users']
//        ];
        $courseassignments = $this->Courseassignments->find()->contain(['Departments', 'Semesters', 'Levels', 'Users']);

        $this->set(compact('courseassignments'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Courseassignment id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    //admin method for viewing courses registered by a student
    public function view($id = null)
    {
        $courseassignment = $this->Courseassignments->get($id, [
            'contain' => ['Departments', 'Semesters', 'Levels', 'Users', 'Subjects']
        ]);

        $this->set('courseassignment', $courseassignment);
          $this->viewBuilder()->setLayout('backend');
    }
    
    
    //student method for viewing their registered courses
    public function myregisteredcourses($id){
          $courseassignment = $this->Courseassignments->get($id, [
            'contain' => ['Departments', 'Semesters', 'Levels', 'Users', 'Subjects']
        ]);

        $this->set('courseassignment', $courseassignment);
          $this->viewBuilder()->setLayout('studentsbackend');
    }

    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function assigncourses()
    {
        //ensure this is an admin
        $admincontroller = new AdminsController();
        $admin = $admincontroller->isadmin();
        if(!$admin){
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
        $courseassignment = $this->Courseassignments->newEmptyEntity();
        if ($this->request->is('post')) {
            $courseassignment = $this->Courseassignments->patchEntity($courseassignment, $this->request->getData());
            $courseassignment->user_id = $this->Auth->user('id');
            if ($this->Courseassignments->save($courseassignment)) {
                $this->Flash->success(__('The courseassignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courseassignment could not be saved. Please, try again.'));
        }
        $departments = $this->Courseassignments->Departments->find('list', ['limit' => 200]);
        $semesters = $this->Courseassignments->Semesters->find('list', ['limit' => 200]);
        $levels = $this->Courseassignments->Levels->find('list', ['limit' => 200]);
        $users = $this->Courseassignments->Users->find('list', ['limit' => 200]);
        $subjects = $this->Courseassignments->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('courseassignment', 'departments', 'semesters', 'levels', 'users', 'subjects'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Courseassignment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editassigncourses($id = null)
    {
        $courseassignment = $this->Courseassignments->get($id, [
            'contain' => ['Subjects']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseassignment = $this->Courseassignments->patchEntity($courseassignment, $this->request->getData());
            if ($this->Courseassignments->save($courseassignment)) {
                $this->Flash->success(__('The courseassignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courseassignment could not be saved. Please, try again.'));
        }
        $departments = $this->Courseassignments->Departments->find('list', ['limit' => 200]);
        $semesters = $this->Courseassignments->Semesters->find('list', ['limit' => 200]);
        $levels = $this->Courseassignments->Levels->find('list', ['limit' => 200]);
        $users = $this->Courseassignments->Users->find('list', ['limit' => 200]);
        $subjects = $this->Courseassignments->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('courseassignment', 'departments', 'semesters', 'levels', 'users', 'subjects'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Courseassignment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseassignment = $this->Courseassignments->get($id);
        if ($this->Courseassignments->delete($courseassignment)) {
            $this->Flash->success(__('The courseassignment has been deleted.'));
        } else {
            $this->Flash->error(__('The courseassignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
