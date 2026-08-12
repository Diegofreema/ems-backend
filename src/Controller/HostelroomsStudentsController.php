<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * HostelroomsStudents Controller
 *
 * @property \App\Model\Table\HostelroomsStudentsTable $HostelroomsStudents
 * @method \App\Model\Entity\HostelroomsStudent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HostelroomsStudentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Hostelrooms', 'Students'],
        ];
        $hostelroomsStudents = $this->paginate($this->HostelroomsStudents);

        $this->set(compact('hostelroomsStudents'));
    }

    /**
     * View method
     *
     * @param string|null $id Hostelrooms Student id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $hostelroomsStudent = $this->HostelroomsStudents->get($id, [
            'contain' => ['Hostelrooms', 'Students'],
        ]);

        $this->set(compact('hostelroomsStudent'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $hostelroomsStudent = $this->HostelroomsStudents->newEmptyEntity();
        if ($this->request->is('post')) {
            $hostelroomsStudent = $this->HostelroomsStudents->patchEntity($hostelroomsStudent, $this->request->getData());
            if ($this->HostelroomsStudents->save($hostelroomsStudent)) {
                $this->Flash->success(__('The hostelrooms student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The hostelrooms student could not be saved. Please, try again.'));
        }
        $hostelrooms = $this->HostelroomsStudents->Hostelrooms->find('list', ['limit' => 200]);
        $students = $this->HostelroomsStudents->Students->find('list', ['limit' => 200]);
        $this->set(compact('hostelroomsStudent', 'hostelrooms', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Hostelrooms Student id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $hostelroomsStudent = $this->HostelroomsStudents->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $hostelroomsStudent = $this->HostelroomsStudents->patchEntity($hostelroomsStudent, $this->request->getData());
            if ($this->HostelroomsStudents->save($hostelroomsStudent)) {
                $this->Flash->success(__('The hostelrooms student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The hostelrooms student could not be saved. Please, try again.'));
        }
        $hostelrooms = $this->HostelroomsStudents->Hostelrooms->find('list', ['limit' => 200]);
        $students = $this->HostelroomsStudents->Students->find('list', ['limit' => 200]);
        $this->set(compact('hostelroomsStudent', 'hostelrooms', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Hostelrooms Student id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $hostelroomsStudent = $this->HostelroomsStudents->get($id);
        if ($this->HostelroomsStudents->delete($hostelroomsStudent)) {
            $this->Flash->success(__('The hostelrooms student has been deleted.'));
        } else {
            $this->Flash->error(__('The hostelrooms student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
