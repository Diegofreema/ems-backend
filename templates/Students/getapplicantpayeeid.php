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
                               
                                <b style="font-size: 21px;"> Fee Payment Invoice - Application Fee </b></h1>
                        
                    <br />    </div>
                        <div class="col-sm-3 m-b-20">
                        <?=$this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right','height'=>130,'width'=>160])?>
                         
                            <br /><br /><br />

                            
                        </div>
                        
                    </div>
                     
                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <h5>Invoice to:</h5>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?=$student->fname.' '.$student->lname.' '.$student->mname  ?></strong></h5></li>
                                <li><span>Address: <?= $student->address ?></span></li>
                                   <li>JAMB Reg No: <?= $student->jambregno ?></li>
                                <li>JAMB Score: <?= $student->jamb ?></li>
                                <li>State: <?= $student->state->name ?></li>
                                  <li>LGA: <?= $student->lga->name ?></li>
                                <li>Country: <?= $student->country->name ?></li>
                                <li>Phone: <?= $student->phone ?></li>
                                <li><a href="#">Email: <?= $student->user->username ?></a></li>
                                <li>Faculty: <?= $student->faculty->name ?></li>
                                <li>Department: <?= $student->department->name ?></li>
                                <li>Program: <?= $student->programe->name ?></li>
                                <li>Application Number: <?= $student->application_no ?> </li>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li> Transaction Ref :<b> <?= $transaction->payref ?> </b></li>
                                <li> Transaction Date : <?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?> </li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($transaction->amount, 2) ?></span></h5></li>
                                <li>Fee: <span>Application Fee</span></li>
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
                                        <td>Application Fee</td>
                                        <td class="d-none d-sm-table-cell"><?= $transaction->payref ?></td>
                                        <td>₦<?= number_format($transaction->amount, 2) ?></td>
                                        <td><?=$transaction->paystatus ?></td>
                                        
                                    </tr>




                            </tbody>
                        </table>
                    </div>
                    <span style="margin-right: 20px; float: right;"> <?= $this->Qr->text($transaction->payref.' '.
                $student->fname.' '.$student->lname.
                ' '.$transaction->transdate.', '.number_format($transaction->amount,2).', '.$transaction->paystatus.', '
                .$student->phone.', '.$student->user->username);?></span> 
                    <div>
                      <br />  <br />  <br />
                        <div class="invoice-info">
                            <h5><b>Information</b></h5>
                            <p class="text-muted">Please note that payment can also be made at the following designated banks:<br />
                                First Bank<br />
                                 GTB Bank<br />
                                 Access Bank<br />
                                 Fidelity Bank<br />
                                <?php if($transaction->paystatus=='completed'){?>
                               For assistance please call: <?= $settings->phone ?> or Mail: <?=  $settings->email ?>
                                <?php } ?>
                           
                            </p>
                        </div>
                    </div>
                </div>
                     <?php
                $name = $student->fname . ' ' . $student->lname . ' ' . $student->mname;
                $terminalId = "0000000001";
                $secret_key = "DEMO_KEY";
                $responseurl = $baseUrl."invoices/verifyetransact";
                //  $str=$transaction->amount.$terminalId.$transaction->payref.$responseurl.$secret_key;
//$checksum=hash("sha256" ,$str);     
                $transId = $transaction->payref;
                $str = $transaction->amount . $terminalId . $transId . $responseurl . $secret_key;
                $checksum = md5($str);
                $cheksum = md5($transaction->amount . $terminalId . $transId . $responseurl . $secret_key);
                ?>

                <form method='POST' action='https://demo.etranzact.com/webconnect/v3/caller.jsp'>
                    <input name="TERMINAL_ID" type="hidden" value="<?=$terminalId?>" />
                    <input name="TRANSACTION_ID" type="hidden" value="<?= $transaction->payref ?>" />
                    <input name="AMOUNT" type="hidden" value="<?= $transaction->amount ?>" />
                    <input name="DESCRIPTION" type="hidden" value="<?= 'UAES Fee Payment ' . $fee->name ?>" />
                    <input name="RESPONSE_URL" type="hidden" value="<?= $responseurl ?>" />
                    <input name="NOTIFICATION_URL" type="hidden" value="AB-12385_TT" />
                    <input type='hidden' name = 'EMAIL' value="<?= $student->user->username ?>">
                    <input type='hidden' name = 'FULL_NAME' value="<?= $name ?>">
                    <input type='hidden' name = 'LOGO_URL' value='http://localhost:81/projects/Webconnect/images/elogo.fw.png'>
                    <input type='hidden' name = 'CURRENCY_CODE' value='NGN'>
                    <input type='hidden' name = 'PHONENO' value="<?= $student->phone ?>">
                    <input name="CHECKSUM" type="hidden" value="<?= $cheksum ?>" />

<!--                    <div class="form-group">
                        <div class="col-sm-12 float-left">
                             link to pay stack online payment 
<!--?=
$this->Html->link(__(' Pay Online (Paystack)'), ['controller' => 'Transactions', 'action' => 'gotopaystack', $student->user->username,
    $student->phone, $name, $transaction->amount, $student->id, $transaction->fee_id,
    $transaction->payref,$transaction->id], ['class' => 'btn btn-success float-right', 'title' => 'pay online with your ATM card'])
?>
                        </div>     link to pay with etransact 
                        <div class="col-sm-4 float-left">
                            <input type="submit" class="btn btn-success" name="submit"  value="Pay Online(eTransact)" />
                        </div>

                    </div>-->
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
    
    
