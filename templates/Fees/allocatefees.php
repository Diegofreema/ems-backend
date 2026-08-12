<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Allocate Fees To Departments</h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
      <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
        <?=$this->Form->control('fee_id', ['options' => $fees,'label'=>'Select Fee','empty'=>'Select Fee','class'=>'select2_multiple form-control form-control-user2','multiple'=>false])?>
        </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
            <?= $this->Form->control('department._ids', ['options' => $departments,'label'=>'Select Departments','empty'=>'Select Departments','class'=>'select2_multiple form-control form-control-user2']);
        ?>
                                        </div>
                            </div>
     <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
