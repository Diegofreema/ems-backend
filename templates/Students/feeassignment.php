<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assign Fee To Student</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Assign Fee', ['controller' => 'Students', 'action' => 'assignfee'], ['title' => 'assign fee']) ?></li>
                    <li class="breadcrumb-item active">Assign Fee To Student</li>
                </ul>
            </div>
        </div>
    </div>
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Assign Fee To Student </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">

                    <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('fee_id', ['options' => $fees, 'label' => 'Select Fee', 'empty' => 'Select Fee', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>
                    
                    <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('session_id', ['options' => $sessions, 'label' => 'Select Session', 'empty' => 'Select Session', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>

                </div>
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Assign', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        </div>
</div></div>