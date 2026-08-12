<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                  
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Add Student CA/Exam Records</h1>
                             <?= $student->regno ?><br /><br />
                        </div>
                        <?= $this->Form->create(null) ?>
                        <fieldset>
                      
                         
                         
                         
                            <div class="form-group row">
                                <div class="col-sm-4">
<?= $this->Form->control('ca', ['label' => "Continouse Assessment", 'class' => 'form-control form-control-user2', 'required','min'=>1, 'max'=>40]) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('exam', ['label' => "Exam Score", 'id'=>'examscore','class' => 'form-control form-control-user2','min'=>1, 'max'=>60]); ?>

                                </div>

                              <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('creditload', ['label' => "Credit Unit", 'id'=>'states1','class' => 'form-control form-control-user2', 'required']); ?>

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