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
                <div class="col-lg-12">
                      <?= $this->Html->image('android-chrome-192x192.png',['class'=>'img-fluid px-3 px-sm-4 mt-3 mb-4 float-left','style'=>'width: 120px; width: 120px;'])?>
           
                    <div class="p-5">
                        <div class="col-auto">
                            
                 </div>
                          <span class="d-block d-sm-none d-none d-sm-block d-md-none"> <br /> <br />    <br />  <br /> </span>
                      <div class="text-center">
                          
                            <h1 class="h4 text-gray-900 mb-4"><?=SCHOOL ?> <br /> Candidates Application Update
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
                                          'class' => 'form-control form-control-user2','disabled'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('lname', ['label' => 'First Name', 'placeholder' => 'First Name', 'required',
                                          'class' => 'form-control form-control-user2','disabled'])
                                    ?>

                                </div>
                               
                            </div>

                       
                       
                              </fieldset>
                        <br />
                        <fieldset>
                          
                            
                            <div class="form-group row">
                            <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('jamb_notifications', ['label' => 'JAMB NOTIFICATION(with visible QRCODE)', 
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>  
                                 <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('jamb_admin_letters', ['label' => 'JAMB ADMISSION LETTER', 'placeholder' => 'olevel cert',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
                                  <div class="col-sm-4 mb-3 mb-sm-0">               
<?= $this->Form->control('olevelresulturls', ['label' => 'WAEC/NECO/NABTEB/A level', 'placeholder' => 'olevel cert',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
                            </div>
                          
                            <div class="form-group row">
                              
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('birthcerturls', ['label' => 'Birth Certificate', 
                                          'class' => 'form-control form-control-user2',  'type' => 'file'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('jambresults', ['label' => 'Jamb Result', 
                                          'class' => 'form-control form-control-user2',  'type' => 'file'])
                                    ?>
                                </div>
                                
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('othercertss', ['label' => 'Others', 'placeholder' => 'Other Cert',
                                          'class' => 'form-control form-control-user2', 'type' => 'file'])
                                    ?>

                                </div>
                            </div>
                            </fieldset>
<br />

                        <br /> <br />
<?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
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
