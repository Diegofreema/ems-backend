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
                    <h3 class="page-title">Edit Assignment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage])
                            ?></li>
                        <li class="breadcrumb-item active">Edit Assignment</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Edit Assignment</h1>
                        </div>
            <?= $this->Form->create($setassignment) ?>
            

            
            <fieldset>
                <legend>Test Configuration</legend>
                 <div class="form-group row">
              <div class="col-sm-4 mb-3 mb-sm-0">
               <?= $this->Form->hidden('subject_id') ?>
               <label class="form-label">Subject</label>
               <div class="form-control form-control-user2" style="background-color: #f8f9fa; color: #6c757d;">
                   <?= h($setassignment->subject->name . ' (' . $setassignment->subject->department->name . ')') ?>
               </div>
               <small class="form-text text-muted">Subject cannot be changed when editing</small>
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
                        <?= $this->Form->control('total_questions', ['label'=>'Total Questions', 'type' => 'number', 'min' => 1, 'max' => 100, 'class'=>'form-control form-control-user2', 'placeholder' => 'Number of questions', 'required' => true, 'id' => 'total-questions'])?>
                    </div>
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('passing_score', ['label'=>'Passing Score (%)', 'type' => 'number', 'min' => 0, 'max' => 100, 'class'=>'form-control form-control-user2', 'placeholder' => 'Minimum score to pass', 'required' => true, 'id' => 'passing-score'])?>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?= $this->Form->control('test_type', ['label'=>'Test Type', 'class'=>'form-control form-control-user2', 'options' => ['cbt_test' => 'CBT Test', 'assignment' => 'Regular Assignment'], 'required' => true, 'id' => 'test-type'])?>
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
            <?= $this->Form->button('Update Test', ['class' => 'btn btn-primary btn-user btn-block', 'id' => 'submitBtn']) ?>
            <?= $this->Form->end() ?>
        </div>
                </div>
            </div>
        </div>
    </div>

</div>
