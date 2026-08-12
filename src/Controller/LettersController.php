<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Letters Controller
 *
 * @property \App\Model\Table\LettersTable $Letters
 * @method \App\Model\Entity\Letter[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LettersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Modes'],
        ];
        $letters = $this->paginate($this->Letters);

        $this->set(compact('letters'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Letter id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $letter = $this->Letters->get($id, [
            'contain' => ['Modes'],
        ]);

        $this->set(compact('letter'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addletter()
    {
        $letter = $this->Letters->newEmptyEntity();
        if ($this->request->is('post')) {
            $letter = $this->Letters->patchEntity($letter, $this->request->getData());
            if ($this->Letters->save($letter)) {
                $this->Flash->success(__('The letter has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The letter could not be saved. Please, try again.'));
        }
        $modes = $this->Letters->Modes->find('list', ['limit' => 200])->all();
        $this->set(compact('letter', 'modes'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Letter id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editletter($id = null)
    {
        $letter = $this->Letters->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $letter = $this->Letters->patchEntity($letter, $this->request->getData());
            if ($this->Letters->save($letter)) {
                $this->Flash->success(__('The letter has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The letter could not be saved. Please, try again.'));
        }
        $modes = $this->Letters->Modes->find('list', ['limit' => 200])->all();
        $this->set(compact('letter', 'modes'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Letter id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $letter = $this->Letters->get($id);
        if ($this->Letters->delete($letter)) {
            $this->Flash->success(__('The letter has been deleted.'));
        } else {
            $this->Flash->error(__('The letter could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
