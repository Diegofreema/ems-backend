<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View Response</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Setassignments', 'action' => 'index', $this->GenerateUrl('Manage Assignments')], ['title' => 'Manage Assignments']) ?></li>
                    <li class="breadcrumb-item">
                        <?php if (isset($setassgnmtid)): ?>
                            <?= $this->Html->link('Submissions', ['controller' => 'Assignments', 'action' => 'viewrespones', $setassgnmtid], ['title' => 'View all submissions']) ?>
                        <?php else: ?>
                            Submissions
                        <?php endif; ?>
                    </li>
                    <li class="breadcrumb-item active">View Response</li>
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
                        <h4 class="card-title mb-0"><?= h($assignment->has('subject') ? $assignment->subject->name : 'Assignment Response') ?></h4>
                        <small class="text-muted">Submitted on: <?= h($assignment->datecreated) ?></small>
                    </div>
                    <div>
                        <?php $status = ucfirst($assignment->status ?? ''); ?>
                        <span class="badge <?= strtolower($assignment->status ?? '') === 'submitted' ? 'badge-success' : 'badge-secondary' ?>" style="font-size:90%;">
                            <?= h($status ?: 'Submitted') ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Response</strong></div>
                    <div class="p-3" style="background:#fafafa;border:1px solid #eee;border-radius:6px;line-height:1.7;">
                        <?= ($assignment->details) ?>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fa fa-book"></i> Course: <strong><?= h($assignment->has('subject') ? $assignment->subject->name : '') ?></strong>
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
                    <h5 class="card-title mb-0">Submission Info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><span class="text-muted">Student:</span> <strong><?= h(($assignment->student->fname ?? $assignment->student->firstname ?? '') . ' ' . ($assignment->student->lname ?? $assignment->student->lastname ?? '')) ?></strong></li>
                        <li class="mb-2"><span class="text-muted">Reg No:</span> <strong><?= h($assignment->student->regno ?? '') ?></strong></li>
                        <li class="mb-2"><span class="text-muted">Course:</span> <strong><?= h($assignment->has('subject') ? $assignment->subject->name : '') ?></strong></li>
                        <li class="mb-2"><span class="text-muted">Created:</span> <?= h($assignment->datecreated) ?></li>
                        <li class="mb-2"><span class="text-muted">Status:</span> <?= h(ucfirst($assignment->status ?? 'Submitted')) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
