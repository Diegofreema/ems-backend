<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Routing\Router;
use Cake\Utility\Xml;

/**
 * Apis Controller
 *
 * @property \App\Model\Table\ApisTable $Apis
 * @method \App\Model\Entity\Api[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApisController extends AppController
{
//    private function initialize(): void
//    {
//        parent::initialize();
//        $this->loadComponent('RequestHandler');
//    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $apis = $this->Apis->find('all')->all();
        debug(json_encode($apis, JSON_PRETTY_PRINT)); exit;

        $this->set(compact('apis'));
      //  $this->set('recipes', $apis);
       $xmlArray = Xml::build($apis);
       // $xml = Xml::fromArray(['response' => $apis->toArray()]);
        echo $xmlArray->asXML();
        $this->viewBuilder()->setOption('serialize', ['apis']);
    }

    /**
     * View method
     *
     * @param string|null $id Api id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $api = $this->Apis->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('api'));
        
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $api = $this->Apis->newEmptyEntity();
        
        $this->request->allowMethod(['post', 'put']);
        $api = $this->Apis->patchEntity($api, $this->request->getData());
       if ($this->Apis->save($api)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set([
            'message' => $message,
            'recipe' => $api,
        ]);
        $this->viewBuilder()->setOption('serialize', ['api', 'message']);
        
        
       
    }

    /**
     * Edit method
     *
     * @param string|null $id Api id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $api = $this->Apis->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $api = $this->Apis->patchEntity($api, $this->request->getData());
            if ($this->Apis->save($api)) {
                $this->Flash->success(__('The api has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The api could not be saved. Please, try again.'));
        }
        $this->set(compact('api'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Api id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $api = $this->Apis->get($id);
        if ($this->Apis->delete($api)) {
            $this->Flash->success(__('The api has been deleted.'));
        } else {
            $this->Flash->error(__('The api could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
