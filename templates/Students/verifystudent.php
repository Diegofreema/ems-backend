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
                     <?= $this->Html->image($settings->logo,['class'=>'img-fluid px-3 px-sm-4 mt-3 mb-4 float-left','style'=>'width: 90px; width: 90px;'])?>
           
                    <div class="p-5">
                        
                          <span class="d-block d-sm-none d-none d-sm-block d-md-none"> <br /> <br />    <br />  <br /> </span>
                      <div class="text-center">
                          
                            <h1 class="h4 text-gray-900 mb-4">Imo State Polytechnic Student Verification</h1>
                        </div>
                     <?= $this->Form->create(null,['url'=>['controller'=>'Students','action'=>'verifystudent']]) ?>
                        <fieldset><legend><strong>Student Details</strong></legend>
                            <div class="form-group row">
                                 <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('regno', ['label' => 'Registration Number', 'placeholder' => 'Student registration Number',
      'class' => 'form-control form-control-user2','id'=>'regno'])
?>
                                </div>
                                
                              
                                <div class="col-sm-12 mb-3 mb-sm-0" id="res"><br /><br />
               <?php  if(!empty($student)){?>
   <?php if(!empty($student->passporturl)){ echo $this->html->image($student->passporturl,['style'=>'width: 220px; height: 220px;']);}?>
<strong><br /><br />Name : </strong> <?=$student->fname .' '.$student->lname. ' '. $student->mname?><br />
<strong>Registration Number : </strong> <?=$student->regno ?><br />
<strong>Faculty : </strong> <?=$student->faculty->name ?><br />
<strong>Department : </strong> <?=$student->department->name ?>
</div>
<?php }
else{
?>
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Error : </strong> Unknown Student data
</div>
<?php } ?>
          </div>
                               
                            </div>

                           
                            
           
                              </fieldset>
                    
<?= $this->Form->button('Verify', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?><br /> <br /><br /> <br />
                    </div>
                </div>
                
                
               
                
            </div>
        </div>
    </div>

</div>

   
    <script language="javascript" type="text/javascript">
    function submitCheckForm() {
       var application_no = document.getElementById('regno').value;
       alert(application_no);
        
   
     $.ajax({
        url: '../Students/validatestudent/'+application_no,
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
