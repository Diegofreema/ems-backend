<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">View Assignment</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Assignments', 'action' => 'myassignments', $this->GenerateUrl('Manage Assignments')], ['title' => 'Manage Assignments'])
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
                        <h4 class="card-title mb-0"><?= h($assignment->has('subject') ? $assignment->subject->name : 'Assignment') ?></h4>
                        <small class="text-muted">Created on: <?= h($assignment->datecreated) ?></small>
                    </div>
                    <div>
                        <?php
                        // Account for server timezone being 1 hour behind system time
                        $currentTime = time() + 3600; // Add 1 hour (3600 seconds) to current time
                        $isClosed = !empty($assignment->closedate) && strtotime($assignment->closedate) < $currentTime;
                        $statusClass = 'badge-warning';
                        if (!empty($assignment->status) && strtolower($assignment->status) === 'closed') {
                            $statusClass = 'badge-secondary';
                        } elseif ($isClosed) {
                            $statusClass = 'badge-danger';
                        } elseif (!empty($assignment->status) && strtolower($assignment->status) === 'open') {
                            $statusClass = 'badge-success';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>" style="font-size: 90%;">
                            <?= h(ucfirst($assignment->status ?? 'Open')) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($assignment->closedate)): ?>
                        <div class="mb-3">
                            <span class="text-muted">Due date:</span>
                            <span class="badge <?= (strtotime($assignment->closedate) < $currentTime) ? 'badge-danger' : 'badge-info' ?>">
                                <?= date('d M Y', strtotime($assignment->closedate)) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="mb-2"><strong>Assignment Details</strong></div>
                    <div class="p-3" style="background:#fafafa;border:1px solid #eee;border-radius:6px;line-height:1.7;">
                        <?= ($assignment->details) ?>
                    </div>

                    <?php if (!empty($answer->details)): ?>
                        <hr />
                        <div class="mb-2"><strong>My Submission</strong></div>
                        <div class="p-3" style="background:#f7fff7;border:1px solid #e6f5e6;border-radius:6px;line-height:1.7;">
                            <?= ($answer->details) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <?php if (!empty($assignment->closedate)): ?>
                            <?php if (strtotime($assignment->closedate) < $currentTime): ?>
                                <i class="fa fa-clock-o text-danger"></i> Closed
                            <?php else: ?>
                                <i class="fa fa-clock-o text-info"></i> Open
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (empty($answer->details)): ?>
                            <?= $this->Html->link(
                                'Submit Response',
                                ['controller' => 'Assignments', 'action' => 'submitassignment', $assignment->subject->id, $assignment->id, $assignment->subject->name],
                                ['title' => 'submit assignment', 'class' => 'btn btn-primary']
                            ) ?>
                        <?php else: ?>
                            <span class="badge badge-success">Submitted</span>
                        <?php endif; ?>
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
                        <li class="mb-2"><span class="text-muted">Course:</span> <strong><?= h($assignment->has('subject') ? $assignment->subject->name : '') ?></strong></li>
                        <li class="mb-2"><span class="text-muted">Created:</span> <?= h($assignment->datecreated) ?></li>
                        <?php if (!empty($assignment->closedate)): ?>
                            <li class="mb-2"><span class="text-muted">Due:</span> <?= date('d M Y', strtotime($assignment->closedate)) ?></li>
                        <?php endif; ?>
                        <li class="mb-2"><span class="text-muted">Status:</span> <?= h(ucfirst($assignment->status ?? 'Open')) ?></li>
                    </ul>
                </div>
            </div>

            <?php if (empty($answer->details)): ?>
                <div class="card">
                    <div class="card-body">
                        <?= $this->Html->link(
                            'Submit Now',
                            ['controller' => 'Assignments', 'action' => 'submitassignment', $assignment->subject->id, $assignment->id, $assignment->subject->name],
                            ['title' => 'submit assignment', 'class' => 'btn btn-success btn-block']
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
