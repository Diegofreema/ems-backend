<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
        <div class="content container-fluid">
          <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Manage Classes</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">Manage Classes</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newdepartment'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new class']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Class Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Description') ?></th>
              
               
                <th scope="col"><?= $this->Paginator->sort('Code') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Description') ?></th>
              
               
                <th scope="col"><?= $this->Paginator->sort('Code') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
              </tfoot>
            
        </thead>
        <tbody>
            <?php foreach ($departments as $department): ?>
            <tr>
               
                <td><?= h($department->name) ?></td>
                <td><?= h($department->description) ?></td>
              
                 <td><?= h($department->deptcode) ?></td>
                <td class="actions">
                  <?= $this->Html->link(__(' View'), ['action' => 'viewdepartment', $department->id,$department->name],
                            ['class'=>'btn btn-round btn-primary fa fa-eye','title'=>'view subjects']) ?>
                    <?= $this->Html->link(__(' Update'), ['action' => 'updatedepartment', $department->id,$department->name],
                            ['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'update department details']) ?>
                    <?= $this->Html->link(__(' Arms'), ['controller' => 'ClassArms', 'action' => 'index', '?' => ['department_id' => $department->id]],
                            ['class'=>'btn btn-round btn-info fa fa-users','title'=>'manage class arms']) ?>
                    <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $department->id], 
                            ['confirm' => __('Are you sure you want to delete # {0}?', $department->name),
                                'class'=>'btn btn-round btn-danger fa fa-times','title'=>'delete department']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>


