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

                                <b style="font-size: 21px;"> Fee Payment Invoice- <?= $transaction->fee->name ?> </b></h1>

                            <br />    </div>
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image('../student_files/'.$transaction->student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right', 'height' => 130, 'width' => 160]) ?>

                            <br /><br /><br />


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <span class="text-muted">Invoice to:</span>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?= ucwords($transaction->student->fname. ' ' . $transaction->student->lname.' '.$transaction->student->mname) ?></strong></h5></li>
                                <li><span>Address: <?= ucwords($transaction->student->address) ?></span></li>
                                <li>JAMB Reg No: <?= $transaction->student->jambregno ?></li>
                                <li>JAMB Score: <?= $transaction->student->jamb ?></li>
                                <li>State: <?= $transaction->student->state->name ?></li>
                                <li>LGA: <?= $transaction->student->lga->name ?></li>
                             
                                <li>Phone: <?= $transaction->student->phone ?></li>
                                <li><a href="#">Email: <?= $transaction->student->user->username ?></a></li>
                               <li>Faculty: <?= $transaction->student->faculty->name ?></li>
                                <li>Department: <?= $transaction->student->department->name ?></li>
                                <li>Program: <?= $transaction->student->programme->name ?></li>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li>Ref :<b> <span class="text-right"><?= $transaction->payref ?> </span></b></li>
                                <li> Registration Number :<b> <span class="text-right"> <?= $transaction->student->regno ?> </span></b></li>
                                <li> Transaction Date : <span class="text-right"><?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?> </span></li>
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
                                                    $transaction->student->fname . ' ' . $transaction->student->lname .
                                                    ' ' . $transaction->transdate . ', ' . number_format($transaction->amount, 2) . ', ' . $transaction->paystatus . ', '
                                                    . $transaction->student->phone . ', ' . $transaction->student->user->username);
                                            ?></span> 
                 
                </div>
                         

                                   <div class="col-sm-12">
                           
               
<button class="btn btn-primary float-left" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print</button>
                    

                </div>

     
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

