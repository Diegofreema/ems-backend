 <div class="container-fluid">
<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Result</h1>
                        </div>
    <?= $this->Form->create($result) ?>
    <fieldset>
        
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                <?= $this->Form->control('faculty_id', ['options' => $faculties, 'required', 'label' => 'Select Faculty',
                                        'placeholder' => 'Faculty', 'class' => 'form-control','onChange'=>'getdepartments(this.value)'])
                                    ?>     
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('department_id', ['options' => $departments, 'required', 'label' =>'Select Department',
                                        'placeholder' => 'Select Department', 'class' => 'form-control','id'=>'dept1'])
                                    ?>
                                </div>

                                <div class="col-sm-4">
                                    <?= $this->Form->control('subject_id', ['options' => $subjects,'label' => 'Select Course', 'required', 'placeholder' => 'Select Course'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                            </div>
                                
                            <div class="form-group row">
                           <div class="col-sm-4">
                                    <?= $this->Form->control('semester_id', ['options' => $semesters,'label' => 'Select Semester', 'required', 'placeholder' => 'Select Semester'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>  
                                <div class="col-sm-4">
                                    <?= $this->Form->control('session_id', ['options' => $sessions,'label' => 'Select Session', 'required', 'placeholder' => 'Select Session'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                                   <div class="col-sm-4">
                                 <?= $this->Form->control('student_id',['options' => $students,'label'=>'Student','disabled','class'=>'form-control'])?>
                                   </div>             
                                </div>
        
        <div class="form-group row">
            <div class="col-sm-4 mb-3 mb-sm-0">
                <?=$this->Form->control('ca',['label'=>'CA','required','class'=>'form-control','required'])?>
                                   </div>  
             <div class="col-sm-4 mb-3 mb-sm-0">
                <?=$this->Form->control('score',['label'=>'Exam Score','class'=>'form-control','required'])?>
                                   </div>  
            
            <div class="col-sm-4 mb-3 mb-sm-0">
                <?=$this->Form->control('grade',['label'=>'Grade','required','class'=>'form-control','required'])?>
                                   </div>  
            
           
             </div>
        
        <div class="form-group row">
             <div class="col-sm-4 mb-3 mb-sm-0">
                <?=$this->Form->control('remark',['label'=>'Remark','class'=>'form-control'])?>
                                   </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
               <?=$this->Form->control('regno',['label'=>'Regno','required','class'=>'form-control','disabled'])?>  
            </div>
            
             <div class="col-sm-4 mb-3 mb-sm-0">
               <?=$this->Form->control('creditload',['label'=>'Credit Load','required','class'=>'form-control'])?>  
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
                <?php $coptions = ['yes'=>'yes','no'=>'no']; ?>
               <?=$this->Form->control('iscarryover',['label'=>'Is Carry Over?','required','class'=>'form-control','options'=>$coptions])?>  
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
               
               <?=$this->Form->control('level_id',['label'=>'Select Level?','required','class'=>'form-control','options'=>$levels])?>  
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

</div></div>
