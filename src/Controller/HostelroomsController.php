<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Datasource\ConnectionManager;

/**
 * Hostelrooms Controller
 *
 * @property \App\Model\Table\HostelroomsTable $Hostelrooms
 *
 * @method \App\Model\Entity\Hostelroom[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HostelroomsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
       
        $hostelrooms = $this->Hostelrooms->find()->contain(['Hostels']);

        $this->set(compact('hostelrooms'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Hostelroom id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewroom($id = null)
    {
        $hostelroom = $this->Hostelrooms->get($id, [
            'contain' => ['Hostels', 'Students.Departments']
        ]);
        // debug(json_encode($hostelroom, JSON_PRETTY_PRINT)); exit;
        $this->set('hostelroom', $hostelroom);
        $this->viewBuilder()->setLayout('backend');
    }

    
    //admin method for viewing students and their rooms
    public function studentrooms(){
       $hostelroom_student_table = TableRegistry::get('HostelroomsStudents');  
       $hostelrooms =  $hostelroom_student_table->find()->contain(['Students.Departments','Hostelrooms.Hostels']);
    //  $hostelrooms =   $this->Hostelrooms->find()->contain(['Hostels', 'Students.Departments']);
         $this->set('hostelrooms', $hostelrooms);
     //  debug(json_encode( $student_rooms, JSON_PRETTY_PRINT)); exit;
     $this->viewBuilder()->setLayout('backend');    
    }


    
    //admin method for ejecting all the students at the end of each session
    public function ejectall(){
         $this->request->allowMethod(['post', 'delete']);
       
       $connection = ConnectionManager::get('default');
        if ($connection->execute('TRUNCATE TABLE Hostelrooms_Students')) {
            //update rooms and make beds available again
         $rooms =   $this->makeroomsavaialable();
            $this->Flash->success(__('The students have all been ejected from the '.$rooms .' rooms'));
        } else {
            $this->Flash->error(__('Sorry, unable to eject students. Please, try again.'));
        }

        return $this->redirect(['action' => 'studentrooms']);
        
    }


    //all hotels rooms after bulk ejection
    private function makeroomsavaialable(){
      $hostelroom_table = TableRegistry::get('Hostelrooms'); 
       $hostelrooms =  $hostelroom_table->find();
       $allrooms = 0;
       foreach ($hostelrooms as $room){
           $allrooms++;
           $the_room =  $hostelroom_table->get($room->id);
        $the_room->occupiedbeds = 0;  
         $hostelroom_table->save($the_room);
        
       }
        return $allrooms;
    }


    //admin method for assigning hostel rooms to students
    public function assignroom($room_id = null){
        $rooms_Table = TableRegistry::get('Hostelrooms');
        $room_students_Table = TableRegistry::get('HostelroomsStudents');
        
        if ($this->request->is('post')) {
            $room_id = $this->request->getData('hostelroom_id');
            $student_ids = $this->request->getData('student_ids');
            
            if (empty($room_id)) {
                $this->Flash->error(__('Please select a room.'));
                return $this->redirect(['action' => 'assignroom']);
            }
            
            if (empty($student_ids)) {
                $this->Flash->error(__('Please select at least one student.'));
                return $this->redirect(['action' => 'assignroom']);
            }
            
            // Get room details to check capacity
            $room = $rooms_Table->get($room_id);
            $available_beds = $room->available_beds - $room->occupiedbeds;
            
            if (count($student_ids) > $available_beds) {
                $this->Flash->error(__('Cannot assign ' . count($student_ids) . ' students. Only ' . $available_beds . ' beds available in this room.'));
                return $this->redirect(['action' => 'assignroom']);
            }
            
            $success_count = 0;
            $error_count = 0;
            $already_assigned = [];
            
            foreach ($student_ids as $student_id) {
                // Check if student already has a room
                $existing_room = $this->checkexistingroom($student_id);
                if (!empty($existing_room->id)) {
                    $already_assigned[] = $student_id;
                    $error_count++;
                    continue;
                }
                
                // Create new room assignment
                $room_students = $room_students_Table->newEmptyEntity();
                $room_students->student_id = $student_id;
                $room_students->hostelroom_id = $room_id;
                
                if ($room_students_Table->save($room_students)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
            
            // Update room occupancy
            if ($success_count > 0) {
                $room->occupiedbeds += $success_count;
                $rooms_Table->save($room);
                
                // Log activity
                $usercontroller = new UsersController();
                $title = "Assigned Hostel Room to " . $success_count . " Students";
                $user_id = $this->Auth->user('id');
                $description = "Hostel Room Assignment - Room ID: " . $room_id . ", Students: " . $success_count;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            }
            
            // Show results
            if ($success_count > 0) {
                $this->Flash->success(__($success_count . ' student(s) have been assigned to the room successfully.'));
            }
            
            if ($error_count > 0) {
                if (!empty($already_assigned)) {
                    $this->Flash->warning(__($error_count . ' student(s) were already assigned to rooms.'));
                } else {
                    $this->Flash->error(__($error_count . ' student(s) could not be assigned. Please try again.'));
                }
            }
            
            return $this->redirect(['action' => 'index']);
        }
        
        // Get students without rooms
        $studentids = $this->getstudentswithrooms();
        $s_ids = $this->getcompleteictfee();
        
        $condition = [];
        $condition['Students.status'] = 'Admitted';
        
        if (!empty(array_filter($s_ids))) {
            $condition['Students.id IN'] = $s_ids;
        }
        
        if (!empty(array_filter($studentids))) {
            $condition['Students.id NOT IN'] = $studentids;
        }
        
        // Get students with full names for better display using join
        $students = $this->Hostelrooms->Students->find()
            ->select(['Students.id', 'Students.fname', 'Students.lname', 'Students.regno', 'Students.department_id', 'Departments.name'])
            ->join([
                'table' => 'departments',
                'alias' => 'Departments',
                'type' => 'LEFT',
                'conditions' => 'Students.department_id = Departments.id'
            ])
            ->where($condition)
            ->order(['Students.fname' => 'ASC', 'Students.lname' => 'ASC'])
            ->limit(2000);
        
        // Create a formatted list for the dropdown
        $studentsList = [];
        foreach ($students as $student) {
            $departmentName = !empty($student->Departments['name']) ? $student->Departments['name'] : 'No Class';
            $studentsList[$student->id] = $student->fname . ' ' . $student->lname . ' (' . $student->regno . ') - ' . $departmentName;
        }
        
        $hostels = $this->Hostelrooms->Hostels->find('list', ['limit' => 200]);
        $hostelrooms = $this->Hostelrooms->find('list', ['limit' => 200]);
        
        // Pre-select room and hostel if room_id is provided
        $selectedRoom = null;
        $selectedHostel = null;
        
        if ($room_id) {
            $selectedRoom = $this->Hostelrooms->get($room_id, [
                'contain' => ['Hostels']
            ]);
            $selectedHostel = $selectedRoom->hostel_id;
            
            // Filter rooms to show only rooms from the same hostel
            $hostelrooms = $this->Hostelrooms->find('list', ['limit' => 200])
                ->where(['hostel_id' => $selectedHostel]);
        }
        
        $this->set(compact('hostels', 'studentsList', 'hostelrooms', 'students', 'selectedRoom', 'selectedHostel'));
        $this->viewBuilder()->setLayout('backend');
    }
    
    
    
    //admin method that returns the ids of all students who currently have a room
    private function getstudentswithrooms(){
        $student_ids = [];
         $room_students_Table = TableRegistry::get('HostelroomsStudents');
         $students_with_rooms = $room_students_Table->find();
         foreach ($students_with_rooms as $hostel_room){
             array_push($student_ids, $hostel_room->student_id);  
         }
          //debug(json_encode($student_ids, JSON_PRETTY_PRINT)); exit;
         return $student_ids;
                
    }


 //get students that has paid ICT fee for the session
    private function getcompleteictfee(){
     $settings =   $this->request->getSession()->read('settings');
      $transactions_Table = TableRegistry::get('Transactions');
     $studentids = [];
     $transactions = $transactions_Table->find()
             ->where(['fee_id'=>5,'paystatus'=>'completed','session_id'=>$settings->session_id]);
       foreach ($transactions as $transaction){
             array_push($studentids, $transaction->student_id);  
         }
       return $studentids;
    }



    //admin method for assigning rooms to students
    public function assignroomtostudent($student_id){
         $rooms_Table = TableRegistry::get('Hostelrooms');
         $room_students_Table = TableRegistry::get('HostelroomsStudents');
         $room_students = $room_students_Table->newEmptyEntity();
          if ($this->request->is('post')) {
              $room_id = $this->request->getData('hostelroom_id');
            // $student_id = $this->request->getData('student_id');
             //if student has an exisiting room
             $exisitingroom = $this->checkexistingroom($student_id);
             if(!empty($exisitingroom->id)){
              $this->Flash->error(__('The student already has a room assigned.'));  
               return $this->redirect(['action' => 'assignroom']);
             }
             
             //$hostelroom =  $room_students_Table->get($room_id);
             $room_students->student_id = $student_id;
             $room_students->hostelroom_id = $room_id;
            
            //$hostelroom = $this->Hostelrooms->patchEntity($hostelroom, $this->request->getData());
            if ($room_students_Table->save($room_students)) {
                //update room occupancy
                $room = $rooms_Table->get($room_id);
                $room->occupiedbeds +=1;
                $rooms_Table->save($room);
               // debug(json_encode($room, JSON_PRETTY_PRINT)); exit;
                //log activity
                $usercontroller = new UsersController();
               
                 $title = "Assigned A Hostel Room to A Student " .$student_id;
                $user_id = $this->Auth->user('id');
                $description = "Hostel Room Assignment" ;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The room has been assigned.'));

                return $this->redirect(['controller'=>'Students','action' => 'managestudents']);
            }
            $this->Flash->error(__('The hostel room could not be assigned. Please, try again.'));
  } 
       $hostels = $this->Hostelrooms->Hostels->find('list', ['limit' => 200]);
        $students = $this->Hostelrooms->Students->find('list', ['limit' => 200])->where(['status'=>'Admitted','id'=>$student_id]);
        $hostelrooms = $this->Hostelrooms->find('list', ['limit' => 200])->where(['occupiedbeds <'=>4]);
        $this->set(compact('hostels', 'students','hostelrooms'));
        $this->viewBuilder()->setLayout('backend');
        
    }



    //check room capacity, not in use
    private function checkroomcapacity($hostelroomid){
         $room_students_Table = TableRegistry::get('HostelroomsStudents');
         $room_space = $room_students_Table->find()->where(['hostelroom_id'=>$hostelroomid])->count();
         return  $room_space;
    }

    
    //check that student is not already asigned to a room
    private function checkexistingroom($studentid){
      $room_students_Table = TableRegistry::get('HostelroomsStudents');
         $room_space = $room_students_Table->find()->where(['student_id'=>$studentid])->first();
         return  $room_space;   
    }


//method that shows available rooms for assignment to students
      public function getrooms($hostel_id){
          $hostelrooms = $this->Hostelrooms->find('list', ['limit' => 200])
                  ->where(['hostel_id'=>$hostel_id]);
          $this->set(compact('hostelrooms'));
          
      }
      
      /**
       * Get room details for AJAX request
       *
       * @param string|null $id Hostelroom id.
       * @return \Cake\Http\Response|null JSON response.
       */
      public function getroomdetails($id = null)
      {
          $this->request->allowMethod(['get']);
          
          // Debug log
          error_log("getroomdetails called with ID: " . $id);
          
          $hostelroom = $this->Hostelrooms->get($id, [
              'contain' => ['Hostels']
          ]);
          
          $response = [
              'id' => $hostelroom->id,
              'room_number' => $hostelroom->room_number,
              'floor' => $hostelroom->floor,
              'available_beds' => $hostelroom->available_beds,
              'occupiedbeds' => $hostelroom->occupiedbeds,
              'description' => $hostelroom->description,
              'hostel_name' => $hostelroom->has('hostel') ? $hostelroom->hostel->name : ''
          ];
          
          // Debug log
          error_log("getroomdetails response: " . json_encode($response));
          
          $this->response = $this->response->withType('application/json');
          $this->response = $this->response->withStringBody(json_encode($response));
          return $this->response;
      }
      
      
      
      //method that ejects student from a hostel room
      public function ejectstudent($student_id, $room_id){
          $rooms_Table = TableRegistry::get('Hostelrooms');
         $room_students_Table = TableRegistry::get('HostelroomsStudents');
         $room_student = $room_students_Table->find()
                 ->where(['student_id'=>$student_id,'hostelroom_id'=>$room_id])
                 ->first();
         if($room_students_Table->delete($room_student)){
             //reduce occupied beds
             $room = $rooms_Table->get($room_id);
             $room->occupiedbeds -=1;
             $rooms_Table->save($room);
            $this->Flash->success(__('The student has been ejected and the room available.'));
        } else {
            $this->Flash->error(__('The student could not be ejected. Please, try again.'));
        }

        return $this->redirect(['action' => 'viewroom',$room_id]);
         
      }

      



      /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function createroom()
    {
        $hostelroom = $this->Hostelrooms->newEmptyEntity();
        if ($this->request->is('post')) {
            $hostelroom = $this->Hostelrooms->patchEntity($hostelroom, $this->request->getData());
            if ($this->Hostelrooms->save($hostelroom)) {
                //log activity
                $usercontroller = new UsersController();
               
                 $title = "Created A Hostel Room ".$hostelroom->id;
                $user_id = $this->Auth->user('id');
                $description = "Created new Hostel Room " . $hostelroom->name;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The hostelroom has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The hostelroom could not be saved. Please, try again.'));
        }
        $hostels = $this->Hostelrooms->Hostels->find('list', ['limit' => 200]);
       // $students = $this->Hostelrooms->Students->find('list', ['limit' => 200])->where(['status'=>'Admitted']);
        $this->set(compact('hostelroom', 'hostels'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Hostelroom id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editroom($id = null)
    {
        $hostelroom = $this->Hostelrooms->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            // Ensure students._ids is present to avoid "Missing field" error
            if (!isset($data['students']['_ids'])) {
                $data['students']['_ids'] = [];
            }
            $hostelroom = $this->Hostelrooms->patchEntity($hostelroom, $data);
            if ($this->Hostelrooms->save($hostelroom)) {
                //log activity
                $usercontroller = new UsersController();
               
                 $title = "Updated A Hostel Room ".$hostelroom->id;
                $user_id = $this->Auth->user('id');
                $description = "Updated Hostel Room " . $hostelroom->room_number;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The hostelroom has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The hostelroom could not be saved. Please, try again.'));
        }
        $hostels = $this->Hostelrooms->Hostels->find('list', ['limit' => 200]);
        $this->set(compact('hostelroom', 'hostels'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Hostelroom id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $hostelroom = $this->Hostelrooms->get($id);
        if ($this->Hostelrooms->delete($hostelroom)) {
            $this->Flash->success(__('The hostelroom has been deleted.'));
        } else {
            $this->Flash->error(__('The hostelroom could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    
    // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
         $this->Auth->allow(['getrooms']);
        if (!$this->Auth->user()) {
            $this->Auth->setConfig('authError', false);
        }
    }
    
    /**
     * Sync bed count method
     * Updates the occupiedbeds field to match the actual number of students in the room
     *
     * @param string|null $id Hostelroom id.
     * @return \Cake\Http\Response|null Redirects to viewroom.
     */
    public function syncbedcount($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $hostelroom = $this->Hostelrooms->get($id, [
            'contain' => ['Students']
        ]);
        
        // Count actual students in the room
        $actualOccupiedBeds = count($hostelroom->students);
        
        // Update the occupiedbeds field
        $hostelroom->occupiedbeds = $actualOccupiedBeds;
        
        if ($this->Hostelrooms->save($hostelroom)) {
            $this->Flash->success(__('Bed count has been synchronized. Occupied beds: ' . $actualOccupiedBeds));
        } else {
            $this->Flash->error(__('Failed to sync bed count. Please, try again.'));
        }

        return $this->redirect(['action' => 'viewroom', $id]);
    }
}
