<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update Student</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Students', ['controller' => 'Students', 'action' => 'managestudents'], ['title' => 'Manage Students']) ?></li>
                    <li class="breadcrumb-item active">Update Student</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <!-- script for the webcam-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Student Data</h1>
                        </div>
                        <?= $this->Form->create($student, ['type' => 'file', 'novalidate' => true]) ?>
                      
                        <!-- Personal Details Section -->
                        <fieldset><legend>Personal Details</legend>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fname', ['label' => 'Surname', 'placeholder' => 'Surname', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('lname', ['label' => 'First Name', 'placeholder' => 'First Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">    
                                    <?= $this->Form->control('mname', ['label' => 'Other Names', 'placeholder' => 'Other Names',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-3 mb-3 mb-sm-0">
                                    <?php $gender = ['Male'=>'Male', 'Female'=>'Female'];
                                    echo $this->Form->control('gender', ['label' => 'Gender', 'placeholder' => 'Gender',
                                        'class' => 'form-control form-control-user2', 'options' => $gender])
                                    ?>      
                                </div>
                                <div class="col-sm-3 mb-3 mb-sm-0">
                                    <?= $this->Form->control('dob', ['label' => 'Date Of Birth', 'placeholder' => 'Date Of Birth',
                                        'class'=>'form-control floating datetimepicker','type'=>'text', 'id' => 'datepicker'])
                                    ?>
                                </div>
                                <div class="col-sm-3 mb-3 mb-sm-0">
                                    <?= $this->Form->control('phone', ['label' => 'Phone', 'placeholder' => 'Phone',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-3 mb-3 mb-sm-0">
                                    <?= $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2', 'type' => 'email', 'readonly'])
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'form-control form-control-user', 'onChange' => 'getClassArms(this.value)']) ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0" id="classArms">
                                    <label for="class_arm_id">Select Class Arm</label>
                                    <select name="class_arm_id" class="form-control form-control-user">
                                        <option value="">Select Class Arm</option>
                                    </select>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('religion', ['label' => 'Religion', 'placeholder' => 'Religion',
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
                                    <?= $this->Form->control('community', ['label' => 'Autonomous Community', 'placeholder' => 'Autonomous Community',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-12 mb-3 mb-sm-0">
                                    <?= $this->Form->control('pschools', ['label' => 'Previous Schools Attended With Date', 'placeholder' => 'Previous Schools Attended With Date',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Location Information -->
                        <fieldset><legend>Location Information</legend>
                            <div class="form-group row"> 
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country','default'=>160, 'empty' => 'Select Country', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'onChange'=>'getstates(this.value)']) ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0" id="states1">
                                    <?= $this->Form->control('state_id', ['options' => $states, 'label' => 'Select State', 'empty' => 'Select State', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'id'=>'states1','onChange'=>'getlgas(this.value)']) ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0" id="lga">
                                    <?= $this->Form->control('lga_id', ['label' => 'Local Government Area', 'options' => $lgas,
                                          'class' => 'select2_multiple form-control form-control-user2', 'required','empty'=>'Select LGA','id'=>'lga'])
                                    ?>
                                </div> 
                            </div>
                        </fieldset>
                        
                        <!-- Documents Section -->
                        <fieldset><legend>Required Documents</legend>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <legend class="h6">Student Passport Photo</legend>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-info btn-sm mb-2" onClick="startCamera()" id="startCameraBtn">
                                            <i class="fa fa-camera"></i> Start Camera
                                        </button>
                                        <div id="my_camera" class="border rounded mx-auto" style="width: 200px; height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                            <div class="text-center">
                                                <i class="fa fa-camera fa-2x mb-2"></i>
                                                <div>Click "Start Camera" to begin</div>
                                            </div>
                                        </div>
                                        <br/>
                                        <button type="button" class="btn btn-primary btn-sm" onClick="take_snapshot()" id="snapbtn" style="display: none;">
                                            <i class="fa fa-camera"></i> Take Snapshot
                                        </button>
                                        <input type="hidden" name="passport_data" id="passport_data_field" value="">
                                        
                                        <hr class="my-3">
                                        <div class="text-center">
                                            <strong>OR</strong>
                                        </div>
                                        <hr class="my-3">
                                        <label class="btn btn-outline-secondary btn-sm">
                                            <i class="fa fa-upload"></i> Upload Photo File
                                            <input type="file" name="passport_file" accept="image/*" style="display: none;" onchange="handleFileUpload(this)">
                                        </label>
                                        <div class="mt-2">
                                            <small class="text-muted">Accepted formats: JPG, JPEG, PNG (Max: 5MB)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <legend class="h6">Birth Certificate</legend>
                                    <?= $this->Form->control('birthcerturls', ['label' => 'Birth Certificate',
                                          'class' => 'form-control form-control-user2', 'type' => 'file', 'accept' => '.pdf,.jpg,.jpeg,.png'])
                                    ?>
                                    <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
                                </div>
                            </div>
                        </fieldset>
                        
                        <!-- Parental Information -->
                        <fieldset><legend>Parental Information</legend>
                            <?php if ($hasParent): ?>
                                <!-- Student has existing parent - show current parent info and allow reassignment -->
                                <div class="alert alert-info">
                                    <strong>Current Parent:</strong> <?= h($student->sparent->fathersname) ?> 
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p><strong>Father's Name:</strong> <?= h($student->sparent->fathersname) ?></p>
                                                <p><strong>Father's Phone:</strong> <?= h($student->sparent->fatherphone) ?></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p><strong>Mother's Name:</strong> <?= h($student->sparent->mothersname) ?></p>
                                                <p><strong>Mother's Phone:</strong> <?= h($student->sparent->motherphone) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">        
                                   <div class="col-sm-6 mb-3 mb-sm-0"> 
                                        <?= $this->Form->control('sparent_id', ['options' => $parents, 'label' => 'Reassign to Different Parent', 'empty' => 'Keep Current Parent', 'class' => 'select2_multiple form-control form-control-user', 'value' => $student->sparent_id]) ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Student doesn't have parent - allow assignment -->
                                <div class="form-group row">        
                                   <div class="col-sm-6 mb-3 mb-sm-0"> 
                                        <?= $this->Form->control('sparent_id', ['options' => $parents, 'label' => 'Select Parent', 'empty' => 'Select Parent', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </fieldset>
                        <br /> <br />
<?= $this->Form->button('Update Student', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    
        function getstates(stateid){ 

    $.ajax({
        url: '../../../Students/getstates/'+stateid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('states1').innerHTML = "";
            document.getElementById('states1').innerHTML = response;
            //location.href = redirect;
        }
    });

}



 function getdepts(facultyid){ 

    $.ajax({
        url: '../../../Students/getdapts/'+facultyid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('depts').innerHTML = "";
            document.getElementById('depts').innerHTML = response;
            //location.href = redirect;
        }
    });

}



function getlgas(stateid){ 

    $.ajax({
        url: '../../../Students/getlgas/'+stateid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('lga').innerHTML = "";
            document.getElementById('lga').innerHTML = response;
            //location.href = redirect;
        }
    });

}

function getClassArms(departmentid){ 

    $.ajax({
        url: '../../../ClassArms/getArmsForDepartment/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            // Preserve the label and only replace the select element
            var label = '<label for="class_arm_id">Select Class Arm</label>';
            document.getElementById('classArms').innerHTML = label + response;
        }
    });

}

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Size in MB
        
        // Show file selected message
        alert('File selected: ' + fileName + ' (' + fileSize + 'MB)');
    }
}

    </script>
    
    
    <script language="JavaScript">
    Webcam.set({
        width: 200,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    // Don't auto-initialize webcam - let user start it manually
    
    function startCamera() {
        Webcam.attach('#my_camera');
        document.getElementById('startCameraBtn').style.display = 'none';
        document.getElementById('snapbtn').style.display = 'inline-block';
    }
    
    function take_snapshot() {   
        Webcam.snap( function(data_uri) { 
            // Set the value of the hidden input field
            document.getElementById('passport_data_field').value = data_uri;
            // Show captured image in the same camera div
            document.getElementById('my_camera').innerHTML = '<img src="'+data_uri+'" style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 4px;"/>';
            $("#snapbtn").hide();
        } );
    }
</script>
