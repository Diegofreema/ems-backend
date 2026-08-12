<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DepartmentsProgrammes Controller
 *
 * @property \App\Model\Table\DepartmentsProgrammesTable $DepartmentsProgrammes
 * @method \App\Model\Entity\DepartmentsProgramme[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsProgrammesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Programmes'],
        ];
        $departmentsProgrammes = $this->paginate($this->DepartmentsProgrammes);

        $this->set(compact('departmentsProgrammes'));
    }

    /**
     * View method
     *
     * @param string|null $id Departments Programme id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $departmentsProgramme = $this->DepartmentsProgrammes->get($id, [
            'contain' => ['Departments', 'Programmes'],
        ]);

        $this->set(compact('departmentsProgramme'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentsProgramme = $this->DepartmentsProgrammes->newEmptyEntity();
        if ($this->request->is('post')) {
            $departmentsProgramme = $this->DepartmentsProgrammes->patchEntity($departmentsProgramme, $this->request->getData());
            if ($this->DepartmentsProgrammes->save($departmentsProgramme)) {
                $this->Flash->success(__('The departments programme has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments programme could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsProgrammes->Departments->find('list', ['limit' => 200]);
        $programmes = $this->DepartmentsProgrammes->Programmes->find('list', ['limit' => 200]);
        $this->set(compact('departmentsProgramme', 'departments', 'programmes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Departments Programme id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $departmentsProgramme = $this->DepartmentsProgrammes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentsProgramme = $this->DepartmentsProgrammes->patchEntity($departmentsProgramme, $this->request->getData());
            if ($this->DepartmentsProgrammes->save($departmentsProgramme)) {
                $this->Flash->success(__('The departments programme has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments programme could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsProgrammes->Departments->find('list', ['limit' => 200]);
        $programmes = $this->DepartmentsProgrammes->Programmes->find('list', ['limit' => 200]);
        $this->set(compact('departmentsProgramme', 'departments', 'programmes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Departments Programme id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentsProgramme = $this->DepartmentsProgrammes->get($id);
        if ($this->DepartmentsProgrammes->delete($departmentsProgramme)) {
            $this->Flash->success(__('The departments programme has been deleted.'));
        } else {
            $this->Flash->error(__('The departments programme could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
