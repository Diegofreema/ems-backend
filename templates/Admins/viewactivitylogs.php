<?php
$userdata = $this->request->getSession()->read('usersinfo');
?>
<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Activity Log</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
          <li class="breadcrumb-item"><?= $this->Html->link('Manage Admins', ['controller' => 'Users', 'action' => 'manageadmins'], ['title' => 'Manage Admins']) ?></li>
          <li class="breadcrumb-item active">Activity Log</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Activity Log</h1>
  </div><!--/end d-sm-flex-->
  <div class="row">
  <!-- Pie Chart -->
  <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><?= $admin->surname . " " . $admin->lastname ?></h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?=  $this->Html->image($admin->adminphoto, ['alt' => 'admin', 'class' => 'img-responsive avatar-view', "width"=>"100%", "height"=>"300px"])?>
      </div>
      <!--/end card body-->
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-4-->

  <!-- Area Chart -->
  <div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Admin Activity Log</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
          <?php foreach($admin->user->logs as $log){ ?>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?= $log->title ?></div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $log->description . " - " . date('D d M,Y H:i',  strtotime($log->timestamp)) ?></div>
          </div>
          <div class="col-auto">
            <i class="text-gray-300"><?= $log->type ?></i>
          </div>
        </div>
           <hr/>
          <?php } ?>
          
       
       
      </div>
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-8-->
</div></div>

