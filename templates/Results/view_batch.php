<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$settings = $this->request->getSession()->read('settings');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Batch Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li> 
                    <li class="breadcrumb-item"><?= $this->Html->link(' Result Approvals', ['action' => 'pendingApproval']) ?></li>
                    <li class="breadcrumb-item active">Batch Details</li>
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
                        <i class="fa fa-list"></i> Student Results in Batch
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($batchResults->count() > 0): ?>
                        
                        <!-- Batch Summary -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-left-primary">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subject</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= h($batchResults->first()->subject->name) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-success">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Class</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= h($batchResults->first()->department->name) ?><?= !empty($batchResults->first()->class_arm) ? ' - ' . h($batchResults->first()->class_arm->arm_name) : '' ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Term</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= h($batchResults->first()->semester->name) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-warning">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Session</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= h($batchResults->first()->session->name) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Student Results Table -->
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batchResults as $index => $result): ?>
                                        <tr>
                                            <td>
                                                <strong><?= h($result->student->fname . ' ' . $result->student->lname) ?></strong>
                                            </td>
                                            <td><?= h($result->student->regno) ?></td>
                                            <td><?= $this->Number->format($result->ca ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->first_exam ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->second_exam ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->third_exam ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->total) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $result->grade === 'A' ? 'success' : ($result->grade === 'B' ? 'primary' : ($result->grade === 'C' ? 'info' : ($result->grade === 'D' ? 'warning' : ($result->grade === 'E' ? 'secondary' : 'danger')))) ?>">
                                                    <?= h($result->grade) ?>
                                                </span>
                                            </td>
                                            <td><?= h($result->remark) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Batch Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <div class="btn-group" role="group">
                                    <?= $this->Html->link(
                                        '<i class="fa fa-arrow-left"></i> Back to Pending Approvals',
                                        ['action' => 'pendingApproval'],
                                        ['class' => 'btn btn-secondary', 'escape' => false]
                                    ) ?>
                                    <a href="<?= $this->Url->build([
                                        'action' => 'approveBatch', 
                                        $subjectId, 
                                        $departmentId, 
                                        $semesterId, 
                                        $sessionId
                                    ]) ?>" 
                                       class="btn btn-success approve-batch-btn"
                                       data-count="<?= $batchResults->count() ?>"
                                       data-subject="<?= h($subject ? $subject->name : 'Unknown') ?>"
                                       data-class="<?= h($department ? $department->name : 'Unknown') ?><?= !empty($batchResults->first()->class_arm) ? ' - ' . h($batchResults->first()->class_arm->arm_name) : '' ?>"
                                       onclick="return confirmApprove(this)">
                                        <i class="fa fa-check"></i> Approve All (<?= $batchResults->count() ?> students)
                                    </a>
                                    <a href="<?= $this->Url->build([
                                        'action' => 'rejectBatch', 
                                        $subjectId, 
                                        $departmentId, 
                                        $semesterId, 
                                        $sessionId
                                    ]) ?>" 
                                       class="btn btn-danger reject-batch-btn"
                                       data-count="<?= $batchResults->count() ?>"
                                       data-subject="<?= h($subject ? $subject->name : 'Unknown') ?>"
                                       data-class="<?= h($department ? $department->name : 'Unknown') ?><?= !empty($batchResults->first()->class_arm) ? ' - ' . h($batchResults->first()->class_arm->arm_name) : '' ?>"
                                       onclick="return confirmReject(this)">
                                        <i class="fa fa-times"></i> Reject All (<?= $batchResults->count() ?> students)
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-muted">No Results Found</h5>
                            <p class="text-muted">This batch may have already been processed.</p>
                            <?= $this->Html->link('Back to Pending Approvals', ['action' => 'pendingApproval'], ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmApprove(element) {
    var subject = element.getAttribute('data-subject');
    var className = element.getAttribute('data-class');
    var count = element.getAttribute('data-count');
    
    var confirmMessage = 'Are you sure you want to APPROVE all ' + count + ' results for:\n\n' +
                       'Subject: ' + subject + '\n' +
                       'Class: ' + className + '\n\n' +
                       'This action will make these results visible to students and parents.';
    
    return confirm(confirmMessage);
}

function confirmReject(element) {
    var subject = element.getAttribute('data-subject');
    var className = element.getAttribute('data-class');
    var count = element.getAttribute('data-count');
    
    var confirmMessage = 'Are you sure you want to REJECT all ' + count + ' results for:\n\n' +
                       'Subject: ' + subject + '\n' +
                       'Class: ' + className + '\n\n' +
                       'This will require the teacher to re-upload corrected results.';
    
    return confirm(confirmMessage);
}
</script>

