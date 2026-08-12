<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="content container-fluid">
          <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Hostel Rooms</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Manage Hostel Rooms</li>
                </ul>
            </div>
        </div>
    </div>
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'createroom'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new hostel room']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Hostel Room Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
               <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
               
                <th scope="col"><?= $this->Paginator->sort('Hostel') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Floor') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Room Number') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Beds') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Occupied Beds') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Description') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hostelrooms as $hostelroom): ?>
            <tr>
               
                <td><?= $hostelroom->has('hostel') ? $hostelroom->hostel->name : '' ?></td>
                <td><?= h($hostelroom->floor) ?></td>
                <td><?= h($hostelroom->room_number) ?></td>
                <td><?= $this->Number->format($hostelroom->available_beds) ?></td>
                <td><?= $this->Number->format($hostelroom->occupiedbeds) ?></td>
                <td><?= h($hostelroom->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__(' '), ['action' => 'viewroom', $hostelroom->id,$this->generateurl($hostelroom->hostel->name)],['class'=>'fa fa-eye btn btn-success','title'=>'view hostel room']) ?>
                    <?= $this->Html->link(__(' '), ['action' => 'editroom', $hostelroom->id,$this->generateurl($hostelroom->hostel->name)],['class'=>'fa fa-edit btn btn-primary','title'=>'update hostel room']) ?>
                    <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $hostelroom->id], 
                            ['confirm' => __('Are you sure you want to delete # {0}?', $hostelroom->hostel->name),'class'=>'btn btn-danger','title'=>'delete hostel room']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
       </tbody>
    </table>
              </div>
            </div>
          </div>
</div>

