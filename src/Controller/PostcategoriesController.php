<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Postcategories Controller
 *
 * @property \App\Model\Table\PostcategoriesTable $Postcategories
 * @method \App\Model\Entity\Postcategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostcategoriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $postcategories = $this->paginate($this->Postcategories);

        $this->set(compact('postcategories'));
    }

    /**
     * View method
     *
     * @param string|null $id Postcategory id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $postcategory = $this->Postcategories->get($id, [
            'contain' => ['Posts'],
        ]);

        $this->set(compact('postcategory'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $postcategory = $this->Postcategories->newEmptyEntity();
        if ($this->request->is('post')) {
            $postcategory = $this->Postcategories->patchEntity($postcategory, $this->request->getData());
            if ($this->Postcategories->save($postcategory)) {
                $this->Flash->success(__('The postcategory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The postcategory could not be saved. Please, try again.'));
        }
        $this->set(compact('postcategory'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Postcategory id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $postcategory = $this->Postcategories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $postcategory = $this->Postcategories->patchEntity($postcategory, $this->request->getData());
            if ($this->Postcategories->save($postcategory)) {
                $this->Flash->success(__('The postcategory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The postcategory could not be saved. Please, try again.'));
        }
        $this->set(compact('postcategory'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Postcategory id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $postcategory = $this->Postcategories->get($id);
        if ($this->Postcategories->delete($postcategory)) {
            $this->Flash->success(__('The postcategory has been deleted.'));
        } else {
            $this->Flash->error(__('The postcategory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
