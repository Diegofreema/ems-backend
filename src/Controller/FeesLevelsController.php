<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * FeesLevels Controller
 *
 * @property \App\Model\Table\FeesLevelsTable $FeesLevels
 * @method \App\Model\Entity\FeesLevel[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FeesLevelsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Fees', 'Levels'],
        ];
        $feesLevels = $this->paginate($this->FeesLevels);

        $this->set(compact('feesLevels'));
    }

    /**
     * View method
     *
     * @param string|null $id Fees Level id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $feesLevel = $this->FeesLevels->get($id, [
            'contain' => ['Fees', 'Levels'],
        ]);

        $this->set(compact('feesLevel'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $feesLevel = $this->FeesLevels->newEmptyEntity();
        if ($this->request->is('post')) {
            $feesLevel = $this->FeesLevels->patchEntity($feesLevel, $this->request->getData());
            if ($this->FeesLevels->save($feesLevel)) {
                $this->Flash->success(__('The fees level has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fees level could not be saved. Please, try again.'));
        }
        $fees = $this->FeesLevels->Fees->find('list', ['limit' => 200])->all();
        $levels = $this->FeesLevels->Levels->find('list', ['limit' => 200])->all();
        $this->set(compact('feesLevel', 'fees', 'levels'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Fees Level id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $feesLevel = $this->FeesLevels->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $feesLevel = $this->FeesLevels->patchEntity($feesLevel, $this->request->getData());
            if ($this->FeesLevels->save($feesLevel)) {
                $this->Flash->success(__('The fees level has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fees level could not be saved. Please, try again.'));
        }
        $fees = $this->FeesLevels->Fees->find('list', ['limit' => 200])->all();
        $levels = $this->FeesLevels->Levels->find('list', ['limit' => 200])->all();
        $this->set(compact('feesLevel', 'fees', 'levels'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Fees Level id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $feesLevel = $this->FeesLevels->get($id);
        if ($this->FeesLevels->delete($feesLevel)) {
            $this->Flash->success(__('The fees level has been deleted.'));
        } else {
            $this->Flash->error(__('The fees level could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
