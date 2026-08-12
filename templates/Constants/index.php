<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>



<!-- Begin Page Content -->
        <div class="container-fluid">

         <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Constants','action' => 'newgradepoint'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new grade point']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Grade Book</h1></div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Grade book Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
               <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
                    <tr>
                    
                    <th>Name</th>
                    <th>Value</th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($constants as $constant): ?>
                <tr>
                  
                    <td><?= h($constant->name) ?></td>
                    <td><?= h($constant->value) ?></td>
                    <td class="actions">
                        
                        <?= $this->Html->link(__(' Edit'), ['action' => 'editgradepoint', $constant->id],['class'=>'btn btn-round btn-primary fa fa-edit']) ?>
                        <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $constant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $constant->id),'class'=>'btn btn-round btn-danger fa fa-times']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
