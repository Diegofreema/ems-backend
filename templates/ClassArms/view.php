<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View Class Arm</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Class Arms', ['controller' => 'ClassArms', 'action' => 'index'], ['title' => 'manage class arms']) ?></li>
                    <li class="breadcrumb-item active">View Class Arm</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Class Arm: <?= h($classArm->department->name . ' ' . $classArm->arm_name) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <!-- Class Arm Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class Arm Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Class:</strong><br>
                            <?= h($classArm->department->name) ?>
                        </div>
                        <div class="col-sm-6">
                            <strong>Arm Name:</strong><br>
                            <?= h($classArm->arm_name) ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Class Teacher:</strong><br>
                            <?php if (!empty($classArm->teacher)): ?>
                                <?= h($classArm->teacher->user->fname . ' ' . $classArm->teacher->user->lname) ?>
                            <?php else: ?>
                                <span class="text-muted">Not Assigned</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <strong>Status:</strong><br>
                            <?php if ($classArm->status === 'active'): ?>
                                <span class="badge badge-success">Active</span>
                            <?php elseif ($classArm->status === 'inactive'): ?>
                                <span class="badge badge-warning">Inactive</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Archived</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($classArm->arm_description)): ?>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <strong>Description:</strong><br>
                            <?= h($classArm->arm_description) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Students List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Students in this Class Arm</h6>
                    <span class="badge badge-info"><?= count($classArm->students) ?> students</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($classArm->students)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Full Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($classArm->students as $student): ?>
                                    <tr>
                                        <td><?= h($student->regno) ?></td>
                                        <td><?= h($student->fname . ' ' . $student->lname) ?></td>
                                        <td>
                                            <?php if ($student->status === 'Admitted'): ?>
                                                <span class="badge badge-success">Admitted</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><?= h($student->status) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No students assigned to this class arm yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= $this->Html->link(__('Edit Class Arm'), ['action' => 'edit', $classArm->id], 
                            ['class' => 'btn btn-warning btn-block']) ?>
                        <?= $this->Html->link(__('Manage Students'), ['action' => 'manageStudents', $classArm->id], 
                            ['class' => 'btn btn-info btn-block']) ?>
                        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], 
                            ['class' => 'btn btn-secondary btn-block']) ?>
                    </div>
                </div>
            </div>

            <!-- Class Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary"><?= count($classArm->students) ?></h4>
                                <p class="text-muted">Total Students</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?= $classArm->status === 'active' ? 'Active' : 'Inactive' ?></h4>
                            <p class="text-muted">Status</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
