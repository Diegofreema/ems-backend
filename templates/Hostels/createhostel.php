<div class="content container-fluid">
  <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">New Student Hostel</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Hostels', ['controller' => 'Hostels', 'action' => 'index'], ['title' => 'manage hostels']) ?></li>
                    <li class="breadcrumb-item active">New Student Hostel</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">New Student Hostel</h1>
                        </div>
    <?= $this->Form->create($hostel) ?>
    <fieldset>
      <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('name', ['label' => 'Hostel Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
              <?php $htypes = ['Female Hostel'=>'Femal Hostl','Male Hotel'=>'Male Hostel'];
              echo $this->Form->control('type', ['options'=>$htypes,'label' => 'Hostel Type', 'required',
                                          'class' => 'form-control form-control-user2','empty'=>'Select'])
                                    ?>
          </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
               <?= $this->Form->control('phone', ['label' => 'Hostel Phone', 
                                          'class' => 'form-control form-control-user2'])
                                    ?>
          </div>
          </div>
          <div class="form-group row">
              <div class="col-sm-12 mb-3 mb-sm-0">
                  <?= $this->Form->control('address', ['label' => 'Hostel Address', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
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
