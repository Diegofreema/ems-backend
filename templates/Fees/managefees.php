<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Fees</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Manage Fees</li>
                </ul>
            </div>
        </div>
    </div>
    <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newfee'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'add new fee'])
?>
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Fee Manager</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                        <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Amount') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Created By') ?></th>
                            <th>Item Code</th>
                            <th>Remita Item Code</th>
                            <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                        </tr>
                    </thead>


                    <tfoot>
                        <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Amount') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Created By') ?></th>
                             <th>Credo Item Code</th>
                             <th>Remita Item Code</th>
                            <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </tfoot>

                 
                    <tbody>
                        <?php foreach ($fees as $fee): ?>
                         

                              <tr>

                                  <td><?= h($fee->name) ?></td>
                                  <td><?= $this->Number->format($fee->amount-500) ?></td>
                                  <td><?= $fee->has('user') ? $fee->user->username : '' ?></td>
                                   <td><?= $fee->itemcode ?></td>
                                   <td><?= $fee->remitaitemcode ?></td>
                                  <td><?= $this->Number->format($fee->status) ?></td>
                                  <td class="actions">
                                      <?= $this->Html->link(__(' View'), ['action' => 'viewfee', $fee->id, $this->Generateurl($fee->name)], ['class' => 'btn btn-round btn-info fa fa-eye', 'title' => 'view fee details'])
                                      ?>
                                      <?= $this->Html->link(__(' Update'), ['action' => 'editfee', $fee->id, $this->Generateurl($fee->name)], ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'update fee'])
                                      ?>
                                      <?php
                                      if ($fee->status ==0) { 
                                         echo $this->Form->postLink(__(' '), ['action' => 'activatefee', $fee->id], ['confirm' => __('Are you sure you want to activate this fee # {0}?', $fee->name),
                                              'class' => 'btn btn-round btn-success fa fa-check-circle', 'title' => 'activate fee']);
                                      } else {
                                       echo $this->Form->postLink(__(' '), ['action' => 'deactivatefee', $fee->id], ['confirm' => __('Are you sure you want to deactivate this fee # {0}?', $fee->name),
                                              'class' => 'btn btn-round btn-danger fa fa-times-circle', 'title' => 'deactivate fee']).'&nbsp;';  
                                    //  echo  $this->Html->link(__(' Allocate'), ['action' => 'allocatefees', $fee->id, $this->Generateurl($fee->name)], ['class' => 'btn btn-round btn-info fa fa-notes', 'title' => 'allocate this fee to departments']);
                                      }
                                      ?>
                                  </td>
                              </tr>
                              
                             
  <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

