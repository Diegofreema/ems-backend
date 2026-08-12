<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update Teacher</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Teachers', ['controller' => 'Teachers', 'action' => 'manageteachers'], ['title' => 'Teachers']) ?></li>
                    <li class="breadcrumb-item active">Update Teacher</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">New Teacher</h1>
                        </div>
                        <?= $this->Form->create($teacher,['type'=>'file']) ?>
                        <fieldset>
                          <div class="form-group row">
                              <div class="col-sm-6 mb-3 mb-sm-0">
                                  <?=
                                      $this->Form->control('firstname', ['label' =>false, 'required', 
                                           'placeholder' => 'first name', 'class' => 'form-control user2'])
                                    ?> 
                              </div>  
                            
                              <div class="col-sm-6 mb-3 mb-sm-0">
                                  <?=
                                      $this->Form->control('lastname', ['label' =>false, 'required', 
                                           'placeholder' => 'last name', 'class' => 'form-control user2'])
                                    ?> 
                              </div> 
                          </div>
                             <div class="form-group row">
                                 <div class="col-sm-6 mb-3 mb-sm-0">
                                     <?=
                                      $this->Form->control('middlename', ['label' =>false, 
                                           'placeholder' => 'middle name', 'class' => 'form-control user2'])
                                    ?>  
                                 </div>
                              <div class="col-sm-6 mb-3 mb-sm-0">
                                  <?php
                                   
                                      echo $this->Form->control('department_id', ['options' =>  $departments , 'label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'Select department', 'empty' => 'Select Class (Optional)', 'onchange' => 'getClassArms(this.value, ' . ($currentClassArmId ?? 'null') . ')'])
                                    ?>
                           </div>
                             </div>
                             <div class="form-group row">
                                 <div class="col-sm-12 mb-3 mb-sm-0">
                                     <label for="class_arm_id">Class Arm Assignment (Optional)</label>
                                     <div id="classArms">
                                         <select name="class_arm_id" class="form-control form-control-user2">
                                             <option value="">No Class Arm Assignment</option>
                                         </select>
                                     </div>
                                     <small class="form-text text-muted">Select a class arm to assign this teacher as a form teacher. Leave empty if this teacher is only a subject teacher.</small>
                                 </div>
                             </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('username', ['label' =>false, 'required', 
                                           'placeholder' => 'username/email address','disabled', 'class' => 'form-control user2', 'value' => $teacher->user->username])
                                    ?>
                                </div>

                                <div class="col-sm-6">
                                    <?php
                                      $gender = ['Male' => 'Male', 'Female' => 'Female'];
                                      echo $this->Form->control('gender', ['options' => $gender, 'label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'gender'])
                                    ?>

                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-8 mb-3 mb-sm-0">

<?= $this->Form->control('address', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'address', 'required']) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('qualification', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'Highest Qualification']); ?>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
<?= $this->Form->control('country_id', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'country', 'empty' => 'Select Country','onChange'=>'getstates(this.value)']) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', ['label' => false, 'id'=>'states1','class' => 'form-control form-control-user2', 'placeholder' => 'state', 'empty' => 'Select State']); ?>

                                </div>

                                <div class="col-sm-4">
<?= $this->Form->control('phone', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'phone']) ?>

                                </div>
                                
                            </div>
                            <div class="form-group row">


                                <div class="col-sm-4">
<?= $this->Form->control('cv', ['label' => 'Upload Your CV', 'type' => 'file', 'class' => 'form-control form-control-user2', 'placeholder' => 'Upload CV']); ?>

                                </div>
                                <div class="col-sm-4">
                                    <?= $this->Form->control('passport', ['label' => 'Passport', 'type' => 'file', 'class' => 'form-control form-control-user2', 'placeholder' => 'Upload passport']); ?>
                                </div>
                                <div class="col-sm-4">
<label for="subjects-ids">Assign Courses</label>
<select name="subjects[_ids][]" multiple="multiple" class="form-control select2_multiple" id="subjects-ids">
    <?php 
    // Get the teacher's currently assigned subject IDs
    $assignedSubjectIds = [];
    if (!empty($teacher->subjects)) {
        foreach ($teacher->subjects as $assignedSubject) {
            $assignedSubjectIds[] = $assignedSubject->id;
        }
    }
    
    foreach ($subjects as $id => $subject): 
        $isSelected = in_array($id, $assignedSubjectIds) ? 'selected' : '';
    ?>
        <option value="<?= h($id) ?>" <?= $isSelected ?>><?= h($subject) ?></option>
    <?php endforeach; ?>
</select>
<small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple subjects.</small>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 mb-3 mb-sm-0">
<?= $this->Form->control('profile', ['label' => false, 'rows' => 6, 'colunm' => 6, 'required', 'class' => 'form-control form-control-user2', 'placeholder' => 'Profile']) ?>
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
<script>
    
        function getstates(stateid){ 

    $.ajax({
        url: '../Teachers/getstates/'+stateid,
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

        function getClassArms(departmentid, currentClassArmId){ 
            console.log('getClassArms called with departmentid:', departmentid, 'currentClassArmId:', currentClassArmId);
            
            if (!departmentid) {
                console.log('No department ID provided');
                return;
            }
            
            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'ClassArms', 'action' => 'getArmsForDepartment']) ?>/'+departmentid,
                method: 'GET',
                dataType: 'text',
                success: function(response) {
                    console.log('AJAX success, response:', response);
                    // Only replace the select element, keep the label and help text
                    document.getElementById('classArms').innerHTML = response;
                    
                    // If there's a current class arm ID and it belongs to the selected department, select it
                    if (currentClassArmId && currentClassArmId !== null) {
                        var selectElement = document.querySelector('#classArms select[name="class_arm_id"]');
                        if (selectElement) {
                            // Check if the current class arm ID exists in the new options
                            var optionExists = false;
                            for (var i = 0; i < selectElement.options.length; i++) {
                                if (selectElement.options[i].value == currentClassArmId) {
                                    selectElement.options[i].selected = true;
                                    optionExists = true;
                                    break;
                                }
                            }
                            // If the current class arm doesn't belong to the selected department, clear selection
                            if (!optionExists) {
                                selectElement.selectedIndex = 0; // Select "No Class Arm Assignment"
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    console.log('Response:', xhr.responseText);
                }
            });
        }
        
        // Load class arms for the current department on page load
        $(document).ready(function() {
            var currentDepartmentId = $('select[name="department_id"]').val();
            console.log('Page loaded, currentDepartmentId:', currentDepartmentId);
            console.log('Current class arm ID:', <?= $currentClassArmId ?? 'null' ?>);
            if (currentDepartmentId) {
                getClassArms(currentDepartmentId, <?= $currentClassArmId ?? 'null' ?>);
            } else {
                console.log('No department selected on page load');
            }
        });
    </script>