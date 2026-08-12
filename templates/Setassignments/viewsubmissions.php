<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'managequestions') !== false) {
    $previousPage = 'Manage Questions';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'managequestions', $setassignment->id];
} else {
    // Default fallback
    $previousPage = 'Manage Questions';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'managequestions', $setassignment->id];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Student Submissions</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Setassignments', 'action' => 'index'], ['title' => 'Manage Assignments'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                            ?></li>
                        <li class="breadcrumb-item active">Student Submissions</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    <!-- Test Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Test: <?= h($setassignment->title) ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Subject:</strong> <?= $setassignment->has('subject') ? $setassignment->subject->name . ' (' . (isset($setassignment->subject->department) ? $setassignment->subject->department->name : 'No Class') . ')' : '' ?></p>
                    <p><strong>Total Questions:</strong> <?= h($setassignment->total_questions) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Time Limit:</strong> <?= h($setassignment->time_limit) ?> minutes</p>
                    <p><strong>Passing Score:</strong> <?= h($setassignment->passing_score) ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Submissions (<?= count($submissions) ?>)</h6>
        </div>
        <div class="card-body">
            <?php if (empty($submissions)): ?>
                <div class="alert alert-info">
                    <strong>No submissions yet!</strong> Students haven't taken this test yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="submissionsTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Student</th>
                                <th width="15%">Submission Date</th>
                                <th width="15%">Time Taken</th>
                                <th width="15%">Score</th>
                                <th width="15%">Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $index => $submission): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?php if (isset($submission->student)): ?>
                                        <strong><?= h($submission->student->fname ?? 'Unknown') ?> <?= h($submission->student->lname ?? '') ?></strong><br>
                                        <small class="text-muted"><?= h($submission->student->regno ?? 'N/A') ?></small>
                                    <?php else: ?>
                                        <span class="text-danger">Student data not loaded</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($submission->end_time ? $submission->end_time->format('d M Y, H:i') : 'N/A') ?></td>
                                <td>
                                    <?php 
                                    if ($submission->start_time && $submission->end_time) {
                                        $startTime = $submission->start_time instanceof \Cake\I18n\FrozenTime ? 
                                                    $submission->start_time->format('Y-m-d H:i:s') : 
                                                    $submission->start_time;
                                        $endTime = $submission->end_time instanceof \Cake\I18n\FrozenTime ? 
                                                  $submission->end_time->format('Y-m-d H:i:s') : 
                                                  $submission->end_time;
                                        
                                        $start = new DateTime($startTime);
                                        $end = new DateTime($endTime);
                                        $diff = $start->diff($end);
                                        echo $diff->format('%H:%I:%S');
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (isset($submission->total_score)): ?>
                                        <span class="badge badge-<?= $submission->total_score >= $setassignment->passing_score ? 'success' : 'danger' ?>">
                                            <?= h($submission->total_score) ?> / <?= h($setassignment->total_questions) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Not Graded</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($submission->graded_at) && !empty($submission->graded_at)): ?>
                                        <?php 
                                        $percentage = ($submission->total_score / $setassignment->total_questions) * 100;
                                        if ($percentage >= $setassignment->passing_score) {
                                            echo '<span class="badge badge-success">Passed</span>';
                                        } else {
                                            echo '<span class="badge badge-danger">Failed</span>';
                                        }
                                        ?>
                                        <br><small class="text-muted">Graded</small>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($submission->graded_at) && !empty($submission->graded_at)): ?>
                                        <?= $this->Html->link(__('View Results'), ['action' => 'gradesubmission', $submission->id], ['class' => 'btn btn-sm btn-success']) ?>
                                    <?php else: ?>
                                        <?= $this->Html->link(__('Grade'), ['action' => 'gradesubmission', $submission->id], ['class' => 'btn btn-sm btn-info']) ?>
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

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-6">
            <?= $this->Html->link(__('Back to Questions'), ['action' => 'managequestions', $setassignment->id], ['class' => 'btn btn-secondary']) ?>
        </div>
        <div class="col-md-6 text-right">
            <?= $this->Html->link(__('Back to Tests'), ['action' => 'index'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    $('#submissionsTable').DataTable({
        "order": [[ 2, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });
});
</script>
