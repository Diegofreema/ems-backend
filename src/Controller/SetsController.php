<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Sets Controller
 *
 * @property \App\Model\Table\SetsTable $Sets
 * @method \App\Model\Entity\Set[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SetsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $sets = $this->paginate($this->Sets);

        $this->set(compact('sets'));
    }

    /**
     * View method
     *
     * @param string|null $id Set id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $set = $this->Sets->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('set'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $set = $this->Sets->newEmptyEntity();
        if ($this->request->is('post')) {
            $set = $this->Sets->patchEntity($set, $this->request->getData());
            if ($this->Sets->save($set)) {
                $this->Flash->success(__('The set has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The set could not be saved. Please, try again.'));
        }
        $this->set(compact('set'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Set id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $set = $this->Sets->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $set = $this->Sets->patchEntity($set, $this->request->getData());
            if ($this->Sets->save($set)) {
                $this->Flash->success(__('The set has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The set could not be saved. Please, try again.'));
        }
        $this->set(compact('set'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Set id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $set = $this->Sets->get($id);
        if ($this->Sets->delete($set)) {
            $this->Flash->success(__('The set has been deleted.'));
        } else {
            $this->Flash->error(__('The set could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
