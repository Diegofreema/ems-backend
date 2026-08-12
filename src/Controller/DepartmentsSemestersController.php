<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DepartmentsSemesters Controller
 *
 * @property \App\Model\Table\DepartmentsSemestersTable $DepartmentsSemesters
 * @method \App\Model\Entity\DepartmentsSemester[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsSemestersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Semesters'],
        ];
        $departmentsSemesters = $this->paginate($this->DepartmentsSemesters);

        $this->set(compact('departmentsSemesters'));
    }

    /**
     * View method
     *
     * @param string|null $id Departments Semester id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $departmentsSemester = $this->DepartmentsSemesters->get($id, [
            'contain' => ['Departments', 'Semesters'],
        ]);

        $this->set(compact('departmentsSemester'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentsSemester = $this->DepartmentsSemesters->newEmptyEntity();
        if ($this->request->is('post')) {
            $departmentsSemester = $this->DepartmentsSemesters->patchEntity($departmentsSemester, $this->request->getData());
            if ($this->DepartmentsSemesters->save($departmentsSemester)) {
                $this->Flash->success(__('The departments semester has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments semester could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsSemesters->Departments->find('list', ['limit' => 200]);
        $semesters = $this->DepartmentsSemesters->Semesters->find('list', ['limit' => 200]);
        $this->set(compact('departmentsSemester', 'departments', 'semesters'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Departments Semester id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $departmentsSemester = $this->DepartmentsSemesters->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentsSemester = $this->DepartmentsSemesters->patchEntity($departmentsSemester, $this->request->getData());
            if ($this->DepartmentsSemesters->save($departmentsSemester)) {
                $this->Flash->success(__('The departments semester has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments semester could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsSemesters->Departments->find('list', ['limit' => 200]);
        $semesters = $this->DepartmentsSemesters->Semesters->find('list', ['limit' => 200]);
        $this->set(compact('departmentsSemester', 'departments', 'semesters'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Departments Semester id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentsSemester = $this->DepartmentsSemesters->get($id);
        if ($this->DepartmentsSemesters->delete($departmentsSemester)) {
            $this->Flash->success(__('The departments semester has been deleted.'));
        } else {
            $this->Flash->error(__('The departments semester could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
