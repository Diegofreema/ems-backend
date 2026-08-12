<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Result[]|\Cake\Collection\CollectionInterface $groupedUploads
 */
?>
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">My Result Uploads</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' Results', ['controller' => 'Teachers', 'action' => 'uploadresults'], ['title' => 'Upload Results']) ?></li>
                    <li class="breadcrumb-item active">My Uploads</li>
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
                        <i class="fa fa-upload"></i> My Result Uploads Status
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($groupedUploads)): ?>
                        <div class="row">
                            <?php foreach ($groupedUploads as $batchKey => $batch): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-<?= $batch['approval_status'] == 'approved' ? 'success' : ($batch['approval_status'] == 'rejected' ? 'danger' : 'warning') ?> shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-<?= $batch['approval_status'] == 'approved' ? 'success' : ($batch['approval_status'] == 'rejected' ? 'danger' : 'warning') ?> text-uppercase mb-1">
                                                        <?= ucfirst($batch['approval_status']) ?>
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        <?= h($batch['subject']->name) ?>
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        <?= h($batch['department']->name) ?><?= !empty($batch['class_arm']) ? ' - ' . h($batch['class_arm']->arm_name) : '' ?> - <?= h($batch['semester']->name) ?>
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        Session: <?= h($batch['session']->name) ?>
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        <i class="fa fa-users"></i> <?= $batch['student_count'] ?> students
                                                    </div>
                                                    <div class="text-xs text-muted mb-2">
                                                        <i class="fa fa-calendar"></i> Uploaded: <?= $batch['upload_date']->format('d M Y, H:i') ?>
                                                    </div>
                                                    
                                                    <?php if ($batch['approval_status'] == 'approved'): ?>
                                                        <div class="text-xs text-success mb-2">
                                                            <i class="fa fa-check-circle"></i> Approved on: <?= $batch['approved_at']->format('d M Y, H:i') ?>
                                                        </div>
                                                    <?php elseif ($batch['approval_status'] == 'rejected'): ?>
                                                        <div class="text-xs text-danger mb-2">
                                                            <i class="fa fa-times-circle"></i> Rejected on: <?= $batch['approved_at']->format('d M Y, H:i') ?>
                                                        </div>
                                                        <?php if (!empty($batch['rejection_reason'])): ?>
                                                            <div class="alert alert-danger py-2 px-3 mb-2">
                                                                <small>
                                                                    <strong>Reason:</strong> <?= h($batch['rejection_reason']) ?>
                                                                </small>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div class="text-xs text-warning mb-2">
                                                            <i class="fa fa-clock-o"></i> Pending admin approval
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="btn-group-vertical" role="group">
                                                        <?php if ($batch['approval_status'] == 'rejected'): ?>
                                                            <?= $this->Html->link(
                                                                '<i class="fa fa-upload"></i> Re-upload',
                                                                ['action' => 'uploadresults'],
                                                                ['class' => 'btn btn-sm btn-primary', 'escape' => false, 'title' => 'Upload Corrected Results']
                                                            ) ?>
                                                        <?php endif; ?>
                                                        
                                                        <?= $this->Html->link(
                                                            '<i class="fa fa-eye"></i> View Details',
                                                            [
                                                                'action' => 'viewmyupload', 
                                                                $batch['subject']->id, 
                                                                $batch['department']->id, 
                                                                $batch['semester']->id, 
                                                                $batch['session']->id
                                                            ],
                                                            ['class' => 'btn btn-sm btn-info', 'escape' => false, 'title' => 'View All Students in Batch']
                                                        ) ?>
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
                            <i class="fa fa-upload fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Uploads Found</h5>
                            <p class="text-muted">You haven't uploaded any results yet.</p>
                            <?= $this->Html->link('Upload Results', ['action' => 'uploadresults'], ['class' => 'btn btn-primary']) ?>
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
        "responsive": true,
        "autoWidth": false
    });
});
</script>
