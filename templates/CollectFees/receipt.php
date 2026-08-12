<?php 
$settings = $this->request->getSession()->read('settings');
use chillerlan\QRCode\{QRCode, QROptions};
?>

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Payment Receipt</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Collect Fees', ['action' => 'index'], ['title' => 'Collect Fees']) ?></li>
                    <li class="breadcrumb-item active">Payment Receipt</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <?php if (empty($transaction) || empty($invoice)): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> Error: Unable to load receipt data. Please try again.
            <br><br>
            <strong>Debug Info:</strong><br>
            Transaction: <?= isset($transaction) ? 'Available' : 'Missing' ?><br>
            Invoice: <?= isset($invoice) ? 'Available' : 'Missing' ?><br>
            Settings: <?= isset($settings) ? 'Available' : 'Missing' ?>
        </div>
    <?php else: ?>

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
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?=$settings->name?></strong><br />
                                <b style="font-size: 23px;"><?=$settings->address?><br />
                                <?=$settings->email?><br /></b>
                                <b style="font-size: 21px;">Fee Payment Receipt - <?= isset($transaction->fee->name) ? $transaction->fee->name : 'Fee Payment'?></b></h1>
                            <br />
                        </div>
                        <div class="col-sm-3 m-b-20">
                            <?php if (!empty($transaction->student->passporturl)): ?>
                                <?=$this->Html->image('../student_files/'.$transaction->student->passporturl, ['alt' => 'passport', 'class' => 'inv-logo float-right','height'=>130,'width'=>160])?>
                            <?php else: ?>
                                <div class="inv-logo float-right" style="height:130px;width:160px;background-color:#f8f9fc;border:1px solid #e3e6f0;display:flex;align-items:center;justify-content:center;">
                                    <span style="color:#6c757d;">No Photo</span>
                                </div>
                            <?php endif; ?>
                            <br /><br /><br />
                        </div>
                    </div>
                     
                    <div class="row">
                        <div class="col-sm-6 col-lg-7 col-xl-8 m-b-20">
                            <h5>Payment Receipt Issued to:</h5>
                            <ul class="list-unstyled">
                                <li><h5><strong>Name: <?= isset($transaction->student->fname) ? $transaction->student->fname.' '.$transaction->student->lname : 'N/A'?></strong></h5></li>
                                <li><span>Registration Number: <?= isset($transaction->student->regno) ? $transaction->student->regno : 'N/A' ?></span></li>
                                <li>Class: <?= isset($transaction->student->department->name) ? $transaction->student->department->name . (!empty($transaction->student->class_arm) ? ' - ' . $transaction->student->class_arm->arm_name : '') : 'N/A' ?></li>
                                <li>Session: <?= isset($transaction->session->name) ? $transaction->session->name : 'N/A' ?></li>
                                <?php if (!empty($transaction->student->phone)): ?>
                                    <li>Phone: <?= $transaction->student->phone ?></li>
                                <?php endif; ?>
                                <?php if (!empty($transaction->student->user->username)): ?>
                                    <li><a href="#">Email: <?= $transaction->student->user->username ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-5 col-xl-4 m-b-20">
                            <span class="text-muted">Payment Details:</span>
                            <ul class="list-unstyled invoice-payment-details">
                                <li>Transaction Ref: <b><?= $transaction->payref ?></b></li>
                                <li>Transaction Date: <?= date('D d M, Y h:i', strtotime($transaction->transdate)) ?></li>
                                <li><h5>Total Paid: <span class="text-right">₦<?= number_format($transaction->amount, 2) ?></span></h5></li>
                                <li>Fee: <span><?= isset($transaction->fee->name) ? $transaction->fee->name : 'N/A' ?></span></li>
                                <li>Payment Method: <span><strong><?= ucfirst(str_replace('_', ' ', $transaction->pgateway)) ?></strong></span></li>
                                <li>Payment Status: <span><strong><?php if($transaction->paystatus=='paid'){ echo '<span class="badge badge-success">' . h($transaction->paystatus).'</span>';}else{
                                    echo $transaction->paystatus;   
                                } ?></strong></span></li>
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
                                    <th>PAYMENT METHOD</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><?= isset($transaction->fee->name) ? $transaction->fee->name : 'Fee Payment' ?></td>
                                    <td class="d-none d-sm-table-cell"><?= $transaction->payref ?></td>
                                    <td>₦<?= number_format($transaction->amount, 2) ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', $transaction->pgateway)) ?></td>
                                    <td><?php if($transaction->paystatus=='paid'){ echo '<span class="badge badge-success">' . h($transaction->paystatus).'</span>';}else{
                                        echo $transaction->paystatus;   
                                    } ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <span style="margin-right: 20px; float: right;">
                        <?php 
                        try {
                            $studentName = isset($transaction->student->fname) ? $transaction->student->fname.' '.$transaction->student->lname : 'Student';
                            $feeName = isset($transaction->fee->name) ? $transaction->fee->name : 'Fee Payment';
                            
                            $data = $transaction->payref.' '.
                                    $studentName.
                                    ' '.$transaction->transdate.', '.number_format($transaction->amount,2). ', '.$feeName.', '.$transaction->paystatus.', '
                                    .$transaction->pgateway;
                            $qrcode = (new QRCode)->render($data);
                            printf('<img src="%s" alt="QR Code" width="100" height="100"/>', $qrcode);
                        } catch (Exception $e) {
                            echo '<div style="width:100px;height:100px;background-color:#f8f9fc;border:1px solid #e3e6f0;display:flex;align-items:center;justify-content:center;font-size:10px;color:#6c757d;">QR Code<br>Error</div>';
                        }
                        ?>
                    </span> 
                    
                    <div>
                        <br /><br /><br />
                        <div class="invoice-info">
                            <h5><b>Information</b></h5>
                            <p class="text-muted">
                                For assistance please call: <?= $settings->phone ?> or Mail: <?=$settings->email?>
                                <br><br>
                                <strong>This receipt serves as proof of payment for the above fee.</strong>
                                <br>
                                <strong>Please keep this receipt for your records.</strong>
                            </p>
                        </div>
                    </div>
                </div>
                
                <br /><br /><br />
                <div class="col-sm-12">
                    <input class="btn btn-primary float-left" type="button" onclick="printDiv('printableArea')" value="Print Receipt" />
                    <?= $this->Html->link(__('Back to Invoice'), 
                        ['action' => 'view', $invoice->id], 
                        ['class' => 'btn btn-secondary float-right']
                    ) ?>
                </div>
                <br /><br /><br />
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<!-- /Page Content -->

<script>
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>

<style>
@media print {
    .page-header, .breadcrumb, .btn {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        font-size: 12px;
    }
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.badge {
    font-size: 0.75rem;
}

.text-muted {
    color: #858796 !important;
}

.table th {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
}

.invoice-payment-details li {
    margin-bottom: 5px;
}
</style>
