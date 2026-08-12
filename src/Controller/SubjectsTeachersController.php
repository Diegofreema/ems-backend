<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SubjectsTeachers Controller
 *
 * @property \App\Model\Table\SubjectsTeachersTable $SubjectsTeachers
 * @method \App\Model\Entity\SubjectsTeacher[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SubjectsTeachersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Teachers', 'Subjects'],
        ];
        $subjectsTeachers = $this->paginate($this->SubjectsTeachers);

        $this->set(compact('subjectsTeachers'));
    }

    /**
     * View method
     *
     * @param string|null $id Subjects Teacher id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $subjectsTeacher = $this->SubjectsTeachers->get($id, [
            'contain' => ['Teachers', 'Subjects'],
        ]);

        $this->set(compact('subjectsTeacher'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $subjectsTeacher = $this->SubjectsTeachers->newEmptyEntity();
        if ($this->request->is('post')) {
            $subjectsTeacher = $this->SubjectsTeachers->patchEntity($subjectsTeacher, $this->request->getData());
            if ($this->SubjectsTeachers->save($subjectsTeacher)) {
                $this->Flash->success(__('The subjects teacher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The subjects teacher could not be saved. Please, try again.'));
        }
        $teachers = $this->SubjectsTeachers->Teachers->find('list', ['limit' => 200]);
        $subjects = $this->SubjectsTeachers->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('subjectsTeacher', 'teachers', 'subjects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Subjects Teacher id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $subjectsTeacher = $this->SubjectsTeachers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subjectsTeacher = $this->SubjectsTeachers->patchEntity($subjectsTeacher, $this->request->getData());
            if ($this->SubjectsTeachers->save($subjectsTeacher)) {
                $this->Flash->success(__('The subjects teacher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The subjects teacher could not be saved. Please, try again.'));
        }
        $teachers = $this->SubjectsTeachers->Teachers->find('list', ['limit' => 200]);
        $subjects = $this->SubjectsTeachers->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('subjectsTeacher', 'teachers', 'subjects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Subjects Teacher id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $subjectsTeacher = $this->SubjectsTeachers->get($id);
        if ($this->SubjectsTeachers->delete($subjectsTeacher)) {
            $this->Flash->success(__('The subjects teacher has been deleted.'));
        } else {
            $this->Flash->error(__('The subjects teacher could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
