<div class="container" id="printableArea">
 <?php $settings = $this->request->getSession()->read('settings')?>
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <br />
             
                <div class="col-lg-12">
                    <div class="p-5">
                       
                       <div class="text-center">
                           <div class="col-md-3">
                <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100,'width'=>130])?>
                </div> 
                           <div class="col-md-3 float-right">
                <?=$this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'img-responsive float-rightt','height'=>100,'width'=>130])?>
                </div> 
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 26px;"><?=$settings->name?></strong><br />
                                <center> <b style="font-size: 14px;  margin-left: 10px;">  <?=$settings->address?></b></center><br />
                               
                              <b style="font-size: 41px; margin-left: -30px;">Photo Card</b></h1>
                        
                        </div>
                        
      <div class="col-sm-12"> <b style="font-size: 14px;">Personal Details  </b>                     
<br /><br />Name : <?= strtoupper($student->fname.' '.$student->mname.' '.$student->lname)  ?><br />
Applicant Address : <?=$student->address  ?>      <br />
 Application Reference Number : <?=$student->application_no ?> <br />
Phone Number : <?=$student->phone ?><br />
Email Address : <?=$student->email ?><br />
Gender : <?=$student->gender ?><br /><br />

<b>Academic Details</b><br />
Application Status : <?php if($student->status=="Admitted"){echo "Enrolled";}else{echo 'Pending';} ?><br />
Reg No : <?=$student->regno ?><br />
 Class :   <?= $student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '') ?><br />
 
<!-- Possible Resumption Date : September <?=date('Y', strtotime($student->joindate))?><br /> -->
<?php
//$entry_year = explode('/',$student->regno);
?>
<!-- Expected End Date : <?=$entry_year[0]+2; ?><br />-->

                      <br />
                      
                     <b> Parental Data</b><br />
                          
Father's Name : <?=$student->sparent->fathersname ?><br />
Father's Occupation : <?=$student->sparent->fathersjob ?><br />
Father's Phone : <?=$student->sparent->fatherphone ?><br />
Mother's Name :  <?=$student->sparent->mothersname ?><br />
Mother's Phone : <?=$student->sparent->motherphone ?><br />
Mother's Occupation : <?=$student->sparent->mothersjob ?><br />

<br /><br />


 <br /><div class="form-group">
	
     <div class="col-sm-4 float-right">
		<input class="btn btn-primary float-right" type="button" onclick="printDiv('printableArea')" value="Print Slip" />
          
	</div>
    
</div>
<br /><br />
 <br />
                  
                       
                    </div>
                  </div>
                  </div>
                  </div>
       
    <br />
                </div>
   
           </div>
        </div>
<script>
    
    function printDiv(divName) { //alert('am called');
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
 }

    </script>
