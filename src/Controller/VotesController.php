<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Votes Controller
 *
 * @property \App\Model\Table\VotesTable $Votes
 * @method \App\Model\Entity\Vote[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VotesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Candidates', 'Students'],
        ];
        $votes = $this->paginate($this->Votes);

        $this->set(compact('votes'));
    }

    /**
     * View method
     *
     * @param string|null $id Vote id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $vote = $this->Votes->get($id, [
            'contain' => ['Candidates', 'Students'],
        ]);

        $this->set(compact('vote'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $vote = $this->Votes->newEmptyEntity();
        if ($this->request->is('post')) {
            $vote = $this->Votes->patchEntity($vote, $this->request->getData());
            if ($this->Votes->save($vote)) {
                $this->Flash->success(__('The vote has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vote could not be saved. Please, try again.'));
        }
        $candidates = $this->Votes->Candidates->find('list', ['limit' => 200])->all();
        $students = $this->Votes->Students->find('list', ['limit' => 200])->all();
        $this->set(compact('vote', 'candidates', 'students'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Vote id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $vote = $this->Votes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $vote = $this->Votes->patchEntity($vote, $this->request->getData());
            if ($this->Votes->save($vote)) {
                $this->Flash->success(__('The vote has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vote could not be saved. Please, try again.'));
        }
        $candidates = $this->Votes->Candidates->find('list', ['limit' => 200])->all();
        $students = $this->Votes->Students->find('list', ['limit' => 200])->all();
        $this->set(compact('vote', 'candidates', 'students'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Vote id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vote = $this->Votes->get($id);
        if ($this->Votes->delete($vote)) {
            $this->Flash->success(__('The vote has been deleted.'));
        } else {
            $this->Flash->error(__('The vote could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
     //method for clearing all votes
    public function clearvotes(){
      
         
        if($this->Votes->deleteAll(['counted'=>'yes'])){
        $this->Flash->success(__('All previous votes have been cleared.'));

                return $this->redirect(['controller'=>'Admins','action' => 'managecandidates']);    
        }else{
          $this->Flash->error(__('Sorry, unable to clear votes.'));

                return $this->redirect(['controller'=>'Admins','action' => 'managecandidates']);   
        }
    }
}
