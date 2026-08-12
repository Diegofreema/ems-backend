<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Record Payment</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Collect Fees', ['action' => 'index'], ['title' => 'Collect Fees']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Student Invoices', ['action' => 'studentInvoices', $invoice->student_id], ['title' => 'Back to Student Invoices']) ?></li>
                    <li class="breadcrumb-item active">Record Payment</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Information</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['url' => ['action' => 'add', $invoice->id]]) ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Student Name</label>
                                <input type="text" class="form-control" value="<?= h($invoice->student->fname . ' ' . $invoice->student->lname) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Registration Number</label>
                                <input type="text" class="form-control" value="<?= h($invoice->student->regno) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Class</label>
                                <input type="text" class="form-control" value="<?= h($invoice->student->department->name . (!empty($invoice->student->class_arm) ? ' - ' . $invoice->student->class_arm->arm_name : '')) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fee Type</label>
                                <input type="text" class="form-control" value="<?= h($invoice->fee->name) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Invoice Amount</label>
                                <input type="text" class="form-control" value="₦<?= number_format($invoice->amount) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Discount <span class="text-danger">*</span></label>
                                <?= $this->Form->control('discount', [
                                    'type' => 'number',
                                    'label' => false,
                                    'class' => 'form-control',
                                    'required' => true,
                                    'min' => 0,
                                    'max' => $invoice->amount/2,
                                    'step' => 1,
                                    'placeholder' => 'Enter discount amount'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Amount Paid<span class="text-danger">*</span></label>
                                <?= $this->Form->control('amount', [
                                    'type' => 'number',
                                    'label' => false,
                                    'class' => 'form-control',
                                    'required' => true,
                                    'min' => 1,
                                    'max' => $invoice->amount,
                                    'step' => 1,
                                    'placeholder' => 'Enter payment amount'
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method <span class="text-danger">*</span></label>
                                <?= $this->Form->control('payment_method', [
                                    'type' => 'select',
                                    'class' => 'form-control',
                                    'required' => true,
                                    'label' => false,
                                    'options' => [
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank Transfer'
                                    ],
                                    'empty' => 'Select payment method'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <input type="text" class="form-control" value="<?= date('d M Y, H:i') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <?= $this->Form->control('notes', [
                            'type' => 'textarea',
                            'class' => 'form-control',
                            'label' => false,
                            'rows' => 3,
                            'placeholder' => 'Add any additional notes about this payment...'
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <?= $this->Form->button(__('Record Payment'), [
                            'type' => 'submit',
                            'class' => 'btn btn-success btn-lg'
                        ]) ?>
                        <?= $this->Html->link(__('Cancel'), 
                            ['action' => 'studentInvoices', $invoice->student_id], 
                            ['class' => 'btn btn-secondary btn-lg']
                        ) ?>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Invoice Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Invoice ID:</strong></td>
                            <td><?= h($invoice->invoiceid) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Created Date:</strong></td>
                            <td><?= $invoice->createdate->format('d M Y, H:i') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Session:</strong></td>
                            <td><?= h($invoice->session->name) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge badge-warning"><?= ucfirst($invoice->paystatus) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Instructions</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fa fa-info-circle"></i> Important Notes:</h6>
                        <ul class="mb-0">
                            <li>Payment amount cannot exceed invoice amount</li>
                            <li>Cash payments are recorded immediately</li>
                            <li>Bank transfers require verification</li>
                            <li>All payments are logged with admin details</li>
                        </ul>
                    </div>
                </div>
            </div>
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

.form-control:read-only {
    background-color: #f8f9fc;
}

.text-danger {
    color: #e74a3b !important;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>
