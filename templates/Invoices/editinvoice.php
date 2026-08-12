<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Edit Invoice</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
          <li class="breadcrumb-item"><?= $this->Html->link('Manage Invoices', ['controller' => 'Invoices', 'action' => 'index'], ['title' => 'Manage Invoices']) ?></li>
          <li class="breadcrumb-item active">Edit Invoice</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Student Payment Update</h1>
                        </div>
    <?= $this->Form->create($invoice) ?>
    <fieldset>
        <legend><?= __('Edit Invoice') ?></legend>
        
         <div class="form-group row">
             <div class="col-sm-4 mb-3 mb-sm-0">
                 <?=$this->Form->control('fee_id', ['label'=>'Select Fee','options' => $fees,'class'=>'form-control'])?>
             </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
                 <?=$this->Form->control('student_id', ['label'=>'Select Student','options' => $students,'class'=>'form-control'])?>
             </div>
            
             <div class="col-sm-4 mb-3 mb-sm-0">
                 <?=$this->Form->control('amount',['label'=>'Fee Amount','class'=>'form-control'])?>
             </div>
        </div>
        
          <div class="form-group row">
               <div class="col-sm-4 mb-3 mb-sm-0">
                 <?=$this->Form->control('session_id', ['label'=>'Select Session','options' => $sessions,'class'=>'form-control'])?>
             </div>
              <div class="col-sm-4 mb-3 mb-sm-0">
                  <?php
           
            echo $this->Form->control('paystatus',['label'=>'Status','class'=>'form-control','disabled'])?>
  
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