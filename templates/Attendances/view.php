<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'index') !== false) {
    $previousPage = 'Attendance Dashboard';
    $previousUrl = ['action' => 'index'];
} else {
    // Default fallback
    $previousPage = 'Attendance Dashboard';
    $previousUrl = ['action' => 'index'];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['title' => 'Teacher dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Attendance', ['action' => 'index'], ['title' => 'Attendance Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li>
                    <li class="breadcrumb-item active">View Attendance</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Date Selection -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-calendar"></i> 
                        Select Date to View Attendance
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date-select">Attendance Date</label>
                                <input type="date" id="date-select" class="form-control" value="<?= h($date) ?>" 
                                       onchange="changeDate(this.value)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quick Date Selection</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="setDate('<?= date('Y-m-d') ?>')">Today</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="setDate('<?= date('Y-m-d', strtotime('-1 day')) ?>')">Yesterday</button>
                                    <button type="button" class="btn btn-sm btn-info" onclick="setDate('<?= date('Y-m-d', strtotime('-7 days')) ?>')">Last Week</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Details -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-list"></i> 
                        Attendance for 
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
                        <?php endif; ?> - <?= date('l, F j, Y', strtotime($date)) ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (empty($attendance)): ?>
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> No Attendance Recorded</h5>
                            <p>No attendance has been recorded for this date. You can take attendance by clicking the button below.</p>
                            <?= $this->Html->link(
                                '<i class="fa fa-plus"></i> Take Attendance for This Date',
                                ['action' => 'take', 'date' => $date],
                                ['class' => 'btn btn-primary', 'escape' => false]
                            ) ?>
                        </div>
                    <?php else: ?>
                        <!-- Attendance Summary -->
                        <div class="row mb-4">
                            <?php
                            $presentCount = 0;
                            $absentCount = 0;
                            $lateCount = 0;
                            $excusedCount = 0;
                            
                            foreach ($attendance as $record) {
                                switch ($record->status) {
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
                            
                            $totalStudents = count($students);
                            $attendanceRate = $totalStudents > 0 ? round((($presentCount + $lateCount) / $totalStudents) * 100, 1) : 0;
                            ?>
                            <div class="col-md-3">
                                <div class="stats-info">
                                    <h6>Present</h6>
                                    <h4 class="text-success"><?= $presentCount ?></h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-info">
                                    <h6>Absent</h6>
                                    <h4 class="text-danger"><?= $absentCount ?></h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-info">
                                    <h6>Late</h6>
                                    <h4 class="text-warning"><?= $lateCount ?></h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-info">
                                    <h6>Attendance Rate</h6>
                                    <h4 class="text-info"><?= $attendanceRate ?>%</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Reg. No.</th>
                                        <th width="25%">Student Name</th>
                                        <th width="15%">Status</th>
                                        <th width="25%">Notes</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $attendanceLookup = [];
                                    foreach ($attendance as $record) {
                                        $attendanceLookup[$record->student_id] = $record;
                                    }
                                    
                                    foreach ($students as $index => $student): 
                                        $attendanceRecord = $attendanceLookup[$student->id] ?? null;
                                    ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= h($student->regno) ?></strong>
                                            </td>
                                            <td>
                                                <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($attendanceRecord): ?>
                                                    <span class="badge <?= $attendanceRecord->getStatusBadgeClass() ?>">
                                                        <?= $attendanceRecord->getStatusDisplay() ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Not Marked</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($attendanceRecord && !empty($attendanceRecord->notes)): ?>
                                                    <small class="text-muted"><?= h($attendanceRecord->notes) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $this->Html->link(
                                                    'Edit',
                                                    ['action' => 'take', 'date' => $date],
                                                    ['class' => 'btn btn-sm btn-outline-primary']
                                                ) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <?= $this->Html->link(
                                    '<i class="fa fa-edit"></i> Edit Attendance',
                                    ['action' => 'take', 'date' => $date],
                                    ['class' => 'btn btn-primary mr-3', 'escape' => false]
                                ) ?>
                                <?= $this->Html->link(
                                    '<i class="fa fa-print"></i> Print Report',
                                    ['action' => 'print', 'date' => $date],
                                    ['class' => 'btn btn-info mr-3', 'escape' => false, 'target' => '_blank']
                                ) ?>
                                <?= $this->Html->link(
                                    '<i class="fa fa-download"></i> Export CSV',
                                    ['action' => 'export', 'date' => $date],
                                    ['class' => 'btn btn-success', 'escape' => false]
                                ) ?>
                            </div>
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
                ['action' => 'index'], 
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

.table th {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.alert {
    border-radius: 0.35rem;
}

.form-control {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
</style>

<script>
function changeDate(date) {
    if (date) {
        window.location.href = '<?= $this->Url->build(['action' => 'view']) ?>?date=' + date;
    }
}

function setDate(date) {
    document.getElementById('date-select').value = date;
    changeDate(date);
}
</script>
