<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'assignedcourses') !== false) {
    $previousPage = 'My Subjects';
    $previousUrl = ['controller' => 'Teachers', 'action' => 'assignedcourses'];
} elseif (strpos($referer, 'setassignments') !== false) {
    $previousPage = 'Manage Assignments';
    $previousUrl = ['controller' => 'Setassignments', 'action' => 'index'];
} else {
    // Default fallback
    $previousPage = 'My Subjects';
    $previousUrl = ['controller' => 'Teachers', 'action' => 'assignedcourses'];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Create Assignment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                            ?></li>
                        <li class="breadcrumb-item active">Create Assignment</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
    

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create New Assignment</h1>
                        </div>
                        
                        <?php if (empty($subjects)): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>No Subjects Assigned</strong><br>
                                You don't have any subjects assigned to your account yet. Please contact the school administration to assign subjects to your teacher account before creating assignments.
                                <br><br>
                                <?= $this->Html->link('Back to Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['class' => 'btn btn-secondary']) ?>
                            </div>
                        <?php else: ?>
                            <?= $this->Form->create($setassignment) ?>
            

            
            <fieldset>
                <legend>Test Configuration</legend>
                 <div class="form-group row">
              <div class="col-sm-4 mb-3 mb-sm-0">
               <?= $this->Form->control('subject_id', ['options' => $subjects,'class'=>'form-control form-control-user2', 'label' => 'Subject', 'required' => true])?>
               
              </div>
              <div class="col-sm-4 mb-3 mb-sm-0">
                   <?= $this->Form->control('title', ['label'=>'Test Title', 'class'=>'form-control form-control-user2', 'placeholder' => 'Enter test title', 'required' => true, 'id' => 'title'])?>
              </div>
                     <div class="col-sm-4 mb-3 mb-sm-0">
                   <?= $this->Form->control('status',['label'=>'Status', 'class'=>'form-control form-control-user2', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true])?>
              </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('total_questions', ['label'=>'Total Questions', 'type' => 'number', 'min' => 1, 'max' => 100, 'class'=>'form-control form-control-user2', 'placeholder' => 'Number of questions', 'required' => true, 'value' => 10, 'id' => 'total-questions'])?>
                    </div>
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('passing_score', ['label'=>'Passing Score (%)', 'type' => 'number', 'min' => 0, 'max' => 100, 'class'=>'form-control form-control-user2', 'placeholder' => 'Minimum score to pass', 'required' => true, 'value' => 50, 'id' => 'passing-score'])?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('test_type', ['label'=>'Test Type', 'class'=>'form-control form-control-user2', 'options' => ['cbt_test' => 'CBT Test', 'assignment' => 'Regular Assignment'], 'default' => 'cbt_test', 'required' => true, 'id' => 'test-type'])?>
                    </div>
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('details',['label'=>'Test Instructions','type'=>'textarea','class'=>'form-control form-control-user2', 'placeholder' => 'Enter test instructions for students', 'required' => true, 'id' => 'details'])?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('opendate',['label'=>'Opening Date & Time','type'=>'datetime-local', 'class'=>'form-control form-control-user2'])?>
                    </div>
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('closedate',['label'=>'Closing Date & Time','type'=>'datetime-local', 'class'=>'form-control form-control-user2'])?>
                    </div>
                </div>
                
                
            </fieldset>
                         <br />
    <br /> <br />
            <?= $this->Form->button('Create Test', ['class' => 'btn btn-primary btn-user btn-block', 'id' => 'submitBtn']) ?>
            <?= $this->Form->end() ?>
                        <?php endif; ?>
        </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    // Debug: Log form data on submit
    $('form').on('submit', function(e) {
        console.log('Form submitted');
        var formData = $(this).serialize();
        console.log('Form data:', formData);
        
        // Log individual field values
        console.log('Title:', $('#title').val());
        console.log('Total Questions:', $('#total-questions').val());
        console.log('Passing Score:', $('#passing-score').val());
        console.log('Details:', $('#details').val());
        console.log('Test Type:', $('#test-type').val());
    });
    
    // Form validation
    $('#submitBtn').click(function(e) {
        var isValid = true;
        var errorMessage = '';
        
        // Check required fields
        if (!$('#title').val().trim()) {
            errorMessage += 'Test Title is required.\n';
            isValid = false;
        }
        
        if (!$('#total-questions').val()) {
            errorMessage += 'Total Questions is required.\n';
            isValid = false;
        }
        
        
        if (!$('#passing-score').val()) {
            errorMessage += 'Passing Score is required.\n';
            isValid = false;
        }
        
        if (!$('#details').val().trim()) {
            errorMessage += 'Test Instructions are required.\n';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please correct the following errors:\n\n' + errorMessage);
            return false;
        }
        
        // Show loading state
        $(this).prop('disabled', true).text('Creating Test...');
    });
});
</script>
