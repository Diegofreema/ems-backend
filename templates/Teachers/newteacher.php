<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Add New Teacher</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Teachers', ['controller' => 'Teachers', 'action' => 'manageteachers', $this->GenerateUrl('Manage Teachers')], ['title' => 'Manage Teachers'])
                            ?></li>
                        <li class="breadcrumb-item active">Add New Teacher</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Add New Teacher</h1>
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
                                   
                                      echo $this->Form->control('department_id', ['options' =>  $departments , 'label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'Select department', 'empty' => 'Select Class', 'onchange' => 'getClassArms(this.value)'])
                                    ?>
                           </div>
                             </div>
                             <div class="form-group row">
                                 <div class="col-sm-12 mb-3 mb-sm-0">
                                     <!-- <label for="class_arm_id">Class Arm Assignment (Optional)</label> -->
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
                                           'placeholder' => 'username/email address', 'class' => 'form-control user2'])
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
<?= $this->Form->control('country_id', ['label' => false, 'class' => 'form-control form-control-user2','default'=>160, 'placeholder' => 'country', 'empty' => 'Select Country','onChange'=>'getstates(this.value)','options'=>$countries]) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', ['label' => false, 'id'=>'states1','class' => 'form-control form-control-user2', 'placeholder' => 'state', 'empty' => 'Select State','options'=>$states,'required']); ?>

                                </div>

                                <div class="col-sm-4">
<?= $this->Form->control('phone', ['label' => false, 'class' => 'form-control form-control-user2', 'placeholder' => 'phone']) ?>

                                </div>
                                
                            </div>
                            <div class="form-group row">


                                <div class="col-sm-4">
<?= $this->Form->control('cvv', ['label' => 'Upload Your CV', 'type' => 'file', 'class' => 'form-control form-control-user2', 'placeholder' => 'Upload CV']); ?>

                                </div>
                                <div class="col-sm-4">
                                    <?= $this->Form->control('passports', ['label' => 'Passport', 'type' => 'file', 'class' => 'form-control form-control-user2', 'placeholder' => 'Uplaod passport']); ?>
                                </div>
<!--                                <div class="col-sm-4">
<?php //$this->Form->control('subjects._ids', ['label' => 'Assign Courses', 'options' => $subjects, 'class' => 'form-control select2_multiple', 'empty' => 'Select Courses']);
?>
                                </div>-->

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

        function getClassArms(departmentid){ 
            $.ajax({
                url: '../ClassArms/getArmsForDepartment/'+departmentid,
                method: 'GET',
                dataType: 'text',
                success: function(response) {
                    // Only replace the select element, keep the label and help text
                    document.getElementById('classArms').innerHTML = response;
                }
            });
        }
    </script>
