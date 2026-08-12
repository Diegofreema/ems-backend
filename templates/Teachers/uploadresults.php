<div class="content container-fluid">

    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Bulk Result Upload</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Bulk Result Upload</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
    
    <div class="row">
        <!-- Form Section - Left Side -->
        <div class="col-lg-8">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="text-center">
                            <?= $this->Html->link(__(' '), ['controller'=>'Results','action' => 'downloadformat'],
                                    ['class'=>'btn-circle btn-lg fa fa-download float-right','title'=>'Download Excel Format']) ?>
                            <h1 class="h4 text-gray-900 mb-4">Upload Student Results</h1>
                        </div>
                        
                        <?php if (empty($subjects)): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>No Subjects Assigned</strong><br>
                                You don't have any subjects assigned to your account yet. Please contact the school administration to assign subjects to your teacher account before uploading results.
                                <br><br>
                                <?= $this->Html->link('Back to Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['class' => 'btn btn-secondary']) ?>
                            </div>
                        <?php else: ?>
                            <?= $this->Form->create(null,['type'=>'file']) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('department_id', ['options' => $departments, 'required', 'label' =>'Select Class',
                                        'empty' => 'Select Class', 'class' => 'form-control', 'onChange' => 'getClassArms(this.value)'])
                                    ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0" id="classArms">
                                    <label for="class_arm_id">Select Class Arm</label>
                                    <select name="class_arm_id" class="form-control">
                                        <option value="">Select Class Arm</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('subject_id', ['options' => $subjects,'label' => 'Select Subject', 'required', 'placeholder' => 'Select Subject'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('semester_id', ['options' => $semesters,'label' => 'Select Term', 'required', 'placeholder' => 'Select Term'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>  
                            </div>
                                
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('session_id', ['options' => $sessions,'label' => 'Select Session', 'required', 'placeholder' => 'Select Session'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('result',['label'=>'Upload Excel File','type'=>'file','required','class'=>'form-control','accept'=>'.xlsx,.csv'])?>
                                </div>  
                            </div>
                        </fieldset>
                        
                        <div class="form-group">
                            <?= $this->Form->button('Upload Results', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        </div>
                        <?= $this->Form->end() ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Instructions Section - Right Side -->
        <div class="col-lg-4">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="p-5">
                        <h5 class="text-primary mb-4"><i class="fa fa-info-circle"></i> Upload Instructions</h5>
                        
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle"></i> Important:</strong> All uploaded results require admin approval before students can view them.
                        </div>
                        
                        <h6 class="text-dark mb-3"><i class="fa fa-list-ol"></i> How to Upload:</h6>
                        <ol class="mb-4">
                            <li>Select <strong>Class, Class Arm, Subject, Term, and Session</strong> from the form</li>
                            <li>Download the Excel format template using the download button</li>
                            <li>Fill the Excel file with student results</li>
                            <li>Upload the completed Excel file</li>
                        </ol>
                        
                        <h6 class="text-dark mb-3"><i class="fa fa-table"></i> Excel Format:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Column</th>
                                        <th>Field</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>A</strong></td>
                                        <td>Registration Number</td>
                                    </tr>
                                    <tr>
                                        <td><strong>B</strong></td>
                                        <td>CA Score</td>
                                    </tr>
                                    <tr>
                                        <td><strong>C</strong></td>
                                        <td>1st Exam</td>
                                    </tr>
                                    <tr>
                                        <td><strong>D</strong></td>
                                        <td>2nd Exam</td>
                                    </tr>
                                    <tr>
                                        <td><strong>E</strong></td>
                                        <td>3rd Exam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <small><i class="fa fa-lightbulb-o"></i> <strong>Tip:</strong> The system will automatically calculate totals and grades based on your scores.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    
        function getdepartments(facultyid){ 

    $.ajax({
        url: '../Results/getdepartments/'+facultyid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('dept1').innerHTML = "";
            document.getElementById('dept1').innerHTML = response;
            //location.href = redirect;
        }
    });

}

function getClassArms(departmentid){ 
    $.ajax({
        url: '../ClassArms/getArmsForDepartment/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            // Preserve the label and only replace the select element
            var label = '<label for="class_arm_id">Select Class Arm</label>';
            document.getElementById('classArms').innerHTML = label + response;
        }
    });
}
    </script>
