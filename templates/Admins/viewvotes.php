<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Election Votes </h1></div>
         

         <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Election Votes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
            <tr>
                <th>#</th>
            
                <th scope="col"><?= $this->Paginator->sort('Student') ?></th>
              <th scope="col"><?= $this->Paginator->sort('Position') ?></th>
             <th scope="col"><?= $this->Paginator->sort('Votes') ?></th>
            
            </tr>
        </thead>
        
        <tbody>
              <?php $count = 0; foreach ($candidates as $candidate): $count++; ?>
            <tr>
                <td><?= $count ?></td>
            
               <td><?= h($candidate->student->regno.' ('.$candidate->student->fname.' '.$candidate->student->lname.')') ?></td>
                <td> <?=$candidate->position->name  ?> </td>
                <td> <?=$candidate->vote  ?> </td>
               
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
