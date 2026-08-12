<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Election Position </h1>
                        </div>
    <?= $this->Form->create($position) ?>
    <fieldset>
       <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('name',['label'=>'Name','class' => 'form-control form-control-user','required','placeholder'=>'position'])?>
                                </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('votingstarts',['label'=>'Voting Starts','class' => 'form-control form-control-user','required','placeholder'=>'voting start time','type'=>'datetime'])?>
                                </div>
           
            <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('votingends',['label'=>'Voting Ends','class' => 'form-control form-control-user','required','placeholder'=>'voting end time','type'=>'datetime'])?>
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


