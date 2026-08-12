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
                <h3 class="page-title">Pending Result Approvals</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li> 
                    <li class="breadcrumb-item active">Result Approvals</li>
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
                        <i class="fa fa-clock-o"></i> Results Pending Approval (Grouped by Subject/Class)
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($groupedBatches)): ?>
                        <div class="row">
                            <?php foreach ($groupedBatches as $batchKey => $batch): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        <?= h($batch['subject']->name) ?>
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        <?= h($batch['department']->name) ?><?= !empty($batch['class_arm']) ? ' - ' . h($batch['class_arm']->arm_name) : '' ?> - <?= h($batch['semester']->name) ?>
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        Session: <?= h($batch['session']->name) ?>
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        <i class="fa fa-users"></i> <?= $batch['student_count'] ?> students
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        <i class="fa fa-user"></i> Uploaded by: <?= h($batch['uploaded_by']->fname . ' ' . $batch['uploaded_by']->lname) ?>
                                                    </div>
                                                    <div class="text-xs text-muted">
                                                        <i class="fa fa-calendar"></i> <?= $batch['upload_date']->format('d M Y, H:i') ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="btn-group-vertical" role="group">
                                                        <?= $this->Html->link(
                                                            '<i class="fa fa-eye"></i> View Batch',
                                                            [
                                                                'action' => 'viewBatch', 
                                                                $batch['subject']->id, 
                                                                $batch['department']->id, 
                                                                $batch['semester']->id, 
                                                                $batch['session']->id
                                                            ],
                                                            ['class' => 'btn btn-sm btn-info mb-1', 'escape' => false, 'title' => 'View All Students in Batch']
                                                        ) ?>
                                                        <a href="<?= $this->Url->build([
                                                            'action' => 'approveBatch', 
                                                            $batch['subject']->id, 
                                                            $batch['department']->id, 
                                                            $batch['semester']->id, 
                                                            $batch['session']->id
                                                        ]) ?>" 
                                                           class="btn btn-sm btn-success mb-1 approve-batch-btn" 
                                                           title="Approve All Students in Batch"
                                                           data-subject="<?= h($batch['subject']->name) ?>"
                                                           data-class="<?= h($batch['department']->name) ?><?= !empty($batch['class_arm']) ? ' - ' . h($batch['class_arm']->arm_name) : '' ?>"
                                                           data-count="<?= $batch['student_count'] ?>"
                                                           onclick="return confirmApprove(this)">
                                                            <i class="fa fa-check"></i> Approve All
                                                        </a>
                                                        <a href="<?= $this->Url->build([
                                                            'action' => 'rejectBatch', 
                                                            $batch['subject']->id, 
                                                            $batch['department']->id, 
                                                            $batch['semester']->id, 
                                                            $batch['session']->id
                                                        ]) ?>" 
                                                           class="btn btn-sm btn-danger reject-batch-btn" 
                                                           title="Reject All Students in Batch"
                                                           data-subject="<?= h($batch['subject']->name) ?>"
                                                           data-class="<?= h($batch['department']->name) ?><?= !empty($batch['class_arm']) ? ' - ' . h($batch['class_arm']->arm_name) : '' ?>"
                                                           data-count="<?= $batch['student_count'] ?>"
                                                           onclick="return confirmReject(this)">
                                                            <i class="fa fa-times"></i> Reject All
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">No Pending Approvals</h5>
                            <p class="text-muted">All results have been processed.</p>
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

