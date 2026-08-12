<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * ClassArms Controller
 *
 * @property \App\Model\Table\ClassArmsTable $ClassArms
 *
 * @method \App\Model\Entity\ClassArm[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClassArmsController extends AppController
{
    /**
     * Index method - List all class arms
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {

        // Get class arms with basic query first, then load associations manually
        $classArms = $this->ClassArms->find()
            ->order(['arm_name' => 'ASC'])
            ->all();
            
        // Load associations manually to avoid query issues
        foreach ($classArms as $classArm) {
            // Load department
            if ($classArm->department_id) {
                $classArm->department = $this->ClassArms->Departments->get($classArm->department_id);
            }
            
            // Load teacher if assigned
            if ($classArm->class_teacher_id) {
                $classArm->teacher = $this->ClassArms->Teachers->get($classArm->class_teacher_id, [
                    'contain' => ['Users']
                ]);
            }
            
            // Load students count
            $classArm->students = $this->ClassArms->Students->find()
                ->where(['class_arm_id' => $classArm->id])
                ->all();
        }
        
        $this->set(compact('classArms'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method - View a specific class arm
     *
     * @param string|null $id ClassArm id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        try {
            // Get the class arm with basic query first
            $classArm = $this->ClassArms->get($id);
            
            // Load associations manually
            if ($classArm->department_id) {
                $classArm->department = $this->ClassArms->Departments->get($classArm->department_id);
            }
            
            if ($classArm->class_teacher_id) {
                $classArm->teacher = $this->ClassArms->Teachers->get($classArm->class_teacher_id, [
                    'contain' => ['Users']
                ]);
            }
            
            // Load students
            $classArm->students = $this->ClassArms->Students->find()
                ->where(['class_arm_id' => $classArm->id, 'status' => 'Admitted'])
                ->order(['fname' => 'ASC', 'lname' => 'ASC'])
                ->all();
                
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Class arm not found.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('classArm', $classArm);
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method - Create new class arm
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        $classArm = $this->ClassArms->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Check if arm name already exists for this department
            $existingArm = $this->ClassArms->find()
                ->where([
                    'department_id' => $data['department_id'],
                    'arm_name' => $data['arm_name']
                ])
                ->first();
                
            if ($existingArm) {
                $this->Flash->error(__('Class arm "' . $data['arm_name'] . '" already exists for this class. Please choose a different arm name.'));
            } else {
                $classArm = $this->ClassArms->patchEntity($classArm, $data);
                $classArm->status = 'active'; // Set default status
            
            if ($this->ClassArms->save($classArm)) {
                // Log activity
                $usercontroller = new UsersController();
                $title = "Created a new class arm " . $classArm->id;
                $user_id = $this->Auth->user('id');
                
                // Get department name for logging
                $department = $this->ClassArms->Departments->get($classArm->department_id);
                $description = "Created new class arm " . $classArm->arm_name . " for " . $department->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);

                $this->Flash->success(__('The class arm has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                // Show validation errors
                $errors = $classArm->getErrors();
                $errorMessages = [];
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorMessages[] = $field . ': ' . $error;
                    }
                }
                $this->Flash->error(__('Validation errors: ' . implode(', ', $errorMessages)));
            }
            }
        }
        
        $departments = $this->ClassArms->Departments->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
            'order' => ['name' => 'ASC']
        ]);
        
        $teachers = $this->ClassArms->Teachers->find()
            ->contain(['Users'])
            ->order(['Users.fname' => 'ASC'])
            ->combine('id', function ($teacher) {
                return $teacher->user->fname . ' ' . $teacher->user->lname;
            });

        $this->set(compact('classArm', 'departments', 'teachers'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method - Update class arm
     *
     * @param string|null $id ClassArm id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

        try {
            // Get the class arm with basic query first
            $classArm = $this->ClassArms->get($id);
            
            // Load associations manually
            if ($classArm->department_id) {
                $classArm->department = $this->ClassArms->Departments->get($classArm->department_id);
            }
            
            if ($classArm->class_teacher_id) {
                $classArm->teacher = $this->ClassArms->Teachers->get($classArm->class_teacher_id, [
                    'contain' => ['Users']
                ]);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Class arm not found.'));
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classArm = $this->ClassArms->patchEntity($classArm, $this->request->getData());
            if ($this->ClassArms->save($classArm)) {
                // Log activity
                $usercontroller = new UsersController();
                $title = "Updated class arm " . $id;
                $user_id = $this->Auth->user('id');
                
                // Get department name for logging
                $department = $this->ClassArms->Departments->get($classArm->department_id);
                $description = "Updated class arm " . $classArm->arm_name . " for " . $department->name;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);

                $this->Flash->success(__('The class arm has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class arm could not be updated. Please, try again.'));
        }
        
        $departments = $this->ClassArms->Departments->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
            'order' => ['name' => 'ASC']
        ]);
        
        $teachers = $this->ClassArms->Teachers->find()
            ->contain(['Users'])
            ->order(['Users.fname' => 'ASC'])
            ->combine('id', function ($teacher) {
                return $teacher->user->fname . ' ' . $teacher->user->lname;
            });

        $this->set(compact('classArm', 'departments', 'teachers'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method - Delete class arm
     *
     * @param string|null $id ClassArm id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

        $this->request->allowMethod(['post', 'delete']);
        $classArm = $this->ClassArms->get($id);
        
        // Check if class arm has students
        $studentCount = $this->ClassArms->Students->find()
            ->where(['class_arm_id' => $id, 'status' => 'Admitted'])
            ->count();
            
        if ($studentCount > 0) {
            $this->Flash->error(__('Cannot delete class arm. It has ' . $studentCount . ' student(s) assigned. Please reassign students first.'));
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->ClassArms->delete($classArm)) {
            // Log activity
            $usercontroller = new UsersController();
            $title = "Deleted class arm " . $id;
            $user_id = $this->Auth->user('id');
            
            // Get department name for logging
            $department = $this->ClassArms->Departments->get($classArm->department_id);
            $description = "Deleted class arm " . $classArm->arm_name . " for " . $department->name;
            $ip = $this->request->clientIp();
            $type = "Delete";
            $usercontroller->makeLog($title, $user_id, $description, $ip, $type);

            $this->Flash->success(__('The class arm has been deleted.'));
        } else {
            $this->Flash->error(__('The class arm could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get class arms for a specific department (AJAX)
     *
     * @param int $departmentId
     * @return \Cake\Http\Response
     */
    public function getArmsForDepartment($departmentId = null)
    {
        $this->request->allowMethod(['get']);
        
        try {
            $classArms = $this->ClassArms->find()
                ->contain(['Departments'])
                ->where(['ClassArms.department_id' => $departmentId, 'ClassArms.status' => 'active'])
                ->order(['ClassArms.arm_name' => 'ASC'])
                ->all();

            // Format the data for the dropdown
            $formattedClassArms = [];
            foreach ($classArms as $classArm) {
                $formattedClassArms[$classArm->id] = $classArm->department->name . ' - ' . $classArm->arm_name;
            }

            $this->set(compact('formattedClassArms'));
            $this->viewBuilder()->setTemplate('ajax_class_arms');
            $this->viewBuilder()->setLayout('ajax');
        } catch (\Exception $e) {
            // Return empty options if there's an error
            $formattedClassArms = [];
            $this->set(compact('formattedClassArms'));
            $this->viewBuilder()->setTemplate('ajax_class_arms');
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    /**
     * Get class arms for a specific department for target selection (AJAX)
     *
     * @param int $departmentId
     * @return \Cake\Http\Response
     */
    public function getTargetArmsForDepartment($departmentId = null)
    {
        $this->request->allowMethod(['get']);
        
        try {
            $classArms = $this->ClassArms->find()
                ->contain(['Departments'])
                ->where(['ClassArms.department_id' => $departmentId, 'ClassArms.status' => 'active'])
                ->order(['ClassArms.arm_name' => 'ASC'])
                ->all();

            // Format the data for the dropdown
            $formattedClassArms = [];
            foreach ($classArms as $classArm) {
                $formattedClassArms[$classArm->id] = $classArm->department->name . ' - ' . $classArm->arm_name;
            }

            $this->set(compact('formattedClassArms'));
            $this->viewBuilder()->setTemplate('ajax_target_class_arms');
            $this->viewBuilder()->setLayout('ajax');
        } catch (\Exception $e) {
            // Return empty options if there's an error
            $formattedClassArms = [];
            $this->set(compact('formattedClassArms'));
            $this->viewBuilder()->setTemplate('ajax_target_class_arms');
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    /**
     * Manage students in a class arm
     *
     * @param int $id ClassArm id
     * @return \Cake\Http\Response|void
     */
    public function manageStudents($id = null)
    {

        try {
            // Get the class arm with basic query first
            $classArm = $this->ClassArms->get($id);
            
            // Load associations manually
            if ($classArm->department_id) {
                $classArm->department = $this->ClassArms->Departments->get($classArm->department_id);
            }
            
            if ($classArm->class_teacher_id) {
                $classArm->teacher = $this->ClassArms->Teachers->get($classArm->class_teacher_id, [
                    'contain' => ['Users']
                ]);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Class arm not found.'));
            return $this->redirect(['action' => 'index']);
        }

        // Get students in this class arm
        $students = $this->ClassArms->Students->find()
            ->where(['class_arm_id' => $id, 'status' => 'Admitted'])
            ->order(['fname' => 'ASC', 'lname' => 'ASC'])
            ->all();

        // Get unassigned students from the same department
        $unassignedStudents = $this->ClassArms->Students->find()
            ->where([
                'department_id' => $classArm->department_id,
                'class_arm_id IS' => null,
                'status' => 'Admitted'
            ])
            ->order(['fname' => 'ASC', 'lname' => 'ASC'])
            ->all();

        $this->set(compact('classArm', 'students', 'unassignedStudents'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Assign student to class arm
     *
     * @return \Cake\Http\Response
     */
    public function assignStudent()
    {
        $this->request->allowMethod(['post']);
        

        $data = $this->request->getData();
        $studentId = $data['student_id'];
        $classArmId = $data['class_arm_id'];

        $student = $this->ClassArms->Students->get($studentId);
        $student->class_arm_id = $classArmId;

        if ($this->ClassArms->Students->save($student)) {
            $this->Flash->success(__('Student has been assigned to class arm.'));
        } else {
            $this->Flash->error(__('Failed to assign student to class arm.'));
        }

        return $this->redirect(['action' => 'manageStudents', $classArmId]);
    }

    /**
     * Remove student from class arm
     *
     * @return \Cake\Http\Response
     */
    public function removeStudent()
    {
        $this->request->allowMethod(['post']);
        

        $data = $this->request->getData();
        $studentId = $data['student_id'];
        $classArmId = $data['class_arm_id'];

        $student = $this->ClassArms->Students->get($studentId);
        $student->class_arm_id = null;

        if ($this->ClassArms->Students->save($student)) {
            $this->Flash->success(__('Student has been removed from class arm.'));
        } else {
            $this->Flash->error(__('Failed to remove student from class arm.'));
        }

        return $this->redirect(['action' => 'manageStudents', $classArmId]);
    }
}
