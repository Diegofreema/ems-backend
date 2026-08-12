<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Admins'],
        ];
        $events = $this->paginate($this->Events);

        $this->set(compact('events'));
    }
    
    
    
    //admin method for managing events
    public function manageevents(){
         $this->paginate = [
            'contain' => ['Admins'],
        ];
        $events = $this->paginate($this->Events);

        $this->set(compact('events'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    //admin  method for taking an event offline
    public function takedown($id=null){
         $event = $this->Events->get($id, [
            'contain' => ['Admins'],
        ]);
         $event->status = "offline";
         $this->Events->save($event);
         $this->Flash->success(__('The event has been taken down.'));

                return $this->redirect(['action' => 'manageevents']);
    }

    
      //admin  method for taking an event offline
    public function golive($id=null){
         $event = $this->Events->get($id, [
            'contain' => ['Admins'],
        ]);
         $event->status = "live";
         $this->Events->save($event);
         $this->Flash->success(__('The event has gone live.'));

                return $this->redirect(['action' => 'manageevents']);
    }


    //admin method for previewing an event
    public function preview($id=null){
          $event = $this->Events->get($id, [
            'contain' => ['Admins'],
        ]);
         $this->set('event', $event);
         $this->viewBuilder()->setLayout('backend'); 
    }

        /**
     * View method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => ['Admins'],
        ]);

        $this->set(compact('event'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function postevents()
           $admincontroller = new AdminsController();
            $admin = $admincontroller->isadmin();
        $event = $this->Events->newEmptyEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            $event->admin_id = $admin->id;
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'manageevents']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $admins = $this->Events->Admins->find('list', ['limit' => 200]);
        $this->set(compact('event', 'admins'));
          $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $admins = $this->Events->Admins->find('list', ['limit' => 200]);
        $this->set(compact('event', 'admins'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        if ($this->Events->delete($event)) {
            $this->Flash->success(__('The event has been deleted.'));
        } else {
            $this->Flash->error(__('The event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
