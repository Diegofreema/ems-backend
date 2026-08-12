<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Candidates/Positions </h1></div>
         

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
               <th scope="col">Votes</th>
             <th scope="col">Actions</th>
             
            </tr>
        </thead>
        
        <tbody>
              <?php foreach ($candidates as $candidate): ?>
            <tr>
               
               <td><?= h($candidate->student->fname.' '.$candidate->student->lname).'( '.$candidate->student->regno.' )' ?></td>
                <td> <?=$candidate->position->name  ?> </td>
                <td> <?=$candidate->totalvotes  ?> </td>
               <td> <?php //echo date('y-m-d',strtotime($candidate->position->votingstarts)).'-'. date('y-m-d',time()); exit;
                if(date('ymd',strtotime($candidate->position->votingstarts))== date('ymd',time())){
               echo $this->Form->postLink(__('Vote'), ['controller' => 'Students', 'action' => 'votecandidate',$candidate->id,$candidate->position->id],
                       ['confirm' => __('Are you sure you want to vote # {0}?', $candidate->student->lname.' for the position of '.$candidate->position->name),
                'class'=>'btn btn-success']);} ?>
                </td>
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
