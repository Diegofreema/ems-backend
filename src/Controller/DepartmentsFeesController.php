<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DepartmentsFees Controller
 *
 * @property \App\Model\Table\DepartmentsFeesTable $DepartmentsFees
 * @method \App\Model\Entity\DepartmentsFee[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsFeesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Fees', 'Departments'],
        ];
        $departmentsFees = $this->paginate($this->DepartmentsFees);

        $this->set(compact('departmentsFees'));
    }

    /**
     * View method
     *
     * @param string|null $id Departments Fee id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $departmentsFee = $this->DepartmentsFees->get($id, [
            'contain' => ['Fees', 'Departments'],
        ]);

        $this->set(compact('departmentsFee'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentsFee = $this->DepartmentsFees->newEmptyEntity();
        if ($this->request->is('post')) {
            $departmentsFee = $this->DepartmentsFees->patchEntity($departmentsFee, $this->request->getData());
            if ($this->DepartmentsFees->save($departmentsFee)) {
                $this->Flash->success(__('The departments fee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments fee could not be saved. Please, try again.'));
        }
        $fees = $this->DepartmentsFees->Fees->find('list', ['limit' => 200])->all();
        $departments = $this->DepartmentsFees->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('departmentsFee', 'fees', 'departments'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Departments Fee id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $departmentsFee = $this->DepartmentsFees->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentsFee = $this->DepartmentsFees->patchEntity($departmentsFee, $this->request->getData());
            if ($this->DepartmentsFees->save($departmentsFee)) {
                $this->Flash->success(__('The departments fee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments fee could not be saved. Please, try again.'));
        }
        $fees = $this->DepartmentsFees->Fees->find('list', ['limit' => 200])->all();
        $departments = $this->DepartmentsFees->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('departmentsFee', 'fees', 'departments'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Departments Fee id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentsFee = $this->DepartmentsFees->get($id);
        if ($this->DepartmentsFees->delete($departmentsFee)) {
            $this->Flash->success(__('The departments fee has been deleted.'));
        } else {
            $this->Flash->error(__('The departments fee could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
