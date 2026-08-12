<?php
$user = $this->request->getSession()->read('usersinfo');
$settings = $this->request->getSession()->read('settings');
?>

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">My Assignments</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">My Assignments</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Assignments</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($assignments)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped custom-table">
                                <thead>
                                                                            <tr>
                                            <th>Subject</th>
                                            <th>Subject Teacher</th>
                                            <th>Type</th>
                                            <th>Details</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td>
                                                <strong><?= h($assignment->subject->name) ?></strong>
                                            </td>
                                            <td>
                                                <strong><?= isset($assignment->teacher) ? h($assignment->teacher->firstname . ' ' . $assignment->teacher->lastname) : 'Not Assigned' ?></strong>
                                            </td>
                                            <td>
                                                <?php if (isset($assignment->test_type) && $assignment->test_type === 'cbt_test'): ?>
                                                    <span class="badge badge-primary">CBT Test</span>
                                                    <?php if (isset($assignment->question_count)): ?>
                                                        <br><small class="text-muted"><?= $assignment->question_count ?> questions</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Assignment</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($assignment->test_type) && $assignment->test_type === 'cbt_test'): ?>
                                                    <strong><?= h($assignment->title ?? 'Test') ?></strong><br>
                                                    <small class="text-muted">
                                                        <?php if (isset($assignment->time_limit)): ?>
                                                            Time: <?= $assignment->time_limit ?> min
                                                        <?php endif; ?>
                                                        <?php if (isset($assignment->passing_score)): ?>
                                                            | Pass: <?= $assignment->passing_score ?>%
                                                        <?php endif; ?>
                                                    </small>
                                                <?php else: ?>
                                                    <?= $this->Text->truncate(h($assignment->description ?? ''), 100) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($assignment->closedate)): ?>
                                                    <?php 
                                                    // Handle both FrozenDate/FrozenTime objects and string dates
                                                    if ($assignment->closedate instanceof \Cake\I18n\FrozenDate || 
                                                        $assignment->closedate instanceof \Cake\I18n\FrozenTime) {
                                                        $closeDate = $assignment->closedate->format('Y-m-d H:i:s');
                                                    } else {
                                                        $closeDate = $assignment->closedate;
                                                    }
                                                    
                                                    // Account for server timezone being 1 hour behind system time
                                                    $currentTime = time() + 3600; // Add 1 hour (3600 seconds) to current time
                                                    $isOverdue = strtotime($closeDate) < $currentTime;
                                                    ?>
                                                    <span class="badge badge-<?= $isOverdue ? 'danger' : 'info' ?>">
                                                        <?= date('d M Y, H:i', strtotime($closeDate)) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">No due date</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($assignment->submitted) {
                                                    echo '<span class="badge badge-success">Completed</span>';
                                                } elseif (isset($assignment->assignment_status) && $assignment->assignment_status === 'in_progress') {
                                                    echo '<span class="badge badge-warning">In Progress</span>';
                                                } else {
                                                    if (!empty($assignment->closedate)) {
                                                        // Handle both FrozenDate/FrozenTime objects and string dates
                                                        if ($assignment->closedate instanceof \Cake\I18n\FrozenDate || 
                                                            $assignment->closedate instanceof \Cake\I18n\FrozenTime) {
                                                            $closeDate = $assignment->closedate->format('Y-m-d H:i:s');
                                                        } else {
                                                            $closeDate = $assignment->closedate;
                                                        }
                                                        
                                                        // Account for server timezone being 1 hour behind system time
                                                        $currentTime = time() + 3600; // Add 1 hour (3600 seconds) to current time
                                                        if (strtotime($closeDate) < $currentTime) {
                                                            echo '<span class="badge badge-danger">Overdue</span>';
                                                        } else {
                                                            echo '<span class="badge badge-info">Available</span>';
                                                        }
                                                    } else {
                                                        echo '<span class="badge badge-info">Available</span>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($assignment->submitted): ?>
                                                    <?php if (isset($assignment->test_type) && $assignment->test_type === 'cbt_test'): ?>
                                                        <?= $this->Html->link(__('View Result'), ['controller' => 'Assignments', 'action' => 'viewcbtresult', $assignment->submission_data->id], ['class' => 'btn btn-sm btn-info']) ?>
                                                    <?php else: ?>
                                                        <?= $this->Html->link(__('View Submission'), ['controller' => 'Assignments', 'action' => 'view', $assignment->id], ['class' => 'btn btn-sm btn-info']) ?>
                                                    <?php endif; ?>
                                                <?php elseif (isset($assignment->assignment_status) && $assignment->assignment_status === 'in_progress'): ?>
                                                    <?php if (isset($assignment->test_type) && $assignment->test_type === 'cbt_test'): ?>
                                                        <?= $this->Html->link(__('Continue Test'), ['controller' => 'Assignments', 'action' => 'startcbt', $assignment->id], ['class' => 'btn btn-sm btn-warning']) ?>
                                                    <?php else: ?>
                                                        <?= $this->Html->link(__('Continue Assignment'), ['controller' => 'Assignments', 'action' => 'view', $assignment->subject_id, $assignment->id, $this->GenerateUrl($assignment->subject->name)], ['class' => 'btn btn-sm btn-warning']) ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php 
                                                    // Check if test is overdue for action button logic
                                                    $isTestOverdue = false;
                                                    if (!empty($assignment->closedate)) {
                                                        // Handle both FrozenDate/FrozenTime objects and string dates
                                                        if ($assignment->closedate instanceof \Cake\I18n\FrozenDate || 
                                                            $assignment->closedate instanceof \Cake\I18n\FrozenTime) {
                                                            $closeDate = $assignment->closedate->format('Y-m-d H:i:s');
                                                        } else {
                                                            $closeDate = $assignment->closedate;
                                                        }
                                                        
                                                        // Account for server timezone being 1 hour behind system time
                                                        $currentTime = time() + 3600; // Add 1 hour (3600 seconds) to current time
                                                        $isTestOverdue = strtotime($closeDate) < $currentTime;
                                                    }
                                                    ?>
                                                    
                                                    <?php if ($isTestOverdue): ?>
                                                        <span class="btn btn-sm btn-secondary disabled">Test Closed</span>
                                                    <?php else: ?>
                                                        <?php if (isset($assignment->test_type) && $assignment->test_type === 'cbt_test'): ?>
                                                            <?= $this->Html->link(__('Start Test'), ['controller' => 'Assignments', 'action' => 'startcbt', $assignment->id], ['class' => 'btn btn-sm btn-success']) ?>
                                                        <?php else: ?>
                                                            <?= $this->Html->link(__('View Assignment'), ['controller' => 'Assignments', 'action' => 'view', $assignment->subject_id, $assignment->id, $this->GenerateUrl($assignment->subject->name)], ['class' => 'btn btn-sm btn-primary']) ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No assignments found for your registered courses in the current semester.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->
