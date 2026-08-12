<?php
declare(strict_types=1);

namespace App\Controller;
 use Cake\ORM\TableRegistry;
use App\Controller\AppController;

/**
 * Studentmessages Controller
 *
 * @property \App\Model\Table\StudentmessagesTable $Studentmessages
 *
 * @method \App\Model\Entity\Studentmessage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StudentmessagesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Users']
        ];
        $studentmessages = $this->paginate($this->Studentmessages);

        $this->set(compact('studentmessages'));
    }

    /**
     * View method
     *
     * @param string|null $id Studentmessage id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function read($id = null)
    {
        $studentmessage = $this->Studentmessages->get($id, [
            'contain' => ['Students', 'Users']
        ]);
        //update the message as seen
        $studentmessage->status = "Seen";
        $this->Studentmessages->save($studentmessage);

        $this->set('studentmessage', $studentmessage);
        
        $this->viewBuilder()->setLayout('adminbackend');
    }

    //admin method for replying to a student's message
    public function adminreply($id,$userid){
       // $admincontroller = new AdminsController();
      // $admin = $admincontroller->isadmin();
          
        $studentmessage = $this->Studentmessages->newEmptyEntity();
        if ($this->request->is('post')) {
            $studentmessage = $this->Studentmessages->patchEntity($studentmessage, $this->request->getData());
            $studentmessage->student_id = $id;
            $studentmessage->user_id = $userid;
            $studentmessage->status = "Unseen";
            $studentmessage->mfor = "Student";
            if ($this->Studentmessages->save($studentmessage)) {
                $this->Flash->success(__('The message has been saved'));

                return $this->redirect(['action' => 'adminreply',$id,$userid]);
            }
            $this->Flash->error(__('The message could not be saved. Please, try again.'));
        }
       // $students = $this->Studentmessages->Students->find('list', ['limit' => 200]);
       // $users = $this->Studentmessages->Users->find('list', ['limit' => 200]);
        $this->set(compact('studentmessage', 'students', 'users'));
        $this->viewBuilder()->setLayout('adminbackend');
        
    }

    
//get the meesgae count for the loggedin user
      public function countmessage($id =null){
          $studentmessages = $this->Studentmessages->find()
                 ->where(['Studentmessages.status'=>'Unseen','mfor'=>'Student','Studentmessages.user_id'=>$id])
                 ->count();
          return $studentmessages;
          
      }

      

      /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newmessage()
    {
        //ensure this is a student
       
        $student = $this->isstudent();
        $studentmessage = $this->Studentmessages->newEmptyEntity();
        if ($this->request->is('post')) {
            $studentmessage = $this->Studentmessages->patchEntity($studentmessage, $this->request->getData());
            $studentmessage->student_id = $student->id;
            $studentmessage->user_id = $student->user_id;
            $studentmessage->status = "Unseen";
            if ($this->Studentmessages->save($studentmessage)) {
                $this->Flash->success(__('The message has been saved. Admin will review and revert to you soon'));

                return $this->redirect(['action' => 'newmessage']);
            }
            $this->Flash->error(__('The message could not be saved. Please, try again.'));
        }
       // $students = $this->Studentmessages->Students->find('list', ['limit' => 200]);
       // $users = $this->Studentmessages->Users->find('list', ['limit' => 200]);
        $this->set(compact('studentmessage', 'students', 'users'));
        $this->viewBuilder()->setLayout('adminbackend');
    }

    
    
      //method that ensure this person is a student
      private function isstudent() {
           $students = TableRegistry::get('Students');
          $student = $students->find()
                  ->contain(['Departments', 'Subjects'])
                  ->where(['user_id' => $this->Auth->user('id')])
                  ->first();
          if (!$student) { //this is not a valid student
              $this->Flash->error(__('Sorry, invalid access'));

              return $this->redirect(['controller'=>'Students','action' => 'index']);
          } else {
              return $student;
          }
      }
      
      
      
    /**
     * Edit method
     *
     * @param string|null $id Studentmessage id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $studentmessage = $this->Studentmessages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentmessage = $this->Studentmessages->patchEntity($studentmessage, $this->request->getData());
            if ($this->Studentmessages->save($studentmessage)) {
                $this->Flash->success(__('The studentmessage has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The studentmessage could not be saved. Please, try again.'));
        }
        $students = $this->Studentmessages->Students->find('list', ['limit' => 200]);
        $users = $this->Studentmessages->Users->find('list', ['limit' => 200]);
        $this->set(compact('studentmessage', 'students', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Studentmessage id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentmessage = $this->Studentmessages->get($id);
        if ($this->Studentmessages->delete($studentmessage)) {
            $this->Flash->success(__('The studentmessage has been deleted.'));
        } else {
            $this->Flash->error(__('The studentmessage could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
