<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Programetypes Controller
 *
 * @property \App\Model\Table\ProgrametypesTable $Programetypes
 * @method \App\Model\Entity\Programetype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProgrametypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $programetypes = $this->paginate($this->Programetypes);

        $this->set(compact('programetypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Programetype id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $programetype = $this->Programetypes->get($id, [
            'contain' => ['Students'],
        ]);

        $this->set(compact('programetype'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $programetype = $this->Programetypes->newEmptyEntity();
        if ($this->request->is('post')) {
            $programetype = $this->Programetypes->patchEntity($programetype, $this->request->getData());
            if ($this->Programetypes->save($programetype)) {
                $this->Flash->success(__('The programetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The programetype could not be saved. Please, try again.'));
        }
        $this->set(compact('programetype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Programetype id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $programetype = $this->Programetypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programetype = $this->Programetypes->patchEntity($programetype, $this->request->getData());
            if ($this->Programetypes->save($programetype)) {
                $this->Flash->success(__('The programetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The programetype could not be saved. Please, try again.'));
        }
        $this->set(compact('programetype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Programetype id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $programetype = $this->Programetypes->get($id);
        if ($this->Programetypes->delete($programetype)) {
            $this->Flash->success(__('The programetype has been deleted.'));
        } else {
            $this->Flash->error(__('The programetype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
