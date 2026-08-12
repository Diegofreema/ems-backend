<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="donotprint" style="padding-bottom: 10px; margin-bottom: 20px;">
        <!--<?= $this->Html->link(__(' '), ['action' => 'newresult'],
        ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'add student result'])
?>
        -->
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Manage Results</h1></div>
    <div class="col-lg-12 donotprint">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Result</h1>
            </div>
                        <?= $this->Form->create(null) ?>
            <fieldset>

                <div class="form-group row">
                   
                    <div class="col-sm-4 mb-3 mb-sm-0">
<?=
$this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Department',
    'empty' => 'Select Department', 'class' => 'select2_multiple form-control', 'id' => 'dept1', 'onChange' => 'getstudents(this.value)'])
?>
                    </div>

                    <div class="col-sm-4">
                        <?=
                        $this->Form->control('subject_id', ['options' => $subjects, 'label' => 'Select Course', 'empty' => 'Select Course'
                            , 'class' => 'select2_multiple form-control'])
                        ?>
                    </div>
                     <div class="col-sm-4">
                        <?=
                        $this->Form->control('semester_id', ['options' => $semesters, 'label' => 'Select Semester', 'empty' => 'Select Semester', 'placeholder' => 'Select Semester'
                            , 'class' => 'form-control'])
                        ?>
                    </div> 
                </div>

                <div class="form-group row">
                    
                    <div class="col-sm-4">
            <?=
            $this->Form->control('session_id', ['options' => $sessions, 'label' => 'Select Session', 'empty' => 'Select Session', 'placeholder' => 'Select Session'
                , 'class' => 'form-control','required'])
            ?>
                    </div>
                    
                      <div class="col-sm-4">
<?=
$this->Form->control('level_id', ['options' => $levels, 'label' => 'Select Class', 'empty' => 'Select Class', 'placeholder' => 'Select Class'
    , 'class' => 'form-control'])
?>
                    </div>
                    
                    <div class="col-sm-4">
<?=
$this->Form->control('unit', [ 'label' => 'Credit Unit',  'placeholder' => 'enter unit'
    , 'class' => 'form-control'])
?>
                    </div>
                  
                </div>
              
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Update Result', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>

        </div>
        <br /> <br />

    </div>
    
      </div>