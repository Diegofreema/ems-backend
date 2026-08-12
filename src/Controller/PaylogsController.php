<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Paylogs Controller
 *
 * @property \App\Model\Table\PaylogsTable $Paylogs
 * @method \App\Model\Entity\Paylog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaylogsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students'],
        ];
        $paylogs = $this->Paylogs->find()->contain(['Students'])->order(['Paylogs.id'=>'DESC'])->limit(3000);

        $this->set(compact('paylogs'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Paylog id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $paylog = $this->Paylogs->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set(compact('paylog'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paylog = $this->Paylogs->newEmptyEntity();
        if ($this->request->is('post')) {
            $paylog = $this->Paylogs->patchEntity($paylog, $this->request->getData());
            if ($this->Paylogs->save($paylog)) {
                $this->Flash->success(__('The paylog has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The paylog could not be saved. Please, try again.'));
        }
        $students = $this->Paylogs->Students->find('list', ['limit' => 200]);
        $this->set(compact('paylog', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Paylog id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $paylog = $this->Paylogs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $paylog = $this->Paylogs->patchEntity($paylog, $this->request->getData());
            if ($this->Paylogs->save($paylog)) {
                $this->Flash->success(__('The paylog has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The paylog could not be saved. Please, try again.'));
        }
        $students = $this->Paylogs->Students->find('list', ['limit' => 200]);
        $this->set(compact('paylog', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Paylog id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $paylog = $this->Paylogs->get($id);
        if ($this->Paylogs->delete($paylog)) {
            $this->Flash->success(__('The paylog has been deleted.'));
        } else {
            $this->Flash->error(__('The paylog could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
