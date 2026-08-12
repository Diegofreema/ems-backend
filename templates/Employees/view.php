<?php
$userdata = $this->request->getSession()->read('usersinfo');
?>
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Employee Profile</h1>
  </div><!--/end d-sm-flex-->
  <div class="row">
  <!-- Pie Chart -->
  <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"> Photo</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?=  $this->Html->image( '../staff_files/'.$employee->photo, ['alt' =>  $employee->sname, 'class' => 'img-responsive avatar-view', "width"=>"100%", "height"=>"300px"])?>
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
        <h6 class="m-0 font-weight-bold text-primary"> Overview</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Name</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->sname . " " .  $employee->mname." ". $employee->fname ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
          
           <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gender</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->gender ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
          
        
         <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Address</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->address ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-globe fa-2x text-gray-300"></i>
          </div>
        </div>
          
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->user->username ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-envelope fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Phone</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->phone ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-phone fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">State of origin</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->state->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-map-marker fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">LGA</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->lga->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-map-marker fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Account Status</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->user->userstatus ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-check fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Grade</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->staffgrade->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-home fa-2x text-gray-300"></i>
          </div>
        </div>
                <hr/>

        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Department</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  $employee->staffdepartment->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-home fa-2x text-gray-300"></i>
          </div>
        </div>
       
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Join Date</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?=  date('D d M, Y',  strtotime($employee->dateadded)) ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-calendar fa-2x text-gray-300"></i>
          </div>
        </div>
       
        <hr/>
         <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">EMPID</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $employee->empid?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-id-card fa-2x text-gray-300"></i>
          </div>
        </div>
        <hr/>
         <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Profile</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $employee->profile?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-book fa-2x text-gray-300"></i>
          </div>
        </div>
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Added by</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $employee->admin->surname.' '.$employee->admin->lastname?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
        <hr/>
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


