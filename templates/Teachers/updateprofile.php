<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
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
                        <?= $this->Form->create($teacher, ['type' => 'file']) ?>
                        <fieldset>
                            <!--         <div class="form-group row">
                                       <div class="col-sm-6 mb-3 mb-sm-0">
                            <?=
                              $this->Form->control('user_id', ['options' => $users, 'required', 'label' => false,
                                  'empty' => 'Select user', 'placeholder' => 'Select user', 'class' => 'form-control user2'])
                            ?>
                                        </div>
        
                                        <div class="col-sm-6">
                            <?php
                              //  $gender = ['Male' => 'Male', 'Female' => 'Female'];
                              echo $this->Form->control('gender', ['options' => $gender, 'label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'gender'])
                            ?>
        
                                        </div>
                                    </div>-->
                            <div class="form-group row">
                                <div class="col-sm-8 mb-3 mb-sm-0">

                                    <?= $this->Form->control('address', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'address', 'required']) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('qualification', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'Highest Qualification']); ?>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <?= $this->Form->control('country_id', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'country', 'empty' => 'Select Country']) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('state_id', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'state', 'empty' => 'Select State']); ?>

                                </div>

                                <div class="col-sm-4">
                                    <?= $this->Form->control('phone', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'phone']) ?>

                                </div>

                            </div>
                            <div class="form-group row">


                                <div class="col-sm-4">
                                    <?= $this->Form->control('ccv', ['label' => 'Upload Your CV', 'type' => 'file', 'class' => 'form-control form-control-user2', 'placeholder' => 'Upload CV']); ?>

                                </div>
                                <div class="col-sm-4">
                                    <?= $this->Form->control('passports', ['label' => 'Passport', 'type' => 'file', 'class' => 'form-control form-control-user2', 'placeholder' => 'Uplaod passport']); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php
                                      $gender = ['Male' => 'Male', 'Female' => 'Female'];
                                      echo $this->Form->control('gender', ['options' => $gender, 'label' => 'Gender', 'class' => 'form-control form-control-user2', 'placeholder' => 'gender'])
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 mb-3 mb-sm-0">
                                    <?= $this->Form->control('profile', ['label' => false, 'rows' => 6, 'colunm' => 6, 'required', 'class' => 'form-control form-control-user2', 'placeholder' => 'Profile']) ?>
                                </div>


                            </div>
                        </fieldset>
                        <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
<!-- /End Page Content -->
