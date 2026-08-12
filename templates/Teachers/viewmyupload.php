<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Result[]|\Cake\Collection\CollectionInterface $batchResults
 */
?>
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">My Upload Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' Results', ['controller' => 'Teachers', 'action' => 'uploadresults'], ['title' => 'Upload Results']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' My Uploads', ['action' => 'myuploads'], ['title' => 'My Uploads']) ?></li>
                    <li class="breadcrumb-item active"><?= h($subject ? $subject->name : 'Upload') ?> - <?= h($department ? $department->name : 'Details') ?></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-list"></i> Student Results in My Upload
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($batchResults->count() > 0): ?>
                        
                        <!-- Batch Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Subject
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <?= h($subject ? $subject->name : 'Unknown') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Class
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <?= h($department ? $department->name : 'Unknown') ?><?= !empty($batchResults->first()->class_arm) ? ' - ' . h($batchResults->first()->class_arm->arm_name) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Term
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <?= h($semester ? $semester->name : 'Unknown') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Session
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <?= h($session ? $session->name : 'Unknown') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Registration</th>
                                        <th>CA</th>
                                        <th>1st Exam</th>
                                        <th>2nd Exam</th>
                                        <th>3rd Exam</th>
                                        <th>Total</th>
                                        <th>Grade</th>
                                        <th>Remark</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batchResults as $result): ?>
                                        <tr>
                                            <td><?= h($result->student->fname . ' ' . $result->student->lname) ?></td>
                                            <td><?= h($result->student->regno) ?></td>
                                            <td><?= $this->Number->format($result->ca ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->first_exam ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->second_exam ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->third_exam ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->total) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $result->grade == 'A' ? 'success' : ($result->grade == 'B' ? 'primary' : ($result->grade == 'C' ? 'info' : ($result->grade == 'D' ? 'warning' : ($result->grade == 'E' ? 'secondary' : 'danger')))) ?>">
                                                    <?= h($result->grade) ?>
                                                </span>
                                            </td>
                                            <td><?= h($result->remark) ?></td>
                                            <td>
                                                <?php if ($result->approval_status == 'approved'): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-check"></i> Approved
                                                    </span>
                                                <?php elseif ($result->approval_status == 'rejected'): ?>
                                                    <span class="badge badge-danger">
                                                        <i class="fa fa-times"></i> Rejected
                                                    </span>
                                                    <?php if (!empty($result->rejection_reason)): ?>
                                                        <br><small class="text-muted"><?= h($result->rejection_reason) ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">
                                                        <i class="fa fa-clock-o"></i> Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Back Button -->
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <?= $this->Html->link(
                                    '<i class="fa fa-arrow-left"></i> Back to My Uploads',
                                    ['action' => 'myuploads'],
                                    ['class' => 'btn btn-secondary', 'escape' => false]
                                ) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-muted">No Results Found</h5>
                            <p class="text-muted">This upload may have been deleted or you don't have access to it.</p>
                            <?= $this->Html->link('Back to My Uploads', ['action' => 'myuploads'], ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]], // Sort by student name
        "columnDefs": [
            { "orderable": false, "targets": [] } // All columns are sortable
        ]
    });
});
</script>
