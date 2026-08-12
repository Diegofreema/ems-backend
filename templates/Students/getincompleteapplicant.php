<script src="https://remitademo.net/payment/v1/remita-pay-inline.bundle.js"></script>
<?php $settings = $this->request->getSession()->read('settings') ?>
<!-- Page Content -->
<div class="container">
<div class="content container-fluid">

    <div class="row" id="printableArea">

        <div class="col-md-12">
            <?php if(!empty($student->id)) { ?>
            <div class="card"><br /><br />
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image($settings->logo, ['alt' => 'BUSCED', 'class' => 'inv-logo']) ?>

                            <br /><br /><br />


                        </div>
                        <div class="col-sm-6 m-b-20 text-center">


                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?= $settings->name ?></strong><br />
                                <b style="font-size: 23px;">  <?= $settings->address ?><br />
                                    <?= $settings->email ?><br /><?= $settings->phone ?><br /></b>

                                <b style="font-size: 21px;"> Fee Payment Invoice- <?= $transaction->fee->name ?> </b></h1>

                            <br />    </div>
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right', 'height' => 130, 'width' => 160]) ?>

                            <br /><br /><br />


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <h5>Invoice to:</h5>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?= $student->fname . ' ' . $student->mname.' '.$student->lname ?></strong></h5></li>
                               <li><span>Application No: <?= $student->application_no ?></span></li>
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
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li> Transaction Ref :<b> <?= $transaction->payref ?> </b></li>
                                <li> Transaction Date : <?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?> </li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($transaction->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?= $transaction->fee->name ?></span></li>
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
                                    <td><?= $transaction->paystatus?></td>

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
                            <p class="text-muted">Please click on the yellow button below to make payment. All applications without application fee payment will not be processed<br />
<?php if ($transaction->gresponse != 'success') { ?>
                                    For assistance please call/WhatsApp: <?=$settings->phone?> or Mail: <?=$settings->email?>
                <?php } ?>

                            </p>
                        </div>
                    </div>
                </div>

                <br /><br /><br />
                
                 <!-- interswitch webpay form  -->

                                

                <br />  <br />  <br />
            </div>
            <?php  }
            
            else{?>
            
            <div class="card" style="padding-left: 20px;"><br /><br /><br />
                <div class="row">
                        
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image($settings->logo, ['alt' => 'BUSCED', 'class' => 'inv-logo', 'height' => 150]) ?>

                            <br /><br /><br />


                        </div>
                        <div class="col-sm-6 m-b-20 text-center">


                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?= $settings->name ?></strong><br />
                                <b style="font-size: 23px;">  <?= $settings->address ?><br />
                                    <?= $settings->email ?><br /></b>

                            <br />   
                       
                        </div>
                        

                    </div>
                <div class="card-body">
                    <div >
         <?= $this->Form->create(null,['url'=>['controller'=>'Students','action'=>'getincompleteapplicant']]) ?>
          <div class="form-group row">
                                 <div class="col-md-12 mb-3 mb-sm-0">
<?= $this->Form->control('application_no', ['label' => false, 'placeholder' => ' Enter your application number to retrieve your application and payment details',
      'class' => 'form-control form-control-user2', 'required','id'=>'application_id'])
?>
                                </div>
  </div>
          
       <br />
          <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-lg']) ?>
<?= $this->Form->end() ?> <br /> <br />
      </div>   </div>   </div>
 <br /> <br />
            <?php  }?>
        </div>
    </div> 
</div>
    </div>
<!-- /Page Content -->

<script type="text/javascript">
  function makePayment() {
  var form = document.querySelector("#payment-form");
  var paymentEngine = RmPaymentEngine.init({
  key:"QzAwMDAyNzEyNTl8MTEwNjE4NjF8OWZjOWYwNmMyZDk3MDRhYWM3YThiOThlNTNjZTE3ZjYxOTY5NDdmZWE1YzU3NDc0ZjE2ZDZjNTg1YWYxNWY3NWM4ZjMzNzZhNjNhZWZlOWQwNmJhNTFkMjIxYTRiMjYzZDkzNGQ3NTUxNDIxYWNlOGY4ZWEyODY3ZjlhNGUwYTY=",
  processRrr: true,
  transactionId: <?=$transaction->payref  ?>, // Replace with a reference you generated or remove the entire field for us to auto-generate a reference for you. Note that you will be able to check the status of this transaction using this transaction Id
extendedData: {
    customFields: [
       {
          name: "rrr",
          value: form.querySelector('input[name="rrr"]').value
       }
     ]
  },
   onSuccess: function (response) {
               console.log('callback Successful Response', response);
 if(response)
 location.href = "<?=$responseurl?>";
           },
    onError: function (response) {
        console.log('callback Error Response', response);
    },
    onClose: function () {
        console.log("closed");
    }
  });
 paymentEngine.showPaymentWidget();
  }
  window.onload = function () {
  setDemoData();
  };
</script>
            
<script>

    function printDiv(divName) { //alert('am called');
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;

    }

</script>

