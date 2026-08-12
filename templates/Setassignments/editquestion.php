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
                    <h3 class="page-title">Edit Question</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Setassignments', 'action' => 'index'], ['title' => 'Manage Assignments'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                            ?></li>
                        <li class="breadcrumb-item active">Edit Question</li>
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
            <p><strong>Subject:</strong> <?= $setassignment->has('subject') ? $setassignment->subject->name . ' (' . (isset($setassignment->subject->department) ? $setassignment->subject->department->name : 'No Class') . ')' : '' ?></p>
        </div>
    </div>

    <!-- Question Form -->
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Edit Question</h1>
                        </div>
                        
                        <?= $this->Form->create($question, ['url' => ['action' => 'editquestion', $question->id], 'method' => 'post']) ?>
                        <fieldset>
                            <legend>Question Details</legend>
                            
                            <div class="form-group row">
                                <div class="col-sm-8 mb-3 mb-sm-0">
                                    <?= $this->Form->control('question_text', ['label'=>'Question Text', 'type'=>'textarea', 'class'=>'form-control form-control-user2', 'placeholder' => 'Enter your question here...', 'rows' => 3, 'required' => true])?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('question_type', ['label'=>'Question Type', 'class'=>'form-control form-control-user2', 'options' => ['multiple_choice' => 'Multiple Choice', 'theory' => 'Theory/Essay'], 'id' => 'questionType', 'default' => 'multiple_choice'])?>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('points', ['label'=>'Points', 'type' => 'number', 'min' => 1, 'max' => 100, 'class'=>'form-control form-control-user2', 'placeholder' => 'Points for this question', 'required' => true])?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('order_number', ['label'=>'Question Order', 'type' => 'number', 'min' => 1, 'class'=>'form-control form-control-user2', 'placeholder' => 'Order in test', 'required' => true])?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('difficulty_level', ['label'=>'Difficulty', 'class'=>'form-control form-control-user2', 'options' => ['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard']])?>
                                </div>
                            </div>
                            
                            <!-- Multiple Choice Options -->
                            <div id="multipleChoiceOptions" style="display: <?= ($question->question_type === 'multiple_choice' && !empty($question->question_options)) ? 'block' : 'none' ?>;">
                                <div class="form-group">
                                    <div class="alert alert-primary">
                                        <h6 class="alert-heading"><i class="fa fa-list"></i> <strong>Multiple Choice Options</strong></h6>
                                        <p class="mb-0">Edit the answer choices below. Students will select from these options during the test.</p>
                                    </div>
                                    <label><strong>Answer Options</strong> <span class="text-danger">*</span></label>
                                    <div class="alert alert-info">
                                        <small><i class="fa fa-info-circle"></i> <strong>Instructions:</strong> You need at least 2 options. Use the radio buttons to mark the correct answer.</small>
                                    </div>
                                    <div id="optionsContainer">
                                        <?php if (!empty($question->question_options)): ?>
                                                                                         <?php foreach ($question->question_options as $index => $option): ?>
                                                 <div class="row mb-3 option-row">
                                                     <div class="col-sm-1">
                                                         <span class="badge badge-primary"><?= chr(65 + $index) ?></span>
                                                     </div>
                                                     <div class="col-sm-7">
                                                         <input type="text" name="options[<?= $option->id ?>]" class="form-control" placeholder="Enter option <?= chr(65 + $index) ?>" value="<?= h($option->option_text) ?>" required>
                                                     </div>
                                                     <div class="col-sm-3">
                                                         <div class="form-check">
                                                             <input class="form-check-input" type="radio" name="correct_option" value="<?= $option->id ?>" id="correct<?= $option->id ?>" <?= $option->is_correct ? 'checked' : '' ?> required>
                                                             <label class="form-check-label" for="correct<?= $option->id ?>"><strong>Correct Answer</strong></label>
                                                         </div>
                                                     </div>
                                                     <div class="col-sm-1">
                                                         <?php if (count($question->question_options) > 2): ?>
                                                             <button type="button" class="btn btn-sm btn-danger remove-option">×</button>
                                                         <?php endif; ?>
                                                     </div>
                                                 </div>
                                             <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-secondary" id="addOption">+ Add Option</button>
                                    <small class="form-text text-muted">You can add up to 6 options total.</small>
                                </div>
                            </div>
                            
                            <!-- Theory Question Instructions -->
                            <div id="theoryInstructions" style="display: <?= $question->question_type === 'theory' ? 'block' : 'none' ?>;">
                                <div class="alert alert-info">
                                    <strong>Theory Question:</strong> Students will provide written answers. You can grade these manually after submission.
                                </div>
                            </div>
                            
                        </fieldset>
                        
                        <br />
                        <br /> 
                        <br />
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $this->Html->link(__('Cancel'), ['action' => 'managequestions', $setassignment->id], ['class' => 'btn btn-secondary btn-user btn-block']) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $this->Form->button('Update Question', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                            </div>
                        </div>
                        
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    var questionTypeSelect = document.getElementById('questionType');
    var multipleChoiceOptions = document.getElementById('multipleChoiceOptions');
    var theoryInstructions = document.getElementById('theoryInstructions');
    var addOptionBtn = document.getElementById('addOption');
    var optionsContainer = document.getElementById('optionsContainer');
    
    // Show/hide options based on question type
    function toggleOptions() {
        var questionType = questionTypeSelect.value;
        if (questionType === 'multiple_choice') {
            // Only show options if there are existing options
            var existingOptions = document.querySelectorAll('input[name^="options"]');
            if (existingOptions.length > 0) {
                multipleChoiceOptions.style.display = 'block';
                theoryInstructions.style.display = 'none';
                // Make options required for multiple choice
                var optionInputs = document.querySelectorAll('input[name^="options"]');
                var correctOptionInputs = document.querySelectorAll('input[name="correct_option"]');
                optionInputs.forEach(function(input) {
                    input.required = true;
                });
                correctOptionInputs.forEach(function(input) {
                    input.required = true;
                });
            } else {
                // No existing options, show theory instructions
                multipleChoiceOptions.style.display = 'none';
                theoryInstructions.style.display = 'block';
            }
        } else {
            multipleChoiceOptions.style.display = 'none';
            theoryInstructions.style.display = 'block';
            // Remove required for theory questions
            var optionInputs = document.querySelectorAll('input[name^="options"]');
            var correctOptionInputs = document.querySelectorAll('input[name="correct_option"]');
            optionInputs.forEach(function(input) {
                input.required = false;
            });
            correctOptionInputs.forEach(function(input) {
                input.required = false;
            });
        }
    }
    
    // Add event listener for question type change
    if (questionTypeSelect) {
        questionTypeSelect.addEventListener('change', toggleOptions);
    }
    
    // Add new option
    if (addOptionBtn) {
        addOptionBtn.addEventListener('click', function() {
            var optionInputs = document.querySelectorAll('input[name^="options"]');
            var optionCount = optionInputs.length;
            
            if (optionCount >= 6) {
                alert('Maximum 6 options allowed');
                return;
            }
            
            // Get option letter (A, B, C, D, E, F)
            var optionLetter = String.fromCharCode(65 + optionCount); // 65 = 'A' in ASCII
            
            // Generate a unique ID for new options
            var newOptionId = 'new_' + Date.now() + '_' + optionCount;
            
            var newOption = document.createElement('div');
            newOption.className = 'row mb-3 option-row';
            newOption.innerHTML = `
                <div class="col-sm-1">
                    <span class="badge badge-primary">${optionLetter}</span>
                </div>
                <div class="col-sm-7">
                    <input type="text" name="options[${newOptionId}]" class="form-control" placeholder="Enter option ${optionLetter}" required>
                </div>
                <div class="col-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct_option" value="${newOptionId}" id="correct${newOptionId}" required>
                        <label class="form-check-label" for="correct${newOptionId}"><strong>Correct Answer</strong></label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-sm btn-danger remove-option">×</button>
                </div>
            `;
            
            optionsContainer.appendChild(newOption);
            updateRemoveButtons();
        });
    }
    
    // Remove option
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-option')) {
            var optionInputs = document.querySelectorAll('input[name^="options"]');
            if (optionInputs.length > 2) {
                e.target.closest('.option-row').remove();
                updateRemoveButtons();
                updateCorrectOptionValues();
            }
        }
    });
    
    function updateRemoveButtons() {
        var optionInputs = document.querySelectorAll('input[name^="options"]');
        var removeButtons = document.querySelectorAll('.remove-option');
        
        if (optionInputs.length <= 2) {
            removeButtons.forEach(function(btn) {
                btn.style.display = 'none';
            });
        } else {
            removeButtons.forEach(function(btn) {
                btn.style.display = 'inline-block';
            });
        }
    }
    
    function updateCorrectOptionValues() {
        var correctOptionInputs = document.querySelectorAll('input[name="correct_option"]');
        correctOptionInputs.forEach(function(input, index) {
            input.value = index;
            input.id = 'correct' + index;
            var label = input.nextElementSibling;
            if (label) {
                label.setAttribute('for', 'correct' + index);
            }
        });
    }
    
    // Form validation
    var form = document.querySelector('form');
    if (form) {
        console.log('Form found, adding submit listener');
        form.addEventListener('submit', function(e) {
            console.log('Form submitted!');
            var questionType = questionTypeSelect.value;
            
            if (questionType === 'multiple_choice') {
                // Check if at least 2 options are filled
                var filledOptions = 0;
                var optionInputs = document.querySelectorAll('input[name^="options"]');
                optionInputs.forEach(function(input) {
                    if (input.value.trim() !== '') {
                        filledOptions++;
                    }
                });
                
                if (filledOptions < 2) {
                    e.preventDefault();
                    alert('Please fill in at least 2 options for multiple choice questions.');
                    return false;
                }
                
                // Check if correct option is selected
                var checkedOption = document.querySelector('input[name="correct_option"]:checked');
                if (!checkedOption) {
                    e.preventDefault();
                    alert('Please select the correct answer.');
                    return false;
                }
            }
            
            // If we get here, validation passed - allow form submission
            console.log('Validation passed, allowing form submission');
        });
    }
    
    // Initialize
    updateRemoveButtons();
});
</script>
