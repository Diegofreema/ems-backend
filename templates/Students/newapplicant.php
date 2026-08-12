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
              
                <div class="col-lg-12">
                    
                    <div class="p-5">
                      <div class="row">
                            <div class="col-sm-3">
                                 <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100])?>
                            </div>
                            <div class="col-sm-6" style="text-align: center">
                                <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 20px;"><?=$settings->name?></strong><br />
                                 <b style="font-size: 14px;">  <?=$settings->address?></b><br />
                               
                                <b style="font-size: 20px;"> Enrollment Application Form</b></h1>
                            </div>
                            
                        </div>
                          <span class="d-block d-sm-none d-none d-sm-block d-md-none"> <br /> <br />    <br />  <br /> </span>
<!--                      <div class="text-center">
                          
                            <h1 class="h4 text-gray-900 mb-4"><?=SCHOOL ?> <br /> Undergraduate Programe/Post UTME Application Form</h1>
                            <span>If you previously submitted an application but could not make payment, <?=
$this->Html->link(__(' Click Here'), ['controller' => 'Students', 'action' => 'getincompleteapplicant'], [ 'title' => 'complete pending application'])?></span>
                <br /> For foreign candidates, please enter 'NA' for JAMB Registration Number,0 for UTME score and 0 for NIN.
                <br />For Country, kindly choose others, also choose others for state and LGA.
                      </div>-->
                      <?= $this->Form->create($student,['type'=>'file']) ?>
                           <fieldset>
                            <legend>Applicant's Information</legend>
                            <div class="form-group row mb-3">
                               
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('lname', ['label' => 'Surname', 'required', 'placeholder' => 'Surname',
                                          'class' => 'form-control form-control-user2'])
                                    ?>

                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('fname', ['label' => 'First Name', 'required', 'placeholder' => 'First Name',
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

                            <div class="form-group row mb-3">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php
                                      $gender = ['Female' => 'Female', 'Male' => 'Male'];
                                      echo $this->Form->control('gender', ['label' => 'Gender', 'placeholder' => 'Gender',
                                          'class' => 'form-control form-control-user2', 'options' => $gender])
                                    ?>      
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">  
                                    <?=
                                      $this->Form->control('dob', ['label' => 'Date Of Birth', 'placeholder' => 'Date Of Birth',
                                          'class' => 'form-control form-control-user2', 'type' => 'date', 'max' => date('Y-m-d')])
                                    ?>
                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'form-control form-control-user', 'placeholder' => 'Select Class']) ?>
                                </div>
                               
                            </div>
                            
                             <div class="form-group row mb-3">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                   <?=
                                      $this->Form->control('passporturls', ['label' => 'Upload Passport', 'placeholder' => 'Upload Passport',
                                          'class' => 'form-control form-control-user2', 'type' => 'file'])
                                    ?>
                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                     <?=
                                      $this->Form->control('phone', ['label' => 'Phone', 'placeholder' => 'Phone',
                                          'class' => 'form-control form-control-user2','required'])
                                    ?>
                                 </div>
                                  <div class="col-sm-4 mb-3 mb-sm-0">
                                     <?=
                                      $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2','required','type'=>'email'])
                                    ?>
                                 </div>
                                 
                                 </div>
                     
                            <div class="form-group row mb-3">
                                

                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('birthcerturls', ['label' => 'Birth Certificate', 'placeholder' => 'Birth Certificate',
                                          'class' => 'form-control form-control-user2', 'type' => 'file'])
                                    ?>
                                </div>
 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country', 'default' => 160, 'empty' => 'Select Country', 'class' => 'form-control form-control-user', 'multiple' => false, 'onChange' => 'getstates(this.value)', 'placeholder' => 'Select Country']) ?>

                                </div>

                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('state_id', ['options' => $states, 'label' => 'Select State', 'empty' => 'Select State', 'class' => 'form-control form-control-user', 'multiple' => false, 'id' => 'states1','required', 'placeholder' => 'Select State']) ?>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                              

                               
                              
                                  <div class="col-sm-8 mb-3 mb-sm-0">
<?=
  $this->Form->control('address', ['label' => 'Home Address', 'placeholder' => 'Home Address',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php
                                      echo $this->Form->control('religion', ['label' => 'Religion', 'placeholder' => 'Religion',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row mb-3">        
                              
                               
                                <div class="col-sm-8 mb-3 mb-sm-0">
                                    <?php
                                      echo $this->Form->control('pschools', ['label' => 'Previous Schools Attended With Date', 'placeholder' => 'Previous Schools Attended With Date',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                            </div>

                          


                          
                        </fieldset>

                        <fieldset><legend><strong>Parents/Guardian Information</strong></legend>

                            <div class="form-group row mb-3">        
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fathersname', ['label' => 'Father\'s Name', 'placeholder' => 'Father\'s Name',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('mothersname', ['label' => 'Mother\'s Name', 'placeholder' => 'Mother\'s Name',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row mb-3">        
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('fatherphone', ['label' => 'Father\'s Phone', 'placeholder' => 'Father\'s Phone',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('motherphone', ['label' => 'Mother\'s Phone', 'placeholder' => 'Mother\'s Phone',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fathersjob', ['label' => 'Father\'s Occupation', 'placeholder' => 'Father\'s Occupation',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>

                            </div>

                            <div class="form-group row mb-3">        
                                <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('mothersjob', ['label' => 'Mother\'s Occupation', 'placeholder' => 'Mother\'s Occupation',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                               
                                 <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('pemailaddress', ['label' =>'Parent\'s Email Address',   'required', 'class'=> 'form-control form-control-user']) ?> 
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
    
    <!-- Date validation -->
    <script>
    $(document).ready(function() {
        // Disable any jQuery UI datepicker on this field
        if (typeof $.fn.datepicker !== 'undefined') {
            $('#datepicker').datepicker('destroy');
        }
        
        // Add custom validation for HTML5 date input
        $('#datepicker').on('change', function() {
            var selectedDate = new Date(this.value);
            var today = new Date();
            if (selectedDate > today) {
                alert('Please select a date in the past for date of birth');
                this.value = '';
            }
        });
    });
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
