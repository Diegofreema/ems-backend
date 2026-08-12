<?php
declare(strict_types=1);

  namespace App\Controller;

  use Cake\ORM\TableRegistry;
  use App\Controller\AppController;
  use Cake\Event\EventInterface;

  /**
   * Topics Controller
   *
   * @property \App\Model\Table\TopicsTable $Topics
   *
   * @method \App\Model\Entity\Topic[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
   */
  class TopicsController extends AppController {

      /**
       * Index method
       *
       * @return \Cake\Http\Response|void
       */
      public function index() {
          $this->paginate = [
              'contain' => ['Subjects', 'Users']
          ];
          $topics = $this->paginate($this->Topics);

          $this->set(compact('topics'));
          $this->viewBuilder()->setLayout('backend');
      }

      public function managetopics() {
          $topics = $this->Topics->find()->contain(['Subjects', 'Users']);
        
          $this->set(compact('topics'));
          $this->viewBuilder()->setLayout('backend');
      }

      //addmin method for viewing the contents of a subject
      public function viewcontents($subject_id) {
         
          $sub_contents = $this->Topics->find()->where(['subject_id' => $subject_id])->contain(['Subjects']);

          $this->set(compact('sub_contents'));
          $this->viewBuilder()->setLayout('backend');
      }
      
      
      //teachers method for viewing course contents
      public function teacherviewcontents($subject_id){
         
          $sub_contents = $this->Topics->find()->where(['subject_id' => $subject_id])->contain(['Subjects']);
          
          // Get subject information for the breadcrumb
          $subject = $this->Topics->Subjects->get($subject_id);

          $this->set(compact('sub_contents', 'subject'));    
       $this->viewBuilder()->setLayout('teachersbackend');     
      }
      
      
      //students method for viewing course contents
      public function viewcoursecontent($subject_id){
          
          $sub_contents = $this->Topics->find()->where(['subject_id' => $subject_id])->contain(['Subjects']);

          $this->set(compact('sub_contents'));
          $this->viewBuilder()->setLayout('studentsbackend');   
      }

      //student method for view the lms module
      public function subjectreader($subject_id) {
          $sub_contents = $this->Topics->find()->where(['subject_id' => $subject_id])->contain(['Subjects']);

          $this->set(compact('sub_contents'));
          $this->viewBuilder()->setLayout('backend');
      }

      //function that gets the topics, called by the javascript
      public function gettopics($subject_id) {
          $topic = $this->Topics->get($subject_id);
          $this->set('topic', $topic);
      }

      /**
       * Add method
       *
       * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
       */
      public function addtopic() {
          $topic = $this->Topics->newEmptyEntity();
          if ($this->request->is('post')) {
              $topic = $this->Topics->patchEntity($topic, $this->request->getData());
              $topic->user_id = $this->Auth->user('id');

              if ($this->Topics->save($topic)) {
                   //log activity
                $usercontroller = new UsersController();
               
                 $title = "Added a Topic ".$topic->title;
                $user_id = $this->Auth->user('id');
                $description = "Added a Topic " . $topic->title;
                $ip = $this->request->clientIp();
                $type = "Add";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                  $this->Flash->success(__('The topic has been saved.'));

                  return $this->redirect(['action' => 'managetopics']);
              }
              $this->Flash->error(__('The topic could not be saved. Please, try again.'));
          }
          $subjects = $this->Topics->Subjects->find('list', ['limit' => 2000]);
          // $admins = $this->Topics->Admins->find('list', ['limit' => 200]);
          $this->set(compact('topic', 'subjects'));
          $this->viewBuilder()->setLayout('backend');
      }

      /**
       * Edit method
       *
       * @param string|null $id Topic id.
       * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
       * @throws \Cake\Network\Exception\NotFoundException When record not found.
       */
      public function edittopic($id = null) {
          $topic = $this->Topics->get($id, [
              'contain' => ['Subjects']
          ]);
          if ($this->request->is(['patch', 'post', 'put'])) {
              $topic = $this->Topics->patchEntity($topic, $this->request->getData());
              $topic->updatedon = date('d M Y');
              if ($this->Topics->save($topic)) {
                   //log activity
                $usercontroller = new UsersController();
               
                 $title = "Updated a Topic ".$topic->title;
                $user_id = $this->Auth->user('id');
                $description = "Updated a Topic " . $topic->title;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                  $this->Flash->success(__('The topic has been updated.'));

                  return $this->redirect(['action' => 'managetopics']);
              }
              $this->Flash->error(__('The topic could not be saved. Please, try again.'));
          }
          $subjects = $this->Topics->Subjects->find('list', ['limit' => 200]);
          // $admins = $this->Topics->Admins->find('list', ['limit' => 200]);
          $this->set(compact('topic', 'subjects'));
          $this->viewBuilder()->setLayout('backend');
      }

      /**
       * View method
       *
       * @param string|null $id Topic id.
       * @return \Cake\Http\Response|void
       * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
       */
      public function view($id = null) {
          $topic = $this->Topics->get($id, [
              'contain' => ['Subjects', 'Users']
          ]);

          $this->set('topic', $topic);
      }

      /**
       * Add method
       *
       * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
       */
      public function add() {
          $topic = $this->Topics->newEmptyEntity();
          if ($this->request->is('post')) {
              $topic = $this->Topics->patchEntity($topic, $this->request->getData());
              if ($this->Topics->save($topic)) {
                  $this->Flash->success(__('The topic has been saved.'));

                  return $this->redirect(['action' => 'index']);
              }
              $this->Flash->error(__('The topic could not be saved. Please, try again.'));
          }
          $subjects = $this->Topics->Subjects->find('list', ['limit' => 200]);
          $users = $this->Topics->Users->find('list', ['limit' => 200]);
          $this->set(compact('topic', 'subjects', 'users'));
      }

      /**
       * Edit method
       *
       * @param string|null $id Topic id.
       * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
       * @throws \Cake\Network\Exception\NotFoundException When record not found.
       */
      public function edit($id = null) {
          $topic = $this->Topics->get($id, [
              'contain' => []
          ]);
          if ($this->request->is(['patch', 'post', 'put'])) {
              $topic = $this->Topics->patchEntity($topic, $this->request->getData());
              if ($this->Topics->save($topic)) {
                  $this->Flash->success(__('The topic has been saved.'));

                  return $this->redirect(['action' => 'index']);
              }
              $this->Flash->error(__('The topic could not be saved. Please, try again.'));
          }
          $subjects = $this->Topics->Subjects->find('list', ['limit' => 200]);
          $users = $this->Topics->Users->find('list', ['limit' => 200]);
          $this->set(compact('topic', 'subjects', 'users'));
      }

      /**
       * Delete method
       *
       * @param string|null $id Topic id.
       * @return \Cake\Http\Response|null Redirects to index.
       * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
       */
      public function delete($id = null) {
          $this->request->allowMethod(['post', 'delete']);
          $topic = $this->Topics->get($id);
          if ($this->Topics->delete($topic)) {
              $this->Flash->success(__('The topic has been deleted.'));
          } else {
              $this->Flash->error(__('The topic could not be deleted. Please, try again.'));
          }

          return $this->redirect(['action' => 'index']);
      }
      
       // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        $this->Auth->allow(['newapplicant']);
        $actions = ['addtopic','edittopic'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
        }
    }

  }
  