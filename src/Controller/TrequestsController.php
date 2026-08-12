<?php
declare(strict_types=1);

namespace App\Controller;
  use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\Event\EventInterface;

/**
 * Trequests Controller
 *
 * @property \App\Model\Table\TrequestsTable $Trequests
 *
 * @method \App\Model\Entity\Trequest[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TrequestsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Continents', 'Countries', 'States', 'Couriers']
        ];
        $trequests = $this->paginate($this->Trequests);

        $this->set(compact('trequests'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    
    
    
    
    
    
    
    /**
     * View method
     *
     * @param string|null $id Trequest id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $trequest = $this->Trequests->get($id, [
            'contain' => ['Students', 'Continents', 'Countries', 'States', 'Couriers']
        ]);

        $this->set('trequest', $trequest);
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $trequest = $this->Trequests->newEmptyEntity();
        if ($this->request->is('post')) {
            $trequest = $this->Trequests->patchEntity($trequest, $this->request->getData());
            if ($this->Trequests->save($trequest)) {
                $this->Flash->success(__('The trequest has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trequest could not be saved. Please, try again.'));
        }
        $students = $this->Trequests->Students->find('list', ['limit' => 200]);
        $continents = $this->Trequests->Continents->find('list', ['limit' => 200]);
        $countries = $this->Trequests->Countries->find('list', ['limit' => 200]);
        $states = $this->Trequests->States->find('list', ['limit' => 200]);
        $couriers = $this->Trequests->Couriers->find('list', ['limit' => 200]);
        $this->set(compact('trequest', 'students', 'continents', 'countries', 'states', 'couriers'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Trequest id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $trequest = $this->Trequests->get($id, [
            'contain' => ['States','Countries','Couriers']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $trequest = $this->Trequests->patchEntity($trequest, $this->request->getData());
            if ($this->Trequests->save($trequest)) {
                 //log activity
                  $usercontroller = new UsersController();

                  $title = "Updated a Transcript Request " . $trequest->id;
                  $user_id = $this->Auth->user('id');
                  $description = "Updated a Request For Transcript from " . $trequest->student_id;
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The transcript request has been updated.'));

                return $this->redirect(['controller'=>'Admins','action' => 'managetranscriptorders']);
            }
            $this->Flash->error(__('The transcript request could not be saved. Please, try again.'));
        }
        //$students = $this->Trequests->Students->find('list', ['limit' => 200]);
        $continents = $this->Trequests->Continents->find('list', ['limit' => 200]);
        $countries = $this->Trequests->Countries->find('list', ['limit' => 5000]);
        $states = $this->Trequests->States->find('list', ['limit' => 5000]);
        $couriers = $this->Trequests->Couriers->find('list', ['limit' => 10]);
        $this->set(compact('trequest',  'continents', 'countries', 'states', 'couriers'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    
    //method that shows the status of the transcript
    public function deliverystatus($id){
        $trequest = $this->Trequests->get($id);
        $trequest->deliverystatus = "Delivered";
        $this->Trequests->save($trequest);
        $this->Flash->success(__('The transcript request has been updated as DELIVERED.'));
         return $this->redirect(['controller'=>'Admins','action' => 'managetranscriptorders']);
        
    }

    

      //function that returns the states on the drop down
      public function getstates($country_id) {
          $statestable = TableRegistry::get('States');
          $states = $statestable->find('list')
                  ->where(['country_id' => $country_id]);
          $this->set(compact('states'));
          //debug(json_encode($states , JSON_PRETTY_PRINT)); exit;
      }
      
         //method that gets the countries in a selected continent
      public function getcountries($continent_id) {
          $countries_Table = TableRegistry::get('Countries');
          $countries = $countries_Table->find('list')->where(['continent_id' => $continent_id]);
          $this->set('countries', $countries);
      }

      
      
    //student method for actually requestion transcript without login
    public function requesttranscript($student_id, $fname){
        $trequest_Table = TableRegistry::get('Trequests');
        $continents_Table = TableRegistry::get('Continents');
        $trequest = $trequest_Table->newEmptyEntity();
        $continent_costs = $continents_Table->find();
        if ($this->request->is('post')) {
            // debug(json_encode( $this->request->getData(), JSON_PRETTY_PRINT)); exit;
            $continentid = $this->request->getData('continent_id');
            $continent = $continents_Table->get($continentid);
            $trequest = $trequest_Table->patchEntity($trequest, $this->request->getData());
            $trequest->amount = $continent->cost;
            $trequest->student_id = $student_id;
            debug(json_encode( $trequest, JSON_PRETTY_PRINT)); exit;
            if ($trequest_Table->save($trequest)) {
                //created invoice
                $invoice_id = $this->creatnewinvoice($student_id, 26, $continent->cost);
                //proceed to payment gateway for payment
                // $transactionController = new TransactionsController();
               $this->Flash->success(__('Success, your transcript request has been submitted and would be processed within the next ten days as soon as we confirm your payment'));
                return $this->redirect(['action' => 'generatetranscriptpayeeid', $invoice_id, $student_id]);
            
                // $url = $this->gotopaystack($incoice_id, $student->id);
                // $this->Flash->success(__('Success, your transcript order has been submitted and would be processed within the next ten days'));
                // return $this->redirect($url);
            } else {
                $this->Flash->error(__('Sorry, unable to submit order. Please try again'));
                // return $this->redirect(['action' => 'myinvoices']);    
            }
        }

        $continents = $trequest_Table->Continents->find('list', ['limit' => 200]);
        $countries = $trequest_Table->Countries->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $states = $trequest_Table->States->find('list', ['limit' => 200])->order(['name'=>'ASC']);
        $couriers = $trequest_Table->Couriers->find('list', ['limit' => 200]);
        $this->set(compact('trequest', 'students', 'continents', 'countries', 'states', 'couriers', 'continent_costs '));
        $this->set('continent_costs', $continent_costs);
          $this->viewBuilder()->setLayout('loginlayout');
        
    }


    /**
     * Delete method
     *
     * @param string|null $id Trequest id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trequest = $this->Trequests->get($id);
        if ($this->Trequests->delete($trequest)) {
             //log activity
                  $usercontroller = new UsersController();

                  $title = "Deleted a Transcript Request " . $trequest->id;
                  $user_id = $this->Auth->user('id');
                  $description = "Deleted a Request For Transcript from " . $trequest->student_id;
                  $ip = $this->request->clientIp();
                  $type = "Delete";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The transcript request has been deleted.'));
        } else {
            $this->Flash->error(__('The trequest could not be deleted. Please, try again.'));
        }

        return $this->redirect(['contriller'=>'Admins','action' => 'managetranscriptorders']);
    }
    
      // allow unrestricted pages
      public function beforeFilter(EventInterface $event) {
        $this->Auth->allow([ 'requesttranscript','transcript','getstates']);
    }
}
