<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SemestersSubjects Controller
 *
 * @property \App\Model\Table\SemestersSubjectsTable $SemestersSubjects
 * @method \App\Model\Entity\SemestersSubject[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SemestersSubjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Semesters', 'Subjects'],
        ];
        $semestersSubjects = $this->paginate($this->SemestersSubjects);

        $this->set(compact('semestersSubjects'));
    }

    /**
     * View method
     *
     * @param string|null $id Semesters Subject id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $semestersSubject = $this->SemestersSubjects->get($id, [
            'contain' => ['Semesters', 'Subjects'],
        ]);

        $this->set(compact('semestersSubject'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $semestersSubject = $this->SemestersSubjects->newEmptyEntity();
        if ($this->request->is('post')) {
            $semestersSubject = $this->SemestersSubjects->patchEntity($semestersSubject, $this->request->getData());
            if ($this->SemestersSubjects->save($semestersSubject)) {
                $this->Flash->success(__('The semesters subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The semesters subject could not be saved. Please, try again.'));
        }
        $semesters = $this->SemestersSubjects->Semesters->find('list', ['limit' => 200]);
        $subjects = $this->SemestersSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('semestersSubject', 'semesters', 'subjects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Semesters Subject id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $semestersSubject = $this->SemestersSubjects->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $semestersSubject = $this->SemestersSubjects->patchEntity($semestersSubject, $this->request->getData());
            if ($this->SemestersSubjects->save($semestersSubject)) {
                $this->Flash->success(__('The semesters subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The semesters subject could not be saved. Please, try again.'));
        }
        $semesters = $this->SemestersSubjects->Semesters->find('list', ['limit' => 200]);
        $subjects = $this->SemestersSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('semestersSubject', 'semesters', 'subjects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Semesters Subject id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $semestersSubject = $this->SemestersSubjects->get($id);
        if ($this->SemestersSubjects->delete($semestersSubject)) {
            $this->Flash->success(__('The semesters subject has been deleted.'));
        } else {
            $this->Flash->error(__('The semesters subject could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
