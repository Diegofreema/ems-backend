<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
        <div class="content container-fluid">
          <!-- Page Header -->
          <div class="page-header">
            <div class="row">
              <div class="col-sm-12">
                <h3 class="page-title">Manage Privileges</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                  <li class="breadcrumb-item active">Manage Privileges</li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /Page Header -->

          <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newprivilege'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new privilege']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Manage Privileges</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
              
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($privileges as $privilege): ?>
            <tr>
              
                <td><?= h($privilege->name) ?></td>
                <td class="actions">
                   <?= $this->Html->link(__(' View'), ['action' => 'viewprivilege', $privilege->id,$privilege->name],
                            ['class'=>'btn btn-info fa fa-eye']) ?>
                    <?= $this->Html->link(__(' Edit'), ['action' => 'editprivilege', $privilege->id,$privilege->name],
                            ['class'=>'btn btn-primary fa fa-edit']) ?>
                    
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
</div></div></div></div>
