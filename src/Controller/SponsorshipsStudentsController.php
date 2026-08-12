<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SponsorshipsStudents Controller
 *
 * @property \App\Model\Table\SponsorshipsStudentsTable $SponsorshipsStudents
 * @method \App\Model\Entity\SponsorshipsStudent[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SponsorshipsStudentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Sponsorships'],
        ];
        $sponsorshipsStudents = $this->paginate($this->SponsorshipsStudents);

        $this->set(compact('sponsorshipsStudents'));
    }

    /**
     * View method
     *
     * @param string|null $id Sponsorships Student id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sponsorshipsStudent = $this->SponsorshipsStudents->get($id, [
            'contain' => ['Students', 'Sponsorships'],
        ]);

        $this->set(compact('sponsorshipsStudent'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $sponsorshipsStudent = $this->SponsorshipsStudents->newEmptyEntity();
        if ($this->request->is('post')) {
            $sponsorshipsStudent = $this->SponsorshipsStudents->patchEntity($sponsorshipsStudent, $this->request->getData());
            if ($this->SponsorshipsStudents->save($sponsorshipsStudent)) {
                $this->Flash->success(__('The sponsorships student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sponsorships student could not be saved. Please, try again.'));
        }
        $students = $this->SponsorshipsStudents->Students->find('list', ['limit' => 200]);
        $sponsorships = $this->SponsorshipsStudents->Sponsorships->find('list', ['limit' => 200]);
        $this->set(compact('sponsorshipsStudent', 'students', 'sponsorships'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Sponsorships Student id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sponsorshipsStudent = $this->SponsorshipsStudents->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sponsorshipsStudent = $this->SponsorshipsStudents->patchEntity($sponsorshipsStudent, $this->request->getData());
            if ($this->SponsorshipsStudents->save($sponsorshipsStudent)) {
                $this->Flash->success(__('The sponsorships student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sponsorships student could not be saved. Please, try again.'));
        }
        $students = $this->SponsorshipsStudents->Students->find('list', ['limit' => 200]);
        $sponsorships = $this->SponsorshipsStudents->Sponsorships->find('list', ['limit' => 200]);
        $this->set(compact('sponsorshipsStudent', 'students', 'sponsorships'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Sponsorships Student id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sponsorshipsStudent = $this->SponsorshipsStudents->get($id);
        if ($this->SponsorshipsStudents->delete($sponsorshipsStudent)) {
            $this->Flash->success(__('The sponsorships student has been deleted.'));
        } else {
            $this->Flash->error(__('The sponsorships student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
