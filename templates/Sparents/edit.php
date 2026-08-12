<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update Parent</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Parents', ['controller' => 'Admins', 'action' => 'viewparents'], ['title' => 'Manage Parents']) ?></li>
                    <li class="breadcrumb-item active">Update Parent</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Update Parent</h1>
                        </div>
    <?= $this->Form->create($sparent) ?>
    <fieldset>
      <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('fathersname',['label' => 'Father\'s Name', 'required',
                                          'class' => 'form-control form-control-user2'])?>
           </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
            <?=$this->Form->control('mothersname',['label' => 'Mother\'s Name', 'required',
                                          'class' => 'form-control form-control-user2'])?>  
          </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
            <?=$this->Form->control('fatherphone',['label' => 'Father\'s Phone', 'required',
                                          'class' => 'form-control form-control-user2'])?>  
          </div>
          </div>
        
         <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('motherphone',['label' => 'Mother\'s Phone', 'required',
                                          'class' => 'form-control form-control-user2'])?>
           </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
            <?=$this->Form->control('fathersjob',['label' => 'Father\'s Occupation', 'required',
                                          'class' => 'form-control form-control-user2'])?>  
          </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
            <?=$this->Form->control('mothersjob',['label' => 'Mother\'s Occupation', 'required',
                                          'class' => 'form-control form-control-user2'])?>  
          </div>
          </div>
        
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('pemailaddress',['label' => 'Email Addresse', 'required',
                                          'class' => 'form-control form-control-user2','type'=>'email','disabled'])?>
           </div>
            
            <div class="col-sm-8 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('address',['label' => 'Addresse', 'required',
                                          'class' => 'form-control form-control-user2'])?>
           </div>
          
          </div>
      
    </fieldset>
     <br /> <br />
                        <?= $this->Form->button('Update Parent', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
