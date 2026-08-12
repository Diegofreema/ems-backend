<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
        <?= $this->Html->link(__(' '), ['action' => 'add'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add addmission']) ?>
        
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Admission mode</h1></div>
         
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Admission Mode</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                 <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
                    
                    <th>Name</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modes as $mode): ?>
                <tr>
                  
                    <td><?= h($mode->name) ?></td>
                    <td class="actions">
                       
                        <?= $this->Html->link(__(' Edit'), ['action' => 'edit', $mode->id]
                                ,['class' => 'btn btn-round btn-info fa fa-edit', 'title' => 'update details']) ?>
                        <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $mode->id],
                                ['confirm' => __('Are you sure you want to delete # {0}?', $mode->id),'class' => 'btn btn-round btn-danger fa fa-times-circle']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
     </table>
              </div>
            </div>
          </div>
    
</div>