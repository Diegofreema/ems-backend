<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CourseregistrationsSubjects Controller
 *
 * @property \App\Model\Table\CourseregistrationsSubjectsTable $CourseregistrationsSubjects
 * @method \App\Model\Entity\CourseregistrationsSubject[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseregistrationsSubjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Courseregistrations', 'Subjects'],
        ];
        $courseregistrationsSubjects = $this->paginate($this->CourseregistrationsSubjects);

        $this->set(compact('courseregistrationsSubjects'));
    }

    /**
     * View method
     *
     * @param string|null $id Courseregistrations Subject id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $courseregistrationsSubject = $this->CourseregistrationsSubjects->get($id, [
            'contain' => ['Courseregistrations', 'Subjects'],
        ]);

        $this->set(compact('courseregistrationsSubject'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $courseregistrationsSubject = $this->CourseregistrationsSubjects->newEmptyEntity();
        if ($this->request->is('post')) {
            $courseregistrationsSubject = $this->CourseregistrationsSubjects->patchEntity($courseregistrationsSubject, $this->request->getData());
            if ($this->CourseregistrationsSubjects->save($courseregistrationsSubject)) {
                $this->Flash->success(__('The courseregistrations subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courseregistrations subject could not be saved. Please, try again.'));
        }
        $courseregistrations = $this->CourseregistrationsSubjects->Courseregistrations->find('list', ['limit' => 200]);
        $subjects = $this->CourseregistrationsSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('courseregistrationsSubject', 'courseregistrations', 'subjects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Courseregistrations Subject id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $courseregistrationsSubject = $this->CourseregistrationsSubjects->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseregistrationsSubject = $this->CourseregistrationsSubjects->patchEntity($courseregistrationsSubject, $this->request->getData());
            if ($this->CourseregistrationsSubjects->save($courseregistrationsSubject)) {
                $this->Flash->success(__('The courseregistrations subject has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courseregistrations subject could not be saved. Please, try again.'));
        }
        $courseregistrations = $this->CourseregistrationsSubjects->Courseregistrations->find('list', ['limit' => 200]);
        $subjects = $this->CourseregistrationsSubjects->Subjects->find('list', ['limit' => 200]);
        $this->set(compact('courseregistrationsSubject', 'courseregistrations', 'subjects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Courseregistrations Subject id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
      //  $this->request->allowMethod(['post', 'delete','put']);
        $courseregistrationsSubject = $this->CourseregistrationsSubjects->get($id);
        if ($this->CourseregistrationsSubjects->delete($courseregistrationsSubject)) {
            $this->Flash->success(__('The courseregistrations subject has been deleted.'));
        } else {
            $this->Flash->error(__('The courseregistrations subject could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
