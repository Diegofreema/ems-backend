<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Hostels Controller
 *
 * @property \App\Model\Table\HostelsTable $Hostels
 *
 * @method \App\Model\Entity\Hostel[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HostelsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $hostels = $this->paginate($this->Hostels);

        $this->set(compact('hostels'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Hostel id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $hostel = $this->Hostels->get($id, [
            'contain' => ['Hostelrooms']
        ]);

        $this->set('hostel', $hostel);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function createhostel()
    {
        $hostel = $this->Hostels->newEmptyEntity();
        if ($this->request->is('post')) {
            $hostel = $this->Hostels->patchEntity($hostel, $this->request->getData());
            if ($this->Hostels->save($hostel)) {
                $this->Flash->success(__('The hostel has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The hostel could not be saved. Please, try again.'));
        }
        $this->set(compact('hostel'));
           $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Hostel id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function updatehostel($id = null)
    {
        $hostel = $this->Hostels->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $hostel = $this->Hostels->patchEntity($hostel, $this->request->getData());
            if ($this->Hostels->save($hostel)) {
                $this->Flash->success(__('The hostel has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The hostel could not be updated. Please, try again.'));
        }
        $this->set(compact('hostel'));
           $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Hostel id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $hostel = $this->Hostels->get($id);
        if ($this->Hostels->delete($hostel)) {
            $this->Flash->success(__('The hostel has been deleted.'));
        } else {
            $this->Flash->error(__('The hostel could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
