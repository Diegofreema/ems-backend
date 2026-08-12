<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

    // Default fallback
    $previousPage = 'Dashboard';
    $previousUrl = ['controller' => 'Teachers', 'action' => 'dashboard'];

?>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Attendance Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['title' => 'Teacher dashboard']) ?></li>
                    <!-- <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li> -->
                    <li class="breadcrumb-item active">Attendance</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Department Info -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-building"></i> 
                        <?php if (!empty($teacherClassArms) && $teacherClassArms->count() > 0): ?>
                            <?php 
                            $classArmNames = [];
                            foreach ($teacherClassArms as $classArm) {
                                $classArmNames[] = $classArm->department->name . ' - ' . $classArm->arm_name;
                            }
                            echo h(implode(', ', $classArmNames));
                            ?>
                        <?php else: ?>
                            <?= h($teacher->department->name ?? 'Department') ?>
                        <?php endif; ?> - Attendance Dashboard
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Teacher:</strong> <?= h($teacher->firstname . ' ' . $teacher->lastname) ?></p>
                            <p><strong>Date:</strong> <?= date('l, F j, Y', strtotime($today)) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Students:</strong> <?= count($students) ?></p>
                            <p><strong>Status:</strong> 
                                <?php if ($attendanceTaken): ?>
                                    <span class="badge badge-success">Attendance Taken</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-plus"></i> Take Today\'s Attendance',
                                ['action' => 'take', 'date' => $today],
                                ['class' => 'btn btn-primary btn-block', 'escape' => false]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-eye"></i> View Today\'s Attendance',
                                ['action' => 'view', 'date' => $today],
                                ['class' => 'btn btn-info btn-block', 'escape' => false]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-chart-bar"></i> Attendance Report',
                                ['action' => 'report'],
                                ['class' => 'btn btn-success btn-block', 'escape' => false]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Html->link(
                                '<i class="fa fa-calendar"></i> Take Other Date',
                                ['action' => 'take'],
                                ['class' => 'btn btn-secondary btn-block', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Summary -->
    <?php if ($attendanceTaken && !empty($todayAttendance)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Today's Attendance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $presentCount = 0;
                        $absentCount = 0;
                        $lateCount = 0;
                        $excusedCount = 0;
                        
                        foreach ($todayAttendance as $attendance) {
                            switch ($attendance->status) {
                                case 'present':
                                    $presentCount++;
                                    break;
                                case 'absent':
                                    $absentCount++;
                                    break;
                                case 'late':
                                    $lateCount++;
                                    break;
                                case 'excused':
                                    $excusedCount++;
                                    break;
                            }
                        }
                        ?>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Present</h6>
                                <h4><?= $presentCount ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Absent</h6>
                                <h4><?= $absentCount ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Late</h6>
                                <h4><?= $lateCount ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Excused</h6>
                                <h4><?= $excusedCount ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Monthly Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">This Month's Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Total Records</h6>
                                <h4><?= isset($monthlyStats['total']) ? $monthlyStats['total'] : 0 ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Present</h6>
                                <h4><?= isset($monthlyStats['present']) ? $monthlyStats['present'] : 0 ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Absent</h6>
                                <h4><?= isset($monthlyStats['absent']) ? $monthlyStats['absent'] : 0 ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Attendance Rate</h6>
                                <h4>
                                    <?php 
                                    $total = isset($monthlyStats['total']) ? $monthlyStats['total'] : 0;
                                    $present = isset($monthlyStats['present']) ? $monthlyStats['present'] : 0;
                                    $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                                    echo $rate . '%';
                                    ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Students List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Students in Your Class</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($students)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Name</th>
                                        <th>Today's Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <?php
                                        $todayStatus = null;
                                        foreach ($todayAttendance as $attendance) {
                                            if ($attendance->student_id == $student->id) {
                                                $todayStatus = $attendance;
                                                break;
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= h($student->regno) ?></td>
                                            <td><?= h($student->fname . ' ' . $student->lname) ?></td>
                                            <td>
                                                <?php if ($todayStatus): ?>
                                                    <span class="badge <?= $todayStatus->getStatusBadgeClass() ?>">
                                                        <?= $todayStatus->getStatusDisplay() ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Not Marked</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $this->Html->link(
                                                    'View History',
                                                    ['action' => 'report', 'student_id' => $student->id],
                                                    ['class' => 'btn btn-sm btn-outline-primary']
                                                ) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-users fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No students found</h5>
                            <?php if (!empty($teacherClassArms) && $teacherClassArms->count() > 0): ?>
                                <p class="text-gray-400">There are no students assigned to your class arms.</p>
                            <?php else: ?>
                                <p class="text-gray-400">You have not been assigned to any class arms yet. Contact your administrator to get assigned to specific class arms.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-12 text-center">
            <?= $this->Html->link(__('Back to Dashboard'), 
                ['controller' => 'Teachers', 'action' => 'dashboard'], 
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>
    </div>
</div>

<style>
.stats-info {
    text-align: center;
    padding: 20px;
    background: #f8f9fc;
    border-radius: 8px;
    margin-bottom: 20px;
}

.stats-info h6 {
    color: #6c757d;
    margin-bottom: 10px;
    font-weight: 600;
}

.stats-info h4 {
    color: #4e73df;
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    margin-bottom: 20px;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.btn-block {
    width: 100%;
    margin-bottom: 10px;
}

.table th {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>
