<div class="container">
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
                            <h1 class="h4 text-gray-900 mb-4">Student Data Capture</h1>
                        </div>
                        <?= $this->Form->create($student,['type'=>'file']) ?>
                      
                            
                            <div class="form-group row">
                              
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    Pass Port  
                <div id="my_camera"></div>
                <br/>
                <input type=button value="Take Snapshot" onClick="take_snapshot()" id="snapbtn">
                <input type="hidden" name="passporturls" class="image-tag11">
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
                <div id="results11"></div>
            </div>
                            </div> 
                            
                              <fieldset><legend>Applicant's Personal Details</legend>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fname', ['label' => 'Surame', 'placeholder' => 'Surame', 'required',
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
                                    <?= $this->Form->control('jambregno', ['label' => 'Jamb Regno', 'placeholder' => 'Jamb Regno',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('jamb', ['label' => 'Jamb Score', 'placeholder' => 'Jamb Score',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div> 
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2', 'required', 'type' => 'email'])
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
                                    <?php echo $this->Form->control('community', ['label' => 'Autonomous Community', 'placeholder' => 'autonomous community',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div> 
                                </div>
                            <div class="form-group row"> 
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country','default'=>160, 'empty' => 'Select Country', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'onChange'=>'getstates(this.value)']) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', ['options' => $states, 'label' => 'Select State', 'empty' => 'Select State', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'id'=>'states1','onChange'=>'getlgas(this.value)']) ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('lga_id', ['label' => 'Local Government Area', 'options' => $lgas,
                                          'class' => 'select2_multiple form-control form-control-user2', 'required','empty'=>'Select LGA','id'=>'lga'])
                                    ?>
                                </div> 
                                
                            </div>
                            
                           <br />
                              </fieldset>
                                 <br />
                                  <fieldset><legend>Application Details</legend>
                                   <div class="form-group row">      
                                 <div class="col-sm-6 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('faculty_id', ['options' => $faculties, 'label' => 'Select School', 'empty' => 'Select School', 'class' => 'form-control form-control-user','onChange'=>'getdepts(this.value)']) ?>
                                </div>
                                  <div class="col-sm-6 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Department', 'empty' => 'Select Departments', 'class' => 'select2_multiple form-control form-control-user','id'=>'depts']) ?>
                                </div>
                                  
                                  </div>
                            
                            <div class="form-group row">
                                 <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('programe_id', ['options' => $programes, 'label' => 'Select Programe', 'empty' => 'Select Programe', 'class' => 'form-control form-control-user','required']) ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php
                                      echo $this->Form->control('level_id', ['label' => 'Class', 'options' => $levels,
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
<!--                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('passporturls', ['label' => 'Upload Passport', 'placeholder' => 'Uplaod Passport',
      'class' => 'form-control form-control-user2', 'type' => 'file','required'])
?>
                                </div>-->
                                  <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('regno', ['label' => 'Registration Number', 'placeholder' => 'Reg Number',
      'class' => 'form-control form-control-user2','required'])
?>
                                </div>
     
                            </div>
                            <div class="form-group row">
                                 <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('olevelresulturls', ['label' => 'WAEC/NECO/NABTEB', 'placeholder' => 'olevel cert',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('birthcerturls', ['label' => 'Jamb Result', 
                                          'class' => 'form-control form-control-user2', 'type' => 'file'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('othercertss', ['label' => 'Others', 'placeholder' => 'Other Cert',
                                          'class' => 'form-control form-control-user2',  'type' => 'file'])
                                    ?>

                                </div>
                            </div>
                            </fieldset>
 <br />
                        <fieldset><legend>Parental Information</legend>
                            <div class="form-group row">        
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fathersname', ['label' => 'Father\'s Name', 'placeholder' => 'Father Name',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('mothersname', ['label' => 'Mother\'s Name', 'placeholder' => 'Mother Name',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                  </div>
                                
                                 <div class="form-group row">   
                                <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('fatherphone', ['label' => 'Father\'s Phone', 'placeholder' => 'Father Phone',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>    
                                
                                <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('motherphone', ['label' => 'Mother\'s Phone', 'placeholder' => 'Mother Phone',
      'class' => 'form-control form-control-user2', 'required'])
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



 function getdepts(facultyid){ 

    $.ajax({
        url: '../Students/getdapts/'+facultyid,
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
    
    
    <script language="JavaScript">
    Webcam.set({
        width: 200,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.attach( '#my_camera' );
    
  
    function take_snapshot() {   
        Webcam.snap( function(data_uri) { 
            $(".image-tag11").val(data_uri);
            document.getElementById('my_camera').innerHTML = '<img src="'+data_uri+'"/>';
           // document.getElementById('snapbtn').hide();
            $("#snapbtn").hide();
        } );
    }
</script>
