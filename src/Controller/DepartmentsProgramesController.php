<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DepartmentsProgrames Controller
 *
 * @property \App\Model\Table\DepartmentsProgramesTable $DepartmentsProgrames
 * @method \App\Model\Entity\DepartmentsPrograme[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsProgramesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Programes'],
        ];
        $departmentsProgrames = $this->paginate($this->DepartmentsProgrames);

        $this->set(compact('departmentsProgrames'));
    }

    /**
     * View method
     *
     * @param string|null $id Departments Programe id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $departmentsPrograme = $this->DepartmentsProgrames->get($id, [
            'contain' => ['Departments', 'Programes'],
        ]);

        $this->set(compact('departmentsPrograme'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentsPrograme = $this->DepartmentsProgrames->newEmptyEntity();
        if ($this->request->is('post')) {
            $departmentsPrograme = $this->DepartmentsProgrames->patchEntity($departmentsPrograme, $this->request->getData());
            if ($this->DepartmentsProgrames->save($departmentsPrograme)) {
                $this->Flash->success(__('The departments programe has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments programe could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsProgrames->Departments->find('list', ['limit' => 200]);
        $programes = $this->DepartmentsProgrames->Programes->find('list', ['limit' => 200]);
        $this->set(compact('departmentsPrograme', 'departments', 'programes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Departments Programe id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $departmentsPrograme = $this->DepartmentsProgrames->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentsPrograme = $this->DepartmentsProgrames->patchEntity($departmentsPrograme, $this->request->getData());
            if ($this->DepartmentsProgrames->save($departmentsPrograme)) {
                $this->Flash->success(__('The departments programe has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments programe could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsProgrames->Departments->find('list', ['limit' => 200]);
        $programes = $this->DepartmentsProgrames->Programes->find('list', ['limit' => 200]);
        $this->set(compact('departmentsPrograme', 'departments', 'programes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Departments Programe id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentsPrograme = $this->DepartmentsProgrames->get($id);
        if ($this->DepartmentsProgrames->delete($departmentsPrograme)) {
            $this->Flash->success(__('The departments programe has been deleted.'));
        } else {
            $this->Flash->error(__('The departments programe could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
