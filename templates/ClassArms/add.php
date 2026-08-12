<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">New Class Arm</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Class Arms', ['controller' => 'ClassArms', 'action' => 'index'], ['title' => 'manage class arms']) ?></li>
                    <li class="breadcrumb-item active">New Class Arm</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create New Class Arm</h1>
                        </div>
                        <?= $this->Form->create($classArm) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('department_id', [
                                        'options' => $departments, 
                                        'label' => 'Class', 
                                        'empty' => 'Select Class',
                                        'class' => 'form-control form-control-user',
                                        'required' => true
                                    ]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('arm_name', [
                                        'label' => 'Arm Name', 
                                        'placeholder' => 'e.g., A, B, C, D...', 
                                        'class' => 'form-control form-control-user',
                                        'required' => true,
                                        'maxlength' => 10
                                    ]) ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('class_teacher_id', [
                                        'options' => $teachers, 
                                        'label' => 'Class Teacher', 
                                        'empty' => 'Select Class Teacher (Optional)',
                                        'class' => 'form-control form-control-user'
                                    ]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('arm_description', [
                                        'label' => 'Description', 
                                        'placeholder' => 'Optional description...', 
                                        'class' => 'form-control form-control-user',
                                        'type' => 'textarea',
                                        'rows' => 3
                                    ]) ?>
                                </div>
                            </div>
                        </fieldset>
                        <br />
                        <?= $this->Form->button('Create Class Arm', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
