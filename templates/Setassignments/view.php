<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View Assignment</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Setassignments', 'action' => 'index', $this->GenerateUrl('Manage Assignments')], ['title' => 'Manage Assignments'])
                        ?></li>
                    <li class="breadcrumb-item active">View Assignment</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0"><?= h($setassignment->has('subject') ? $setassignment->subject->name : 'Assignment') ?></h4>
                        <small class="text-muted">Created: <?= h($setassignment->datecreated) ?></small>
                    </div>
                    <div>
                        <?php
                        $isClosed = !empty($setassignment->closedate) && strtotime($setassignment->closedate) < time();
                        $statusClass = 'badge-warning';
                        if (!empty($setassignment->status) && strtolower($setassignment->status) === 'closed') {
                            $statusClass = 'badge-secondary';
                        } elseif ($isClosed) {
                            $statusClass = 'badge-danger';
                        } elseif (!empty($setassignment->status) && strtolower($setassignment->status) === 'open') {
                            $statusClass = 'badge-success';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>" style="font-size: 90%;">
                            <?= h(ucfirst($setassignment->status ?? 'Open')) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($setassignment->closedate)): ?>
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted">Due date:</span>
                                <span class="badge <?= (strtotime($setassignment->closedate) < time()) ? 'badge-danger' : 'badge-info' ?>">
                                    <?= date('d M Y', strtotime($setassignment->closedate)) ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted">Submissions:</span>
                                <span class="badge badge-primary"><?= (int)($submissionCount ?? 0) ?></span>
                                <?= $this->Html->link('View submissions', ['controller' => 'Setassignments', 'action' => 'viewsubmissions', $setassignment->id], ['class' => 'btn btn-sm btn-outline-primary', 'style' => 'margin-left:8px;']) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted">Submissions:</span>
                                <span class="badge badge-primary"><?= (int)($submissionCount ?? 0) ?></span>
                                <?= $this->Html->link('View submissions', ['controller' => 'Setassignments', 'action' => 'viewsubmissions', $setassignment->id], ['class' => 'btn btn-sm btn-outline-primary', 'style' => 'margin-left:8px;']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-2"><strong>Details</strong></div>
                    <div class="p-3" style="background:#fafafa;border:1px solid #eee;border-radius:6px;line-height:1.7;">
                        <?= $this->Text->autoParagraph(($setassignment->details)); ?>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fa fa-user"></i>
                        Teacher: <strong><?= h($setassignment->has('teacher') ? $setassignment->teacher->lastname.' '.$setassignment->teacher->firstname : '') ?></strong>
                    </div>
                    <div>
                        <?= $this->Html->link('Back to Assignments', ['controller' => 'Setassignments', 'action' => 'index', $this->GenerateUrl('Manage Assignments')], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assignment Info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><span class="text-muted">Subject:</span> <strong><?= h($setassignment->has('subject') ? $setassignment->subject->name : '') ?></strong></li>
                        <li class="mb-2"><span class="text-muted">Teacher:</span> <?= h($setassignment->has('teacher') ? $setassignment->teacher->lastname.' '.$setassignment->teacher->firstname : '') ?></li>
                        <li class="mb-2"><span class="text-muted">Term:</span> <?= h($setassignment->has('semester') ? $setassignment->semester->name : '') ?></li>
                        <li class="mb-2"><span class="text-muted">Status:</span> <?= h(ucfirst($setassignment->status ?? 'Open')) ?></li>
                        <?php if (!empty($setassignment->closedate)): ?>
                            <li class="mb-2"><span class="text-muted">Closing Date:</span> <?= h($setassignment->closedate) ?></li>
                        <?php endif; ?>
                        <li class="mb-2"><span class="text-muted">Date Created:</span> <?= h($setassignment->datecreated) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
