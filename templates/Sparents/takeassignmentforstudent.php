<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'mykidsassignments') !== false) {
    $previousPage = 'My Kids Assignments';
    $previousUrl = ['controller' => 'Sparents', 'action' => 'mykidsassignments'];
} else {
    // Default fallback
    $previousPage = 'My Kids Assignments';
    $previousUrl = ['controller' => 'Sparents', 'action' => 'mykidsassignments'];
}

// Early safety check
if (!isset($questions) || empty($questions)) {
    echo '<div class="alert alert-warning">No questions available for this test.</div>';
    return;
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Taking Test for <?= h($student->fname . ' ' . $student->lname) ?></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Sparents', 'action' => 'dashboard'], ['title' => 'Parent dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li>
                    <li class="breadcrumb-item active">Taking Test</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Test Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-info-circle"></i> Test Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Student:</strong> <?= h($student->fname . ' ' . $student->lname) ?> (<?= h($student->regno) ?>)</p>
                    <p><strong>Subject:</strong> <?= h($setassignment->subject->name) ?></p>
                    <p><strong>Test Title:</strong> <?= h($setassignment->title) ?></p>
                    <p><strong>Total Questions:</strong> <?= h($setassignment->total_questions) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Test Duration:</strong> No time limit</p>
                    <p><strong>Passing Score:</strong> <?= h($setassignment->passing_score) ?>%</p>
                    <p><strong>Test Type:</strong> <?= ucfirst(str_replace('_', ' ', $setassignment->test_type)) ?></p>
                    <?php if (!empty($setassignment->opendate)): ?>
                        <p><strong>Opens:</strong> 
                            <?php if ($setassignment->opendate instanceof \Cake\I18n\FrozenDate): ?>
                                <?= h($setassignment->opendate->format('d M Y, H:i')) ?>
                            <?php else: ?>
                                <?= h(date('d M Y, H:i', strtotime($setassignment->opendate))) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($setassignment->closedate)): ?>
                        <p><strong>Closes:</strong> 
                            <?php if ($setassignment->closedate instanceof \Cake\I18n\FrozenDate): ?>
                                <?= h($setassignment->closedate->format('d M Y, H:i')) ?>
                            <?php else: ?>
                                <?= h(date('d M Y, H:i', strtotime($setassignment->closedate))) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($setassignment->details)): ?>
                <div class="alert alert-info mt-3">
                    <strong>Test Instructions:</strong><br>
                    <?= nl2br(h($setassignment->details)) ?>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-warning">
                <strong><i class="fa fa-exclamation-triangle"></i> Important Notice:</strong><br>
                You are taking this test on behalf of <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>. 
                Please ensure you answer the questions as your child would, or assist them in completing the test.
            </div>
        </div>
    </div>

    <!-- Test Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-edit"></i> Test Questions
                                                 <span id="question-counter" class="ml-2 text-info">(Question 1 of <?= isset($questions) ? $questions->count() : 0 ?>)</span>
                    </h6>
                </div>
                <div class="col-md-4 text-right">
                    <div class="h3 mb-0">
                        <i class="fa fa-check-circle"></i> 
                        <span>Test in Progress</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?= $this->Form->create(null, ['id' => 'test-form', 'type' => 'post']) ?>
            
            <?php if (empty($questions)): ?>
                <div class="alert alert-warning">
                    <strong>No questions found!</strong> This test appears to have no questions yet.
                </div>
            <?php else: ?>
                <!-- Progress Bar -->
                <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%;" 
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <span id="progress-text">0%</span>
                    </div>
                </div>

                <!-- Questions -->
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-block mb-4 p-4 border rounded" id="question-<?= $index + 1 ?>">
                        <div class="question-header d-flex justify-content-between align-items-start mb-3">
                                                         <h5 class="mb-0">Question <?= $index + 1 ?> of <?= isset($questions) ? $questions->count() : 0 ?></h5>
                            <div class="question-meta">
                                <span class="badge badge-info"><?= h($question->points) ?> point<?= $question->points != 1 ? 's' : '' ?></span>
                                <span class="badge badge-secondary"><?= ucfirst(str_replace('_', ' ', $question->question_type)) ?></span>
                            </div>
                        </div>
                        
                        <div class="question-text mb-4">
                            <h6><?= nl2br(h($question->question_text)) ?></h6>
                        </div>
                        
                        <?php if ($question->question_type === 'multiple_choice'): ?>
                            <!-- Multiple Choice Options -->
                            <div class="answer-options">
                                <h6>Select the correct answer:</h6>
                                <?php foreach ($question->question_options as $optionIndex => $option): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?= $question->id ?>]" 
                                               id="option_<?= $question->id ?>_<?= $optionIndex ?>" 
                                               value="<?= $option->id ?>" required>
                                        <label class="form-check-label" for="option_<?= $question->id ?>_<?= $optionIndex ?>">
                                            <span class="option-letter me-2"><?= chr(65 + $optionIndex) ?></span>
                                            <?= h($option->option_text) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <!-- Theory Question -->
                            <div class="theory-answer">
                                <h6>Your answer:</h6>
                                <textarea name="answers[<?= $question->id ?>]" 
                                          class="form-control" rows="4" 
                                          placeholder="Type your answer here..." required></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <!-- Navigation Buttons -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-secondary" id="prev-btn" onclick="previousQuestion()">
                            <i class="fa fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-info" id="next-btn" onclick="nextQuestion()">
                            Next <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-success btn-lg" id="submit-btn" style="display: none;">
                            <i class="fa fa-check"></i> Submit Test
                        </button>
                    </div>
                </div>
                
                <!-- Question Navigation -->
                <div class="question-nav mb-4">
                    <h6>Quick Navigation:</h6>
                    <div class="btn-group" role="group">
                                                 <?php for ($i = 1; $i <= (isset($questions) ? $questions->count() : 0); $i++): ?>
                            <button type="button" class="btn btn-outline-primary btn-sm question-nav-btn" 
                                    onclick="goToQuestion(<?= $i ?>)">
                                <?= $i ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
            
                                      </form>
        </div>
    </div>
</div>

<style>
.question-block {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    display: none; /* Hide all questions by default */
}

.question-block:first-child {
    display: block; /* Show only the first question initially */
}

.question-block {
    background-color: #ffffff;
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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

.question-nav-btn {
    margin: 2px;
}

.question-nav-btn.answered {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.question-nav-btn.current {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

#timer {
    color: #dc3545;
    font-weight: bold;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    background-color: #007bff;
    transition: width 0.3s ease;
}
</style>

<script>
let currentQuestion = 1;
 const totalQuestions = <?= isset($questions) ? $questions->count() : 0 ?>;
let answeredQuestions = new Set();
let timeRemaining = 0; // Timer disabled

// Initialize the test
document.addEventListener('DOMContentLoaded', function() {
    showQuestion(1);
    updateProgress();
    
    // Timer disabled
});

// Show specific question
function showQuestion(questionNumber) {
    // Hide all questions
    for (let i = 1; i <= totalQuestions; i++) {
        document.getElementById('question-' + i).style.display = 'none';
    }
    
    // Show current question
    document.getElementById('question-' + questionNumber).style.display = 'block';
    
    // Update current question variable
    currentQuestion = questionNumber;
    
    // Update navigation buttons
    updateNavigationButtons();
    updateQuestionNav();
    
    // Debug info
    console.log('Showing question:', questionNumber);
}

// Next question
function nextQuestion() {
    if (currentQuestion < totalQuestions) {
        currentQuestion++;
        showQuestion(currentQuestion);
    }
}

// Previous question
function previousQuestion() {
    if (currentQuestion > 1) {
        currentQuestion--;
        showQuestion(currentQuestion);
    }
}

// Go to specific question
function goToQuestion(questionNumber) {
    currentQuestion = questionNumber;
    showQuestion(questionNumber);
}

// Update navigation buttons
function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    // Show/hide Previous button
    prevBtn.style.display = currentQuestion === 1 ? 'none' : 'inline-block';
    
    // Show/hide Next button
    nextBtn.style.display = currentQuestion === totalQuestions ? 'none' : 'inline-block';
    
    // Show Submit button only on last question
    submitBtn.style.display = currentQuestion === totalQuestions ? 'inline-block' : 'none';
    
    // Debug info
    console.log('Current question:', currentQuestion, 'Total questions:', totalQuestions);
    console.log('Prev visible:', prevBtn.style.display !== 'none');
    console.log('Next visible:', nextBtn.style.display !== 'none');
    console.log('Submit visible:', submitBtn.style.display !== 'none');
}

// Update question navigation
function updateQuestionNav() {
    const navBtns = document.querySelectorAll('.question-nav-btn');
    navBtns.forEach((btn, index) => {
        btn.classList.remove('current');
        if (index + 1 === currentQuestion) {
            btn.classList.add('current');
        }
    });
    
    // Update question counter
    const questionCounter = document.getElementById('question-counter');
    if (questionCounter) {
        questionCounter.textContent = `(Question ${currentQuestion} of ${totalQuestions})`;
    }
}

// Update progress
function updateProgress() {
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const percentage = Math.round((answeredQuestions.size / totalQuestions) * 100);
    
    progressBar.style.width = percentage + '%';
    progressText.textContent = percentage + '%';
}

// Mark question as answered
function markQuestionAnswered(questionId) {
    answeredQuestions.add(questionId);
    updateProgress();
    
    // Update navigation buttons
    const questionNumber = Array.from(document.querySelectorAll('.question-block')).findIndex(block => 
        block.querySelector(`input[name="answers[${questionId}]"], textarea[name="answers[${questionId}]"]`)
    ) + 1;
    
    if (questionNumber > 0) {
        const navBtn = document.querySelector(`.question-nav-btn:nth-child(${questionNumber})`);
        if (navBtn) {
            navBtn.classList.add('answered');
        }
    }
}

// Timer functionality disabled

// Track answered questions
document.addEventListener('change', function(e) {
    if (e.target.name && e.target.name.startsWith('answers[')) {
        const questionId = e.target.name.match(/\[(\d+)\]/)[1];
        markQuestionAnswered(questionId);
    }
});

// Form submission confirmation
document.getElementById('test-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    if (answeredQuestions.size < totalQuestions) {
        const unanswered = totalQuestions - answeredQuestions.size;
        if (!confirm(`You have ${unanswered} unanswered question(s). Are you sure you want to submit the test?`)) {
            return;
        }
    }
    
    if (!confirm('Are you sure you want to submit this test? You cannot change your answers after submission.')) {
        return;
    }
    
    // Disable submit button to prevent double submission
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('submit-btn').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
    
    // Collect form data manually
    const formData = new FormData();
    
    // Add all answers to form data - FIXED: Only collect checked radio buttons and filled textareas
    document.querySelectorAll('input[name^="answers["], textarea[name^="answers["]').forEach(function(input) {
        if (input.type === 'radio') {
            // Only add checked radio buttons
            if (input.checked && input.value) {
                formData.append(input.name, input.value);
            }
        } else if (input.type === 'textarea') {
            // Add textarea values if they have content
            if (input.value && input.value.trim() !== '') {
                formData.append(input.name, input.value);
            }
        }
    });
    
    // Debug: Log what we're sending
    console.log('Sending form data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Submit via AJAX to bypass FormProtection
    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text();
        }
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (typeof data === 'object' && data.success) {
            // JSON response - success
            alert(data.message || 'Test submitted successfully!');
            if (data.redirect) {
                window.location.href = '<?= $this->Url->build(['controller' => 'Sparents', 'action' => 'viewstudentresult']) ?>/' + data.redirect;
            } else {
                window.location.href = '<?= $this->Url->build(['controller' => 'Sparents', 'action' => 'mykidsassignments']) ?>';
            }
        } else if (typeof data === 'string') {
            // HTML response - check for success indicators
            if (data.includes('success') || data.includes('redirect') || data.includes('Assignment completed successfully')) {
                alert('Test submitted successfully!');
                window.location.href = '<?= $this->Url->build(['controller' => 'Sparents', 'action' => 'mykidsassignments']) ?>';
            } else {
                alert('Submission failed. Please try again.');
                document.getElementById('submit-btn').disabled = false;
                document.getElementById('submit-btn').innerHTML = '<i class="fa fa-check"></i> Submit Test';
            }
        } else {
            // JSON response - failure
            alert(data.message || 'Submission failed. Please try again.');
            document.getElementById('submit-btn').disabled = false;
            document.getElementById('submit-btn').innerHTML = '<i class="fa fa-check"></i> Submit Test';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Submission failed. Please try again.');
        document.getElementById('submit-btn').disabled = false;
        document.getElementById('submit-btn').innerHTML = '<i class="fa fa-check"></i> Submit Test';
    });
});
</script>
