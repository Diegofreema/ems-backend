<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update Fee</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Fees', ['controller' => 'Fees', 'action' => 'managefees'], ['title' => 'manage fees']) ?></li>
                    <li class="breadcrumb-item active">Update Fee</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Update Fee</h1>
                        </div>
                        <?= $this->Form->create($fee) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                        <?= $this->Form->control('name', [ 'label' => 'Fee Name','placeholder' => 'Fee Name', 'required',
                                            'class' => 'form-control form-control-user2'])
                                        ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('amount', ['label' => 'Amount', 'placeholder' => 'Amount', 'required',
                                        'class' => 'form-control form-control-user'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                <?= $this->Form->control('departments._ids', ['options' => $departments,'label'=>'Select Class','empty'=>'Select Class','class'=>'select2_multiple form-control form-control-user'])?>
                                <input type="hidden" name="levels[_ids][]" value="">
                                    </div>
                            </div>
                             <div class="form-group row">
                                <!-- <div class="col-sm-4 mb-3 mb-sm-0">
              <?= $this->Form->control('levels._ids', ['label' =>false, 'options' => $levels,'class' => 'select2_multiple form-control form-control-user'])?>
                    </div> -->
                           
                                  <div class="col-sm-4 mb-3 mb-sm-0">
              <?php $feetypes = ['enrolled'=>'Enrolled','none_enrolled'=>'None Enrolled'];
                echo $this->Form->control('feetype', ['options'=>$feetypes,'label' => 'Fee Type', 'class' => 'form-control form-control-user2', 'placeholder'=>'fee type'])?>
                
</div>
<div class="col-sm-4 mb-3 mb-sm-0">
              <?= $this->Form->control('itemcode', ['label' => 'Item Code', 'class' => 'form-control form-control-user2',  'placeholder'=>'Credo item code'])?>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
              <?= $this->Form->control('remitaitemcode', ['label' => 'Remita Item Code', 'class' => 'form-control form-control-user2',  'placeholder'=>'Remita item code'])?>
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

