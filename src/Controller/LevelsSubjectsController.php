<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * LevelsSubjects Controller
 *
 * @property \App\Model\Table\LevelsSubjectsTable $LevelsSubjects
 * @method \App\Model\Entity\LevelsSubject[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LevelsSubjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Subjects', 'Levels'],
        ];
        $levelsSubjects = $this->paginate($this->LevelsSubjects);

        $this->set(compact('levelsSubjects'));
    }

    /**
     * View method
     *
     * @param string|null $id Levels Subject id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $levelsSubject = $this->LevelsSubjects->get($id, [
            'contain' => ['Subjects', 'Levels'],
        ]);

        $this->set(compact('levelsSubject'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $levelsSubject = $this->LevelsSubjects->newEmptyEntity();
        if ($this->request->is('post')) {
            $levelsSubject = $this->LevelsSubjects->patchEntity($levelsSubject, $this->request->getData());
            if ($this->LevelsSubjects->save($levelsSubject)) {
                $this->Flash->success(__('The levels subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The levels subject could not be saved. Please, try again.'));
        }
        $subjects = $this->LevelsSubjects->Subjects->find('list', ['limit' => 200]);
        $levels = $this->LevelsSubjects->Levels->find('list', ['limit' => 200]);
        $this->set(compact('levelsSubject', 'subjects', 'levels'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Levels Subject id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $levelsSubject = $this->LevelsSubjects->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $levelsSubject = $this->LevelsSubjects->patchEntity($levelsSubject, $this->request->getData());
            if ($this->LevelsSubjects->save($levelsSubject)) {
                $this->Flash->success(__('The levels subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The levels subject could not be saved. Please, try again.'));
        }
        $subjects = $this->LevelsSubjects->Subjects->find('list', ['limit' => 200]);
        $levels = $this->LevelsSubjects->Levels->find('list', ['limit' => 200]);
        $this->set(compact('levelsSubject', 'subjects', 'levels'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Levels Subject id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $levelsSubject = $this->LevelsSubjects->get($id);
        if ($this->LevelsSubjects->delete($levelsSubject)) {
            $this->Flash->success(__('The levels subject has been deleted.'));
        } else {
            $this->Flash->error(__('The levels subject could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
