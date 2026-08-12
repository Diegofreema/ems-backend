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
                               
                                <b style="font-size: 21px;"> Fee Payment Invoice- <?= $transaction->fee->name?> </b></h1>
                        
                    <br />    </div>
                        <div class="col-sm-3 m-b-20">
                        <?=$this->Html->image('../student_files/'.$transaction->student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right','height'=>130,'width'=>160])?>
                         
                            <br /><br /><br />

                            
                        </div>
                        
                    </div>
                     
                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <h5>Payment Receipt Issued to:</h5>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?=$transaction->student->fname.' '.$transaction->student->mname.' '.$transaction->student->lname  ?></strong></h5></li>
                                <li><span>Address: <?= $transaction->student->address ?></span></li>
                                <li>State: <?= $transaction->student->state->name ?></li>
                                  <li>LGA: <?= $transaction->student->lga->name ?></li>
                                <li>Country: <?= $transaction->student->country->name ?></li>
                                <li>Phone: <?= $transaction->student->phone ?></li>
                                <li><a href="#">Email: <?= $transaction->student->user->username ?></a></li>
                          <li>Faculty: <?= $transaction->student->faculty->name ?></li>
                                <li>Department: <?= $transaction->student->department->name ?></li>
                                 <li>Session: <?= $transaction->session->name ?></li>
                                 <li>Programe: <?= $transaction->student->programme->name ?></li>
                                 <li>Level: <?= $transaction->student->level->name ?></li>
                                
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li> Transaction Ref :<b> <?= $transaction->payref ?> </b></li>
                                <li> Transaction Date : <?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?> </li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($transaction->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?= $transaction->fee->name ?></span></li>
                                  <li>Pay Status: <span><B><?php if($transaction->paystatus=='completed'){ echo '<span class="badge badge-success">' . h($transaction->paystatus).'</span>';}else{
                     echo $transaction->paystatus;   
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
                                        <td><?= $transaction->fee->name ?></td>
                                        <td class="d-none d-sm-table-cell"><?= $transaction->payref ?></td>
                                        <td>₦<?= number_format($transaction->amount, 2) ?></td>
                                        <td><?php if($transaction->paystatus=='completed'){ echo '<span class="badge badge-success">' . h($transaction->paystatus).'</span>';}else{
                     echo $transaction->paystatus;   
                    } ?></td>
                                        
                                    </tr>




                            </tbody>
                        </table>
                    </div>
                    <span style="margin-right: 20px; float: right;"> <?= $this->Qr->text($transaction->payref.' '.
                $transaction->student->fname.' '.$transaction->student->mname.' '.$transaction->student->lname.
                ' '.$transaction->transdate.', '.number_format($transaction->amount,2). ', '.$transaction->fee->name.', '.$transaction->paystatus.', '
                .$transaction->student->phone.', '.$transaction->student->user->username);?></span> 
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
                    <?php 
                        $name = $transaction->student->fname.' '.$transaction->student->lname.' '.$transaction->student->mname;
                         $terminalId = "0000000001"; $secret_key="DEMO_KEY";
                         $responseurl = "http://localhost/nerp/invoices/verifyetransact";
                   $str=$transaction->amount.$terminalId.$transaction->payref.$responseurl.$secret_key;
$checksum=hash("sha256" ,$str);     
                        
                        ?>
                          
                         <form method='POST' action='http://demo.etranzact.com/bankIT/'>
<input name="TERMINAL_ID" type="hidden" value="0000000001" />
<input name="TRANSACTION_ID" type="hidden" value="<?=$transaction->payref  ?>" />
<input name="AMOUNT" type="hidden" value="<?=$transaction->amount ?>" />
<input name="DESCRIPTION" type="hidden" value="<?='IMOPOLY Fee Payment '. $transaction->fee->name  ?>" />
<input name="RESPONSE_URL" type="hidden" value="http://localhost/nerp/invoices/verifyetransact" />
<input name="NOTIFICATION_URL" type="hidden" value="AB-12385_TT" />
<input name="CHECKSUM" type="hidden" value="<?= $checksum ?>" />
<input name="LOGO_URL" type="hidden" value="http://nsuid.com.ng/faq.php/mfmlogo.jpg" />

 <div class="form-group">
<!--	<div class="col-sm-12 float-left">
             link to pay stack online payment 
             <?= $this->Html->link(__(' Pay Online'), ['controller'=>'Transactions','action' => 'gotopaystack', $student->user->username,
                    $student->phone,$name,$transaction->amount,$student->id,$transaction->fee_id,
                $transaction->payref],
                            ['class'=>'btn btn-success float-right','title'=>'pay online with your ATM card']) ?>
        </div>   -->
     <!-- link to pay with etransact -->
<!--                 <div class="col-sm-4 float-left">
            <input type="submit" class="btn btn-success" name="submit"  value="Pay Online(eTransact)" />
	</div>-->
     
</div>
</form>
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
    
    
