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
                                    
                                    <?= $settings->email ?><br /><?= $settings->phone ?><br /></b>

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
                                <li>JAMB Reg No: <?= $student->jambregno ?></li>
                                <li>JAMB Score: <?= $student->jamb ?></li>
                                <li>State: <?= $student->state->name ?></li>
                                <li>LGA: <?= $student->lga->name ?></li>
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
                                <li> Registration Number :<b> <span class="text-right"> <?= $student->regno ?> </span></b></li>
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
                                    <td><?= $transaction->fee->name ?></td>
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
                            <h5><b>Information</b></h5>
                            <p class="text-muted">Please click on the green button below to make payment using your card. <br />
                                Alternatively, print this invoice and visit any commercial bank and make a payment in
                                favour of Claretian University MaryLand Nekede Imo State via InterSwitch PayDirect Platform.<br />
<?php if ($transaction->paystatus != 'completed') { ?>
                                    <br />For assistance please call or WhatsApp: <?= $settings->phone ?> or Mail: <?= $settings->email ?>
                <?php } ?>

                            </p>
                        </div>
                    </div>
                </div>
    

        <!-- interswitch webpay form  -->

<form name="form1">
 



                                         <?php if($transaction->paystatus=='completed'){?>
<!--                                     <button class="btn btn-white" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print</button>     -->
                                         <?php } else{  
                                             echo $this->Html->link(__(' Pay Online (Paystack)'), ['controller' => 'Students', 'action' => 'gotopaystackotherfees', $student->id,
    $transaction->id, $transaction->invoice_id], ['class' => 'btn btn-success float-right', 'title' => 'pay online with your ATM card','style'=>'margin-right: 15px;']);
                                         } 
                                         
                                         ?>
                                  
                                   </form><br /> 
                                   <div class="col-sm-12">
                    &nbsp;  

    
  <button class="btn btn-primary float-left" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print Slip</button>
                    

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
        description: "Payment for Transcript Services",
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

