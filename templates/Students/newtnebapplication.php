 <style>
        div.background {
 background-image: url(../img/logo-icon.png);
  background-repeat: no-repeat;
  background-size: 100%;
  opacity: 0.5;
}
    </style>
<div class="container">
<?php $settings = $this->request->getSession()->read('settings')?>
    <div class="card o-hidden border-0 shadow-lg my-5">
           
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            
            <div class="row">
                <br />
<!--            <div class="col-md-3 d-sm-none d-md-block d-none d-sm-block">
             <?= $this->Html->image($settings->logo,['class'=>'img-fluid px-3 px-sm-4 mt-3 mb-4 float-left','style'=>'width: 90px; width: 90px;'])?>
            </div>     -->
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                      <?= $this->Html->image('android-chrome-192x192.png',['class'=>'img-fluid px-3 px-sm-4 mt-3 mb-4 float-left','style'=>'width: 120px; width: 120px;'])?>
           
                    <div class="p-5">
                        <div class="col-auto">
                            
<!--                            <button class="btn btn-info float-right" title="click to check your application status"
                                    data-toggle="modal" data-target="#myModal" >Check Application Status</button>-->
                 </div>
                          <span class="d-block d-sm-none d-none d-sm-block d-md-none"> <br /> <br />    <br />  <br /> </span>
                      <div class="text-center">
                          
                            <h1 class="h4 text-gray-900 mb-4"><?=SCHOOL ?> <br /> Transnational Education Application Form</h1>
                            <span>If you previously submitted an application but could not make payment, <?=
$this->Html->link(__(' Click Here'), ['controller' => 'Students', 'action' => 'getincompleteapplicant'], [ 'title' => 'complete pending application'])?></span>
                      </div>
                        <?= $this->Form->create($student,['type'=>'file']) ?>
                          <fieldset><br /><br />

                            <div class="form-group row">
                                 <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('passporturls', ['label' => 'Upload Passport', 'placeholder' => 'Uplaod Passport',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
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
                               
                            </div>

                            <div class="form-group row">
                                 <div class="col-sm-4 mb-3 mb-sm-0">    
<?= $this->Form->control('mname', ['label' => 'Other Names', 'placeholder' => 'Other Names',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                               <?php $gender = ['Male'=>'Male', 'Female'=>'Female'];
                                 echo $this->Form->control('gender', ['label' => 'Gender', 'placeholder' => 'Gender',
      'class' => 'form-control form-control-user2', 'options' => $gender])
?>      
                                 </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('phone', ['label' => 'Phone Number', 'placeholder' => 'Phone',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                            </div>
                            
                                 

                            <div class="form-group row">
                                 <div class="col-sm-4 mb-3 mb-sm-0">  
<?= $this->Form->control('dob', ['label' => 'Date Of Birth', 'placeholder' => 'Date Of Birth',
      'class' => 'form-control floating datetimepicker','type' => 'text', 'id' => 'datepicker'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('jambregno', ['label' => 'JAMB Registration Number', 'placeholder' => 'Jamb Registration number',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('jamb', ['label' => 'UTME Aggregate Score', 'placeholder' => 'Jamb Score',
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
<?= $this->Form->control('nin', ['label' => 'NIN', 'placeholder' => 'Enter your NIN',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                                </div>
                            <div class="form-group row"> 
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country','default'=>160, 'empty' => 'Select Country', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'onChange'=>'getstates(this.value)']) ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', ['options' => $states, 'label' => 'Select State', 'empty' => 'Select State', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false,'id'=>'states1','onChange'=>'getlgas(this.value)','required']) ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('lga_id', ['label' => 'Local Government Area', 'options' => $lgas,
                                          'class' => 'select2_multiple form-control form-control-user', 'required','empty'=>'Select LGA','id'=>'lga','required'])
                                    ?>
                                </div> 
                                
                            </div>
                            
                             <div class="form-group row"> 
                                 
                                  <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('community', ['label' => 'Autonomous Community/Home Town', 'placeholder' => 'autonomous community',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div> 
                                  <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2', 'required', 'type' => 'email'])
                                    ?>
                                </div>
                                 
                                 </div>
                              </fieldset>
                        <br />
                        <fieldset><legend><strong>Application Details</strong></legend>
                             <div class="form-group row">
                                 
                                 <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('faculty_id', ['options' => $faculties, 'label' => 'Select School/Institute', 'empty' => 'Select School', 'class' => 'form-control form-control-user','onChange'=>'getdepts(this.value)','required']) ?>
                                </div>
                                  <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Course', 'empty' => 'Select Departments', 'class' => 'select2_multiple form-control form-control-user','id'=>'depts','onChange'=>'getprogrames(this.value)','required']) ?>
                                </div>
                                 
                                    <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('programme_id', ['options' => $programes, 'label' => 'Select Programe', 'empty' => 'Select Programe', 'class' => 'form-control form-control-user','id'=>'dprogrames','required']) ?>
                                </div>
                                  
                                  </div>
                            
                            
                            
                            <div class="form-group row">
                                
                              
                                
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php
                                      echo $this->Form->control('level_id', ['label' => 'Class', 'options' => $levels,
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                  <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('programetype_id', ['label' => 'Programe Type', 
                                          'class' => 'form-control form-control-user2', 'required', 'options' => $programetypes])
                                    ?>
                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('olevelresulturls', ['label' => 'WAEC/NECO/NABTEB/A LEVEL', 'placeholder' => 'olevel cert',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
                            </div>
                             <div class="form-group row">
                                     
                            <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('jamb_notifications', ['label' => 'JAMB NOTIFICATION(with visible QRCODE)', 
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>  
                                 <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('jamb_admin_letters', ['label' => 'JAMB Admission Letter', 'placeholder' => 'olevel cert',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
                                   <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('birthcerturls', ['label' => 'Birth Certificate', 
                                          'class' => 'form-control form-control-user2',  'type' => 'file'])
                                    ?>
                                </div>
                                
                            </div>
                            <div class="form-group row">
                               
                               
                                  <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('jambresults', ['label' => 'Jamb Result', 
                                          'class' => 'form-control form-control-user2',  'type' => 'file'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('othercertss', ['label' => 'LGA Identification Letter', 'placeholder' => 'LGA Identification Letter',
                                          'class' => 'form-control form-control-user2', 'type' => 'file'])
                                    ?>

                                </div>
                            </div>
                            </fieldset>
<br />
<fieldset><legend><strong>Parents/Guardian Information</strong></legend>
                            <div class="form-group row">        
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fathersname', ['label' => 'Father\'s Name', 'placeholder' => 'Father Name',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('mothersname', ['label' => 'Mother\'s Name', 'placeholder' => 'Mother Name',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('fatherphone', ['label' => 'Father\'s Phone', 'placeholder' => 'Father Phone',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                            </div>

                            <div class="form-group row">        
                                
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('motherphone', ['label' => 'Mother\'s Phone', 'placeholder' => 'Mother Phone',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                               
                                
           

                            </div>

                            
       </fieldset>
                        <br /> <br />
<?= $this->Form->button('Apply', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- <div class="col-md-12"> Powered By <a target="_blank" href="https://www.netpro.africat">Netpro International Limited</a>
     
</div>-->
     <br /> <br />
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
        url: '../Students/getTneDapts/'+facultyid,
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


function getprogrames(departmentid){ 

    $.ajax({
        url: '../Students/getprogrames/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
          //  console.log(response);
            document.getElementById('dprogrames').innerHTML = "";
            document.getElementById('dprogrames').innerHTML = response;
            //location.href = redirect;
        }
    });

}


    </script>
    
    <!-- The Modal -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header bg bg-info">
          <h4 class="modal-title" style="color: white; align-self: center">Check Your Application Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
         <?= $this->Form->create(null,['url'=>['controller'=>'Students','action'=>'checkstatus'],'id'=>'statuscheck']) ?>
          <div class="col-sm-12 mb-3 mb-sm-0">
<?= $this->Form->control('application_no', ['label' => false, 'placeholder' => 'Enter your application Number',
      'class' => 'form-control form-control-user2', 'required','id'=>'application_id'])
?>
                                </div>
          
          <div class="col-sm-12 mb-3 mb-sm-0" id="res">
              
          </div>
          
          <br /> <br />
          <?= $this->Form->button('Check Status', ['class' => 'btn btn-primary btn-sm','onClick'=>'submitCheckForm()']) ?>
<?= $this->Form->end() ?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
     </div>
    
    
    <script language="javascript" type="text/javascript">
    function submitCheckForm() {
       var application_no = document.getElementById('application_id').value;
      // alert(application_no);
        
   
     $.ajax({
        url: '../Students/checkstatus/'+application_no,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
            document.getElementById('res').innerHTML = "";
            document.getElementById('res').innerHTML = response;
            //location.href = redirect;
            
        }
    });   
    event.preventDefault();
    }
</script>
