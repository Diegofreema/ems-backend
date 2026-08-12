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
                            <h1 style = "text-align:center" class="h4 text-gray-900 mb-4"><strong style="font-size: 18px;"><?=$settings->name?></strong><br />
                                <center> <b style="font-size: 18px; float: left; margin-left: 150px;">  <?=$settings->address?></b></center><br />
                               
                                <b style="font-size: 17px; margin-left: -80px; text-align:center"> Acceptance Letter</b></h1>
                        
                        </div>
                        <div class="col-sm-12">
                      <span class="float-left"><br />
                            Office Of The Registrar<br />
                            The Vice Chancellor : <?=$settings->rector?><br />
                            Registrar : <?=$settings->registrar?><br />
                        </span>    
                          <div class="col-sm-6 float-right">
                              <span class="float-right">
                            P.M.B 1017, Maryland<br />
                            Nekede, Imo State Nigerian<br />
                            Date : <?=date('D d M, Y', strtotime($student->joindate))?><br />
                            </span><br /> 
                        </div> 
                                <br />  
                        </div>
      <div class="col-sm-12">   <br />                    
<br /><br /> <?= strtoupper($student->fname.' '.$student->lname.' '.$transaction->student->mname)  ?><br />
 <?=$student->address  ?>      <br />
 Application Reference Number : <?=$student->application_no ?> (You must quote this in all correspondence)<br /><br />
Dear <b><?= strtoupper($student->fname.' '.$student->lname)  ?></b><br />
  <br />We are delighted to bring to your notice that you have been offered a conditional place in the following programme.<br />

 <br /> Course   <?=$student->department->name ?><br />
 
Department : <?=$student->department->name ?><br />
Class : <?=$student->level->name ?><br />
Program : <?=$student->programme->name ?><br />
 Year Of Entry :  <?=$student->admissiondate?>     <br /> 
 Start Date : September <?=$student->admissiondate?><br /> 
<!-- Expected End Date : <?=$student->admissiondate+2 ?><br />-->

                      <br />
                      
                     <b> Conditions Of Offer</b><br />
                          
<?=$conditions->conditiond ?>

                     <br />
                     <?=$settings->registrar?><br />
                    Director Of Admission<br />
 <div class="form-group">
	
     <div class="col-sm-4 float-right">
		<input class="btn btn-primary float-right" type="button" onclick="printDiv('printableArea')" value="Print Slip" />
          
	</div>
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
