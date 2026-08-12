<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Students - <?= h($classArm->arm_name) ?></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Class Arms', ['controller' => 'ClassArms', 'action' => 'index'], ['title' => 'manage class arms']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('View Class Arm', ['controller' => 'ClassArms', 'action' => 'view', $classArm->id], ['title' => 'view class arm']) ?></li>
                    <li class="breadcrumb-item active">Manage Students</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Class Arm Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Class Arm Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <strong>Class:</strong><br>
                    <?= h($classArm->has('department') ? $classArm->department->name : 'No Department') ?>
                </div>
                <div class="col-sm-6">
                    <strong>Arm Name:</strong><br>
                    <?= h($classArm->arm_name) ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-6">
                    <strong>Class Teacher:</strong><br>
                    <?php if (!empty($classArm->teacher) && $classArm->teacher->has('user')): ?>
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
        </div>
    </div>

    <!-- Students in this Class Arm -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students in <?= h($classArm->arm_name) ?> (<?= count($students) ?> students)</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($students)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="assignedStudentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= h($student->regno) ?></td>
                                    <td><?= h($student->fname . ' ' . $student->lname . ' ' . $student->mname) ?></td>
                                    <td><?= h($student->email) ?></td>
                                    <td><?= h($student->phone) ?></td>
                                    <td>
                                        <?= $this->Form->postLink(__(' Remove'), ['action' => 'removeStudent'], 
                                            ['data' => ['student_id' => $student->id, 'class_arm_id' => $classArm->id],
                                             'confirm' => __('Are you sure you want to remove {0} from this class arm?', $student->fname . ' ' . $student->lname),
                                             'class'=>'btn btn-sm btn-danger fa fa-times','title'=>'remove student']) ?>
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

    <!-- Unassigned Students -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Unassigned Students from <?= h($classArm->has('department') ? $classArm->department->name : 'Same Department') ?> (<?= count($unassignedStudents) ?> students)</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($unassignedStudents)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="unassignedStudentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unassignedStudents as $student): ?>
                                <tr>
                                    <td><?= h($student->regno) ?></td>
                                    <td><?= h($student->fname . ' ' . $student->lname . ' ' . $student->mname) ?></td>
                                    <td><?= h($student->email) ?></td>
                                    <td><?= h($student->phone) ?></td>
                                    <td>
                                        <?= $this->Form->postLink(__(' Assign'), ['action' => 'assignStudent'], 
                                            ['data' => ['student_id' => $student->id, 'class_arm_id' => $classArm->id],
                                             'confirm' => __('Are you sure you want to assign {0} to this class arm?', $student->fname . ' ' . $student->lname),
                                             'class'=>'btn btn-sm btn-success fa fa-plus','title'=>'assign student']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No unassigned students available for this class.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row">
        <div class="col-12">
            <?= $this->Html->link(__(' Back to Class Arm Details'), ['action' => 'view', $classArm->id], 
                ['class'=>'btn btn-secondary fa fa-arrow-left','title'=>'back to class arm details']) ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables for both tables
    if ($.fn.DataTable) {
        $('#assignedStudentsTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "order": [[1, "asc"]]
        });
        
        $('#unassignedStudentsTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "order": [[1, "asc"]]
        });
    }
});
</script>