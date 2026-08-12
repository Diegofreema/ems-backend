<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Staffdepartments Controller
 *
 * @property \App\Model\Table\StaffdepartmentsTable $Staffdepartments
 * @method \App\Model\Entity\Staffdepartment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StaffdepartmentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $staffdepartments = $this->paginate($this->Staffdepartments);

        $this->set(compact('staffdepartments'));
    }

    /**
     * View method
     *
     * @param string|null $id Staffdepartment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $staffdepartment = $this->Staffdepartments->get($id, [
            'contain' => ['Teachers'],
        ]);

        $this->set(compact('staffdepartment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $staffdepartment = $this->Staffdepartments->newEmptyEntity();
        if ($this->request->is('post')) {
            $staffdepartment = $this->Staffdepartments->patchEntity($staffdepartment, $this->request->getData());
            if ($this->Staffdepartments->save($staffdepartment)) {
                $this->Flash->success(__('The staffdepartment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staffdepartment could not be saved. Please, try again.'));
        }
        $this->set(compact('staffdepartment'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Staffdepartment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $staffdepartment = $this->Staffdepartments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffdepartment = $this->Staffdepartments->patchEntity($staffdepartment, $this->request->getData());
            if ($this->Staffdepartments->save($staffdepartment)) {
                $this->Flash->success(__('The staffdepartment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staffdepartment could not be saved. Please, try again.'));
        }
        $this->set(compact('staffdepartment'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Staffdepartment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staffdepartment = $this->Staffdepartments->get($id);
        if ($this->Staffdepartments->delete($staffdepartment)) {
            $this->Flash->success(__('The staffdepartment has been deleted.'));
        } else {
            $this->Flash->error(__('The staffdepartment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
