<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

/**
 * Employees Controller
 *
 * @property \App\Model\Table\EmployeesTable $Employees
 * @method \App\Model\Entity\Employee[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EmployeesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['States', 'Lgas', 'Staffgrades', 'Staffdepartments', 'Users', 'Admins'],
        ];
        $employees = $this->paginate($this->Employees);

        $this->set(compact('employees'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $employee = $this->Employees->get($id, [
            'contain' => ['States', 'Lgas', 'Staffgrades', 'Staffdepartments', 'Users', 'Admins'],
        ]);

        $this->set(compact('employee'));
          $this->viewBuilder()->setLayout('backend');
    }

    
    
    //admin  method for enabling an employee
    public function disableemployee($userid){
            //ensure this admin is loggedin
       // $admincontroller = new AdminsController();
      //  $admin = $admincontroller->isadmin();
         $users_Table = TableRegistry::get('Users');
        $user = $users_Table->get($userid);
        $user->userstatus = "Disabled";
        $users_Table->save($user);
        //log activity
                $usercontroller = new UsersController();

                $title = "Disabled an employee ";
                $user_id = $this->Auth->user('id');
                $description = "Disabled an employee with user id " . $userid;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
               $this->Flash->success(__('The employee account has been disabled.'));
                 return $this->redirect(['action' => 'index']);
        
    }

    
    
    
       //admin  method for enabling an employee
    public function enableemployee($userid){
            //ensure this admin is loggedin
      //  $admincontroller = new AdminsController();
      //  $admin = $admincontroller->isadmin();
         $users_Table = TableRegistry::get('Users');
        $user = $users_Table->get($userid);
        $user->userstatus = "Enabled";
        $users_Table->save($user);
        //log activity
                $usercontroller = new UsersController();

                $title = "Enabled an employee ";
                $user_id = $this->Auth->user('id');
                $description = "Enabled an employee with user id " . $userid;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The employee account has been enabled.'));
                 return $this->redirect(['action' => 'index']);
        
    }





    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function newemployee()
    {
        $employee = $this->Employees->newEmptyEntity();
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $fname = $this->request->getData('fname');
            $mname = $this->request->getData('mname');
            $sname = $this->request->getData('sname');
            
            $user_id = $this->getlogindetails($email, $fname, $sname, $mname);
            if(is_numeric($user_id)){
                  $admin =   $this->request->getSession()->read('admin');  
          //upload photo
                  $photo = $this->request->getData('photo');
            $filename =  $photo->getClientFilename();
            if (!empty($filename)) {
              $studentscontroller = new StudentsController();
              $image =  $studentscontroller->handlefileupload($this->request->getData('photo'), 'staff_files/');
            }
            else{
          $image =  "" ;   
            }
            $employee = $this->Employees->patchEntity($employee, $this->request->getData());
             $employee->admin_id =  $admin->id;
             $employee->photo = $image;
             $employee->user_id =  $user_id;
            if ($this->Employees->save($employee)) {
                //assign Employee id
                $this->getemployeeid($employee->id);
               //log activity
                $usercontroller = new UsersController();

                $title = "New Employee added " . $employee->empid;
                $user_id = $this->Auth->user('id');
                $description = "Added a new employee " . $employee->sname;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The employee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            }
            $this->Flash->error(__('The employee could not be saved. Please, try again.'));
        }
          $states = $this->Employees->States->find('list', ['limit' => 200])->where(['country_id'=>160]);
        $lgas = $this->Employees->Lgas->find('list', ['limit' => 200])->where(['state_id'=>2647]);
        $staffgrades = $this->Employees->Staffgrades->find('list', ['limit' => 200]);
        $staffdepartments = $this->Employees->Staffdepartments->find('list', ['limit' => 200]);
        $users = $this->Employees->Users->find('list', ['limit' => 200]);
        $admins = $this->Employees->Admins->find('list', ['limit' => 200]);
        $this->set(compact('employee', 'states', 'lgas', 'staffgrades', 'staffdepartments', 'users', 'admins'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    //returns lga in a chosen state
    public function getlgas($state_id) {
        $lgas_Table = TableRegistry::get('Lgas');
        $lgas = $lgas_Table->find('list')->where(['state_id' => $state_id])->order(['name' => 'ASC']);
        $this->set('lgas', $lgas);
    }
    
    
    
    
    /**
     * Edit method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editemployee($id = null)
    {
        $employee = $this->Employees->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
         $admin =   $this->request->getSession()->read('admin');  
          //upload photo
                  $photo = $this->request->getData('photo');
            $filename =  $photo->getClientFilename();
            if (!empty($filename)) {
              $studentscontroller = new StudentsController();
              $image =  $studentscontroller->handlefileupload($this->request->getData('photo'), 'staff_files/');
            }
            else{
          $image =  $employee->photo ;   
            }
            $employee = $this->Employees->patchEntity($employee, $this->request->getData());
             $employee->admin_id =  $admin->id;
             $employee->photo = $image;
            if ($this->Employees->save($employee)) {
               //log activity
                $usercontroller = new UsersController();

                $title = "Updated an Employee " . $employee->empid;
                $user_id = $this->Auth->user('id');
                $description = "Employee data update " . $employee->sname;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The employee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The employee could not be saved. Please, try again.'));
        }
        $states = $this->Employees->States->find('list', ['limit' => 200])->where(['country_id'=>160]);
        $lgas = $this->Employees->Lgas->find('list', ['limit' => 200])->where(['state_id'=>2647]);
        $staffgrades = $this->Employees->Staffgrades->find('list', ['limit' => 200]);
        $staffdepartments = $this->Employees->Staffdepartments->find('list', ['limit' => 200]);
       // $users = $this->Employees->Users->find('list', ['limit' => 200]);
       // $admins = $this->Employees->Admins->find('list', ['limit' => 200]);
        $this->set(compact('employee', 'states', 'lgas', 'staffgrades', 'staffdepartments'));
          $this->viewBuilder()->setLayout('backend');
    }

    
    
    
    //method that creates login details for employees
    private function getlogindetails($email, $fname, $lname, $mname) {
        $users_Table = TableRegistry::get('Users');
        //ensure username does not exit already
        $old_user = $users_Table->find()->where(['username' => $email])->first();

        if (empty($old_user)) {
            $user = $users_Table->newEmptyEntity();
            $user->role_id = 3; //employee
            $user->password = "employee123";
            $user->username = $email;
            $user->fname = $fname;
            $user->lname = $lname;
            $user->mname = $mname;
            if ($users_Table->save($user)) {
                return $user->id;
            } else {
                return "Failed";
            }
        } else {
            //username already exits 
            $this->Flash->error(__('Username/Email Already in Use. Please use a different email address'));
            return $this->redirect(['action' => 'newemployee']);
        }
    }

  
    //methdo that assigns employee id to a new employee
    private function getemployeeid($empid){
   $employee =      $this->Employees->get($empid);
    $employee->empid = "UAES".$empid;
    $this->Employees->save($employee);
    return;
    }


    //admin method for creating staff department
    public function departments(){
        $staffdepartments_Table = TableRegistry::get('Staffdepartments');
        $staffdepartments =  $staffdepartments_Table->find(); 
        $this->set('staffdepartments', $staffdepartments);
          $this->viewBuilder()->setLayout('backend');
        
    }

    
    //admin method for creating staff departments
    public function createdepartment(){
       $staffdepartments_Table = TableRegistry::get('Staffdepartments');  
         $staffdepartment =  $staffdepartments_Table->newEmptyEntity();
        if ($this->request->is('post')) {
            $staffdepartment =  $staffdepartments_Table->patchEntity($staffdepartment, $this->request->getData());
            if ( $staffdepartments_Table->save($staffdepartment)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Created a staff department " . $staffdepartment->name;
                $user_id = $this->Auth->user('id');
                $description = "Added a new staff department " . $staffdepartment->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The staffdepartment has been saved.'));

                return $this->redirect(['action' => 'departments']);
            }
            $this->Flash->error(__('The staffdepartment could not be saved. Please, try again.'));
        }
        $this->set(compact('staffdepartment'));
        
        $this->viewBuilder()->setLayout('backend');  
    }

    
    //admin method for updating a department
    public function editdepartment($id=null){
           $staffdepartments_Table = TableRegistry::get('Staffdepartments');  
         $staffdepartment =  $staffdepartments_Table->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffdepartment =  $staffdepartments_Table->patchEntity($staffdepartment, $this->request->getData());
            if ( $staffdepartments_Table->save($staffdepartment)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Updated a staff department " . $staffdepartment->name;
                $user_id = $this->Auth->user('id');
                $description = "A staff department updated " . $staffdepartment->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The staff department has been saved.'));

                return $this->redirect(['action' => 'departments']);
            }
            $this->Flash->error(__('The staff department could not be saved. Please, try again.'));
        }
        $this->set(compact('staffdepartment'));
        
        $this->viewBuilder()->setLayout('backend');     
    }

    
    //admin method for creating staff grades
    public function newstaffgrade(){
          $Staffgrades_Table = TableRegistry::get('Staffgrades');  
         $staffgrade = $Staffgrades_Table->newEmptyEntity();
        if ($this->request->is('post')) {
            $staffgrade = $Staffgrades_Table->patchEntity($staffgrade, $this->request->getData());
            if ($Staffgrades_Table->save($staffgrade)) {
               //log activity
                $usercontroller = new UsersController();
                $title = "created a new staff grade " . $staffgrade->name;
                $user_id = $this->Auth->user('id');
                $description = "A new staff grade added " . $staffgrade->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The staff grade has been saved.'));

                return $this->redirect(['action' => 'staffgrades']);
            }
            $this->Flash->error(__('The staff grade could not be saved. Please, try again.'));
        }
        $this->set(compact('staffgrade'));
        
      $this->viewBuilder()->setLayout('backend');   
    }

    
    
    //admin method updating a staff grade
    public function editstaffgrade($id=null){
        $Staffgrades_Table = TableRegistry::get('Staffgrades');   
        $staffgrade = $Staffgrades_Table->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffgrade = $Staffgrades_Table->patchEntity($staffgrade, $this->request->getData());
            if ($Staffgrades_Table->save($staffgrade)) {
              //log activity
                $usercontroller = new UsersController();

                $title = "Updated a staff grade " . $staffgrade->name;
                $user_id = $this->Auth->user('id');
                $description = "A staff department updated " . $staffgrade->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The staff grade has been saved.'));

                return $this->redirect(['action' => 'staffgrades']);
            }
            $this->Flash->error(__('The staff grade could not be saved. Please, try again.'));
        }
        
      $this->set(compact('staffgrade'));
        
      $this->viewBuilder()->setLayout('backend');     
    }





    //admin method for viewing all staf grades
    public function staffgrades(){
         $Staffgrades_Table = TableRegistry::get('Staffgrades'); 
        $staffgrades = $Staffgrades_Table->find();
           $this->set(compact('staffgrades'));
        
        $this->viewBuilder()->setLayout('backend'); 
    }

        /**
     * Delete method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $employee = $this->Employees->get($id);
        $employeeid = $employee->empid;
        $sname = $employee->sname;
        if ($this->Employees->delete($employee)) {
             //log activity
                $usercontroller = new UsersController();

                $title = "Deleted an Employee " . $employeeid;
                $user_id = $this->Auth->user('id');
                $description = "Deleted an employee data " .  $sname;
                $ip = $this->request->clientIp();
                $type = "Delete";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The employee has been deleted.'));
        } else {
            $this->Flash->error(__('The employee could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    
       // allow unrestricted pages
            public function beforeFilter(EventInterface $event) {
                // $this->Auth->allow(['bookin', 'welcome','addimage']);
                $actions = ['newemployee', 'editemployee'];

                if (in_array($this->request->getParam('action'), $actions)) {
                    // turn form protection 
                    $this->FormProtection->setConfig('validate', false);
                }
            }
}
