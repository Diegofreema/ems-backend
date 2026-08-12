<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Forum Controller
 *
 * @property \App\Model\Table\ForumTable $Forum
 * @method \App\Model\Entity\Forum[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ForumController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Categories'],
        ];
        $forum = $this->paginate($this->Forum);

        $this->set(compact('forum'));
    }

    /**
     * View method
     *
     * @param string|null $id Forum id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $forum = $this->Forum->get($id, [
            'contain' => ['Users', 'Categories'],
        ]);

        $this->set(compact('forum'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $forum = $this->Forum->newEmptyEntity();
        if ($this->request->is('post')) {
            $forum = $this->Forum->patchEntity($forum, $this->request->getData());
            if ($this->Forum->save($forum)) {
                $this->Flash->success(__('The forum has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The forum could not be saved. Please, try again.'));
        }
        $users = $this->Forum->Users->find('list', ['limit' => 200]);
        $categories = $this->Forum->Categories->find('list', ['limit' => 200]);
        $this->set(compact('forum', 'users', 'categories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Forum id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $forum = $this->Forum->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $forum = $this->Forum->patchEntity($forum, $this->request->getData());
            if ($this->Forum->save($forum)) {
                $this->Flash->success(__('The forum has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The forum could not be saved. Please, try again.'));
        }
        $users = $this->Forum->Users->find('list', ['limit' => 200]);
        $categories = $this->Forum->Categories->find('list', ['limit' => 200]);
        $this->set(compact('forum', 'users', 'categories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Forum id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $forum = $this->Forum->get($id);
        if ($this->Forum->delete($forum)) {
            $this->Flash->success(__('The forum has been deleted.'));
        } else {
            $this->Flash->error(__('The forum could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
