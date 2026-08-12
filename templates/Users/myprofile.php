<?php
$userdata = $this->request->getSession()->read('usersinfo');
?>
  <div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">User Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">User Profile</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
  <div class="row">
  <!-- Pie Chart -->
  <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">User Photo</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?=  $this->Html->image($admin->adminphoto, ['alt' => $admin->surname, 'class' => 'img-responsive avatar-view', "width"=>"100%", "height"=>"300px"])?>
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
        <h6 class="m-0 font-weight-bold text-primary">User Overview</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Name</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->surname . " " . $admin->lastname ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
          
           <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gender</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->gender ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
          
        
         <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Address</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->address ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-globe fa-2x text-gray-300"></i>
          </div>
        </div>
          
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->user->username ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-envelope fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Phone</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->phone ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-phone fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Account Status</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->user->userstatus ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-map-marker fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
<!--        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Department</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->department->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-home fa-2x text-gray-300"></i>
          </div>
        </div>
        /end no-gutters
        <hr/>-->
<!--         <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">State</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->state->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-globe fa-2x text-gray-300"></i>
          </div>
        </div>-->
        <!--/end no-gutters-->
<!--        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Country</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $admin->country->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-globe fa-2x text-gray-300"></i>
          </div>
        </div>
        /end no-gutters
        <hr/>-->
       
      </div>
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-8-->
</div></div>
