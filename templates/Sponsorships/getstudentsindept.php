  <div class="col-sm-12 mb-6 mb-sm-0"><br />
                 <?=$this->Form->control('students._ids', ['label'=>'Select Students','options' => $students, 'class' => 'select2_multiple form-control form-control-user',
                     'required','id'=>'students']);
                ?>
                   </div>