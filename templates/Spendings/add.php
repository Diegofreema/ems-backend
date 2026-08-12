<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">New Expenses</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
  
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Classes', ['controller' => 'Departments', 'action' => 'managedepartments'], ['title' => 'manage classes']) ?></li>
                    <li class="breadcrumb-item active">New Expenses</li>
                </ul>
            </div>
        </div>
    </div>
   <div class="card o-hidden border-0 shadow-lg my-5">
      
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">New Expenses</h1>
                        </div>
                         <?= $this->Form->create($spending) ?>
                        <div class="form-group row">
                        <div class="col-sm-12 mb-3 mb-sm-0">
                                    <?= $this->Form->control('amount', ['label' => "Amount", 'placeholder' => 'Amount', 'required',
                                        'class' => 'form-control form-control-user', 'id' => 'amount'])
                                    ?>
                                </div>
                        <div class="col-sm-12 mb-3 mb-sm-0">
                                    <?= $this->Form->control('description', ['label' => "Description", 'placeholder' => 'description', 'required',
                                        'class' => 'form-control form-control-user', 'id' => 'description'])
                                    ?>
                                </div>
                        
                        </div>
                
            </fieldset>
         <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
        </div>
    </div>
</div>
