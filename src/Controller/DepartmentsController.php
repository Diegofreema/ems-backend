<?php
declare(strict_types=1);

  namespace App\Controller;

  use Cake\Mailer\Email;
  use Cake\Event\Event;
  use Cake\ORM\TableRegistry;
  use App\Controller\AppController;

/**
 * Departments Controller
 *
 * @property \App\Model\Table\DepartmentsTable $Departments
 *
 * @method \App\Model\Entity\Department[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function managedepartments() {
//        $this->paginate = [
//            'contain' => ['Faculties']
//        ];
          $departments = $this->Departments->find();

          $this->set(compact('departments'));
          $this->viewBuilder()->setLayout('backend');
      }

      
        //method i used to move departments and faculties from cbt
      public function getdeptsandfacs(){
          
          $cats_Table = TableRegistry::get('Subcategory');
           $depts_Table = TableRegistry::get('Departments');
          $cats =  $cats_Table->find(); $count = 0;
          foreach ($cats as $cat){
              $count++;
             $dept =   $depts_Table->newEmptyEntity();
             if($cat->category_id==27){
              $dept->name = $cat->subcategory_name;
              $dept->faculty_id = 2;
              $depts_Table->save($dept);
             }
             elseif($cat->category_id==26){
                 $dept->name = $cat->subcategory_name;
              $dept->faculty_id = 1;
              $depts_Table->save($dept);
             }
              elseif($cat->category_id==29){
                 $dept->name = $cat->subcategory_name;
              $dept->faculty_id = 3;
              $depts_Table->save($dept);
             }
              elseif($cat->category_id==30){
                 $dept->name = $cat->subcategory_name;
              $dept->faculty_id = 5;
              $depts_Table->save($dept);
             }
              elseif($cat->category_id==28){
                 $dept->name = $cat->subcategory_name;
              $dept->faculty_id = 4;
              $depts_Table->save($dept);
             }
              
          }
            echo ' total of '.$count; exit;
          
           $this->viewBuilder()->setLayout('backend');
      }

      
      
      
      
      

    /**
     * View method
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
      
      
       public function viewdepartment($id = null) {
          $department = $this->Departments->get($id, [
              'contain' => ['Fees', 'Subjects.Semesters','Subjects.Levels','Semesters', 'ClassArms.Teachers.Users', 'ClassArms.Students']
          ]);
         //  debug(json_encode( $department, JSON_PRETTY_PRINT)); exit;
          $this->set('department', $department);
          $this->viewBuilder()->setLayout('backend');
      }
      
      
      
      
//    public function view($id = null)
//    {
//        $department = $this->Departments->get($id, [
//            'contain' => ['Faculties', 'Subjects', 'Fees', 'Levels', 'Semesters', 'Admins', 'Coursematerials', 'Departmentssubjects', 'Dstudents', 'Feeallocations', 'Results', 'Students', 'Teachers', 'Users']
//        ]);
//
//        $this->set('department', $department);
//    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addcourses()
    {
        $department = $this->Departments->newEmptyEntity();
        if ($this->request->is('post')) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());
            if ($this->Departments->save($department)) {
                $this->Flash->success(__('The department has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department could not be saved. Please, try again.'));
        }
        $faculties = $this->Departments->Faculties->find('list', ['limit' => 200]);
        $subjects = $this->Departments->Subjects->find('list', ['limit' => 200]);
        $fees = $this->Departments->Fees->find('list', ['limit' => 200]);
        $levels = $this->Departments->Levels->find('list', ['limit' => 200]);
        $semesters = $this->Departments->Subjects->Semesters->find('list', ['limit' => 200]);
        $this->set(compact('department', 'faculties', 'subjects', 'fees', 'levels', 'semesters'));
        $this->viewBuilder()->setLayout('backend');
    }
    
    
    
    
    //admin method for creating new classes
    
    public function newdepartment() {
          $department = $this->Departments->newEmptyEntity();
          if ($this->request->is('post')) {
              $data = $this->request->getData();
              // Set deptcode to match name
              $data['deptcode'] = $data['name'];
              $department = $this->Departments->patchEntity($department, $data);
            //   debug($department); exit;
              if ($this->Departments->save($department)) {
                  //log activity
                  $usercontroller = new UsersController();

                  $title = "Created a new class " . $department->id;
                  $user_id = $this->Auth->user('id');
                  $description = "Created new class " . $department->name;
                  $ip = $this->request->clientIp();
                  $type = "Add";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                  $this->Flash->success(__('The class has been added successfully.'));

                  return $this->redirect(['action' => 'managedepartments']);
              }
              $this->Flash->error(__('The class could not be saved. Please, try again.'));
          }
        //   $faculties = $this->Departments->Faculties->find('list', ['limit' => 200]);
          $fees = $this->Departments->Fees->find('list', ['limit' => 200]);
        //   $programes = $this->Departments->Programmes->find('list', ['limit' => 200])->order(['name'=>'ASC']);
          $this->set(compact('department', 'fees'));
          $this->viewBuilder()->setLayout('backend');
      }


      
      
      //admin method for updating a departmen
      public function updatedepartment($id = null) {
            $department = $this->Departments->get($id, [
            'contain' => ['Fees', 'Levels', 'Programmes', 'Semesters'],
        ]);
          if ($this->request->is(['patch', 'post', 'put'])) {
              $department = $this->Departments->patchEntity($department, $this->request->getData());
              if ($this->Departments->save($department)) {
                  //log activity
                  $usercontroller = new UsersController();

                  $title = "Updated a department " . $id;
                  $user_id = $this->Auth->user('id');
                  $description = "Updated a department " . $department->name;
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);

                  $this->Flash->success(__('The department has been updated.'));

                  return $this->redirect(['action' => 'managedepartments']);
              }
              $this->Flash->error(__('The department could not be updated. Please, try again.'));
          }
          $faculties = $this->Departments->Faculties->find('list', ['limit' => 200]);
          $fees = $this->Departments->Fees->find('list', ['limit' => 200]);
         $programes = $this->Departments->Programmes->find('list', ['limit' => 200])->order(['name'=>'ASC']);
          $this->set(compact('department', 'faculties', 'fees','programes'));
          $this->viewBuilder()->setLayout('backend');
      }
      
      
      
    /**
     * Edit method
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function allocatecourses($id = null)
    {
        $department = $this->Departments->get($id, [
            'contain' => ['Subjects.Semesters', 'Fees', 'Levels','Faculties','Semesters']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());
            if ($this->Departments->save($department)) {
                $this->Flash->success(__('The department has been saved.'));

                return $this->redirect(['action' => 'managedepartments']);
            }
            $this->Flash->error(__('The department could not be saved. Please, try again.'));
        }
        $faculties = $this->Departments->Faculties->find('list', ['limit' => 200]);
        $subjects = $this->Departments->Subjects->find('list', ['limit' => 200]);
        $fees = $this->Departments->Fees->find('list', ['limit' => 200]);
        $levels = $this->Departments->Levels->find('list', ['limit' => 200]);
        $semesters = $this->Departments->Subjects->Semesters->find('list', ['limit' => 200]);
        $this->set(compact('department', 'faculties', 'subjects', 'fees', 'levels','semesters'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
          $this->request->allowMethod(['post', 'delete']);
          $department = $this->Departments->get($id);
          if ($this->Departments->delete($department)) {
              //log activity
              $usercontroller = new UsersController();

              $title = "Deleted a department " . $id;
              $user_id = $this->Auth->user('id');
              $description = "Deleted a department " . $department->name;
              $ip = $this->request->clientIp();
              $type = "Delete";
              $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
              $this->Flash->success(__('The department has been deleted.'));
          } else {
              $this->Flash->error(__('The department could not be deleted. Please, try again.'));
          }

          return $this->redirect(['action' => 'managedepartments']);
      }
}
