<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Library</h1></div>
         
          
         <div class="row">
           <?php foreach ($borrowedbooks as $book): ?>
       <div class="card col-md-4" style="width:400px">
           <?= $this->Html->image($book->book->coverphoto, ['alt' => 'EMS', 'class' => 'card-img-top','style'=>'height: 230px; width: 335px;'])?>
            
  <div class="card-body">
    <h4 class="card-title">Author : <?= h($book->book->author) ?></h4>
    <p class="card-text">Title : <?= h($book->book->title) ?></p>
     <p class="card-text">Status : <?= h($book->status) ?></p>
     <p class="card-text">Date Taken : <?= h(date('D d M Y', strtotime($book->date))) ?></p>
    <p class="card-text">Return Date : <?php echo $book->datetoreturn; 
    //echo strtotime($today) .'here '.strtotime(date('Y M D',strtotime($book->datetoreturn)));
     if(strtotime(date("d-M-Y",strtotime($book->datetoreturn)))<strtotime((date("d-M-Y")))){
          echo '<span class="text-danger"> (This book is over due, please return!).</span>';    
      } 
      else{
         echo '<span class="text-success">'.$book->datetoreturn.'</span>'; 
      }
      
      ?></p>
            
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
