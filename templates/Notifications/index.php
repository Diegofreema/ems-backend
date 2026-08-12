<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="content container-fluid">
          <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Notifications</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Manage Notifications</li>
                </ul>
            </div>
        </div>
    </div>
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newnotification'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'Add new notification message']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Notifications</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Notifications Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
            
               <th scope="col"><?= $this->Paginator->sort('Title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Message') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Date Created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Admin') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Recipients') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Views') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tfoot>
             <tr>
            
                <th scope="col"><?= $this->Paginator->sort('Title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Message') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Date Created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Admin') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Recipients') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Views') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($notifications as $notification): ?>
            <tr>
           
                <td><?= h($notification->title) ?></td>
                <td><?= h($notification->message) ?></td>
                <td><?= h($notification->datecreated) ?></td>
                <td><?= $notification->has('user') ? $this->Html->link($notification->user->username, ['controller' => 'Users', 'action' => 'view', $notification->user->id]) : '' ?></td>
                <td><?= h($notification->recipients) ?></td>
                <td><?= h($notification->status) ?></td>
                <td><?= $this->Number->format($notification->viewcount) ?></td>
                <td class="actions">
                   
                    <?= $this->Html->link(__('Edit'), ['action' => 'editnotification', $notification->id,$notification->title],['class'=>'btn btn-primary fa fa-edit','title'=>'update notification']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $notification->id], 
                            ['confirm' => __('Are you sure you want to delete # {0}?', $notification->id),'class'=>'btn btn-danger','title'=>'delete notification']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>