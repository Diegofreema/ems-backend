<?php
$userdata = $this->request->getSession()->read('usersinfo');
 $settings = $this->request->getSession()->read('settings');
?>
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Parent's Data</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Parents', ['controller' => 'Admins', 'action' => 'viewparents'], ['title' => 'Manage Parents']) ?></li>
                    <li class="breadcrumb-item active">Parent's Data</li>
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
        <h6 class="m-0 font-weight-bold text-primary">Father : <?=$sparent->fathersname.'<br />Mother '.$sparent->mothersname?></h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?=  $this->Html->image($settings->logo, ['alt' => 'Admin', 'class' => 'img-responsive avatar-view', "width"=>"100%", "height"=>"300px"])?>
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
          <h6 class="m-0 font-weight-bold text-primary">Parent Overview
             </h6>
           <span class="float-right">
        <?= $this->Html->link(__('Update'), ['controller' => 'Sparents', 'action' => 'edit',$sparent->id,$this->generateurl($sparent->mothersname)],['class'=>'float-right']) ?>
              </span>
      </div>
      <!-- Card Body -->
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mother's Phone</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sparent->motherphone?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
           <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Father's Phone</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sparent->fatherphone?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sparent->user->username ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-envelope fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Address</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sparent->address ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-phone fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Father's Occupation</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sparent->fathersjob?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-home fa-2x text-gray-300"></i>
          </div>
        </div>
       <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mother's Occupation</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sparent->mothersjob?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-home fa-2x text-gray-300"></i>
          </div>
        </div>
        
       
       
        <!--/end no-gutters-->
        <hr/>
        
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Child/Children</div>
            <?php foreach($sparent->students as $student):   ?>
            <div class="h5 mb-0 font-weight-bold text-gray-800">  <?= $this->Html->link(' '.$student->fname.' '.$student->lname, ['controller'=>'Students','action' => 'viewstudent', $student->id,$this->Generateurl($student->fname)],
                            ['class'=>'fa fa-eye','title'=>'view student details']) ?></div>
            
            <?php endforeach ?>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
        
        <!--/end no-gutters-->
        
       
      </div>
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-8-->
</div>
</div>
