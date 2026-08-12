<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Admins','action' => 'addcandidate'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'create new candidate']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Election Candidates/Positions </h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Candidates</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
                
               <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
              <th scope="col"><?= $this->Paginator->sort('Position') ?></th>
             <th scope="col"><?= $this->Paginator->sort('Votes') ?></th>
             <th scope="col">Action</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                
                <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
              <th scope="col"><?= $this->Paginator->sort('Position') ?></th>
             <th scope="col"><?= $this->Paginator->sort('Votes') ?></th>
              <th scope="col">Action</th>
            </tr>
        </tfoot>
        <tbody>
              <?php foreach ($candidates as $candidate): ?>
            <tr>
               
               <td><?= h($candidate->student->fname.' '.$candidate->student->lname).'( '.$candidate->student->regno.' )' ?></td>
                <td> <?=$candidate->position->name  ?> </td>
                <td> <?=$candidate->totalvotes  ?> </td>
                <td><?= $this->Form->postLink(__(' Delete'), ['controller'=>'Candidates','action' => 'delete', $candidate->id], 
                        ['confirm' => __('Are you sure you want to delete # {0}?', $candidate->student->lname),'class'=>'btn btn-danger']) ?> </td>
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
