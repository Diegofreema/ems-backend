<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DepartmentsSubjects Controller
 *
 * @property \App\Model\Table\DepartmentsSubjectsTable $DepartmentsSubjects
 * @method \App\Model\Entity\DepartmentsSubject[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsSubjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Subjects'],
        ];
        $departmentsSubjects = $this->paginate($this->DepartmentsSubjects);

        $this->set(compact('departmentsSubjects'));
    }

    /**
     * View method
     *
     * @param string|null $id Departments Subject id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $departmentsSubject = $this->DepartmentsSubjects->get($id, [
            'contain' => ['Departments', 'Subjects'],
        ]);

        $this->set(compact('departmentsSubject'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentsSubject = $this->DepartmentsSubjects->newEmptyEntity();
        if ($this->request->is('post')) {
            $departmentsSubject = $this->DepartmentsSubjects->patchEntity($departmentsSubject, $this->request->getData());
            if ($this->DepartmentsSubjects->save($departmentsSubject)) {
                $this->Flash->success(__('The departments subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments subject could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsSubjects->Departments->find('list', ['limit' => 200]);
        $subjects = $this->DepartmentsSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('departmentsSubject', 'departments', 'subjects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Departments Subject id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $departmentsSubject = $this->DepartmentsSubjects->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentsSubject = $this->DepartmentsSubjects->patchEntity($departmentsSubject, $this->request->getData());
            if ($this->DepartmentsSubjects->save($departmentsSubject)) {
                $this->Flash->success(__('The departments subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments subject could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsSubjects->Departments->find('list', ['limit' => 200]);
        $subjects = $this->DepartmentsSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('departmentsSubject', 'departments', 'subjects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Departments Subject id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentsSubject = $this->DepartmentsSubjects->get($id);
        if ($this->DepartmentsSubjects->delete($departmentsSubject)) {
            $this->Flash->success(__('The departments subject has been deleted.'));
        } else {
            $this->Flash->error(__('The departments subject could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
