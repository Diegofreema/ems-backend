<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;

/**
 * Eresources Controller
 *
 * @property \App\Model\Table\EresourcesTable $Eresources
 * @method \App\Model\Entity\Eresource[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EresourcesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments'],
        ];
           if ($this->request->is(['patch', 'post', 'put'])) {
         $deptid =  $this->request->getData('department_id');  
        $author =  $this->request->getData('author'); 
         $title =  $this->request->getData('title');
         
               
           }
        $departments_Table = TableRegistry::get('Departments');
        $eresources = $this->paginate($this->Eresources);

        $departments = $this->Eresources->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('eresources','departments'));
         $this->viewBuilder()->setLayout('backend');
    }
    
    
    //admin page for managing eresourcses
    public function manageresources(){
     $this->paginate = [
            'contain' => ['Departments'],
        ];
           if ($this->request->is(['patch', 'post', 'put'])) {
         $deptid =  $this->request->getData('department_id');  
        $author =  $this->request->getData('author'); 
         $title =  $this->request->getData('title');
         $condition = [];
         if(!empty($deptid)){
          $condition['department_id'] = $deptid;   
         }
         if(!empty($title)){
          $condition['title'] = $title;   
         }
        if(!empty($author)){
          $condition['author'] = $author;   
         } 
         
        $eresources = $this->Eresources->find()->where([$condition])->order(['Eresources.id'=>'DESC'])->limit(50);  
        $this->paginate($eresources);
               
           }else{
      //  $departments_Table = TableRegistry::get('Departments');
        $eresources = $this->paginate($this->Eresources);

           }
        $departments = $this->Eresources->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('eresources','departments'));
         $this->viewBuilder()->setLayout('backend');    
        
        
    }

    /**
     * View method
     *
     * @param string|null $id Eresource id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $eresource = $this->Eresources->get($id, [
            'contain' => ['Departments'],
        ]);

        $this->set(compact('eresource'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addresource()
    {
        $eresource = $this->Eresources->newEmptyEntity();
        if ($this->request->is('post')) {
          //  debug(json_encode($this->request->getData(), JSON_PRETTY_PRINT)); exit;
               //upload material
            $resource =  $this->request->getData('filenameurls');

            $filename = $resource->getClientFilename();
            if (!empty($filename)) {
                $resourceurl = $this->handlefileupload($this->request->getData('filenameurls'));
            }
            $eresource = $this->Eresources->patchEntity($eresource, $this->request->getData());
             $eresource->filenameurl = $resourceurl;
            
            if ($this->Eresources->save($eresource)) {
                $this->Flash->success(__('The e-resource has been saved.'));

                return $this->redirect(['controller'=>'Users','action' => 'dashboard']);
            }
            $this->Flash->error(__('The eresource could not be saved. Please, try again.'));
        }
        $departments = $this->Eresources->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('eresource', 'departments'));
         $this->viewBuilder()->setLayout('backend');
    }

    
      //the file upload method
    public function handlefileupload($filename) {
        $attachment = $filename;
        $folder = "eresources/";
        $name = $attachment->getClientFilename();
        $extension = strrchr($name, '.');
        $type = $attachment->getClientMediaType();

        $size = $attachment->getSize();
//        if(($size > 3000000)){
//          $this->Flash->error(__('There was an error uploading your file. Ensure file is <1mb and of right format(word or pdf).  Please, try again.'));
//            return 0;   
//        }
        // echo $type.' '. $size; exit;
        $tmpName = $attachment->getStream()->getMetadata('uri');
        $error = $attachment->getError();
        //  if(empty($tmpName)){}
        if (empty($filename)) {
            $this->Flash->error(__('There was an error uploading your file. Ensure file is <1mb and of right format(word or pdf).  Please, try again.'));
            return 0;
        }
        if ($error != 0) {
            $this->Flash->error(__('There was an error uploading your file. Ensure file is <1mb and of right format(word or pdf).  Please, try again.'));
            return 0;
        }
        $filenametobd = uniqid(date('d_m_y_h_i_s')) . '_' . $name;
        if ((($error == 0) && ($size < 8000000)) && (($type == "image/png") || ($type == "image/jpeg") || ($type == "image/pjpeg") || ($type == "image/x-png") || ($type == "application/pdf"))) {
            $attachment->moveTo($folder . $filenametobd);
            return $filenametobd;
        } else {
            $this->Flash->error(__('There was an error uploading your file. Ensure file is <3mb and of right format(word or pdf).  Please, try again.'));
            return 0;
        }
    }
    /**
     * Edit method
     *
     * @param string|null $id Eresource id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $eresource = $this->Eresources->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $eresource = $this->Eresources->patchEntity($eresource, $this->request->getData());
            if ($this->Eresources->save($eresource)) {
                $this->Flash->success(__('The eresource has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The eresource could not be saved. Please, try again.'));
        }
        $departments = $this->Eresources->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('eresource', 'departments'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Eresource id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $eresource = $this->Eresources->get($id);
        if ($this->Eresources->delete($eresource)) {
            $this->Flash->success(__('The eresource has been deleted.'));
        } else {
            $this->Flash->error(__('The eresource could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
      // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
        $this->Auth->allow(['adde']);

        $actions = ['addresource'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
            //turn off csrf
            // $this->eventManager()->off($this->Csrf);
        }
    }
}
