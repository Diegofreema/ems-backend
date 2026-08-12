<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'myassignments') !== false) {
    $previousPage = 'My Assignments';
    $previousUrl = ['controller' => 'Assignments', 'action' => 'myassignments'];
} else {
    // Default fallback
    $previousPage = 'My Assignments';
    $previousUrl = ['controller' => 'Assignments', 'action' => 'myassignments'];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">CBT Test Instructions</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                        ?></li>
                    <li class="breadcrumb-item active">Test Instructions</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?= h($test->title ?? 'CBT Test') ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="test-info mb-4">
                                <h5>Test Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Subject:</strong> <?= h($test->subject->name) ?></p>
                                        <p><strong>Total Questions:</strong> <?= $questionCount ?></p>
                                        <p><strong>Time Limit:</strong> <?= isset($test->time_limit) ? $test->time_limit . ' minutes' : 'No time limit' ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Passing Score:</strong> <?= isset($test->passing_score) ? $test->passing_score . '%' : 'Not specified' ?></p>
                                        <p><strong>Due Date:</strong> 
                                            <?php if (!empty($test->closedate)): ?>
                                                <?php 
                                                // Handle both FrozenDate/FrozenTime objects and string dates
                                                if ($test->closedate instanceof \Cake\I18n\FrozenDate || 
                                                    $test->closedate instanceof \Cake\I18n\FrozenTime) {
                                                    $closeDate = $test->closedate->format('Y-m-d H:i:s');
                                                } else {
                                                    $closeDate = $test->closedate;
                                                }
                                                
                                                // Account for server timezone being 1 hour behind system time
                                                $currentTime = time() + 3600; // Add 1 hour (3600 seconds) to current time
                                                $isOverdue = strtotime($closeDate) < $currentTime;
                                                ?>
                                                <span class="text-<?= $isOverdue ? 'danger' : 'info' ?>">
                                                    <?= date('d M Y, H:i', strtotime($closeDate)) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">No due date</span>
                                            <?php endif; ?>
                                        </p>
                                        <p><strong>Teacher:</strong> 
                                            <?= isset($test->teacher) ? h($test->teacher->firstname . ' ' . $test->teacher->lastname) : 'Not assigned' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($test->details)): ?>
                                <div class="test-instructions mb-4">
                                    <h5>Test Instructions</h5>
                                    <div class="alert alert-info">
                                        <?= nl2br(h($test->details)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="test-rules mb-4">
                                <h5>Important Rules</h5>
                                <div class="alert alert-warning">
                                    <ul class="mb-0">
                                        <li>Read each question carefully before answering</li>
                                        <li>For multiple choice questions, select only one answer</li>
                                        <li>For theory questions, provide detailed answers</li>
                                        <li>You cannot go back to previous questions once submitted</li>
                                        <li>Ensure you have a stable internet connection</li>
                                        <li>Submit your test before the time expires</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Ready to Start?</h6>
                                    <p class="card-text">Click the button below to begin your test.</p>
                                    
                                    <?php if (!empty($test->time_limit)): ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-clock-o"></i>
                                            <strong>Time Limit:</strong> <?= $test->time_limit ?> minutes
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-grid gap-2">
                                        <?= $this->Html->link(__('Start Test Now'), 
                                            ['controller' => 'Assignments', 'action' => 'takecbt', $test->id], 
                                            ['class' => 'btn btn-success btn-lg', 'confirm' => 'Are you sure you want to start this test? You cannot pause or restart once begun.']
                                        ) ?>
                                        
                                        <?= $this->Html->link(__('Back to Assignments'), 
                                            ['controller' => 'Assignments', 'action' => 'myassignments'], 
                                            ['class' => 'btn btn-outline-secondary']
                                        ) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
