<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Cafcredit Controller
 *
 * @property \App\Model\Table\CafcreditTable $Cafcredit
 * @method \App\Model\Entity\Cafcredit[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CafcreditController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $cafcredit = $this->paginate($this->Cafcredit);

        $this->set(compact('cafcredit'));
    }

    /**
     * View method
     *
     * @param string|null $id Cafcredit id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cafcredit = $this->Cafcredit->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('cafcredit'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cafcredit = $this->Cafcredit->newEmptyEntity();
        if ($this->request->is('post')) {
            $cafcredit = $this->Cafcredit->patchEntity($cafcredit, $this->request->getData());
            if ($this->Cafcredit->save($cafcredit)) {
                $this->Flash->success(__('The cafcredit has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cafcredit could not be saved. Please, try again.'));
        }
        $this->set(compact('cafcredit'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cafcredit id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cafcredit = $this->Cafcredit->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cafcredit = $this->Cafcredit->patchEntity($cafcredit, $this->request->getData());
            if ($this->Cafcredit->save($cafcredit)) {
                $this->Flash->success(__('The cafcredit has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cafcredit could not be saved. Please, try again.'));
        }
        $this->set(compact('cafcredit'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cafcredit id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cafcredit = $this->Cafcredit->get($id);
        if ($this->Cafcredit->delete($cafcredit)) {
            $this->Flash->success(__('The cafcredit has been deleted.'));
        } else {
            $this->Flash->error(__('The cafcredit could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
