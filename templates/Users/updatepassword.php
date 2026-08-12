<!-- Page Wrapper -->

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Change Password</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?>
                    </li>
                    <li class="breadcrumb-item active"> Update Password</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-0">
                <div class="card-header">
                    <h4 class="card-title mb-0">Password Manager</h4>
                </div>
                <div class="card-body">
                     <?= $this->Form->create(null) ?>
                        <div class="row">

                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Current Password</label>
                                    <div class="col-lg-9">
                                          <?= $this->Form->control('currentpass', ['label' => false, 'class' => 'form-control', 'required', 'placeholder' =>'current password','type'=>'password']) ?>  
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">New Password</label>
                                    <div class="col-lg-9">
                                        <?= $this->Form->control('password', ['label' => false, 'class' => 'form-control', 'required', 'placeholder' =>'new password','type'=>'password']) ?>  
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Repeat Password</label>
                                    <div class="col-lg-9">
                                        <?= $this->Form->control('cpassword', ['label' => false, 'class' => 'form-control', 'required', 'placeholder' => 'repeat password','type'=>'password']) ?>  

                                    </div>
                                </div>

                             
                                 
                              
                            </div>
                            
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                   <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>

</div>			
