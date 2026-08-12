<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'viewsubmissions') !== false) {
    $previousPage = 'View Submissions';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'viewsubmissions', $submission->setassignment_id];
} else {
    // Default fallback
    $previousPage = 'View Submissions';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'viewsubmissions', $submission->setassignment_id];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Grade Submission</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Setassignments', 'action' => 'index'], ['title' => 'Manage Assignments'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                            ?></li>
                        <li class="breadcrumb-item active">Grade Submission</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    <!-- Student Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Student Name:</strong> <?= h($submission->student->fname . ' ' . $submission->student->lname) ?></p>
                    <p><strong>Matric Number:</strong> <?= h($submission->student->regno) ?></p>
                    <p><strong>Class:</strong> <?= h($submission->student->department->name . (!empty($submission->student->class_arm) ? ' - ' . $submission->student->class_arm->arm_name : '')) ?></p>
                    <p><strong>Test:</strong> <?= h($submission->setassignment->title) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Submission Date:</strong> <?= h($submission->end_time ? $submission->end_time->format('d M Y, H:i') : 'N/A') ?></p>
                    <p><strong>Time Taken:</strong> 
                        <?php 
                        if (isset($submission->start_time) && isset($submission->end_time)) {
                            $start = new DateTime($submission->start_time);
                            $end = new DateTime($submission->end_time);
                            $diff = $start->diff($end);
                            echo $diff->format('%H:%I:%S');
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                    <p><strong>Current Score:</strong> 
                        <?php if (isset($submission->total_score)): ?>
                            <span class="badge badge-<?= $submission->total_score >= $submission->setassignment->passing_score ? 'success' : 'danger' ?>">
                                <?= h($submission->total_score) ?> / <?= h($submission->setassignment->total_questions) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">Not Graded</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Answers and Grading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php if (isset($submission->graded_at) && !empty($submission->graded_at)): ?>
                    Student Answers & Final Results (Graded on <?= $submission->graded_at->format('d M Y, H:i') ?>)
                <?php else: ?>
                    Student Answers & Grading
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <?php if (isset($submission->graded_at) && !empty($submission->graded_at)): ?>
                <!-- Read-only view for already graded submissions -->
                <div class="alert alert-info mb-4">
                    <strong><i class="fa fa-lock"></i> Grading Locked</strong><br>
                    This submission was graded on <?= $submission->graded_at->format('d M Y, H:i') ?>. 
                    The grading is locked to prevent changes. If you need to regrade, please contact the system administrator.
                </div>
                
                <?php if (empty($submission->student_answers)): ?>
                    <div class="alert alert-warning">
                        <strong>No answers found!</strong> This submission appears to be incomplete.
                    </div>
                <?php else: ?>
                    <?php foreach ($submission->student_answers as $index => $answer): ?>
                    <div class="question-block mb-4 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-8">
                                <h6><strong>Question <?= $index + 1 ?>:</strong> <?= h($answer->question->question_text) ?></h6>
                                <p><small class="text-muted">
                                    Type: <?= ucfirst(str_replace('_', ' ', $answer->question->question_type)) ?> | 
                                    Points: <?= h($answer->question->points) ?>
                                </small></p>
                                
                                <?php if ($answer->question->question_type === 'multiple_choice'): ?>
                                    <div class="ml-3">
                                        <p><strong>Student's Answer:</strong></p>
                                        <?php 
                                        $selectedOption = null;
                                        foreach ($answer->question->question_options as $option) {
                                            if ($option->id == $answer->selected_option_id) {
                                                $selectedOption = $option;
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($selectedOption): ?>
                                            <div class="alert alert-<?= $selectedOption->is_correct ? 'success' : 'danger' ?>">
                                                <strong><?= h($selectedOption->option_text) ?></strong>
                                                <?= $selectedOption->is_correct ? ' ✓ (Correct)' : ' ✗ (Incorrect)' ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">No answer selected</div>
                                        <?php endif; ?>
                                        
                                        <p><strong>All Options:</strong></p>
                                        <ul class="list-unstyled">
                                            <?php foreach ($answer->question->question_options as $option): ?>
                                            <li class="<?= $option->is_correct ? 'text-success' : 'text-muted' ?>">
                                                <?= h($option->option_text) ?>
                                                <?= $option->is_correct ? ' ✓' : '' ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="ml-3">
                                        <p><strong>Student's Answer:</strong></p>
                                        <div class="alert alert-info">
                                            <?= !empty($answer->theory_answer) ? nl2br(h($answer->theory_answer)) : 'No answer provided' ?>
                                        </div>
                                        
                                        <!-- Show final grade for theory questions -->
                                        <div class="form-group">
                                            <label><strong>Final Grade:</strong></label>
                                            <div class="alert alert-<?= ($answer->theory_score > 0) ? 'success' : 'warning' ?>">
                                                <strong><?= h($answer->theory_score) ?> / <?= h($answer->question->points) ?> points</strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="text-right">
                                    <?php if ($answer->question->question_type === 'multiple_choice'): ?>
                                        <?php if ($selectedOption && $selectedOption->is_correct): ?>
                                            <span class="badge badge-success">Correct</span>
                                            <br><small class="text-muted">Auto-graded</small>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Incorrect</span>
                                            <br><small class="text-muted">Auto-graded</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-<?= ($answer->theory_score > 0) ? 'success' : 'warning' ?>">
                                            <?= h($answer->theory_score) ?> pts
                                        </span>
                                        <br><small class="text-muted">Manually graded</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Show final teacher comments -->
                    <?php if (!empty($submission->teacher_comments)): ?>
                    <div class="form-group">
                        <label><strong>Teacher Comments:</strong></label>
                        <div class="alert alert-info">
                            <?= nl2br(h($submission->teacher_comments)) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Editable grading form for ungraded submissions -->
                <?= $this->Form->create($submission, ['url' => ['action' => 'gradesubmission', $submission->id]]) ?>
                
                <?php if (empty($submission->student_answers)): ?>
                    <div class="alert alert-warning">
                        <strong>No answers found!</strong> This submission appears to be incomplete.
                    </div>
                <?php else: ?>
                    <?php foreach ($submission->student_answers as $index => $answer): ?>
                    <div class="question-block mb-4 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-8">
                                <h6><strong>Question <?= $index + 1 ?>:</strong> <?= h($answer->question->question_text) ?></h6>
                                <p><small class="text-muted">
                                    Type: <?= ucfirst(str_replace('_', ' ', $answer->question->question_type)) ?> | 
                                    Points: <?= h($answer->question->points) ?>
                                </small></p>
                                
                                <?php if ($answer->question->question_type === 'multiple_choice'): ?>
                                    <div class="ml-3">
                                        <p><strong>Student's Answer:</strong></p>
                                        <?php 
                                        $selectedOption = null;
                                        foreach ($answer->question->question_options as $option) {
                                            if ($option->id == $answer->selected_option_id) {
                                                $selectedOption = $option;
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($selectedOption): ?>
                                            <div class="alert alert-<?= $selectedOption->is_correct ? 'success' : 'danger' ?>">
                                                <strong><?= h($selectedOption->option_text) ?></strong>
                                                <?= $selectedOption->is_correct ? ' ✓ (Correct)' : ' ✗ (Incorrect)' ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">No answer selected</div>
                                        <?php endif; ?>
                                        
                                        <p><strong>All Options:</strong></p>
                                        <ul class="list-unstyled">
                                            <?php foreach ($answer->question->question_options as $option): ?>
                                            <li class="<?= $option->is_correct ? 'text-success' : 'text-muted' ?>">
                                                <?= h($option->option_text) ?>
                                                <?= $option->is_correct ? ' ✓' : '' ?>
                                                <?= ($option->id == $answer->selected_option_id) ? ' (Selected)' : '' ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="ml-3">
                                        <p><strong>Student's Answer:</strong></p>
                                        <div class="alert alert-info">
                                            <?= !empty($answer->theory_answer) ? nl2br(h($answer->theory_answer)) : 'No answer provided' ?>
                                        </div>
                                        
                                        <!-- Manual grading for theory questions -->
                                        <div class="form-group">
                                            <label>Grade (0 - <?= h($answer->question->points) ?> points):</label>
                                            <input type="number" name="theory_score_<?= $answer->id ?>" 
                                                   class="form-control" min="0" max="<?= h($answer->question->points) ?>"
                                                   value="<?= isset($answer->theory_score) ? h($answer->theory_score) : 0 ?>"
                                                   style="width: 100px;">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="text-right">
                                    <?php if ($answer->question->question_type === 'multiple_choice'): ?>
                                        <?php if ($selectedOption && $selectedOption->is_correct): ?>
                                            <span class="badge badge-success">Correct</span>
                                            <br><small class="text-muted">Auto-graded</small>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Incorrect</span>
                                            <br><small class="text-muted">Auto-graded</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Manual Grade Required</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Overall Comments -->
                    <div class="form-group">
                        <label for="teacher_comments"><strong>Teacher Comments:</strong></label>
                        <textarea name="teacher_comments" id="teacher_comments" class="form-control" rows="4" 
                                  placeholder="Provide feedback and comments for the student..."><?= h($submission->teacher_comments ?? '') ?></textarea>
                    </div>
                    
                    <!-- Submit Grade Button -->
                    <div class="text-center">
                        <?= $this->Form->button('Submit Grade', ['class' => 'btn btn-primary btn-lg']) ?>
                    </div>
                <?php endif; ?>
                
                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-6">
            <?= $this->Html->link(__('Back to Submissions'), ['action' => 'viewsubmissions', $submission->setassignment_id], ['class' => 'btn btn-secondary']) ?>
        </div>
        <div class="col-md-6 text-right">
            <?= $this->Html->link(__('Back to Tests'), ['action' => 'index'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

</div>
