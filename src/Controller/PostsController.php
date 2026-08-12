<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Postcategories', 'Users','Comments'],'oder'=>['dateadded'=>'DESC']
        ];
        $posts = $this->paginate($this->Posts);
         $post = $this->Posts->newEmptyEntity();
        $postcategories = $this->Posts->Postcategories->find('list', ['limit' => 200])->all();

        $this->set(compact('posts','postcategories','post'));
        $this->viewBuilder()->setLayout('studentsbackend');
    }

    
    //admin method for managing posts on the forum
    public function manageposts(){
        $posts = $this->Posts->find()->contain(['Postcategories', 'Users','Comments'])
                ->order(['dateadded'=>'DESC'])->limit(40);
        $postcategories = $this->Posts->Postcategories->find('list', ['limit' => 200])->all();
        $this->set(compact('posts','postcategories'));
         $this->viewBuilder()->setLayout('backend'); 
    }



    //admin method for reading a post
    public function previewpost($id){
           $post = $this->Posts->get($id, [
            'contain' => ['Postcategories','Users'], 
            
        ]);
        //inrement view count
        $post->viewscount+=1;
        $this->Posts->save($post);
        //get comment
        $comments_Table = TableRegistry::get('Comments');
        $comments  =  $comments_Table->find()->contain(['Users.Students'])
                ->where(['post_id'=>$post->id,'status'=>'Live'])
                ->orderDesc('datecreated')->limit(10);
        
    //debug(json_encode($post, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('post','comments'));
          $this->viewBuilder()->setLayout('backend');
        
    }
    
    
    //admin methdo for closing coment on a post on the forum
    public function closecomment($id){
          $post = $this->Posts->get($id);
        //inrement view count 6170021642
        $post->allowcomments = "No";
        $this->Posts->save($post);
         $this->Flash->success(__('Comment closed'));
         //log activity
                $usercontroller = new UsersController();

                $title = "Close Commenting on a post ".$this->Auth->user('username');
                $user_id = $this->Auth->user('id');
                $description = "Closed commenting on post " . $post->title;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                return $this->redirect(['action' => 'manageposts']);
                  // $this->viewBuilder()->setLayout('backend');
        
    }
    
    //admin method for allowing comments on a post
    public function allowcomment($id){
       $post = $this->Posts->get($id);
        //inrement view count
        $post->allowcomments = "Yes";
        $this->Posts->save($post);
         $this->Flash->success(__('Comments Allowed'));
         //log activity
                $usercontroller = new UsersController();

                $title = "Allowed Commenting on a post ".$this->Auth->user('username');
                $user_id = $this->Auth->user('id');
                $description = "Allowed commenting on post " . $post->title;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                return $this->redirect(['action' => 'manageposts']);
                   //$this->viewBuilder()->setLayout('backend');   
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function readpost($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => ['Postcategories'], 
            
        ]);
        //inrement view count
        $post->viewscount+=1;
        $this->Posts->save($post);
        //get comment
        $comments_Table = TableRegistry::get('Comments');
        $comments  =  $comments_Table->find()->contain(['Users.Students'])
                ->where(['post_id'=>$post->id,'status'=>'Live'])
                ->orderDesc('datecreated')->limit(10);
        
    //debug(json_encode($post, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('post','comments'));
          $this->viewBuilder()->setLayout('studentsbackend');
    }
    
 



    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addpost()
    {
        $post = $this->Posts->newEmptyEntity();
        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
            $post->user_id =  $this->Auth->user('id');
            $post->postdetails = $this->request->getData('postdetails');
            
             // debug(json_encode($post, JSON_PRETTY_PRINT)); exit;
            if ($this->Posts->save($post)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Close Commenting on a post ".$this->Auth->user('username');
                $user_id = $this->Auth->user('id');
                $description = "Closed commenting on post " . $post->title;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The post has been saved.'));
                if($this->Auth->user('role_id')==1||$this->Auth->user('role_id')==5){
                   return $this->redirect(['action' => 'manageposts']); 
                }else{
                return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $postcategories = $this->Posts->Postcategories->find('list', ['limit' => 200])->all();
        $users = $this->Posts->Users->find('list', ['limit' => 200])->all();
        $this->set(compact('post', 'postcategories', 'users'));
          $this->viewBuilder()->setLayout('studentsbackend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editpost($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => [],
        ]);
        //ensure user owns this post
        if($post->user_id!= $this->Auth->user('id')){
        $this->Flash->error(__('Error, invalid access.'));   
        return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
             $post->postdetails = $this->request->getData('postdetails');
            if ($this->Posts->save($post)) {
                //log activity
                $usercontroller = new UsersController();

                $title = "Updated a post ".$this->Auth->user('username');
                $user_id = $this->Auth->user('id');
                $description = "Updated a post " . $post->title;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
                $this->Flash->success(__('The post has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be updated. Please, try again.'));
        }
        $postcategories = $this->Posts->Postcategories->find('list', ['limit' => 200])->all();
       
        $this->set(compact('post', 'postcategories'));
          $this->viewBuilder()->setLayout('studentsbackend');
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $post = $this->Posts->get($id);
        if ($this->Posts->delete($post)) {
            //log activity
                $usercontroller = new UsersController();

                $title = "Deleted a post ".$this->Auth->user('username');
                $user_id = $this->Auth->user('id');
                $description = "Deleted post " . $post->title;
                $ip = $this->request->clientIp();
                $type = "Edit";
                $usercontroller->makeLog($title, $user_id, $description, $ip, $type);
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
      public function beforeFilter(EventInterface $event) {
          $actions = ['addpost', 'editpost'];
        if (in_array($this->request->getParam('action'), $actions)) {
            // turn form protection 
            $this->FormProtection->setConfig('validate', false);
        }
      }
}
