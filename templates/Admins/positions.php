<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Levels','action' => 'addnewclass'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'create new class']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Positions</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Position</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
                
               <th scope="col"><?= $this->Paginator->sort('Position') ?></th>
               <th scope="col"><?= $this->Paginator->sort('Start Time') ?></th>
               <th scope="col"><?= $this->Paginator->sort('End Time') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                
               <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </tfoot>
        <tbody>
              <?php foreach ($positions as $position): ?>
            <tr>
               
               <td><?= h($position->name) ?></td>
               <td><?= h(date('D d M Y h:i:s a', strtotime($position->votingstarts))) ?></td>
               <td><?= h(date('D d M Y h:i:s a', strtotime($position->votingends))) ?></td>
                <td class="actions">
                  
                    <?= $this->Html->link(__('Update'), ['controller'=>'Admins','action' => 'editposition', $position->id,$this->GenerateUrl($position->name)], ['class'=>'btn btn-round btn-primary fa fa-edit']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller'=>'Positions','action' => 'delete', $position->id], 
                            ['confirm' => __('Are you sure you want to delete # {0}?', $position->name),'class'=>'btn btn-round btn-danger']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
