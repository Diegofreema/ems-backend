<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Admins','action' => 'addnewbook'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new book']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Library</h1></div>
           <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search for Books </h1>
                        </div>
     <?= $this->Form->create(null); ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('booktitle',['label'=>'Book Title','class' => 'form-control form-control-user2','placeholder'=>'enter book title']);
        ?>
        </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
             <?php
            echo $this->Form->control('bookauthor',['label'=>'Author','class' => 'form-control form-control-user2','placeholder'=>'enter book author']);
        ?>
        </div>
            
             <div class="col-sm-4 mb-3 mb-sm-0">
             <?php
            echo $this->Form->control('isbn',['label'=>'ISBN','class' => 'form-control form-control-user2','placeholder'=>'enter book isbn']);
        ?>
        </div>
       
            </div>
    </fieldset>
   <br /> <br />
            <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block ']) ?>  
   <?= $this->Form->end() ?>
                       
                    </div>
          
          
          
         <div class="row">
           <?php foreach ($books as $book): ?>
       <div class="card col-md-4" style="width:400px; margin: 10px; padding: 10px;">
           <?= $this->Html->image($book->coverphoto, ['alt' => 'EMS', 'class' => 'card-img-top','style'=>'height: 230px; width: 335px;'])?>
            
  <div class="card-body">
    <h4 class="card-title">Author : <?= h($book->author) ?></h4>
    <p class="card-text">Title : <?= h($book->title) ?></p>
     <p class="card-text">Status : <?= h($book->isavailable) ?></p>
    <?php if($book->isavailable=="Unavailable") {}
    else{ echo$this->Html->link(__(' Assign Book'), ['controller'=>'Admins','action' => 'assignbook', $book->id,$this->GenerateUrl($book->title)], ['class'=>'btn btn-primary float-right fa fa-archive','title'=>'assign to student']) ?>
                  <?= $this->Html->link(__(' Update Book'), ['controller'=>'Admins','action' => 'updatebook', $book->id,$this->GenerateUrl($book->title)], 
    ['class'=>'btn btn-success float-left fa fa-edit','title'=>'update book']);} ?>
                 
  </div>
</div>
       <?php endforeach; ?>   
          </div>
          
          

          <!-- DataTales Example -->
<!--          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Library Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
                
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('author') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pubdate') ?></th>
                <th scope="col"><?= $this->Paginator->sort('isavailable') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('isbn') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tfoot>
             <tr>
                
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('author') ?></th>
                <th scope="col"><?= $this->Paginator->sort('pubdate') ?></th>
                <th scope="col"><?= $this->Paginator->sort('isavailable') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('isbn') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
            
                <td><?= h($book->title) ?></td>
                <td><?= h($book->author) ?></td>
                <td><?= h($book->pubdate) ?></td>
                <td><?= h($book->isavailable) ?></td>
                    <td><?= h($book->isbn) ?></td>
              
                <td class="actions">
                  
                    <?= $this->Html->link(__('Update'), ['controller'=>'Admins','action' => 'updatebook', $book->id,$this->GenerateUrl($book->title)], ['class'=>'btn btn-round btn-primary fa fa-edit']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller'=>'Books','action' => 'delete', $book->id], 
                            ['confirm' => __('Are you sure you want to delete # {0}?', $book->title),'class'=>'btn btn-round btn-danger']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>-->

        </div>