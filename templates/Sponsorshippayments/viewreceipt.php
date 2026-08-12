<?php $settings = $this->request->getSession()->read('settings')?>
   
    <!-- Page Content -->
<div class="content container-fluid">

    <br /><br /><br />

    <div class="row" id="printableArea">
        
        <div class="col-md-12">
            <div class="card"><br /><br /><br />
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 m-b-20">
                        <?=$this->Html->image('android-chrome-192x192.png', ['alt' => 'LOGO', 'class' => 'inv-logo','height'=>100,'width'=>130])?>
                         
                            <br /><br /><br />

                            
                        </div>
                        <div class="col-sm-6 m-b-20 text-center">
                            
                          
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?=$settings->name?></strong><br />
                                <b style="font-size: 23px;">  <?=$settings->address?><br />
                               <?=$settings->email?><br /></b>
                               
                                <b style="font-size: 21px;"> Fee Payment Invoice- <?= $fee->name?> </b></h1>
                        
                    <br />    </div>
                        <div class="col-sm-3 m-b-20">
                        <?=$this->Html->image('../student_files/'.$paydetails->student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right','height'=>130,'width'=>160])?>
                         
                            <br /><br /><br />

                            
                        </div>
                        
                    </div>
                     
                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <h5>Payment Receipt Issued to:</h5>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?=$paydetails->student->fname.' '.$paydetails->student->mname.' '.$paydetails->student->lname  ?></strong></h5></li>
                                <li><span>Address: <?= $paydetails->student->address ?></span></li>
                       
                                <li>Phone: <?= $paydetails->student->phone ?></li>
                            
                                 <li>Session: <?= $paydetails->session->name ?></li>
                               
                                 <li>Level: <?= $paydetails->student->level->name ?></li>
                                
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li> Transaction Ref :<b> (Sponsored)</b></li>
                                <li> Transaction Date : <?= date('D d M, Y h:i', strtotime($paydetails->datecreated)) ?> </li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($fee->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?= $fee->name ?></span></li>
                                  <li>Pay Status: <span><B><?php if($transaction->paystatus=='completed'){ echo '<span class="badge badge-success">' . h($paydetails->sponsorshippayment->paystatus).'</span>';}else{
                     echo $paydetails->sponsorshippayment->paystatus;   
                    } ?></B></span></li>
 <!--                                 <li>City: <span>London E1 8BF</span></li>
                                <li>Address: <span>3 Goodman Street</span></li>
                                <li>IBAN: <span>KFH37784028476740</span></li>
                                <li>SWIFT code: <span>BPT4E</span></li>-->
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ITEM</th>
                                    <th class="d-none d-sm-table-cell">TRANSACTION REF</th>
                                    <th>AMOUNT</th>
                                      <th>STATUS</th>
                                    
                                </tr>
                            </thead>
                          
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><?= $fee->name ?></td>
                                        <td class="d-none d-sm-table-cell"><?= $paydetails->sponsorshippayment->syref ?></td>
                                        <td>₦<?= number_format($fee->amount, 2) ?></td>
                                        <td><?php if($paydetails->sponsorshippayment->paystatus=='completed'){ echo '<span class="badge badge-success">' . h($paydetails->sponsorshippayment->paystatus).'</span>';}else{
                     echo $paydetails->sponsorshippayment->paystatus;   
                    } ?></td>
                                        
                                    </tr>




                            </tbody>
                        </table>
                    </div>
                    <span style="margin-right: 20px; float: right;"> <?= $this->Qr->text($paydetails->sponsorshippayment->syref.' '.
                $paydetails->student->fname.' '.$paydetails->student->mname.' '.$paydetails->student->lname.
                ' '.$paydetails->sponsorshippayment->datecreated.', '.number_format($paydetails->sponsorshippayment->amount,2). ', '.$fee->name.', '.$paydetails->sponsorshippayment->datecreated->paystatus.', '
                .$paydetails->student->phone.', '.$paydetails->student->email);?></span> 
                    <div>
                      <br />  <br />  <br />
                        <div class="invoice-info">
                            <h5><b>Information</b></h5>
                            <p class="text-muted">
  
                               For assistance please call: <?= $settings->phone ?> or Mail: <?=$settings->email  ?>
                              
                           
                            </p>
                        </div>
                    </div>
                </div>
                   
                          
                        
<br /><br /><br />
                <div class="col-sm-12">
                   
		<input class="btn btn-primary float-left" type="button" onclick="printDiv('printableArea')" value="Print Slip" />
	
                  </div>

                 <br />  <br />  <br />
            </div>
            
        </div>
    </div>
</div>
<!-- /Page Content -->
<script>
    
    function printDiv(divName) { //alert('am called');
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
     
 }

    </script>
    
    
