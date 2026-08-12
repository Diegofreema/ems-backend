<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Update Profile</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
            ?></li>
            <li class="breadcrumb-item active">Update Profile</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
<!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
          <div class="col-lg-12">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Update Profile</h1>
              </div>
                  <?= $this->Form->create($admin,['type'=>'file','class'=>'user']) ?>
            
                <div class="form-group row">
                 
                  <div class="col-sm-6">
                  <?=$this->Form->control('surname', ['label' => false, 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'Last Name'])?>
                    
                  </div>
                     <div class="col-sm-6">
                   <?=$this->Form->control('lastname', ['label' => false, 'class' => 'form-control form-control-user2','id'=>'exampleFirstName'])?>
               
                  </div>
                 
                </div>
                
                  <div class="form-group row">
                   <div class="col-sm-6 mb-3 mb-sm-0">
                  <?= $this->Form->control('gender', ['label' => false, 'class' => 'form-control form-control-user2','placeholder'=>'gender'])?>
               
                  </div>
                 
           <div class="col-sm-6 mb-3 mb-sm-0">
              <?= $this->Form->control('dob', ['label' => false, 'class' => 'form-control form-control-user2', 'type' => 'text', 'id' => 'datepicker','placeholder'=>'date of birth'])?>
                    </div>
                      
                </div>
           
                
                 <div class="form-group row">
                  
                  <div class="col-sm-6">
                  <?= $this->Form->control('address', ['label' => false, 'class' => 'form-control form-control-user2'])?>
               
                  </div>
                      <div class="col-sm-6 mb-3 mb-sm-0">
              <?= $this->Form->control('phone', ['label' =>false, 'class' => 'form-control form-control-user2','placeholder'=>'phone'])?>
                    </div>
                </div>
                
                
<!--                  <div class="form-group row">
                 
                  <div class="col-sm-6">
                
              <?= $this->Form->control('country_id', ['label' => false, 'class' => 'form-control form-control-user2','placeholder'=>'country'])?>
                    </div>
                  <div class="col-sm-6">
                  <?= $this->Form->control('state_id', ['label' => false, 'class' => 'form-control form-control-user2','placeholder'=>'state']);?>

               
                  </div>
                </div>-->
                
                
                 <div class="form-group row">
                 
                 
                </div>
                
                
                <div class="form-group">

        <?= $this->Form->control('profile', ['label' => false, 'rows' => 6, 'colunm' => 6, 'required', 'class' => 'form-control form-control-user2','placeholder'=>'profile'])?>
               
                </div>
                
                
                
                
                  <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">

              <?= $this->Form->control('passport', ['label' => false, 'type' => 'file', 'class' => 'form-control form-control-user2','placeholder'=>'passport']);?>

                    </div>
                  
                </div>
                
                
              <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
             
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

