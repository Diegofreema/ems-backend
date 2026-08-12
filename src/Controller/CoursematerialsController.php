<?php
declare(strict_types=1);

  namespace App\Controller;

  use App\Controller\AppController;

  /**
   * Coursematerials Controller
   *
   * @property \App\Model\Table\CoursematerialsTable $Coursematerials
   *
   * @method \App\Model\Entity\Coursematerial[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
   */
  class CoursematerialsController extends AppController {

      /**
       * Index method
       *
       * @return \Cake\Http\Response|void
       */
      public function index() {
          $this->paginate = [
              'contain' => ['Subjects', 'Teachers', 'Departments']
          ];
          $coursematerials = $this->paginate($this->Coursematerials);

          $this->set(compact('coursematerials'));
      }

      /**
       * View method
       *
       * @param string|null $id Coursematerial id.
       * @return \Cake\Http\Response|void
       * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
       */
      public function view($id = null) {
          $coursematerial = $this->Coursematerials->get($id, [
              'contain' => ['Subjects', 'Teachers', 'Departments']
          ]);

          $this->set('coursematerial', $coursematerial);
      }

      /**
       * Add method
       *
       * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
       */
      public function uploadmaterial() {
          //get this teacher
          $teacherscontroller = new TeachersController();
          $teacher = $teacherscontroller->isteacher();
          $coursematerial = $this->Coursematerials->newEmptyEntity();
          if ($this->request->is('post')) {
              $course_file = $this->request->getData('dfileurl');
              if (!empty($course_file['tmp_name'])) {
                  $material = $this->uploadcoursematerial($course_file, "coursematerials/");
              }
              $coursematerial = $this->Coursematerials->patchEntity($coursematerial, $this->request->getData());
              $coursematerial->teacher_id = $teacher->id;
              $coursematerial->fileurl = $material;
              //check if file already exists
              $oldmaterial = $this->Coursematerials->find()->where(['fileurl' => $material])->first();
              if (empty($oldmaterial)) {
                  if ($this->Coursematerials->save($coursematerial)) {
                      //log activity
                      $usercontroller = new UsersController();

                      $title = "Uploaded course material ";
                      $user_id = $this->Auth->user('id');
                      $description = "Uploaded " . $coursematerial->title;
                      $ip = $this->request->clientIp();
                      $type = "Add";
                      $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                      $this->Flash->success(__('The course material has been uploaded.'));

                      return $this->redirect(['action' => 'uploadmaterial']);
                  }
              } else { //materail already exists so just update
                  $oldmaterial = $this->Coursematerials->patchEntity($oldmaterial, $this->request->getData());
                  $oldmaterial->fileurl = $material;
                  $oldmaterial->updatedon = date('d M Y H:i a');
                  $this->Coursematerials->save($oldmaterial);
                  //log activity
                  $usercontroller = new UsersController();

                  $title = "Uploaded course material ";
                  $user_id = $this->Auth->user('id');
                  $description = "Uploaded " . $coursematerial->title;
                  $ip = $this->request->clientIp();
                  $type = "Update";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                  $this->Flash->success(__('The course material has been updated.'));

                  return $this->redirect(['action' => 'uploadmaterial']);
              }
              $this->Flash->error(__('The coursematerial could not be saved. Please, try again.'));
          }
          $teacher_subjects = [];
          foreach ($teacher->subjects as $subject) {
              array_push($teacher_subjects, $subject->id);
          }
          $subjects = $this->Coursematerials->Subjects->find('list', ['limit' => 200])
                  ->where(['id IN ' => $teacher_subjects])
                  ->order(['name' => 'ASC']);
          // $teachers = $this->Coursematerials->Teachers->find('list', ['limit' => 200]);
          $departments = $this->Coursematerials->Departments->find('list', ['limit' => 200])->where(['id' => $teacher->department->id]);
          $this->set(compact('coursematerial', 'subjects', 'departments'));
          $this->viewBuilder()->setLayout('adminbackend');
      }

      //method for uploading course materials
      private function uploadcoursematerial($file, $folder) {
          $extension = ['.docx', '.doc', '.pdf', '.txt'];
          //  $finfo = new \finfo(FILEINFO_MIME_TYPE);
          // $file_type = $finfo->file(h($file['tmp_name']), FILEINFO_MIME_TYPE);
          // $ext = pathinfo($file_type, PATHINFO_EXTENSION);
          $ext = strrchr($file['name'], '.');
          // echo $ext; exit;
          if (in_array($ext, $extension)) {
              $file_name = $file['name'];

              if (!file_exists($folder . $file_name . $ext)) {
                  $file_name = $this->GenerateUrl($file_name).$ext;

                  move_uploaded_file($file["tmp_name"], $folder . $file_name);

                  chmod($folder . $file_name, 0644);
                  return $message = $file_name;
              } else {
                  unlink($folder . $file_name);
                  // echo $file_name; exit;
                  move_uploaded_file($file["tmp_name"], $folder . $file_name);
                  chmod($folder . $file_name, 0644);
                  return $message = $file_name;
              }
          } else {
              return $message = 'Unable to upload file, please ensure you are uploading a docx,doc,pdf or txt file. ';
              // debug(json_encode( $error, JSON_PRETTY_PRINT)); exit;
          }
      }

      //method that transforms the name of files before upload
      private function GenerateUrl($s) {
          //Convert accented characters, and remove parentheses and apostrophes
          $from = explode(',', "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,(,),[,],'");
          $to = explode(',', 'c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,,,,,,');
          //Do the replacements, and convert all other non-alphanumeric characters to spaces
          $s = preg_replace('~[^a-zA-Z0-9]+~', '-', str_replace($from, $to, trim($s)));
          //Remove a - at the beginning or end and make lowercase
          return strtolower(preg_replace('/^-/', '', preg_replace('/-$/', '', $s)));
      }

      /**
       * Edit method
       *
       * @param string|null $id Coursematerial id.
       * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
       * @throws \Cake\Network\Exception\NotFoundException When record not found.
       */
      public function edit($id = null) {
          $coursematerial = $this->Coursematerials->get($id, [
              'contain' => []
          ]);
          if ($this->request->is(['patch', 'post', 'put'])) {
              $coursematerial = $this->Coursematerials->patchEntity($coursematerial, $this->request->getData());
              if ($this->Coursematerials->save($coursematerial)) {
                  $this->Flash->success(__('The coursematerial has been saved.'));

                  return $this->redirect(['action' => 'index']);
              }
              $this->Flash->error(__('The coursematerial could not be saved. Please, try again.'));
          }
          $subjects = $this->Coursematerials->Subjects->find('list', ['limit' => 200]);
          $teachers = $this->Coursematerials->Teachers->find('list', ['limit' => 200]);
          $departments = $this->Coursematerials->Departments->find('list', ['limit' => 200]);
          $this->set(compact('coursematerial', 'subjects', 'teachers', 'departments'));
      }

      /**
       * Delete method
       *
       * @param string|null $id Coursematerial id.
       * @return \Cake\Http\Response|null Redirects to index.
       * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
       */
      public function delete($id = null) {
          $this->request->allowMethod(['post', 'delete']);
          $coursematerial = $this->Coursematerials->get($id);
          if ($this->Coursematerials->delete($coursematerial)) {
              $this->Flash->success(__('The coursematerial has been deleted.'));
          } else {
              $this->Flash->error(__('The coursematerial could not be deleted. Please, try again.'));
          }

          return $this->redirect(['action' => 'index']);
      }

  }
  