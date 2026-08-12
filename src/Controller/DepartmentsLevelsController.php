<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DepartmentsLevels Controller
 *
 * @property \App\Model\Table\DepartmentsLevelsTable $DepartmentsLevels
 * @method \App\Model\Entity\DepartmentsLevel[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DepartmentsLevelsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Departments', 'Levels'],
        ];
        $departmentsLevels = $this->paginate($this->DepartmentsLevels);

        $this->set(compact('departmentsLevels'));
    }

    /**
     * View method
     *
     * @param string|null $id Departments Level id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $departmentsLevel = $this->DepartmentsLevels->get($id, [
            'contain' => ['Departments', 'Levels'],
        ]);

        $this->set(compact('departmentsLevel'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $departmentsLevel = $this->DepartmentsLevels->newEmptyEntity();
        if ($this->request->is('post')) {
            $departmentsLevel = $this->DepartmentsLevels->patchEntity($departmentsLevel, $this->request->getData());
            if ($this->DepartmentsLevels->save($departmentsLevel)) {
                $this->Flash->success(__('The departments level has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments level could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsLevels->Departments->find('list', ['limit' => 200]);
        $levels = $this->DepartmentsLevels->Levels->find('list', ['limit' => 200]);
        $this->set(compact('departmentsLevel', 'departments', 'levels'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Departments Level id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $departmentsLevel = $this->DepartmentsLevels->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $departmentsLevel = $this->DepartmentsLevels->patchEntity($departmentsLevel, $this->request->getData());
            if ($this->DepartmentsLevels->save($departmentsLevel)) {
                $this->Flash->success(__('The departments level has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The departments level could not be saved. Please, try again.'));
        }
        $departments = $this->DepartmentsLevels->Departments->find('list', ['limit' => 200]);
        $levels = $this->DepartmentsLevels->Levels->find('list', ['limit' => 200]);
        $this->set(compact('departmentsLevel', 'departments', 'levels'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Departments Level id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $departmentsLevel = $this->DepartmentsLevels->get($id);
        if ($this->DepartmentsLevels->delete($departmentsLevel)) {
            $this->Flash->success(__('The departments level has been deleted.'));
        } else {
            $this->Flash->error(__('The departments level could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
