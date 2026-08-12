<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$status = ['success'=>'Paid', 'Unpaid'=>'Unpaid'];
?>

<!-- Begin Page Content -->
        <div class="content container-fluid">
          <!-- Page Header -->
          <div class="page-header">
            <div class="row">
              <div class="col-sm-12">
                <h3 class="page-title">Manage Expenses</h3>
                <ul class="breadcrumb">
                  
                  <span style="float: left;"> <?= $this->Html->link(__('Add New Spending'), ['action' => 'add'], ['class' => 'button pull-right']) ?> </span>
                </ul>
              </div>
            </div>
          </div>
          <!-- /Page Header -->

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Expenses</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
              <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
           <th> Amount </th>
                 <th>Description</th>
                <th>Date</th>
                
                   <th>Action</th>
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
           <th> Amount </th>
                 <th>Description</th>
                <th>Date</th>
                
                   <th>Action</th>
               
            </tr>
              </tfoot>
            
        
         <tbody>
            <?php $paidsum = 0; foreach ($spendings as $spending): 
                $paidsum = $spending->amount+$paidsum; 
            ?>
            <tr>
                 <td>₦<?= $spending->amount?></td>
                <td><?= h($spending->description) ?></td>
                <td><?= h($spending->datecreated) ?></td>
                <td>
                <?= $this->Html->link(__(' Update'), ['action' => 'edit', $spending->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $spending->id], ['confirm' => __('Are you sure you want to delete # {0}?', $spending->id), 'class' => 'side-nav-item']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            
        </tbody>
        
                </table>
                  Total : <span class="text-info" style="text-decoration: underline #00c292 solid;">₦<?= number_format($paidsum)?></span>   
              </div>
            </div>
          </div>

        </div>

