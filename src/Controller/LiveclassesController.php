<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Liveclasses Controller
 *
 * @property \App\Model\Table\LiveclassesTable $Liveclasses
 * @method \App\Model\Entity\Liveclass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LiveclassesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Teachers'],
        ];
        $liveclasses = $this->paginate($this->Liveclasses);

        $this->set(compact('liveclasses'));
    }

    /**
     * View method
     *
     * @param string|null $id Liveclass id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $liveclass = $this->Liveclasses->get($id, [
            'contain' => ['Teachers'],
        ]);

        $this->set(compact('liveclass'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addlink()
    {
        $liveclass = $this->Liveclasses->newEmptyEntity();
        if ($this->request->is('post')) {
            $liveclass = $this->Liveclasses->patchEntity($liveclass, $this->request->getData());
            //get teacher
            $teacherscontroller = new TeachersController();
             $teacher = $teacherscontroller->isteacher();
             $liveclass->teacher_id = $teacher->id;
            if ($this->Liveclasses->save($liveclass)) {
                $this->Flash->success(__('The live class has been saved.'));

                return $this->redirect(['controller'=>'Teachers','action' => 'myeclasses']);
            }
            $this->Flash->error(__('The live class could not be saved. Please, try again.'));
        }
        $teachers = $this->Liveclasses->Teachers->find('list', ['limit' => 200])->all();
        $this->set(compact('liveclass', 'teachers'));
         $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Liveclass id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $liveclass = $this->Liveclasses->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $liveclass = $this->Liveclasses->patchEntity($liveclass, $this->request->getData());
            if ($this->Liveclasses->save($liveclass)) {
                $this->Flash->success(__('The liveclass has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The liveclass could not be saved. Please, try again.'));
        }
        $teachers = $this->Liveclasses->Teachers->find('list', ['limit' => 200])->all();
        $this->set(compact('liveclass', 'teachers'));
    }

    
    //method for the embeded live class
    public function livelecture($id){
    $liveclass = $this->Liveclasses->get($id, [
            'contain' => [],
        ]);
     $this->set(compact('liveclass'));
      $this->viewBuilder()->setLayout('teachersbackend');
    
    }
    
    /**
     * Delete method
     *
     * @param string|null $id Liveclass id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $liveclass = $this->Liveclasses->get($id);
        if ($this->Liveclasses->delete($liveclass)) {
            $this->Flash->success(__('The liveclass has been deleted.'));
        } else {
            $this->Flash->error(__('The liveclass could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
