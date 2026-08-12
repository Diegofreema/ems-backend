<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'dashboard') !== false) {
    $previousPage = 'Dashboard';
    $previousUrl = ['controller' => 'Sparents', 'action' => 'dashboard'];
} else {
    // Default fallback
    $previousPage = 'Dashboard';
    $previousUrl = ['controller' => 'Sparents', 'action' => 'dashboard'];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">My Kids Assignments</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Sparents', 'action' => 'dashboard'], ['title' => 'Parent dashboard']) ?></li>
                    <!-- <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li> -->
                    <li class="breadcrumb-item active">My Kids Assignments</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <?php if (empty($students)): ?>
        <div class="alert alert-info">
            <strong>No students found!</strong> You don't have any children registered in the system.
        </div>
    <?php else: ?>
        <!-- Students and Their Assignments -->
        <?php foreach ($students as $student): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-user-graduate"></i> 
                        <?= h($student->fname . ' ' . $student->lname) ?> 
                        <small class="text-muted">(<?= h($student->regno) ?>)</small>
                    </h6>
                </div>
                
                <div class="card-body">
                    <?php 
                    $studentAssignments = array_filter($allAssignments, function($item) use ($student) {
                        return $item['student']['id'] == $student->id;
                    });
                    ?>
                    
                    <?php if (empty($studentAssignments)): ?>
                        <div class="alert alert-info">
                            <strong>No tests available!</strong> <?= h($student->fname) ?> doesn't have any tests available at the moment.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Test Title</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentAssignments as $assignmentData): ?>
                                        <?php 
                                        $assignment = $assignmentData['assignment'];
                                        $setassignment = $assignmentData['setassignment'];
                                        $status = $assignmentData['status'];
                                        
                                        // Check if test is overdue
                                        $isOverdue = false;
                                        if (!empty($setassignment->closedate)) {
                                            // Handle both FrozenDate/FrozenTime objects and string dates
                                            if ($setassignment->closedate instanceof \Cake\I18n\FrozenDate || 
                                                $setassignment->closedate instanceof \Cake\I18n\FrozenTime) {
                                                $closeDate = $setassignment->closedate->format('Y-m-d H:i:s');
                                            } else {
                                                $closeDate = $setassignment->closedate;
                                            }
                                            
                                            // Account for server timezone being 1 hour behind system time
                                            $currentTime = new \DateTime();
                                            $currentTime->add(new \DateInterval('PT1H')); // Add 1 hour to current time
                                            
                                            $isOverdue = $currentTime > new \DateTime($closeDate);
                                        }
                                        ?>
                                        <tr class="<?= $isOverdue ? 'table-warning' : '' ?>">
                                            <td>
                                                <strong><?= h($setassignment->subject->name) ?></strong>
                                                <?php if (isset($setassignment->subject->department) && !empty($setassignment->subject->department->name)): ?>
                                                    <br><small class="text-muted"><?= h($setassignment->subject->department->name) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= h($setassignment->title) ?></strong>
                                                <br><small class="text-muted">
                                                    <?= h($setassignment->total_questions ?? 'Unknown') ?> questions • 
                                                    <?= h($setassignment->time_limit ?? 'No limit') ?> minutes
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($status === 'completed'): ?>
                                                    <span class="badge badge-success">Completed</span>
                                                <?php elseif ($status === 'in_progress'): ?>
                                                    <span class="badge badge-warning">In Progress</span>
                                                <?php elseif ($isOverdue): ?>
                                                    <span class="badge badge-danger">Overdue</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info">Available</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($setassignment->closedate)): ?>
                                                    <?php 
                                                    // Handle both FrozenDate/FrozenTime objects and string dates
                                                    if ($setassignment->closedate instanceof \Cake\I18n\FrozenDate || 
                                                        $setassignment->closedate instanceof \Cake\I18n\FrozenTime) {
                                                        $closeDate = $setassignment->closedate->format('Y-m-d H:i:s');
                                                    } else {
                                                        $closeDate = $setassignment->closedate;
                                                    }
                                                    echo date('d M Y, H:i', strtotime($closeDate));
                                                    ?>
                                                    <?php if ($isOverdue): ?>
                                                        <br><small class="text-danger">Overdue</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No due date</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($status === 'completed'): ?>
                                                    <?= $this->Html->link(__('View Result'), 
                                                        ['action' => 'viewstudentresult', $assignment->id], 
                                                        ['class' => 'btn btn-sm btn-success']
                                                    ) ?>
                                                <?php elseif ($status === 'in_progress'): ?>
                                                    <?= $this->Html->link(__('Continue Test'), 
                                                        ['controller' => 'Sparents', 'action' => 'takeassignmentforstudent', $setassignment->id, $student->id], 
                                                        ['class' => 'btn btn-sm btn-warning']
                                                    ) ?>
                                                <?php elseif ($isOverdue): ?>
                                                    <span class="text-muted">Test closed</span>
                                                <?php else: ?>
                                                    <?= $this->Html->link(__('Take Test'), 
                                                        ['controller' => 'Sparents', 'action' => 'takeassignmentforstudent', $setassignment->id, $student->id], 
                                                        ['class' => 'btn btn-sm btn-primary']
                                                    ) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-12 text-center">
            <?= $this->Html->link(__('Back to Dashboard'), 
                ['controller' => 'Sparents', 'action' => 'dashboard'], 
                ['class' => 'btn btn-secondary']
            ) ?>
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
