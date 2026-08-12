<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Muteds Controller
 *
 * @property \App\Model\Table\MutedsTable $Muteds
 * @method \App\Model\Entity\Muted[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MutedsController extends AppController
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
        $muteds = $this->paginate($this->Muteds);

        $this->set(compact('muteds'));
    }

    /**
     * View method
     *
     * @param string|null $id Muted id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $muted = $this->Muteds->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set(compact('muted'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $muted = $this->Muteds->newEmptyEntity();
        if ($this->request->is('post')) {
            $muted = $this->Muteds->patchEntity($muted, $this->request->getData());
            if ($this->Muteds->save($muted)) {
                $this->Flash->success(__('The muted has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The muted could not be saved. Please, try again.'));
        }
        $students = $this->Muteds->Students->find('list', ['limit' => 200])->all();
        $this->set(compact('muted', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Muted id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $muted = $this->Muteds->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $muted = $this->Muteds->patchEntity($muted, $this->request->getData());
            if ($this->Muteds->save($muted)) {
                $this->Flash->success(__('The muted has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The muted could not be saved. Please, try again.'));
        }
        $students = $this->Muteds->Students->find('list', ['limit' => 200])->all();
        $this->set(compact('muted', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Muted id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $muted = $this->Muteds->get($id);
        if ($this->Muteds->delete($muted)) {
            $this->Flash->success(__('The muted has been deleted.'));
        } else {
            $this->Flash->error(__('The muted could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
