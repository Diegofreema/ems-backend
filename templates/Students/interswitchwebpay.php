<?php $settings = $this->request->getSession()->read('settings') ?>
<!-- Page Content -->
<div class="container">
<div class="content container-fluid">

    <div class="row" id="printableArea">
 <?= $this->Flash->render() ?>
        <div class="col-md-12">
            <div class="card"><br />
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image('android-chrome-192x192.png', ['alt' => 'LOGO', 'class' => 'inv-logo', 'height' => 100, 'width' => 130]) ?>

                            <br /><br /><br />


                        </div>
                        <div class="col-sm-6 m-b-20 text-center">


                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?= ucwords($settings->name) ?></strong><br />
                                <b style="font-size: 23px;">  <?= ucwords($settings->address) ?><br />
                                   fees@claretianuniversity.edu.ng<br /> <?= $settings->phone ?><br /></b>

                                <b style="font-size: 21px;"> Fee Payment Invoice- <?= $fee->name ?> </b></h1>

                            <br />    </div>
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right', 'height' => 130, 'width' => 160]) ?>

                            <br /><br /><br />


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <span class="text-muted">Invoice to:</span>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?= ucwords($student->fname. ' ' . $student->mname . ' ' . $student->lname) ?></strong></h5></li>
                                <li><span>Address: <?= ucwords($student->address) ?></span></li>
                                <li>State: <?= $student->state->name ?></li>
                                <li>LGA: <?php if(!empty($student->lga->name)){ echo $student->lga->name;} ?></li>
                                <li>Country: <?= $student->country->name ?></li>
                                <li>Phone: <?= $student->phone ?></li>
                                <li><a href="#">Email: <?= $student->user->username ?></a></li>
                               <li>Faculty: <?= $student->faculty->name ?></li>
                                <li>Department: <?= $student->department->name ?></li>
                                <li>Program: <?= $student->programme->name ?></li>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li>Ref :<b> <span class="text-right"><?= $transaction->payref ?> </span></b></li>
                                <li> Transaction Date : <span class="text-right"><?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?> </span></li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($transaction->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?= $fee->name ?></span></li>
                                <li>Pay Status: <span><B><?php
                                            if ($transaction->paystatus == 'completed') {
                                                echo '<span class="badge badge-success">' . h($transaction->paystatus) . '</span>';
                                            } else {
                                                echo $transaction->paystatus;
                                            }
                                            ?></B></span></li>
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
                                    <td class="d-none d-sm-table-cell"><?= $transaction->payref ?></td>
                                    <td>₦<?= number_format($transaction->amount, 2) ?></td>
                                    <td><?php
                                            if ($transaction->paystatus == 'completed') {
                                                echo '<span class="badge badge-success">' . h($transaction->paystatus) . '</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">' . h($transaction->paystatus) . '</span>';
                                            }
                                            ?></td>

                                </tr>




                            </tbody>
                        </table>
                    </div>
                    <span style="margin-right: 20px; float: right;"> <?=
                                            $this->Qr->text($transaction->payref . ' ' .
                                                    $student->fname . ' ' . $student->lname .
                                                    ' ' . $transaction->transdate . ', ' . number_format($transaction->amount, 2) . ', ' . $transaction->paystatus . ', '
                                                    . $student->phone . ', ' . $student->user->username);
                                            ?></span> 
                    <div>
                        <br />  <br />  <br />
                        <div class="invoice-info">
                            <h5><b>Information(<?=$transaction->id?>)</b></h5>
                            <p class="text-muted">Please click on either the  green or yellow button below to make payment. All applications without application fee payment will not be processed<br />
<?php if ($transaction->paystatus != 'completed') { ?>
                                    For assistance please call: <?= $settings->phone ?> or Mail: <?= $settings->email ?>
                <?php } ?>

                            </p>
                        </div>
                    </div>
                </div>
                                                      <?php 
// echo $service_charge = $transaction->charge; 20*1000/100
 $amount_to_pay = round($transaction->amount); //$transaction->amount-$transaction->charge;
 $split_amount = "";
 $total = round($amount_to_pay,2); 
 $amount_to_pass =  $total*100;
 $netpro_split = 200;
 $cun_split = $transaction->amount-500;
                             
                             ?>

        <!-- interswitch webpay form  -->

<form name="form1" action="<?=GATEWAYURL_LIVE?>" method="post">
    <input name="product_id" type="hidden" value="<?=PRODUCT_ID_LIVE?>" />
    <input name="pay_item_id" type="hidden" value="<?=PAYMENT_ITEM_ID_LIVE ?>" />
<input name="amount" type="hidden" value='<?= $amount_to_pass?>' />
<input name="currency" type="hidden" value="566" />
<input name="site_name" type="hidden" value="https://portal.claretianuniversity.edu.ng" />
<input name="site_redirect_url" type="hidden" value="<?=$baseUrl.'transactions/getwebpayinterswitch/'?>"/>
<input name="txn_ref" type="hidden" value="<?=$transaction->payref?>" />
<input name="cust_id" type="hidden" value="<?=$student->id?>" >
<input name="cust_name" type="hidden" value="<?= ucfirst($student->fname . ' ' . $student->lname) ?>" />
<?php
//$fluterWave_return_url = $baseUrl.'transactions/getpaymentstatusfluter/';
$return_url = $baseUrl.'transactions/getwebpayinterswitch/';
$payref = $transaction->payref;
//$amount = $total*100;
$product_id = PRODUCT_ID_LIVE;
$payment_item = PAYMENT_ITEM_ID_LIVE;
$string1  = $payref. PRODUCT_ID_LIVE.PAYMENT_ITEM_ID_LIVE. $amount_to_pass.$return_url.MACKEY_LIVE; 
  $hashed = hash("sha512", $string1 );
  $xml = Cake\Utility\Xml::build('<payment_item_detail>
        <item_details detail_ref="' . $payref . '" college="CUN" department="ITServices" faculty="Engineering"> 
            <item_detail item_id="1" item_name="Application fees1" item_amt="' . $netpro_split*100 . '" bank_id="7" acct_num="1019186840" /> 
            <item_detail item_id="2" item_name="Application fees2" item_amt="' . $cun_split*100 . '" bank_id="8" acct_num="2031912457" />
        </item_details> 
    </payment_item_detail>');
  //echo $xml->asXML(); 
//  
//  
//  $xml2 = Cake\Utility\Xml::build("<payment_item_detail>
//        <item_details detail_ref='$payref' institution='NetproInternationalLTD' sub_location='ITServices' location='Abuja'> 
//            <item_detail item_id='1' item_name='Court fees1' item_amt='$netpro_split' bank_id='7' acct_num='1019186840' /> 
//            <item_detail item_id='2' item_name='Items fees2' item_amt='$judiciary_split' bank_id='7' acct_num='1021435611' />
//        </item_details> 
//    </payment_item_detail>");
  
?>
<input name="hash" type="hidden" value="<?=$hashed?>" />
<input name="payment_params" type="hidden" value="college_split" />
<input name="xml_data" type="hidden" value='<?= $xml->asXML() ?>' />

                                         <?php if($transaction->paystatus=='completed'){?>
<!--                                     <button class="btn btn-white" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print</button>     -->
                                         <?php } else{  
                                             echo $this->Form->button('Pay Online(Interswitch)',['class'=>'btn btn-primary pull-right','title'=>'pay online with InterSwitch','style'=>'margin-right: 20px;']);
                                         } 
                                         
                                         ?>

                                   </form><br /> 
                                   <div class="col-sm-12">
                    &nbsp;  
<?php if($transaction->paystatus !='completed'){
    
echo $this->Html->image('interswitch.png', ['alt' => 'InterSwitch logo', 'class' => 'inv-logo pull-right']);

} ?>
     <?php if($transaction->paystatus !='completed'){?>
                     
                            <!-- link to pay stack online payment -->
<?=
$this->Html->link(__(' Pay Online (Paystack)'), ['controller' => 'Transactions', 'action' => 'gotopaystack', $student->user->username,
    $student->phone, $student->fname.' '.$student->lname, $transaction->amount, $student->id, $transaction->fee_id,
    $transaction->payref,$transaction->id], ['class' => 'btn btn-success float-left', 'title' => 'pay online with your ATM card'])
?>
                       
                    <!-- flutterwave payment -->
<!--<form>
  <script src="https://checkout.flutterwave.com/v3.js"></script>
  <button class="btn btn-success pull-left" title="Pay online with fluter wave" type="button" onClick='makePayment()'> Pay Online(Fluter Wave)</button>
</form>-->
     <?php } ?><br />                               
               
<br /><br /> <br /> <button class="btn btn-primary float-left" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print Slip</button>
                    

                </div>

      <script type="text/javascript">
  function makePayment() {
    FlutterwaveCheckout({
      public_key: "FLWPUBK_TEST-SANDBOXDEMOKEY-X",
      tx_ref: "<?=$payref ?>",
      amount: <?=$transaction->amount?>,
      currency: "NGN",
      payment_options: "card,ussd,qr,barter",
      redirect_url: // specified redirect URL
        "<?=$fluterWave_return_url?>",
      meta: {
        consumer_id: "<?=$student->id ?>",
        consumer_mac: "<?= $student->application_no ?>",
      },
      customer: {
        email: "<?= $student->user->username ?>",
        phonenumber: "<?= $student->phone ?>",
        name: "<?= ucwords($student->fname. ' ' . $student->mname . ' ' . $student->lname) ?>",
      },
      subaccounts: [
        {
          id: "RS_27DAA62C0AD9FBD1C553CB6EC4C1737F",
          transaction_split_ratio: 2,
          transaction_charge_type: "flat",
          transaction_charge: 1200
        },
       
      ],
      callback: function (data) {
        console.log(data);
      },
      customizations: {
        title: "Application Fee Payment",
        description: "Payment for post UTME Exams at UAES",
        logo: "https://assets.piedpiper.com/logo.png",
      },
    });
  }
</script>
                <br />  <br />  <br />
            </div>

        </div>
    </div>
</div></div>
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

