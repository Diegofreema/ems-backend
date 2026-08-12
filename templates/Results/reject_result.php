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
                <h3 class="page-title">Reject Result</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li> 
                    <li class="breadcrumb-item"><?= $this->Html->link(' Result Approvals', ['action' => 'pendingApproval']) ?></li>
                    <li class="breadcrumb-item active">Reject Result</li>
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
                        <i class="fas fa-times"></i> Reject Result
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Result Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Student Information</h6>
                            <p><strong>Name:</strong> <?= h($result->student->fname . ' ' . $result->student->lname) ?></p>
                            <p><strong>Registration:</strong> <?= h($result->student->regno) ?></p>
                            <p><strong>Class:</strong> <?= h($result->department->name) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Result Information</h6>
                            <p><strong>Subject:</strong> <?= h($result->subject->name) ?></p>
                            <p><strong>Term:</strong> <?= h($result->semester->name) ?></p>
                            <p><strong>Session:</strong> <?= h($result->session->name) ?></p>
                            <p><strong>Uploaded by:</strong> <?= h($result->user->fname . ' ' . $result->user->lname) ?></p>
                        </div>
                    </div>
                    
                    <!-- Result Scores -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Result Scores</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Homework/Project</th>
                                            <th>1st CA</th>
                                            <th>2nd CA</th>
                                            <th>Exam</th>
                                            <th>Total</th>
                                            <th>Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $this->Number->format($result->homework_project ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->first_ca ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->second_ca ?? 0) ?></td>
                                            <td><?= $this->Number->format($result->score) ?></td>
                                            <td><?= $this->Number->format($result->total) ?></td>
                                            <td><?= h($result->grade) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rejection Form -->
                    <?= $this->Form->create(null) ?>
                    <div class="form-group">
                        <label for="rejection_reason" class="font-weight-bold">Reason for Rejection <span class="text-danger">*</span></label>
                        <?= $this->Form->textarea('rejection_reason', [
                            'class' => 'form-control',
                            'rows' => 4,
                            'placeholder' => 'Please provide a detailed reason for rejecting this result...',
                            'required' => true
                        ]) ?>
                        <small class="form-text text-muted">This reason will be visible to the teacher who uploaded the result.</small>
                    </div>
                    
                    <div class="form-group">
                        <?= $this->Form->button('<i class="fas fa-times"></i> Reject Result', [
                            'class' => 'btn btn-danger',
                            'escape' => false
                        ]) ?>
                        <?= $this->Html->link('<i class="fas fa-arrow-left"></i> Cancel', 
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
                        <i class="fas fa-info-circle"></i> Rejection Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success"></i> Check for calculation errors</li>
                        <li class="mb-2"><i class="fas fa-check text-success"></i> Verify grade assignments</li>
                        <li class="mb-2"><i class="fas fa-check text-success"></i> Ensure all required fields are filled</li>
                        <li class="mb-2"><i class="fas fa-check text-success"></i> Validate student information</li>
                        <li class="mb-2"><i class="fas fa-check text-success"></i> Check for duplicate entries</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Once rejected, the teacher will need to re-upload the corrected result.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
