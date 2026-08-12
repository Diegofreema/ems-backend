<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Approvedresults Controller
 *
 * @property \App\Model\Table\ApprovedresultsTable $Approvedresults
 * @method \App\Model\Entity\Approvedresult[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprovedresultsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Sessions', 'Semesters', 'Admins'],
        ];
        $approvedresults = $this->paginate($this->Approvedresults);

        $this->set(compact('approvedresults'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Approvedresult id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $approvedresult = $this->Approvedresults->get($id, [
            'contain' => ['Sessions', 'Semesters', 'Admins'],
        ]);

        $this->set(compact('approvedresult'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $approvedresult = $this->Approvedresults->newEmptyEntity();
        if ($this->request->is('post')) {
         $admin =     $this->request->getSession()->read('admin');
            $approvedresult = $this->Approvedresults->patchEntity($approvedresult, $this->request->getData());
           $approvedresult->admin_id =  $admin->id;
            if ($this->Approvedresults->save($approvedresult)) {
                $this->Flash->success(__('The results has been approved for viewing by students.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The approvedresult could not be saved. Please, try again.'));
        }
        $sessions = $this->Approvedresults->Sessions->find('list', ['limit' => 200])->all();
        $semesters = $this->Approvedresults->Semesters->find('list', ['limit' => 200])->all();
        $admins = $this->Approvedresults->Admins->find('list', ['limit' => 200])->all();
        $this->set(compact('approvedresult', 'sessions', 'semesters', 'admins'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Approvedresult id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $approvedresult = $this->Approvedresults->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $approvedresult = $this->Approvedresults->patchEntity($approvedresult, $this->request->getData());
            if ($this->Approvedresults->save($approvedresult)) {
                $this->Flash->success(__('The approvedresult has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The approvedresult could not be saved. Please, try again.'));
        }
        $sessions = $this->Approvedresults->Sessions->find('list', ['limit' => 200])->all();
        $semesters = $this->Approvedresults->Semesters->find('list', ['limit' => 200])->all();
        $admins = $this->Approvedresults->Admins->find('list', ['limit' => 200])->all();
        $this->set(compact('approvedresult', 'sessions', 'semesters', 'admins'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Approvedresult id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $approvedresult = $this->Approvedresults->get($id);
        if ($this->Approvedresults->delete($approvedresult)) {
            $this->Flash->success(__('The approvedresult has been deleted.'));
        } else {
            $this->Flash->error(__('The approvedresult could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
