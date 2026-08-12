<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Assign Privileges</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
          <li class="breadcrumb-item"><?= $this->Html->link('Manage Admins', ['controller' => 'Users', 'action' => 'manageadmins'], ['title' => 'Manage Admins']) ?></li>
          <li class="breadcrumb-item active">Assign Privileges</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Assign Privileges</h1>
                        </div>
    <?= $this->Form->create($admin) ?>
    <fieldset>
    
         <div class="form-group row">
                  
                    
                     <div class="col-sm-4">
                  <?=$this->Form->control('surname', ['label' =>'Surname', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'surname'])?>
                    
                  </div>
                    
                      <div class="col-sm-4">
                  <?=$this->Form->control('lastname', ['label' => 'Last Name', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'last name'])?>
                    
                  </div>
               <div class="col-sm-4 mb-3 mb-sm-0">
                  <?php $gender = ['Male'=>'Male', 'Female'=>'Female'];  ?>
               

                  <?= $this->Form->control('gender', ['options'=>$gender,'label' => 'Gender', 'class' => 'form-control form-control-user2','placeholder'=>'gender'])?>

               
                      </div>
                </div>
        <div class="form-group row">
             <div class="col-sm-4 mb-3 mb-sm-0">
                
               

                  <?= $this->Form->control('phone', ['label' =>'Phone', 'class' => 'form-control form-control-user2','placeholder'=>'phone'])?>

               
                      </div>
              <div class="col-sm-8">
                  <?=$this->Form->control('privileges._ids', ['label' => 'Select Privileges','options'=>$privileges, 'class' => 'form-control select2_multiple',
                    'placeholder'=>'department'])?>
                    
                  </div>
                 
             
                </div>
        
      
    </fieldset>
      <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
</div>
</div></div></div></div></div></div>
