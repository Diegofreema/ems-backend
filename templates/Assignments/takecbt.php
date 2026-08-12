<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'startcbt') !== false) {
    $previousPage = 'Test Instructions';
    $previousUrl = ['controller' => 'Assignments', 'action' => 'startcbt', $test->id];
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
                <h3 class="page-title">Taking CBT Test</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                        ?></li>
                    <li class="breadcrumb-item active">Test in Progress</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Test Header -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1"><?= h($test->title ?? 'CBT Test') ?></h4>
                            <p class="mb-0">Subject: <?= h($test->subject->name) ?></p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="h3 mb-0">
                                <i class="fa fa-check-circle"></i> 
                                <span>Test in Progress</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Form -->
    <?= $this->Form->create(null, ['id' => 'cbt-form']) ?>
    
    <!-- Hidden fields for timing -->
    <input type="hidden" name="actual_start_time" id="actual-start-time" value="">
    <input type="hidden" name="actual_duration" id="actual-duration" value="">
    
    <div class="row">
        <div class="col-md-8">
            <!-- Questions -->
            <?php foreach ($questions as $index => $question): ?>
                <div class="card mb-4 question-card" id="question-<?= $question->id ?>">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Question <?= $index + 1 ?> of <?= count($questions) ?>
                            <?php if (isset($question->points)): ?>
                                <span class="badge badge-info float-right"><?= $question->points ?> point<?= $question->points != 1 ? 's' : '' ?></span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="question-text mb-3">
                            <h6><?= nl2br(h($question->question_text)) ?></h6>
                        </div>

                        <?php if ($question->question_type === 'multiple_choice'): ?>
                            <!-- Multiple Choice Options -->
                            <div class="options-container">
                                <?php foreach ($question->question_options as $optionIndex => $option): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?= $question->id ?>]" 
                                               value="<?= $option->id ?>" 
                                               id="option-<?= $question->id ?>-<?= $option->id ?>"
                                               required>
                                        <label class="form-check-label" for="option-<?= $question->id ?>-<?= $option->id ?>">
                                            <span class="option-letter"><?= chr(65 + $optionIndex) ?></span>
                                            <?= h($option->option_text) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <!-- Theory Question -->
                            <div class="theory-answer">
                                <label for="theory-<?= $question->id ?>">Your Answer:</label>
                                <textarea class="form-control" 
                                          name="answers[<?= $question->id ?>]" 
                                          id="theory-<?= $question->id ?>" 
                                          rows="4" 
                                          placeholder="Type your detailed answer here..."
                                          required></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="col-md-4">
            <!-- Progress and Navigation -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h6 class="mb-0">Test Progress</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    
                                            <div class="question-navigation mb-3">
                            <h6>Quick Navigation:</h6>
                            <div class="row">
                                <?php foreach ($questions as $index => $question): ?>
                                    <div class="col-3 mb-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary question-nav-btn" 
                                                data-question="<?= $question->id ?>"
                                                title="Go to Question <?= $index + 1 ?>">
                                            <?= $index + 1 ?>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <div class="test-actions">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="submit-test">
                                <i class="fa fa-check"></i> Submit Test
                            </button>
                            
                            <?= $this->Html->link(__('Exit Test'), 
                                ['controller' => 'Assignments', 'action' => 'myassignments'], 
                                ['class' => 'btn btn-outline-danger', 'confirm' => 'Are you sure you want to exit? Your progress will be lost.']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<style>
.question-card {
    border-left: 4px solid #007bff;
}

.option-letter {
    display: inline-block;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 50%;
    margin-right: 10px;
    font-weight: bold;
    color: #495057;
}


.form-check-input:checked + .form-check-label .option-letter {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.question-nav-btn {
    min-width: 40px;
}

.question-nav-btn.answered {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}



.sticky-top {
    z-index: 1020;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cbt-form');
    const questions = <?= json_encode($questions) ?>;
    const timeLimit = 0; // Timer disabled
    
    // Initialize progress
    updateProgress();
    
    // Question navigation (optional - for large tests)
    document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            // Scroll to question instead of hiding/showing
            const questionCard = document.getElementById(`question-${questions[index].id}`);
            if (questionCard) {
                questionCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if all questions are answered
        const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked, textarea[name^="answers"]').length;
        const totalQuestions = questions.length;
        
        if (answeredQuestions < totalQuestions) {
            if (!confirm('You have not answered all questions. Are you sure you want to submit?')) {
                return;
            }
        }
        
        if (confirm('Are you sure you want to submit your test? You cannot change your answers after submission.')) {
            form.submit();
        }
    });
    
    // Track answered questions
    document.addEventListener('change', function(e) {
        if (e.target.name && e.target.name.startsWith('answers')) {
            updateProgress();
        }
    });
    
    // Timer functionality disabled
    
    // Duration tracking for accurate timing
    const startTime = new Date();
    const actualStartTimeField = document.getElementById('actual-start-time');
    const actualDurationField = document.getElementById('actual-duration');
    
    // Set the actual start time
    actualStartTimeField.value = startTime.toISOString();
    
    // Update duration every second (background tracking only)
    const durationTimer = setInterval(function() {
        const currentTime = new Date();
        const duration = Math.floor((currentTime - startTime) / 1000); // Duration in seconds
        
        // Store the duration in seconds for server processing
        actualDurationField.value = duration;
    }, 1000);
    
    // Update duration when form is submitted
    form.addEventListener('submit', function(e) {
        // Calculate final duration before submission
        const currentTime = new Date();
        const finalDuration = Math.floor((currentTime - startTime) / 1000);
        actualDurationField.value = finalDuration;
        
        // Clear the duration timer
        clearInterval(durationTimer);
    });
    

    
    function updateProgress() {
        const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked, textarea[name^="answers"]').length;
        const totalQuestions = questions.length;
        const progress = (answeredQuestions / totalQuestions) * 100;
        
        // Update progress bar
        const progressBar = document.getElementById('progress-bar');
        progressBar.style.width = progress + '%';
        progressBar.textContent = Math.round(progress) + '%';
        
        // Update navigation buttons
        questions.forEach((question, index) => {
            const btn = document.querySelector(`[data-question="${question.id}"]`);
            if (btn) {
                btn.classList.remove('answered');
                
                // Check if this question is answered
                const questionInputs = document.querySelectorAll(`[name="answers[${question.id}]"]`);
                let isAnswered = false;
                
                questionInputs.forEach(input => {
                    if (input.type === 'radio' && input.checked) {
                        isAnswered = true;
                    } else if (input.type === 'textarea' && input.value.trim() !== '') {
                        isAnswered = true;
                    }
                });
                
                if (isAnswered) {
                    btn.classList.add('answered');
                }
            }
        });
    }
});
</script>
