<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use App\Controller\AppController;

/**
 * News Controller
 *
 * @property \App\Model\Table\NewsTable $News
 *
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NewsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users']
        ];
        $latest_news = $this->paginate($this->News);

        $this->set(compact('latest_news'));
         // $this->viewBuilder()->setLayout('webland');
    }

    
    //the news and events page
    public function managenews(){
//         $this->paginate = [
//            'contain' => ['Admins']
//        ];
        $news = $this->News->find()->order(['dateposted'=>'DESC'])->limit(8);

        $this->set(compact('news'));
         $this->viewBuilder()->setLayout('backend');
    }

    

    /**
     * View method
     *
     * @param string|null $id News id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function readnews($id = null)
    {
        $news = $this->News->get($id, [
           // 'contain' => ['Users']
        ]);
        //ensure this article is still available for viewing
        if($news->status !="live"){
            $this->Flash->error(__('The article you are looking has either been removed on never existed.')); 
              return $this->redirect(['action' => 'index']);
        }
         //debug(json_encode( $news, JSON_PRETTY_PRINT)); exit;
        //add view count
         $news->viewcount +=1;
         $this->News->save($news);
        $this->set('news', $news);
        $this->viewBuilder()->setLayout('webland');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function postnews()
    {//check privilege
        $news = $this->News->newEmptyEntity();
        if ($this->request->is('post')) {
            
            $admincontroller = new AdminsController();
            $admin = $admincontroller->isadmin();
            //check for image
               //upload o level
            $studentcontroller = new StudentsController();
                  $newsimage = $this->request->getData('nimage');
            $filename =  $newsimage->getClientFilename();
            if (!empty($filename)) {
              
              $news_pix = $studentcontroller->handlefileupload($this->request->getData('nimage'), 'img/');
            }
            else{
            $news_pix  = "";   
            }
            $news = $this->News->patchEntity($news, $this->request->getData());
            $news->user_id =   $admin->user_id;
            $news->newsimage =  $news_pix;
            if ($this->News->save($news)) {
                  //log activity
                      $usercontroller = new UsersController();
                      $title = "Posted A News Article ";
                      $user_id = $this->Auth->user('id');
                      $description = "News Posting ";
                      $ip = $this->request->clientIp();
                      $type = "Add";
                      $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The news has been posted.'));

                return $this->redirect(['action' => 'managenews']);
            }
            $this->Flash->error(__('The news could not be saved. Please, try again.'));
        }
      //  $users = $this->News->Users->find('list', ['limit' => 200]);
        $this->set(compact('news'));
        $this->viewBuilder()->setLayout('backend');
    }

    
    
     
    //admin  method for taking an event offline
    public function takedown($id=null){
         $news = $this->News->get($id, [
           // 'contain' => ['Admins'],
        ]);
        $news->status = "offline";
         $this->News->save($news);
         $this->Flash->success(__('The News has been taken down.'));

                return $this->redirect(['action' => 'managenews']);
    }

    
      //admin  method for taking an event offline
    public function golive($id=null){
         $news = $this->News->get($id, [
           // 'contain' => ['Admins'],
        ]);
         $news->status = "live";
         $this->News->save($news);
         $this->Flash->success(__('The News has gone live.'));

                return $this->redirect(['action' => 'managenews']);
    }
    
    
    
    
    
    //admin method for previewing a news article
    public function preview($id=null){
         $news = $this->News->get($id, [
           // 'contain' => ['Admins'],
        ]);
         $this->set('news', $news);
         $this->viewBuilder()->setLayout('backend');  
    }

        /**
     * Edit method
     *
     * @param string|null $id News id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function update($id = null)
    {
        $news = $this->News->get($id, [
           // 'contain' => ['Admins']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
          //  $admincontroller = new AdminsController();
          //  $admin = $admincontroller->isadmin();
            //check for image
               //upload o level
            $studentcontroller = new StudentsController();
                  $newsimage = $this->request->getData('nimage');
            $filename =  $newsimage->getClientFilename();
            if (!empty($filename)) {
              
              $news_pix = $studentcontroller->handlefileupload($this->request->getData('nimage'), 'img/');
            }
            else{
            $news_pix  =  $news->newsimage;   
            }
            $news = $this->News->patchEntity($news, $this->request->getData());
          //  $news->admin_id = $admin->id;
            $news->newsimage =  $news_pix;
            if ($this->News->save($news)) {
                 //log activity
                      $usercontroller = new UsersController();

                      $title = "Updated A News Article ";
                      $user_id = $this->Auth->user('id');
                      $description = "News Update ";
                      $ip = $this->request->clientIp();
                      $type = "Edit";
                      $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The news has been updated.'));

                return $this->redirect(['action' => 'managenews']);
            }
            $this->Flash->error(__('The news could not be saved. Please, try again.'));
        }
      
        $this->set(compact('news'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Delete method
     *
     * @param string|null $id News id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
        $this->request->allowMethod(['post', 'delete']);
        $news = $this->News->get($id);
        $news->status = "Disabled";
        if ($this->News->save($news)) {
            $this->Flash->success(__('The news has been disabled.'));
        } else {
            $this->Flash->error(__('The news could not be disabled. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
     // allow unrestricted pages
    public function beforeFilter(EventInterface $event) {
          $this->Auth->allow(['index','readnews']);
          
//          $actions = ['search', 'getpaymentnotification', 'transvalidate', 'verifyKey'];
//        if (in_array($this->request->getParam('action'), $actions)) {
//
//            // turn form protection 
//
//            $this->FormProtection->setConfig('validate', false);
//        }
          
      }

}
