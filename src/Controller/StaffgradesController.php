<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Staffgrades Controller
 *
 * @property \App\Model\Table\StaffgradesTable $Staffgrades
 * @method \App\Model\Entity\Staffgrade[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StaffgradesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $staffgrades = $this->paginate($this->Staffgrades);

        $this->set(compact('staffgrades'));
    }

    /**
     * View method
     *
     * @param string|null $id Staffgrade id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $staffgrade = $this->Staffgrades->get($id, [
            'contain' => ['Teachers'],
        ]);

        $this->set(compact('staffgrade'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $staffgrade = $this->Staffgrades->newEmptyEntity();
        if ($this->request->is('post')) {
            $staffgrade = $this->Staffgrades->patchEntity($staffgrade, $this->request->getData());
            if ($this->Staffgrades->save($staffgrade)) {
                $this->Flash->success(__('The staffgrade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staffgrade could not be saved. Please, try again.'));
        }
        $this->set(compact('staffgrade'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Staffgrade id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $staffgrade = $this->Staffgrades->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $staffgrade = $this->Staffgrades->patchEntity($staffgrade, $this->request->getData());
            if ($this->Staffgrades->save($staffgrade)) {
                $this->Flash->success(__('The staffgrade has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The staffgrade could not be saved. Please, try again.'));
        }
        $this->set(compact('staffgrade'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Staffgrade id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $staffgrade = $this->Staffgrades->get($id);
        if ($this->Staffgrades->delete($staffgrade)) {
            $this->Flash->success(__('The staffgrade has been deleted.'));
        } else {
            $this->Flash->error(__('The staffgrade could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
