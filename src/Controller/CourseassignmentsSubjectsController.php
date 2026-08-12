<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CourseassignmentsSubjects Controller
 *
 * @property \App\Model\Table\CourseassignmentsSubjectsTable $CourseassignmentsSubjects
 * @method \App\Model\Entity\CourseassignmentsSubject[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseassignmentsSubjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Courseassignments', 'Subjects'],
        ];
        $courseassignmentsSubjects = $this->paginate($this->CourseassignmentsSubjects);

        $this->set(compact('courseassignmentsSubjects'));
    }

    /**
     * View method
     *
     * @param string|null $id Courseassignments Subject id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $courseassignmentsSubject = $this->CourseassignmentsSubjects->get($id, [
            'contain' => ['Courseassignments', 'Subjects'],
        ]);

        $this->set(compact('courseassignmentsSubject'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $courseassignmentsSubject = $this->CourseassignmentsSubjects->newEmptyEntity();
        if ($this->request->is('post')) {
            $courseassignmentsSubject = $this->CourseassignmentsSubjects->patchEntity($courseassignmentsSubject, $this->request->getData());
            if ($this->CourseassignmentsSubjects->save($courseassignmentsSubject)) {
                $this->Flash->success(__('The courseassignments subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courseassignments subject could not be saved. Please, try again.'));
        }
        $courseassignments = $this->CourseassignmentsSubjects->Courseassignments->find('list', ['limit' => 200]);
        $subjects = $this->CourseassignmentsSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('courseassignmentsSubject', 'courseassignments', 'subjects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Courseassignments Subject id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $courseassignmentsSubject = $this->CourseassignmentsSubjects->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseassignmentsSubject = $this->CourseassignmentsSubjects->patchEntity($courseassignmentsSubject, $this->request->getData());
            if ($this->CourseassignmentsSubjects->save($courseassignmentsSubject)) {
                $this->Flash->success(__('The courseassignments subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courseassignments subject could not be saved. Please, try again.'));
        }
        $courseassignments = $this->CourseassignmentsSubjects->Courseassignments->find('list', ['limit' => 200]);
        $subjects = $this->CourseassignmentsSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('courseassignmentsSubject', 'courseassignments', 'subjects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Courseassignments Subject id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseassignmentsSubject = $this->CourseassignmentsSubjects->get($id);
        if ($this->CourseassignmentsSubjects->delete($courseassignmentsSubject)) {
            $this->Flash->success(__('The courseassignments subject has been deleted.'));
        } else {
            $this->Flash->error(__('The courseassignments subject could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
