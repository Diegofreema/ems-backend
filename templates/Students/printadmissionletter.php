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
                <?=$this->Html->image($student->passporturl, ['alt' => 'passport', 'class' => 'img-responsive float-rightt','height'=>100,'width'=>130])?>
                </div> 
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 26px;"><?=$settings->name?></strong><br />
                                <center> <b style="font-size: 18px; float: left; margin-left: 20px;">  <?=$settings->address?></b></center><br />
                               
                               <br /> <b style="font-size: 21px;">Provisional Letter</b></h1>
                        
                        </div>
                        <div class="col-sm-12">
                      <span class="float-left">
                            Office Of The Registrar<br />
                            Rector : <?=$settings->rector?><br />
                            Registrar : <?=$settings->registrar?><br />
                            Our Ref: <?=$student->regno?>
                            
                        </span>    
                          <div class="col-sm-6 float-right">
                              <span class="float-right">
                            P.M.B 1472, OWERRI<br />
                            Imo State Nigerian<br />
                            Date : <?=date('D d M, Y', strtotime($student->joindate))?><br />
                            </span><br /> 
                        </div> 
                                <br />  
                        </div>
   <br />   <div class="col-sm-12">   <br />                    
<br /><br /> 
        <br />  Name of Candidate : <?= strtoupper($student->fname.' '.$student->lname.' '.$student->mname)  ?><br />
 
Programme of Study : <?=$student->programe->name ?><br />
 In The Course Of : <?=$student->department->name ?><br />
 
In The Department Of : <?=$student->department->name ?><br />
For The  : <?=date('Y', strtotime($student->joindate))?> / <?=date('Y', strtotime($student->joindate))+1?> Academic Session


                      <br />     
<br /> <?= $letter->conditiond ?>

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

