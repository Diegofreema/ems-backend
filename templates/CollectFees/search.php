<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Search Student</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Collect Fees', ['action' => 'index'], ['title' => 'Collect Fees']) ?></li>
                    <li class="breadcrumb-item active">Search Student</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Search for Student</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['url' => ['action' => 'search']]) ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Search Term</label>
                                <?= $this->Form->control('search_term', [
                                    'type' => 'text',
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter student name or registration number...',
                                    'value' => $searchTerm
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <?= $this->Form->button(__('Search'), [
                                        'type' => 'submit',
                                        'class' => 'btn btn-primary btn-block'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= $this->Form->end() ?>

                    <?php if (!empty($students)): ?>
                        <hr>
                        <h5>Search Results (<?= count($students) ?> found)</h5>
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Registration Number</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td>
                                                <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>
                                            </td>
                                            <td>
                                                <?= h($student->regno) ?>
                                            </td>
                                            <td>
                                                <?= h($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '')) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-success"><?= ucfirst($student->status) ?></span>
                                            </td>
                                            <td>
                                                <?= $this->Html->link(__('View Invoices'), 
                                                    ['action' => 'studentInvoices', $student->id], 
                                                    ['class' => 'btn btn-sm btn-info']
                                                ) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif (!empty($searchTerm)): ?>
                        <hr>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> No students found matching "<?= h($searchTerm) ?>".
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

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}
</style>
