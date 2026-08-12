<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Semesters Controller
 *
 * @property \App\Model\Table\SemestersTable $Semesters
 *
 * @method \App\Model\Entity\Semester[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SemestersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function managesemesters()
    {
        $semesters = $this->paginate($this->Semesters);

        $this->set(compact('semesters'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Semester id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $semester = $this->Semesters->get($id, [
            'contain' => ['Results']
        ]);

        $this->set('semester', $semester);
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function newsemester()
    {
        $semester = $this->Semesters->newEmptyEntity();
        if ($this->request->is('post')) {
            $semester = $this->Semesters->patchEntity($semester, $this->request->getData());
            if ($this->Semesters->save($semester)) {
                $this->Flash->success(__('The term has been saved.'));

                return $this->redirect(['action' => 'managesemesters']);
            }
            $this->Flash->error(__('The term could not be saved. Please, try again.'));
        }
        $this->set(compact('semester'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Semester id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updatesemester($id = null)
    {
        $semester = $this->Semesters->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $semester = $this->Semesters->patchEntity($semester, $this->request->getData());
            if ($this->Semesters->save($semester)) {
                $this->Flash->success(__('The term has been saved.'));

                return $this->redirect(['action' => 'managesemesters']);
            }
            $this->Flash->error(__('The term could not be saved. Please, try again.'));
        }
        $this->set(compact('semester'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Semester id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $semester = $this->Semesters->get($id);
        if ($this->Semesters->delete($semester)) {
            $this->Flash->success(__('The term has been deleted.'));
        } else {
            $this->Flash->error(__('The term could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'managesemesters']);
    }
}
