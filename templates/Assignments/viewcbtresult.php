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
                <h3 class="page-title">Test Results</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                        ?></li>
                    <li class="breadcrumb-item active">Test Results</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Results Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?= h($assignment->setassignment->title ?? 'CBT Test') ?> - Results Summary</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="result-info">
                                <p><strong>Subject:</strong> <?= h($assignment->subject->name) ?></p>
                                <p><strong>Test Date:</strong> 
                                    <?php 
                                    $startTime = $assignment->start_time instanceof \Cake\I18n\FrozenTime ? 
                                                $assignment->start_time->format('Y-m-d H:i:s') : 
                                                $assignment->start_time;
                                    echo date('d M Y, H:i', strtotime($startTime));
                                    ?>
                                </p>
                                <p><strong>Duration:</strong> <?= $duration ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="score-summary text-center">
                                <div class="score-circle mx-auto mb-3">
                                    <div class="score-value h2 mb-0"><?= $percentage ?>%</div>
                                    <div class="score-label">Score</div>
                                </div>
                                <div class="score-details">
                                    <p class="mb-1"><strong>Correct Answers:</strong> <?= $correctAnswers ?> / <?= $totalQuestions ?></p>
                                    <p class="mb-1"><strong>Total Score:</strong> <?= $totalScore ?> points</p>
                                    <?php if (isset($assignment->setassignment->passing_score)): ?>
                                        <p class="mb-0">
                                            <strong>Passing Score:</strong> <?= $assignment->setassignment->passing_score ?>%
                                            <?php if ($percentage >= $assignment->setassignment->passing_score): ?>
                                                <span class="badge badge-success ml-2">PASSED</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger ml-2">FAILED</span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Question-by-Question Results</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($studentAnswers)): ?>
                        <?php foreach ($studentAnswers as $index => $answer): ?>
                            <div class="question-result mb-4 p-3 border rounded">
                                <div class="question-header d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="mb-0">Question <?= $index + 1 ?></h6>
                                    <div class="question-meta">
                                        <?php if (isset($answer->question->points)): ?>
                                            <span class="badge badge-info"><?= $answer->question->points ?> point<?= $answer->question->points != 1 ? 's' : '' ?></span>
                                        <?php endif; ?>
                                        <?php if ($answer->question->question_type === 'multiple_choice'): ?>
                                            <span class="badge badge-secondary">Multiple Choice</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Theory</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="question-text mb-3">
                                    <strong><?= nl2br(h($answer->question->question_text)) ?></strong>
                                </div>
                                
                                                                 <?php if ($answer->question->question_type === 'multiple_choice'): ?>
                                     <!-- Multiple Choice Results -->
                                     <div class="answer-options mb-3">
                                         <h6>Options:</h6>
                                         <?php 
                                         // Get question options from the database
                                         $questionOptionsTable = \Cake\ORM\TableRegistry::get('QuestionOptions');
                                         $questionOptions = $questionOptionsTable->find()
                                             ->where(['question_id' => $answer->question->id])
                                             ->order(['order_number' => 'ASC'])
                                             ->all();
                                         
                                         if (!empty($questionOptions)):
                                             foreach ($questionOptions as $optionIndex => $option): ?>
                                                 <div class="option-result p-2 mb-2 border rounded <?= $option->id == $answer->question_option_id ? 'bg-light' : '' ?>">
                                                     <span class="option-letter me-2"><?= chr(65 + $optionIndex) ?></span>
                                                     <?= h($option->option_text) ?>
                                                     
                                                     <?php if ($option->is_correct): ?>
                                                         <span class="badge badge-success ml-2">Correct Answer</span>
                                                     <?php endif; ?>
                                                     
                                                     <?php if ($option->id == $answer->question_option_id): ?>
                                                         <span class="badge badge-primary ml-2">Your Answer</span>
                                                     <?php endif; ?>
                                                 </div>
                                             <?php endforeach; 
                                         else: ?>
                                             <div class="alert alert-warning">Question options not available</div>
                                         <?php endif; ?>
                                     </div>
                                     
                                                                           <div class="result-status">
                                          <?php 
                                          // Check if the selected answer is correct
                                          $isCorrect = false;
                                          if ($answer->selected_option_id) {
                                              foreach ($questionOptions as $option) {
                                                  if ($option->id == $answer->selected_option_id && $option->is_correct) {
                                                      $isCorrect = true;
                                                      break;
                                                  }
                                              }
                                          }
                                          ?>
                                          <?php if ($isCorrect): ?>
                                              <span class="badge badge-success">Correct ✓</span>
                                          <?php else: ?>
                                              <span class="badge badge-danger">Incorrect ✗</span>
                                          <?php endif; ?>
                                      </div>
                                <?php else: ?>
                                    <!-- Theory Question Results -->
                                    <div class="theory-answer mb-3">
                                        <h6>Your Answer:</h6>
                                        <div class="p-3 bg-light border rounded">
                                            <?= nl2br(h($answer->theory_answer)) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="result-status">
                                        <?php if (isset($answer->theory_score) && $answer->theory_score !== null): ?>
                                            <span class="badge badge-<?= ($answer->theory_score > 0) ? 'success' : 'warning' ?>">
                                                <?= h($answer->theory_score) ?> / <?= h($answer->question->points) ?> points
                                            </span>
                                            <small class="text-muted d-block mt-1">
                                                <?php if ($answer->theory_score > 0): ?>
                                                    ✓ Graded by your teacher
                                                <?php else: ?>
                                                    No points awarded
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Manual Grading Required</span>
                                            <small class="text-muted d-block mt-1">Theory questions require manual review by your teacher.</small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No answers found for this test.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <?= $this->Html->link(__('Back to Assignments'), 
                ['controller' => 'Assignments', 'action' => 'myassignments'], 
                ['class' => 'btn btn-primary']
            ) ?>
            
            <?= $this->Html->link(__('Print Results'), 
                '#', 
                ['class' => 'btn btn-outline-secondary', 'onclick' => 'window.print()']
            ) ?>
        </div>
    </div>
</div>

<style>
.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #28a745);
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.score-value {
    font-weight: bold;
    line-height: 1;
}

.score-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.question-result {
    background-color: #f8f9fa;
}

.option-result {
    transition: background-color 0.2s;
}

.option-result:hover {
    background-color: #e9ecef;
}

.option-letter {
    display: inline-block;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
    background-color: #6c757d;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.8rem;
}

@media print {
    .page-header,
    .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>
