<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingsController extends AppController {

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index() {
        $settings = $this->paginate($this->Settings);

        $this->set(compact('settings'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $setting = $this->Settings->get($id, [
            'contain' => []
        ]);

        $this->set('setting', $setting);
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $setting = $this->Settings->newEmptyEntity();
        if ($this->request->is('post')) {
            $setting = $this->Settings->patchEntity($setting, $this->request->getData());
            if ($this->Settings->save($setting)) {
                $this->Flash->success(__('The setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting could not be saved. Please, try again.'));
        }
        $this->set(compact('setting'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editsettings($id = null) {
        $setting = $this->Settings->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            //upload logo
           // debug(json_encode($this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $imagearray = $this->request->getUploadedFile('logos');
             $name = $imagearray->getClientFilename();
            if (!empty($name)) {
                $studentcontroller = new StudentsController();
                $school_logo =  $studentcontroller->handlefileupload($this->request->getData('logos'), 'img/');
            }
            else{
            $school_logo =  $setting->logo;  
            }
            
            //upload school stamp
            $stamparray = $this->request->getUploadedFile('school_stamp');
            $stampname = $stamparray->getClientFilename();
            if (!empty($stampname)) {
                $studentcontroller = new StudentsController();
                $school_stamp =  $studentcontroller->handlefileupload($this->request->getData('school_stamp'), 'img/');
            }
            else{
            $school_stamp =  $setting->school_stamp;  
            }
           
            $setting = $this->Settings->patchEntity($setting, $this->request->getData());
            $setting->logo =    $school_logo;
            $setting->school_stamp = $school_stamp;
            
            // Process date fields
            if (!empty($this->request->getData('currenttermends'))) {
                $setting->currenttermends = date('d/m/Y', strtotime($this->request->getData('currenttermends')));
            }
            
            if (!empty($this->request->getData('nexttermbegins'))) {
                $setting->nexttermbegins = date('d/m/Y', strtotime($this->request->getData('nexttermbegins')));
            }
            
            // debug("Final entity before save:");
            // debug("currenttermends: " . $setting->currenttermends);
            // debug("nexttermbegins: " . $setting->nexttermbegins);
            
            // Use direct SQL update since CakePHP save has issues
            $connection = $this->Settings->getConnection();
            $sql = "UPDATE settings SET currenttermends = ?, nexttermbegins = ? WHERE id = ?";
            $params = [$setting->currenttermends, $setting->nexttermbegins, $setting->id];
            
            $result = $connection->execute($sql, $params);
            // debug("Direct SQL result: " . ($result ? "SUCCESS" : "FAILED"));
            
            // Check if it worked
            $updatedSetting = $this->Settings->get($setting->id);
           
            
            if ($this->Settings->save($setting)) {
                $this->Flash->success(__('The setting has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting could not be saved. Please, try again.'));
        }
        $sessions = $this->Settings->Sessions->find('list',['limit' => 200])->order(['name'=>'ASC']);
        $semesters = $this->Settings->Semesters->find('list',['limit' => 200])->order(['name'=>'ASC']);
        $this->set(compact('setting','sessions','semesters'));
      $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $setting = $this->Settings->get($id);
        if ($this->Settings->delete($setting)) {
            $this->Flash->success(__('The setting has been deleted.'));
        } else {
            $this->Flash->error(__('The setting could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
