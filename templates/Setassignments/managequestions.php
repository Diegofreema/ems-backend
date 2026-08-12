<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'setassignments') !== false) {
    $previousPage = 'Manage Assignments';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'index'];
} else {
    // Default fallback
    $previousPage = 'Manage Assignments';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'index'];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Manage Assignment Questions</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                            ?></li>
                        <li class="breadcrumb-item active">Manage Questions</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    <!-- Test Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Test Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Subject:</strong> 
                        <?php if ($setassignment->has('subject')): ?>
                            <?= h($setassignment->subject->name) ?>
                            <?php if (isset($setassignment->subject->department) && !empty($setassignment->subject->department->name)): ?>
                                <span class="text-muted">(<?= h($setassignment->subject->department->name) ?>)</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                    <p><strong>Test Title:</strong> <?= h($setassignment->title) ?></p>
                    <p><strong>Status:</strong> <?= h($setassignment->status) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Questions:</strong> <?= h($setassignment->total_questions) ?></p>
                    <p><strong>Time Limit:</strong> <?= h($setassignment->time_limit) ?> minutes</p>
                    <p><strong>Passing Score:</strong> <?= h($setassignment->passing_score) ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Management -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Test Questions</h6>
            <?= $this->Html->link(__('Add Question'), ['action' => 'addquestion', $setassignment->id], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
        <div class="card-body">
            <?php if (empty($questions)): ?>
                <div class="alert alert-info">
                    <strong>No questions added yet!</strong> Click "Add Question" to start building your test.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="questionsTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="40%">Question</th>
                                <th width="15%">Type</th>
                                <th width="10%">Points</th>
                                <th width="20%">Options</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $index => $question): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= h($question->question_text) ?></strong>
                                    <?php if ($question->question_type === 'multiple_choice' && !empty($question->question_options)): ?>
                                        <br><small class="text-muted">
                                            Options: 
                                            <?php foreach ($question->question_options as $option): ?>
                                                <span class="badge <?= $option->is_correct ? 'badge-success' : 'badge-secondary' ?>">
                                                    <?= h($option->option_text) ?>
                                                    <?= $option->is_correct ? ' (Correct)' : '' ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $question->question_type === 'multiple_choice' ? 'primary' : 'warning' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $question->question_type)) ?>
                                    </span>
                                </td>
                                <td><?= h($question->points) ?></td>
                                <td>
                                    <?php if ($question->question_type === 'multiple_choice'): ?>
                                        <?= count($question->question_options) ?> options
                                    <?php else: ?>
                                        Theory question
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'editquestion', $question->id], ['class' => 'btn btn-sm btn-warning']) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'deletequestion', $question->id], 
                                        ['confirm' => __('Are you sure you want to delete this question?'), 'class' => 'btn btn-sm btn-danger']) ?>
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
            <?= $this->Html->link(__('Back to Tests'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <div class="col-md-6 text-right">
            <?php if (!empty($questions)): ?>
                <?= $this->Html->link(__('View Submissions'), ['action' => 'viewsubmissions', $setassignment->id], ['class' => 'btn btn-info']) ?>
                <?= $this->Html->link(__('Preview Test'), ['action' => 'view', $setassignment->id], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    $('#questionsTable').DataTable({
        "order": [[ 0, "asc" ]],
        "pageLength": 25,
        "responsive": true
    });
});
</script>
