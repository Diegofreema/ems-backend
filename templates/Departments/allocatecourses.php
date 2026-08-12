<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Course Allocation</h1>
                        </div>
    <?= $this->Form->create($department) ?>
          <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                        <?= $this->Form->control('faculty_id', ['options' => $faculties, 'label' => 'Faculty', 'empty'=>'Select faculty','placeholder' => 'Description', 'required',
                                            'class' => 'form-control'])
                                        ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('name', ['label' => 'Department Name', 'placeholder' => 'Department Name', 'required',
                                        'class' => 'form-control form-control-user'])
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <?= $this->Form->control('description', ['label' => 'Description', 'placeholder' => 'Description',
                                        'class' => 'form-control form-control-user'])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('deptcode', ['label' => 'Department Code', 'placeholder' => 'Department Code', 'required',
                                        'class' => 'form-control form-control-user'])
                                    ?>
                                </div>
                            </div>
              <div class="form-group row">
                  <div class="col-sm-6">
                  <?= $this->Form->control('levels._ids', ['options' => $levels, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user']) ?> 
     
                  </div>
                  
                  <div class="col-sm-6">
                  <?= $this->Form->control('semesters._ids', ['options' => $semesters, 'label' => 'Select Semester', 'empty' => 'Select Semester', 'class' => 'select2_multiple form-control form-control-user']) ?> 
     
                  </div>
               </div>
              
              
              
                            <div class="form-group row">
                                <div class="col-sm-6">
                            <?= $this->Form->control('subjects._ids', ['options' => $subjects, 'label' => 'Assign Subjects', 'empty' => 'Select Subjects', 'class' => 'select2_multiple form-control form-control-user']) ?> 

                                </div>
                                <div class="col-sm-6">
                                  <?= $this->Form->control('fees._ids', ['options' => $fees, 'label' => 'Select Fees', 'empty' => 'Select Fees', 'class' => 'select2_multiple form-control form-control-user', 'required']) ?>

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
