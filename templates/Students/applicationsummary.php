<div class="container" id="printableArea">
 <?php $settings = $this->request->getSession()->read('settings')?>
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <br />
             
                <div class="col-lg-12">
                    <div class="p-5">
             
                        <div class="row">
                            <div class="col-sm-3">
                                 <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100])?>
                            </div>
                            <div class="col-sm-6" style="text-align: center">
                                <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 20px;"><?=$settings->name?></strong><br />
                                 <b style="font-size: 14px;">  <?=$settings->address?></b><br />
                               
                                <b style="font-size: 20px;"> Application Summary</b></h1>
                            </div>
                            <div class="col-sm-3">
                                <?=$this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'img-responsive float-right','height'=>130])?>
                            </div>
                        </div>
                        
      <div class="col-sm-12"> <b style="font-size: 14px;">Personal Details  </b>                     
<br /><br />Name : <?= strtoupper($student->fname.' '.$student->lname.' '.$student->mname)  ?><br />
Applicant Address : <?=$student->address  ?>      <br />
 Application Reference Number : <?=$student->application_no ?> <br />

Phone Number : <?=$student->phone ?><br />
Email Address : <?=$student->email ?><br />
Gender : <?=$student->gender ?><br />
Date Of Birth : <?=$student->dob ?><br />
State of Origin : <?=$student->state->name ?><br />
<!-- Local Government of Origin : <?=$student->lga->name ?><br /> -->
Autonomous Community : <?=$student->community ?><br />

<b><br />Application Details</b><br />
School : <br />
 Department :   <?=$student->department->name ?><br />
 
Class : <?=$student->department->name ?><br />


  Application Status : <?=$student->status ?> <br />
 Application Date :  <?=date('D d M, Y', strtotime($student->joindate))?>     <br /> 
<!-- Possible Resumption Date : September <?=date('Y', strtotime($student->joindate))?><br /> 
 Expected End Date : <?=date('Y', strtotime($student->joindate))+2; ?><br />-->

                      <br />
                      
                     <b> Parental Data</b><br />
                          
Father's Name :<?=$student->fathersname ?><br />
Father's Occupation :<?=$student->fathersjob ?><br />
Father's Phone :<?=$student->fatherphone ?><br />
Mother's Phone :<?=$student->motherphone ?><br />
Mothers Name : <?=$student->mothersname ?><br />
Mother's Occupation :<?=$student->mothersjob ?><br />

 <div class="form-group">
	<br /><br />
     <div class="col-sm-4 float-right">
		<input class="btn btn-primary float-right" type="button" onclick="printDiv('printableArea')" value="Print Photo Card" />
          
	</div>
     <?= $this->Html->link(__('Back'), ['controller'=>'Students','action' => 'generateapplicantpayeeid',$invoice->id,$student->id,$this->generateurl($student->fname)],['class'=>'btn btn-success float-left',
                     'title'=>'back to applicant payee ID page']) ?>
</div>
<br /><br /><br />
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
