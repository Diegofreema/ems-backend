<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Mode of Admission</h1>
                        </div>
            <?= $this->Form->create($mode) ?>
            <fieldset>
                  <div class="col-sm-6 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('name',['required','label'=>false,'placeholder'=>' Name','class' => 'form-control form-control-user']);
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
