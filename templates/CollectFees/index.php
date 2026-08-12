<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Collect Fees</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item active">Collect Fees</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Unpaid</h5>
                    <h3 class="text-white"><?= $totalUnpaid ?></h3>
                    <p class="text-white">Invoices</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Total Paid</h5>
                    <h3 class="text-white"><?= $totalPaid ?></h3>
                    <p class="text-white">Invoices</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Outstanding Amount</h5>
                    <h3 class="text-white">₦<?= number_format($totalAmount->total ?? 0) ?></h3>
                    <p class="text-white">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Quick Actions</h5>
                    <div class="mt-2">
                        <?= $this->Html->link('Search Student', ['action' => 'search'], ['class' => 'btn btn-light btn-sm']) ?>
                        <?= $this->Html->link('View Reports', ['action' => 'reports'], ['class' => 'btn btn-light btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unpaid Invoices Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Unpaid Invoices</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($invoices)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Created Date</th>
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
                                                <strong><?= h($invoice->student->fname . ' ' . $invoice->student->lname) ?></strong>
                                                <br><small class="text-muted"><?= h($invoice->student->regno) ?></small>
                                            </td>
                                            <td>
                                                <?= h($invoice->student->department->name . (!empty($invoice->student->class_arm) ? ' - ' . $invoice->student->class_arm->arm_name : '')) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= h($invoice->fee->name) ?></span>
                                            </td>
                                            <td>
                                                <strong>₦<?= number_format($invoice->amount) ?></strong>
                                            </td>
                                            <td>
                                                <?= $invoice->createdate->format('d M Y, H:i') ?>
                                            </td>
                                            <td>
                                                <?= $this->Html->link(__('Collect Payment'), 
                                                    ['action' => 'add', $invoice->id], 
                                                    ['class' => 'btn btn-sm btn-success']
                                                ) ?>
                                                <?= $this->Html->link(__('View Details'), 
                                                    ['action' => 'view', $invoice->id], 
                                                    ['class' => 'btn btn-sm btn-info']
                                                ) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="dataTables_info">
                                    Showing <?= $this->Paginator->counter('{{start}} to {{end}} of {{count}} entries') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end">
                                        <?= $this->Paginator->prev('« Previous') ?>
                                        <?= $this->Paginator->numbers() ?>
                                        <?= $this->Paginator->next('Next »') ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No unpaid invoices found for the current session.
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
