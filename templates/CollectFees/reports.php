<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Payment Reports</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Collect Fees', ['action' => 'index'], ['title' => 'Collect Fees']) ?></li>
                    <li class="breadcrumb-item active">Payment Reports</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Filter Form -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Reports</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['url' => ['action' => 'reports'], 'method' => 'get']) ?>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <?= $this->Form->control('start_date', [
                                    'type' => 'date',
                                    'class' => 'form-control',
                                    'value' => $startDate
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <?= $this->Form->control('end_date', [
                                    'type' => 'date',
                                    'class' => 'form-control',
                                    'value' => $endDate
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <?= $this->Form->control('payment_method', [
                                    'type' => 'select',
                                    'class' => 'form-control',
                                    'options' => [
                                        '' => 'All Methods',
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank Transfer'
                                    ],
                                    'value' => $paymentMethod
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Payments</h5>
                    <h3 class="text-white">₦<?= number_format($totalAmount->total ?? 0) ?></h3>
                    <p class="text-white">Amount Collected</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Cash Payments</h5>
                    <h3 class="text-white">₦<?= number_format($cashTotal->total ?? 0) ?></h3>
                    <p class="text-white">Cash Collected</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Bank Transfers</h5>
                    <h3 class="text-white">₦<?= number_format($bankTransferTotal->total ?? 0) ?></h3>
                    <p class="text-white">Bank Transfers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Transactions</h5>
                    <h3 class="text-white"><?= count($payments) ?></h3>
                    <p class="text-white">Payment Records</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Records</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($payments)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td>
                                                <?= $payment->transdate->format('d M Y, H:i') ?>
                                            </td>
                                            <td>
                                                <strong><?= h($payment->student->fname . ' ' . $payment->student->lname) ?></strong>
                                                <br><small class="text-muted"><?= h($payment->student->regno) ?></small>
                                            </td>
                                            <td>
                                                <?= h($payment->student->department->name . (!empty($payment->student->class_arm) ? ' - ' . $payment->student->class_arm->arm_name : '')) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= h($payment->fee->name) ?></span>
                                            </td>
                                            <td>
                                                <strong>₦<?= number_format($payment->amount) ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($payment->pgateway === 'cash'): ?>
                                                    <span class="badge badge-success">Cash</span>
                                                <?php elseif ($payment->pgateway === 'bank_transfer'): ?>
                                                    <span class="badge badge-info">Bank Transfer</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?= ucfirst(str_replace('_', ' ', $payment->pgateway)) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?= h($payment->payref) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No payment records found for the selected criteria.
                        </div>
                    <?php endif; ?>
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
</style>
