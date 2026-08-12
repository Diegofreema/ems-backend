<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Spendings Controller
 *
 * @property \App\Model\Table\SpendingsTable $Spendings
 * @method \App\Model\Entity\Spending[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SpendingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $spendings = $this->paginate($this->Spendings);

        $this->set(compact('spendings'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Spending id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $spending = $this->Spendings->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('spending'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $spending = $this->Spendings->newEmptyEntity();
        if ($this->request->is('post')) {
            $spending = $this->Spendings->patchEntity($spending, $this->request->getData());
            if ($this->Spendings->save($spending)) {
                $this->Flash->success(__('The spending has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The spending could not be saved. Please, try again.'));
        }
        $this->set(compact('spending'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Spending id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $spending = $this->Spendings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $spending = $this->Spendings->patchEntity($spending, $this->request->getData());
            if ($this->Spendings->save($spending)) {
                $this->Flash->success(__('The spending has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The spending could not be saved. Please, try again.'));
        }
        $this->set(compact('spending'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Spending id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $spending = $this->Spendings->get($id);
        if ($this->Spendings->delete($spending)) {
            $this->Flash->success(__('The spending has been deleted.'));
        } else {
            $this->Flash->error(__('The spending could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
