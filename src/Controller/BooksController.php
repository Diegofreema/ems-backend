<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Books Controller
 *
 * @property \App\Model\Table\BooksTable $Books
 * @method \App\Model\Entity\Book[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BooksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dept = $this->request->getData('department_id');
            $author = $this->request->getData('author');
            $title = $this->request->getData('title');
             $condition = [];
            if (!empty($dept)) {
                $condition['department_id'] = $dept;
            }
            if (!empty( $author)) {
                $condition['author'] =  $author;
            }
            if (!empty($title)) {
                $condition['title'] = $title;
            }
           
            $books = $this->Books->find()->contain(['Departments'])->where($condition)->order(['title'=>'ASC']);

        }else{
        $this->paginate = ['contain' => ['Departments'],];
        $books = $this->paginate($this->Books);
       // debug(json_encode($books, JSON_PRETTY_PRINT)); exit;
        }
        $departments = $this->Books->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('books','departments'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * View method
     *
     * @param string|null $id Book id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $book = $this->Books->get($id, [
            'contain' => ['Users', 'Departments', 'Borrowedbooks'],
        ]);

        $this->set(compact('book'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addbook()
    {
        $book = $this->Books->newEmptyEntity();
        if ($this->request->is('post')) {
            $book = $this->Books->patchEntity($book, $this->request->getData());
            $book->user_id = $this->Auth->user('id');
            if ($this->Books->save($book)) {
                $this->Flash->success(__('The book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }
        $users = $this->Books->Users->find('list', ['limit' => 200])->all();
        $departments = $this->Books->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('book', 'users', 'departments'));
         $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Edit method
     *
     * @param string|null $id Book id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function updatebook($id = null)
    {
        $book = $this->Books->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $book = $this->Books->patchEntity($book, $this->request->getData());
            if ($this->Books->save($book)) {
                $this->Flash->success(__('The book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }
        $users = $this->Books->Users->find('list', ['limit' => 200])->all();
        $departments = $this->Books->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('book', 'users', 'departments'));
         $this->viewBuilder()->setLayout('backend');
    }

    
    
    
    //students methods for finding books
    public function findbooks(){
       if ($this->request->is(['patch', 'post', 'put'])) {
            $dept = $this->request->getData('department_id');
            $author = $this->request->getData('author');
            $title = $this->request->getData('title');
             $condition = [];
            if (!empty($dept)) {
                $condition['department_id'] = $dept;
            }
            if (!empty( $author)) {
                $condition['author'] =  $author;
            }
            if (!empty($title)) {
                $condition['title'] = $title;
            }
           
            $books = $this->Books->find()->contain(['Departments'])->where($condition)->order(['title'=>'ASC']);

        }else{
        $this->paginate = ['contain' => ['Departments'],];
        $books = $this->paginate($this->Books);
       // debug(json_encode($books, JSON_PRETTY_PRINT)); exit;
        }
        $departments = $this->Books->Departments->find('list', ['limit' => 200])->all();
        $this->set(compact('books','departments')); 
        
         $this->viewBuilder()->setLayout('studentsbackend');    
    }
    
    
    
    /**
     * Delete method
     *
     * @param string|null $id Book id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $book = $this->Books->get($id);
        if ($this->Books->delete($book)) {
            $this->Flash->success(__('The book has been deleted.'));
        } else {
            $this->Flash->error(__('The book could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
