<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Assign Courses To Departments</h1>
                        </div>
    <?= $this->Form->create($courseassignment) ?>
    <fieldset>
   <div class="form-group row">        
       <div class="col-sm-4 mb-3 mb-sm-0">
           
           <?= $this->Form->control('department_id', ['options' => $departments,'label'=>'Select Department','required','class'=>'select2_multiple form-control form-control-user'])?>
       </div>
       
        <div class="col-sm-4 mb-3 mb-sm-0">
           
           <?= $this->Form->control('semester_id', ['options' => $semesters,'label'=>'Select Semester','required','class'=>'form-control form-control-user'])?>
       </div>
       
       <div class="col-sm-4 mb-3 mb-sm-0">
           
           <?= $this->Form->control('level_id', ['options' => $levels,'label'=>'Select Level','required','class'=>'form-control form-control-user'])?>
       </div>
   </div>
        
         <div class="form-group row">        
             <div class="col-sm-12 mb-3 mb-sm-0">
                 <?= $this->Form->control('subjects._ids', ['options' => $subjects,'label'=>'Select Subjects','required','class'=>'select2_multiple form-control form-control-user'])?>
             </div>
        <?php
         
           
           
        ?>
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