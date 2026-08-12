<div class="container">

    <div class="page-header" style="margin-top:20px;">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Submit Assignment</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' My Assignments', ['controller' => 'Assignments', 'action' => 'myassignments', $this->GenerateUrl('My Assignments')], ['title' => 'My Assignments']) ?></li>
                    <li class="breadcrumb-item active">Submit Assignment</li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (isset($setassignment)): ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= h($setassignment->subject->name ?? 'Assignment') ?></strong>
                    <?php if (!empty($setassignment->closedate)): ?>
                        <small class="text-muted">&middot; Due: <?= date('d M Y', strtotime($setassignment->closedate)) ?></small>
                    <?php endif; ?>
                </div>
                <?php if (isset($setassignment->teacher)): ?>
                    <span class="badge badge-light">Teacher: <?= h(($setassignment->teacher->firstname ?? '') . ' ' . ($setassignment->teacher->lastname ?? '')) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="mb-2"><strong>Question</strong></div>
                <div class="p-3" style="background:#fafafa;border:1px solid #eee;border-radius:6px;line-height:1.7;">
                    <?= ($setassignment->details ?? '') ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card o-hidden border-0 shadow-lg my-4">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="h5 text-gray-900 mb-1">Your Response</h4>
                                <small class="text-muted">Provide a clear and detailed answer. You can format your response using the editor below.</small>
                            </div>
                            <div>
                                <?= $this->Html->link('Back to Assignments', ['controller' => 'Assignments', 'action' => 'myassignments', $this->GenerateUrl('My Assignments')], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                            </div>
                        </div>

                        <?= $this->Form->create($assignment) ?>
                        <fieldset>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-sm-12 mb-3 mb-sm-0">
                                            <?= $this->Form->control('subject_id', [
                                                'label' => 'Course Title',
                                                'options' => $subjects,
                                                'class' => 'form-control form-control-user2'
                                            ]) ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-12 mb-3 mb-sm-0">
                                            <?= $this->Form->control('details', [
                                                'label' => 'Your Answer',
                                                'type' => 'textarea',
                                                'class' => 'summernote',
                                                'placeholder' => 'Type your answer here...'
                                            ]) ?>
                                            <small class="text-muted">Tip: Use headings, bullet points and images (if permitted) to make your answer clearer.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">Review your response before submitting.</div>
                            <div>
                                <?= $this->Form->button('Submit Response', ['class' => 'btn btn-primary']) ?>
                                <?= $this->Html->link('Cancel', ['controller' => 'Assignments', 'action' => 'myassignments', $this->GenerateUrl('My Assignments')], ['class' => 'btn btn-light']) ?>
                            </div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
