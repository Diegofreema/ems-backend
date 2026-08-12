<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry;

/**
 * Timetables Controller
 *
 * @property \App\Model\Table\TimetablesTable $Timetables
 * @method \App\Model\Entity\Timetable[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TimetablesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Sessions', 'Departments', 'Semesters'],
        ];
        $timetables = $this->paginate($this->Timetables);

        $this->set(compact('timetables'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Timetable id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $timetable = $this->Timetables->get($id, [
            'contain' => ['Sessions', 'Departments', 'Levels', 'Semesters'],
        ]);
       //  debug(json_encode( $timetable, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('timetable'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addtimetable()
    {
        $timetable = $this->Timetables->newEmptyEntity();
        if ($this->request->is('post')) {
          //  debug(json_encode($this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $timetable = $this->Timetables->patchEntity($timetable, $this->request->getData());
            if ($this->Timetables->save($timetable)) {
                $this->Flash->success(__('The timetable has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time table could not be saved. Please, try again.'));
        }
        $sessions = $this->Timetables->Sessions->find('list', ['limit' => 200]);
        $departments = $this->Timetables->Departments->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $levels = $this->Timetables->Levels->find('list', ['limit' => 200]);
        $semesters = $this->Timetables->Semesters->find('list', ['limit' => 200]);
        $this->set(compact('timetable', 'sessions', 'departments', 'levels', 'semesters'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Timetable id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function update($id = null)
    {
        $timetable = $this->Timetables->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $timetable = $this->Timetables->patchEntity($timetable, $this->request->getData());
            if ($this->Timetables->save($timetable)) {
                $this->Flash->success(__('The timetable has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The timetable could not be saved. Please, try again.'));
        }
        $sessions = $this->Timetables->Sessions->find('list', ['limit' => 200]);
        $departments = $this->Timetables->Departments->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $levels = $this->Timetables->Levels->find('list', ['limit' => 200]);
        $semesters = $this->Timetables->Semesters->find('list', ['limit' => 200]);
        $this->set(compact('timetable', 'sessions', 'departments', 'levels', 'semesters'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    //method that shows the student their time table for the semester
    public function mytimetable(){
         $settings_Table = TableRegistry::get('Settings');
       $settings = $settings_Table->get(1, ['contain' => ['Sessions', 'Semesters']]);
       $this->request->getSession()->write('settings', $settings);
     $student =   $this->request->getSession()->read('student');  
      
      $timetable_sTable = TableRegistry::get('Timetables');
        
      $timetable =  $timetable_sTable->find()->contain(['Departments','Sessions','Semesters'])
              ->where(['department_id'=>$student->department_id,
          'session_id'=>$settings->session_id,'semester_id'=>$settings->semester_id])->first();
    // debug(json_encode($timetable, JSON_PRETTY_PRINT)); exit;
        $this->set('timetable',$timetable);
        
        
         $this->viewBuilder()->setLayout('studentsbackend');
    }








    /**
     * Delete method
     *
     * @param string|null $id Timetable id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $timetable = $this->Timetables->get($id);
        if ($this->Timetables->delete($timetable)) {
            $this->Flash->success(__('The timetable has been deleted.'));
        } else {
            $this->Flash->error(__('The timetable could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
