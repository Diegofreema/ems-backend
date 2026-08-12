<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Invoice Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Collect Fees', ['action' => 'index'], ['title' => 'Collect Fees']) ?></li>
                    <li class="breadcrumb-item active">Invoice Details</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Invoice Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Invoice ID:</strong></td>
                                    <td><?= h($invoice->invoiceid) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Student Name:</strong></td>
                                    <td><?= h($invoice->student->fname . ' ' . $invoice->student->lname) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Number:</strong></td>
                                    <td><?= h($invoice->student->regno) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Class:</strong></td>
                                    <td><?= h($invoice->student->department->name . (!empty($invoice->student->class_arm) ? ' - ' . $invoice->student->class_arm->arm_name : '')) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Fee Type:</strong></td>
                                    <td><?= h($invoice->fee->name) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td><strong>₦<?= number_format($invoice->amount) ?></strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php if ($invoice->paystatus === 'success'): ?>
                                            <span class="badge badge-success">Paid</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date:</strong></td>
                                    <td><?= $invoice->createdate->format('d M Y, H:i') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($invoice->paystatus === 'paid' && !empty($invoice->payday)): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <h6><i class="fa fa-check-circle"></i> Payment Information:</h6>
                                    <p class="mb-0">
                                        <strong>Payment Date:</strong> <?= date('d M Y, H:i', strtotime($invoice->payday)) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Actions</h4>
                </div>
                <div class="card-body">
                    <?php if ($invoice->paystatus === 'unpaid'): ?>
                        <?= $this->Html->link(__('Collect Payment'), 
                            ['action' => 'add', $invoice->id], 
                            ['class' => 'btn btn-success btn-block mb-2']
                        ) ?>
                    <?php endif; ?>
                    
                    <?php if ($invoice->paystatus === 'success' && !empty($invoice->transactions)): ?>
                        <?= $this->Html->link(__('Print Receipt'), 
                            ['action' => 'receipt', $invoice->id], 
                            ['class' => 'btn btn-primary btn-block mb-2']
                        ) ?>
                    <?php endif; ?>
                    
                    <?= $this->Html->link(__('Back to List'), 
                        ['action' => 'index'], 
                        ['class' => 'btn btn-secondary btn-block mb-2']
                    ) ?>
                    
                    <?= $this->Html->link(__('Search Student'), 
                        ['action' => 'search'], 
                        ['class' => 'btn btn-info btn-block']
                    ) ?>
                </div>
            </div>

            <?php if (!empty($invoice->transactions)): ?>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payment History</h4>
                    </div>
                    <div class="card-body">
                        <?php foreach ($invoice->transactions as $transaction): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Amount:</small><br>
                                        <strong>₦<?= number_format($transaction->amount) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Date:</small><br>
                                        <?= $transaction->transdate->format('d M Y, H:i') ?>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12">
                                        <small class="text-muted">Method:</small>
                                        <span class="badge badge-info"><?= ucfirst(str_replace('_', ' ', $transaction->pgateway)) ?></span>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12">
                                        <small class="text-muted">Reference:</small><br>
                                        <small><?= h($transaction->payref) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
}

.badge {
    font-size: 0.75rem;
}

.text-muted {
    color: #858796 !important;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
</style>
