<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">View/Update Applicant</h1>
                        </div>
                        <?= $this->Form->create($student,['type'=>'file']) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fname', ['label' => 'First Name', 'placeholder' => 'First Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('lname', ['label' => 'Last Name', 'placeholder' => 'Last Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">    
<?= $this->Form->control('mname', ['label' => 'Middle Name', 'placeholder' => 'Middle Name',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">  
<?= $this->Form->control('dob', ['label' => 'Date Of Birth', 'placeholder' => 'Date Of Birth',
      'class' => 'form-control form-control-user2','type' => 'text', 'id' => 'datepicker'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Department', 'empty' => 'Select Departments', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                </div>

                                <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('programe_id', ['label' => 'Prgrame','options'=>$programes, 'placeholder' => 'programe',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('jamb', ['label' => 'Jamb Score', 'placeholder' => 'Jamb Score',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>

                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('level_id', ['label' => 'Class', 'placeholder' => 'class','options'=>$levels,
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2', 'required', 'type' => 'email','disabled'])
                                    ?>
                                </div>
                                
                            </div>
                            <div class="form-group row">
                               
                                
                                  <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country', 'empty' => 'Select Country', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false]) ?>

                                </div>

                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', [ 'label' => 'Select State', 'options' => $states,'empty' => 'Select State', 'class' => 'form-control select2_multiple', 'multiple' => false,'default'=>$student->state->id]) ?>
                                </div>
 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('lga_id', ['label' => 'LGA', 'options'=>$lgas,'placeholder' => 'local government of origin',
                                          'class' => 'form-control form-control-user2'])
                                    ?>

                                </div>
                            </div>

                            <div class="form-group row">        
                                <div class="col-sm-8 mb-3 mb-sm-0">
<?= $this->Form->control('address', ['label' => 'Address', 'placeholder' => 'Address',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('phone', ['label' => 'Phone', 'placeholder' => 'Phone',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                            </div>
                        <div class="form-group row"> 
                            <div class="col-sm-8 mb-3 mb-sm-0">
                            <?= $this->Form->control('universitymail', ['label' => 'Assign University Email Address', 'placeholder' => 'School Email Address',
      'class' => 'form-control form-control-user2', 'required','type'=>'email'])
?>    
                            </div>  
                            
                          </div>    
                            

 
                        </fieldset>
                        <br /> <br />
<?= $this->Form->button('Update', ['class' => 'btn btn-primary btn-user float-right']) ?>
<?= $this->Form->end() ?>
                          <?= $this->Form->postLink(__('Delete'), ['action' => 'deleteapplicant', $student->id], 
    ['confirm' => __('Are you sure you want to delete # {0}?', $student->fname),'class' => 'btn btn-round btn-danger', 'title' => 'delete this applicant data'])?> 
             <br /> 
                    </div>
                     </div>
            </div>
        </div>
    </div>

</div>
