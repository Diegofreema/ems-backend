<?php use chillerlan\QRCode\{QRCode, QROptions};
    $userdata = $this->request->getSession()->read('usersinfo');
    $userrole = $this->request->getSession()->read('usersroles');
    $settings = $this->request->getSession()->read('settings');
    ?> 

<div class="container">
<div class="content container-fluid">

    <br /><br />

    <div class="row" id="printableArea">

        <div class="col-md-12">
            <div class="card"><br /><br /><br />
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 m-b-20">
                        <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100])?>

                            <br /><br /><br />


                        </div>
                        <div class="col-sm-6 m-b-20 text-center">


                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?= ucwords($settings->name) ?></strong><br />
                                <b style="font-size: 23px;">  <?= ucwords($settings->address) ?><br />
                                    <?= $settings->email ?><br /> <?= $settings->phone ?><br /></b>

                                <b style="font-size: 21px;"> Fee Payment Receipt - Application Fee </b></h1>

                            <br />    </div>
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right', 'width' => 160]) ?>

                            <br /><br /><br />


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <span class="text-muted">Invoice to:</span>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?= ucwords($student->fname. ' ' . $student->lname . ' ' . $student->mname) ?></strong></h5></li>
                                <li><span>Address: <?= ucwords($student->address) ?></span></li>
                                <li>State: <?= $student->state->name ?></li>
                                <li>Phone: <?= $student->phone ?></li>
                                <li><a href="#">Email: <?= $student->user->username ?></a></li>
                                <li>Class : <?= $student->department->name ?><br /></li>
                                <li>Application Date : <?= date('D d M Y H:i', strtotime($student->joindate)) ?><br /></li>
                                <li>Application Number: <?= $student->application_no ?> <br /></li>
                                <li>Academic Session : <?= $settings->session->name ?><br /></li>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li>Ref :<b> <span class="text-right"><?= $transaction->payref ?> </span></b></li>
                                <li> Application No :<b> <span class="text-right"> <?= $student->application_no ?> </span></b></li>
                                <li> Transaction Date : <span class="text-right"><?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?> </span></li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($transaction->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?= $transaction->fee->name ?></span></li>
                                <li>Pay Status: <span><B><?php
                                            if ($transaction->paystatus == 'completed') {
                                                echo '<span class="badge badge-success">' . h($transaction->paystatus) . '</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">' .$transaction->paystatus. '</span>';
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
                                    <td>
                                      <?php  if ($transaction->paystatus == 'completed') {
                                                echo '<span class="badge badge-success">' . h($transaction->paystatus) . '</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">' .$transaction->paystatus. '</span>';
                                            }
                                            ?>
                                    </td>

                                </tr>




                            </tbody>
                        </table>
                    </div>
                   <span style="margin-right: 20px; float: right;">
                        <?php 
         $data = $transaction->payref.' '.
               $student->fname. ' ' . $student->lname . ' ' . $student->mname.
                ' '.$transaction->transdate.', '.number_format($transaction->amount,2). ', '.$transaction->fee->name.', '.$transaction->paystatus.', '
                .$student->phone.', '.$student->user->username;
        $qrcode = (new QRCode)->render($data);
        printf('<img src="%s" alt="QR Code" width="100" height="100"/>', $qrcode);
                        
                         ?>
</span> 
                    <div>
                        <br />  <br />  <br />
                       
                    </div>
                </div>
               

                <br /><br /><br />
                <div class="col-sm-12">

                    <input class="btn btn-primary float-left" type="button" onclick="printDiv('printableArea')" value="Print Receipt" />

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

