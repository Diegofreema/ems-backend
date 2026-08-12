  <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
                <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Time Table </h1>
                        </div>
      <?= $this->Form->create($timetable) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('session_id',['label' => 'Session', 
     'class'=>'form-control floating', 'options' => $sessions]);
            
            ?>
        </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('department_id',['label' => 'Select Department',
     'class'=>'form-control floating', 'options' => $departments,'required']);
            
            ?>
        </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('semester_id', ['options' => $semesters, 'label' => 'Select Semester', 'empty' => 'Select Semester', 'class' => 'form-control form-control-user']) ?>
                                </div>
       
            </div>
         <div class="form-group row">
        <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('level_id', ['options' => $levels, 'label' => 'Select Level', 'empty' => 'Select Level', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>
                
         </div>
        
        <div class="col-sm-12 mb-6 mb-sm-0">
<?= $this->Form->control('timetable', [ 'label' => 'Time Table', 'type' => 'textarea', 'class'=>'ckeditor']) ?>
                    </div>
    </fieldset>
   <br /> <br />
                    <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
                
                
   
</div>

