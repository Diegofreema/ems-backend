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
                <h3 class="page-title">Reject Batch</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li> 
                    <li class="breadcrumb-item"><?= $this->Html->link(' Result Approvals', ['action' => 'pendingApproval']) ?></li>
                    <li class="breadcrumb-item active">Reject Batch</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fa fa-times"></i> Reject Batch of Results
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Batch Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Batch Information</h6>
                            <p><strong>Subject:</strong> <?= h($batchResults->first()->subject->name) ?></p>
                            <p><strong>Class:</strong> <?= h($batchResults->first()->department->name) ?><?= !empty($batchResults->first()->class_arm) ? ' - ' . h($batchResults->first()->class_arm->arm_name) : '' ?></p>
                            <p><strong>Term:</strong> <?= h($batchResults->first()->semester->name) ?></p>
                            <p><strong>Session:</strong> <?= h($batchResults->first()->session->name) ?></p>
                            <p><strong>Students:</strong> <?= $batchResults->count() ?> students</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Upload Information</h6>
                            <p><strong>Uploaded by:</strong> <?= h($batchResults->first()->user->fname . ' ' . $batchResults->first()->user->lname) ?></p>
                            <p><strong>Upload Date:</strong> <?= $batchResults->first()->uploaddate->format('d M Y, H:i') ?></p>
                        </div>
                    </div>
                    
                    <!-- Students List -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Students in this batch:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Registration</th>
                                        <th>Total Score</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batchResults as $result): ?>
                                        <tr>
                                            <td><?= h($result->student->fname . ' ' . $result->student->lname) ?></td>
                                            <td><?= h($result->student->regno) ?></td>
                                            <td><?= $this->Number->format($result->total) ?></td>
                                            <td><?= h($result->grade) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Rejection Form -->
                    <?= $this->Form->create(null) ?>
                    <div class="form-group">
                        <label for="rejection_reason" class="font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                        <?= $this->Form->textarea('rejection_reason', [
                            'class' => 'form-control',
                            'rows' => 4,
                            'placeholder' => 'Please provide a detailed reason for rejecting this batch of results...',
                            'required' => true
                        ]) ?>
                        <small class="form-text text-muted">This reason will be visible to the teacher who uploaded the results.</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times"></i> Reject All <?= $batchResults->count() ?> Results
                        </button>
                        <?= $this->Html->link('<i class="fa fa-arrow-left"></i> Cancel', 
                            ['action' => 'pendingApproval'], 
                            ['class' => 'btn btn-secondary', 'escape' => false]
                        ) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fa fa-info-circle"></i> Rejection Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fa fa-check text-success"></i> Check for calculation errors</li>
                        <li class="mb-2"><i class="fa fa-check text-success"></i> Verify grade assignments</li>
                        <li class="mb-2"><i class="fa fa-check text-success"></i> Ensure all required fields are filled</li>
                        <li class="mb-2"><i class="fa fa-check text-success"></i> Validate student information</li>
                        <li class="mb-2"><i class="fa fa-check text-success"></i> Check for duplicate entries</li>
                        <li class="mb-2"><i class="fa fa-check text-success"></i> Verify subject/class alignment</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Once rejected, the teacher will need to re-upload the corrected results for all students in this batch.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
