<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Verify Bank Payment</h1>
                        </div>
                        <?= $this->Form->create(null)?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('rrr', ['label' => 'TRANSACTION REF', 'placeholder' => 'Enter transaction ref', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                             
                            </div>


                        </fieldset>
                        <br /> <br />
<?= $this->Form->button('Verify', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


