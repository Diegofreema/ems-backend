<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update Class</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
  
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Classes', ['controller' => 'Departments', 'action' => 'managedepartments'], ['title' => 'manage classes']) ?></li>
                    <li class="breadcrumb-item active">Update Class</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Update Class</h1>
                        </div>
                        <?= $this->Form->create($department) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                    $this->Form->control('name', ['label' => false, 'placeholder' => 'Class Name', 'required',
                                        'class' => 'form-control form-control-user'])
                                    ?>
                                </div>

                                <div class="col-sm-4">


                                    <?=
                                    $this->Form->control('description', ['label' => false, 'placeholder' => 'Description', 'required',
                                        'class' => 'form-control form-control-user'])
                                    ?>
                                </div>
                                <div class="col-sm-4">
<?=
$this->Form->control('deptcode', ['label' => false, 'placeholder' => 'Class Code', 'required',
    'class' => 'form-control form-control-user'])
?>
                                </div>

                            </div>

                            <div class="form-group row">
                                

                                
                                <div class="col-sm-6">
                            <!-- <?= $this->Form->control('subjects._ids', ['options' => $subjects, 'label' => 'Assign Subjects', 'empty' => 'Select Subjects', 'class' => 'select2_multiple form-control form-control-user']) ?> -->
                            <input type="hidden" name="subjects[_ids][]" value="">

                                </div>

                                
                                <div class="col-sm-6">
                                  <?= $this->Form->control('fees._ids', ['options' => $fees, 'label' => 'Select Fees', 'empty' => 'Select Fees', 'class' => 'select2_multiple form-control form-control-user', 'required']) ?>

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
