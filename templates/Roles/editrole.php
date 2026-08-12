<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Update Role</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
          <li class="breadcrumb-item"><?= $this->Html->link('Roles', ['controller' => 'Roles', 'action' => 'manageroles'], ['title' => 'Roles']) ?></li>
          <li class="breadcrumb-item active">Edit Role</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Update Role</h1>
                        </div>
    <?= $this->Form->create($role) ?>
    <fieldset>
        <div class="col-sm-6 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('role_name',['required','label'=>false,'placeholder'=>'Role Name','class' => 'form-control form-control-user']);
        ?>
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
