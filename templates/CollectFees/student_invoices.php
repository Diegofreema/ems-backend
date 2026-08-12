<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Student Invoices</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Collect Fees', ['action' => 'index'], ['title' => 'Collect Fees']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Search Student', ['action' => 'search'], ['title' => 'Search Student']) ?></li>
                    <li class="breadcrumb-item active">Student Invoices</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Student Information -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Student Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Name:</strong> <?= h($student->fname . ' ' . $student->lname) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Registration Number:</strong> <?= h($student->regno) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Class:</strong> <?= h($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '')) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong> 
                            <span class="badge badge-success"><?= ucfirst($student->status) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Invoices Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Student Invoices</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($invoices)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Payment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td>
                                                <strong><?= h($invoice->invoiceid) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= h($invoice->fee->name) ?></span>
                                            </td>
                                            <td>
                                                <strong>₦<?= number_format($invoice->amount) ?></strong>
                                            </td>
                                          
                                            <td>
                                                <?php if ($invoice->paystatus === 'success'): ?>
                                                    <span class="badge badge-success">Paid</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Unpaid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $invoice->createdate->format('d M Y, H:i') ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($invoice->payday)): ?>
                                                    <?= date('d M Y, H:i', strtotime($invoice->payday)) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $this->Html->link(__('View Details'), 
                                                    ['action' => 'view', $invoice->id], 
                                                    ['class' => 'btn btn-sm btn-info']
                                                ) ?>
                                                <?php if ($invoice->paystatus === 'success' && !empty($invoice->transactions)): ?>
                                                    <?= $this->Html->link(__('Print Receipt'), 
                                                        ['action' => 'receipt', $invoice->id], 
                                                        ['class' => 'btn btn-sm btn-primary']
                                                    ) ?>
                                                <?php endif; ?>
                                                <?php if ($invoice->paystatus === 'Unpaid'): ?>
                                                    <?= $this->Html->link(__('Collect Fee'), 
                                                        ['action' => 'add', $invoice->id], 
                                                        ['class' => 'btn btn-sm btn-success']
                                                    ) ?>
                                                <?php endif; ?>                                                
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No invoices found for this student in the current session.
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
