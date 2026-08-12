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

                                <b style="font-size: 21px;"> <br />Fee Payment Invoice </b></h1>

                            <br />    </div>
                        <div class="col-sm-3 m-b-20">
                            

                            <br /><br /><br />


                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <span class="text-muted">Invoice to:</span>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?= ucwords($sponsorshippayment->sponsorship->sponsor->name) ?></strong></h5></li>
                                <li><span>Address: <?= ucwords($sponsorshippayment->sponsorship->sponsor->address) ?></span></li>
                               
                                <li>Phone: <?= $sponsorshippayment->sponsorship->sponsor->phone ?></li>
                                <li><a href="#">Email: <?= $sponsorshippayment->sponsorship->sponsor->emailaddress ?></a></li>
                               
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li>Ref :<b> <span class="text-right"><?= $sponsorshippayment->sref ?> </span></b></li>
                               
                                <li> Transaction Date : <span class="text-right"><?= date('D d M, Y h:i', strtotime($sponsorshippayment->datecreated)) ?> </span></li>
                                <li><h5>Total Due: <span class="text-right">₦<?= number_format($sponsorshippayment->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?php if($sponsorshippayment->isictfee=="Yes"){echo "ICT Fee ";}
                                    else{ echo "Tuition Fee ";} ?></span></li>
                                <li>Pay Status: <span><B><?php
                                            if ($sponsorshippayment->paystatus == 'completed') {
                                                echo '<span class="badge badge-success">' . h($sponsorshippayment->paystatus) . '</span>';
                                            } else {
                                                echo $sponsorshippayment->paystatus;
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
                                    <td><?php if($sponsorshippayment->isictfee=="Yes"){echo "ICT Fee for : ".$sponsorshippayment->sponsorship->noofstudents .' Student(s)';}
                                    else{ echo "Tuition for ".$sponsorshippayment->sponsorship->noofstudents." Student(s)";} ?>
                                        </td>
                                    <td class="d-none d-sm-table-cell"><?= $sponsorshippayment->sref ?></td>
                                    <td>₦<?= number_format($sponsorshippayment->amount, 2) ?></td>
                                    <td><?php
                                            if ($sponsorshippayment->paystatus == 'completed') {
                                                echo '<span class="badge badge-success">' . h($sponsorshippayment->paystatus) . '</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">' . h($sponsorshippayment->paystatus) . '</span>';
                                            }
                                            ?></td>

                                </tr>




                            </tbody>
                        </table>
                    </div>
                    <span style="margin-right: 20px; float: right;"> <?=
                                            $this->Qr->text($sponsorshippayment->sref . ' ' .
                                                   $sponsorshippayment->sponsorship->sponsor->name .
                                                    ' ' . $sponsorshippayment->datecreated . ', ' . number_format($sponsorshippayment->amount, 2) . ', ' . $sponsorshippayment->paystatus . ', '
                                                    . $sponsorshippayment->sponsorship->sponsor->phone . ', ' . $sponsorshippayment->sponsorship->sponsor->emailaddress);
                                            ?></span> 
                    <div>
                        <br />  <br />  <br />
                        <div class="invoice-info">
                            <h5><b>Pay To</b></h5>
                            <?php if($sponsorshippayment->isictfee == 'Yes'){?>
                                <p class="text-muted">Account number: 1019186840 <br />
                                Bank: United Bank For Africa  <br />
                                Account name: NETPRO INTL NIG LTD-ACCT 2<br />

                            </p>
                                
                           <?php } elseif($sponsorshippayment->isictfee =='No'){?>          
                            <p class="text-muted">Account number: 2040566953 <br />
                                Bank: First Bank of Nigeria <br />
                                Account name: CLARETIAN UNIVERSITY OF NIGERIA NEKEDE IMO STATE <br />

                            </p>
                                    <?php } ?>
                        </div>
                    </div>
                </div>
          
<br /><br /> <div class="col-sm-6 m-b-20 text-center"> 
    <button class="btn btn-primary float-left" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print Slip</button>
                    

                <br />  <br /> 
                 </div>
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

