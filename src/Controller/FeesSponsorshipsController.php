<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * FeesSponsorships Controller
 *
 * @property \App\Model\Table\FeesSponsorshipsTable $FeesSponsorships
 * @method \App\Model\Entity\FeesSponsorship[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FeesSponsorshipsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Fees', 'Sponsorships'],
        ];
        $feesSponsorships = $this->paginate($this->FeesSponsorships);

        $this->set(compact('feesSponsorships'));
    }

    /**
     * View method
     *
     * @param string|null $id Fees Sponsorship id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $feesSponsorship = $this->FeesSponsorships->get($id, [
            'contain' => ['Fees', 'Sponsorships'],
        ]);

        $this->set(compact('feesSponsorship'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $feesSponsorship = $this->FeesSponsorships->newEmptyEntity();
        if ($this->request->is('post')) {
            $feesSponsorship = $this->FeesSponsorships->patchEntity($feesSponsorship, $this->request->getData());
            if ($this->FeesSponsorships->save($feesSponsorship)) {
                $this->Flash->success(__('The fees sponsorship has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fees sponsorship could not be saved. Please, try again.'));
        }
        $fees = $this->FeesSponsorships->Fees->find('list', ['limit' => 200])->all();
        $sponsorships = $this->FeesSponsorships->Sponsorships->find('list', ['limit' => 200])->all();
        $this->set(compact('feesSponsorship', 'fees', 'sponsorships'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Fees Sponsorship id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $feesSponsorship = $this->FeesSponsorships->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $feesSponsorship = $this->FeesSponsorships->patchEntity($feesSponsorship, $this->request->getData());
            if ($this->FeesSponsorships->save($feesSponsorship)) {
                $this->Flash->success(__('The fees sponsorship has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fees sponsorship could not be saved. Please, try again.'));
        }
        $fees = $this->FeesSponsorships->Fees->find('list', ['limit' => 200])->all();
        $sponsorships = $this->FeesSponsorships->Sponsorships->find('list', ['limit' => 200])->all();
        $this->set(compact('feesSponsorship', 'fees', 'sponsorships'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Fees Sponsorship id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $feesSponsorship = $this->FeesSponsorships->get($id);
        if ($this->FeesSponsorships->delete($feesSponsorship)) {
            $this->Flash->success(__('The fees sponsorship has been deleted.'));
        } else {
            $this->Flash->error(__('The fees sponsorship could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
