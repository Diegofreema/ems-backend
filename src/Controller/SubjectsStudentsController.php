<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SubjectsStudents Controller
 *
 * @property \App\Model\Table\SubjectsStudentsTable $SubjectsStudents
 * @method \App\Model\Entity\SubjectsStudent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SubjectsStudentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Subjects', 'Students'],
        ];
        $subjectsStudents = $this->paginate($this->SubjectsStudents);

        $this->set(compact('subjectsStudents'));
    }

    /**
     * View method
     *
     * @param string|null $id Subjects Student id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $subjectsStudent = $this->SubjectsStudents->get($id, [
            'contain' => ['Subjects', 'Students'],
        ]);

        $this->set(compact('subjectsStudent'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $subjectsStudent = $this->SubjectsStudents->newEmptyEntity();
        if ($this->request->is('post')) {
            $subjectsStudent = $this->SubjectsStudents->patchEntity($subjectsStudent, $this->request->getData());
            if ($this->SubjectsStudents->save($subjectsStudent)) {
                $this->Flash->success(__('The subjects student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The subjects student could not be saved. Please, try again.'));
        }
        $subjects = $this->SubjectsStudents->Subjects->find('list', ['limit' => 200]);
        $students = $this->SubjectsStudents->Students->find('list', ['limit' => 200]);
        $this->set(compact('subjectsStudent', 'subjects', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Subjects Student id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $subjectsStudent = $this->SubjectsStudents->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subjectsStudent = $this->SubjectsStudents->patchEntity($subjectsStudent, $this->request->getData());
            if ($this->SubjectsStudents->save($subjectsStudent)) {
                $this->Flash->success(__('The subjects student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The subjects student could not be saved. Please, try again.'));
        }
        $subjects = $this->SubjectsStudents->Subjects->find('list', ['limit' => 200]);
        $students = $this->SubjectsStudents->Students->find('list', ['limit' => 200]);
        $this->set(compact('subjectsStudent', 'subjects', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Subjects Student id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $subjectsStudent = $this->SubjectsStudents->get($id);
        if ($this->SubjectsStudents->delete($subjectsStudent)) {
            $this->Flash->success(__('The subjects student has been deleted.'));
        } else {
            $this->Flash->error(__('The subjects student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
