
<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newrole'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'create new role']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Roles</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Roles Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
          <tr>
                
                <th scope="col"><?= $this->Paginator->sort('role_name') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
                
                <th scope="col"><?= $this->Paginator->sort('role_name') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
              </tfoot>
      
        <tbody>
            <?php foreach ($roles as $role): ?>
            <tr>
              
                <td><?= h($role->role_name) ?></td>
                <td class="actions">
                    <?= $this->Html->link(' ', ['action' => 'viewrole', $role->id]
                            ,['class' => 'btn btn-round btn-info fa fa-eye', 'title' => 'view role details']) ?>
                    <?= $this->Html->link(__(' '), ['action' => 'editrole', $role->id],
                             ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'update role']) ?>
                    <?= $this->Form->postLink(__(' '), ['action' => 'delete', $role->id],
                            ['confirm' => __('Are you sure you want to delete # {0}?', $role->id)
                                , 'class' => 'btn btn-round btn-danger fa fa-times-circle', 'title' => 'delete role']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
    
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
