<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Update Profile</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Update Profile</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Update Profile</h1>
                        </div>
                        <?= $this->Form->create($student, ['type' => 'file']) ?>
                        <fieldset><legend>Student's Data</legend>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('fname', ['label' => 'First Name', 'placeholder' => 'First Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('lname', ['label' => 'Last Name', 'placeholder' => 'Last Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">    
                                    <?=
                                      $this->Form->control('mname', ['label' => 'Middle Name', 'placeholder' => 'Middle Name',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                            </div>
                            
                             <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                               <?php $gender = ['Male'=>'Male', 'Female'=>'Female'];
                                 echo $this->Form->control('gender', ['label' => 'Gender', 'placeholder' => 'Gender',
      'class' => 'form-control form-control-user2', 'options' => $gender])
?>      
                                 </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('phone', ['label' => 'Phone', 'placeholder' => 'Phone',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">  
<?= $this->Form->control('dob', ['label' => 'Date Of Birth', 'placeholder' => 'Date Of Birth',
      'class' => 'form-control form-control-user2','type' => 'text', 'id' => 'datepicker'])
?>
                                </div>
                               

                            </div>
                            
                            

                            <div class="form-group row">
                                
                                 <div class="col-sm-4 mb-3 mb-sm-0">
<?=
  $this->Form->control('passporturls', ['label' => 'Upload Passport', 'placeholder' => 'Uplaod Passport',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
                                
                                   <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2', 'required', 'type' => 'email', 'disabled'])
                                    ?>
                                </div>
                              <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country', 'empty' => 'Select Country', 'class' => 'select2_multiple form-control form-control-user2', 'multiple' => false, 'onChange'=>'getstates(this.value)']) ?>

                                </div>

                            </div>
                             <div class="form-group row">
                        
                           
                                 <div class="col-sm-8 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('address', ['label' => 'Address', 'placeholder' => 'Address',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                  <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', ['options' => $states, 'label' => 'Select State', 'empty' => 'Select State', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'id'=>'states1','onChange'=>'getlgas(this.value)']) ?>
                                </div>
                                 
                               
                                 
                            </div>
                            
                             <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('lga_id', ['label' => 'Local Government Area', 'options' => $lgas,
                                          'class' => 'select2_multiple form-control form-control-user2', 'required','empty'=>'Select LGA','id'=>'lga','multiple' => false])
                                    ?>
                                </div>   
                             
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('community', ['label' => 'Autonomous Community', 'placeholder' => 'autonomous community',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div> 
                                 
                                
                             </div>
                            
                            

                           

                            </fieldset>
                                <fieldset><legend>Parental Information</legend>
                            <div class="form-group row">        
                                <div class="col-sm-6 mb-3 mb-sm-0">
<?=
  $this->Form->control('fathersname', ['label' => 'Father\'s Name', 'placeholder' => 'Father Name',
      'class' => 'form-control form-control-user2', 'value' => isset($student->sparent) ? $student->sparent->fathersname : '', 'disabled'])
?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('mothersname', ['label' => 'Mother\'s Name', 'placeholder' => 'Mother Name',
                                          'class' => 'form-control form-control-user2', 'value' => isset($student->sparent) ? $student->sparent->mothersname : '', 'disabled'])
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">        
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('fatherphone', ['label' => 'Father\'s Phone', 'placeholder' => 'Father Phone',
                                          'class' => 'form-control form-control-user2', 'value' => isset($student->sparent) ? $student->sparent->fatherphone : '', 'disabled'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?=
  $this->Form->control('motherphone', ['label' => 'Mother\'s Phone', 'placeholder' => 'Mother Phone',
      'class' => 'form-control form-control-user2', 'value' => isset($student->sparent) ? $student->sparent->motherphone : '', 'disabled'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?=
  $this->Form->control('fathersjob', ['label' => 'Father\'s Occupation', 'placeholder' => 'Father Occupation',
      'class' => 'form-control form-control-user2', 'value' => isset($student->sparent) ? $student->sparent->fathersjob : '', 'disabled'])
?>
                                </div>

                            </div>

                            <div class="form-group row">        
                                <div class="col-sm-6 mb-3 mb-sm-0">
<?=
  $this->Form->control('mothersjob', ['label' => 'Mother\'s Occupation', 'placeholder' => 'Mother Occupation',
      'class' => 'form-control form-control-user2', 'value' => isset($student->sparent) ? $student->sparent->mothersjob : '', 'disabled'])
?>
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
        url: '../Students/getstates/'+stateid,
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

function getlgas(stateid){ 

    $.ajax({
        url: '../Students/getlgas/'+stateid,
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
    </script>
