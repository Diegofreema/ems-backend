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
                <h3 class="page-title">Attendance Report</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['title' => 'Teacher dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Attendance', ['action' => 'index'], ['title' => 'Attendance Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li>
                    <li class="breadcrumb-item active">Report</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Date Range Selection -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-calendar-alt"></i> 
                        Select Date Range for Report
                    </h4>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= $this->Url->build(['action' => 'report']) ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" 
                                           value="<?= h($startDate) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" 
                                           value="<?= h($endDate) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quick Selection</label>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="setDateRange('week')">This Week</button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="setDateRange('month')">This Month</button>
                                        <button type="button" class="btn btn-sm btn-info" onclick="setDateRange('semester')">This Semester</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-chart-bar"></i> 
                        Overall Statistics (<?= date('M j', strtotime($startDate)) ?> - <?= date('M j, Y', strtotime($endDate)) ?>)
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Total Records</h6>
                                <h4 class="text-primary"><?= $stats['total'] ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Present</h6>
                                <h4 class="text-success"><?= $stats['present'] ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Absent</h6>
                                <h4 class="text-danger"><?= $stats['absent'] ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Attendance Rate</h6>
                                <h4 class="text-info">
                                    <?php 
                                    $rate = $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 1) : 0;
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

    <!-- Individual Student Reports -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-users"></i> 
                        Individual Student Attendance
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="10%">Reg. No.</th>
                                    <th width="20%">Student Name</th>
                                    <th width="15%">Present</th>
                                    <th width="15%">Absent</th>
                                    <th width="15%">Late</th>
                                    <th width="15%">Excused</th>
                                    <th width="10%">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <?php
                                    $studentStats = [
                                        'present' => 0,
                                        'absent' => 0,
                                        'late' => 0,
                                        'excused' => 0,
                                        'total' => 0
                                    ];
                                    
                                    if (isset($studentAttendance[$student->id])) {
                                        foreach ($studentAttendance[$student->id] as $record) {
                                            $studentStats[$record->status]++;
                                            $studentStats['total']++;
                                        }
                                    }
                                    
                                    $studentRate = $studentStats['total'] > 0 ? 
                                        round((($studentStats['present'] + $studentStats['late']) / $studentStats['total']) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= h($student->regno) ?></strong>
                                        </td>
                                        <td>
                                            <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?= $studentStats['present'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger"><?= $studentStats['absent'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning"><?= $studentStats['late'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= $studentStats['excused'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $studentRate >= 80 ? 'badge-success' : ($studentRate >= 60 ? 'badge-warning' : 'badge-danger') ?>">
                                                <?= $studentRate ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Attendance Records -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-list-alt"></i> 
                        Detailed Attendance Records
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (empty($studentAttendance) || array_sum(array_map('count', $studentAttendance)) === 0): ?>
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> No Records Found</h5>
                            <p>No attendance records found for the selected date range.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="15%">Reg. No.</th>
                                        <th width="20%">Student Name</th>
                                        <th width="15%">Status</th>
                                        <th width="25%">Notes</th>
                                        <th width="15%">Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $allRecords = [];
                                    foreach ($studentAttendance as $studentId => $records) {
                                        foreach ($records as $record) {
                                            $allRecords[] = $record;
                                        }
                                    }
                                    
                                    // Sort by date descending
                                    usort($allRecords, function($a, $b) {
                                        return strtotime($b->attendance_date) - strtotime($a->attendance_date);
                                    });
                                    
                                    foreach ($allRecords as $record): 
                                        $student = null;
                                        foreach ($students as $s) {
                                            if ($s->id == $record->student_id) {
                                                $student = $s;
                                                break;
                                            }
                                        }
                                        if (!$student) continue;
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?= date('M j, Y', strtotime($record->attendance_date)) ?></strong>
                                            </td>
                                            <td>
                                                <strong><?= h($student->regno) ?></strong>
                                            </td>
                                            <td>
                                                <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge <?= $record->getStatusBadgeClass() ?>">
                                                    <?= $record->getStatusDisplay() ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($record->notes)): ?>
                                                    <small class="text-muted"><?= h($record->notes) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= h($teacher->firstname . ' ' . $teacher->lastname) ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-12 text-center">
            <?= $this->Html->link(
                '<i class="fa fa-print"></i> Print Report',
                ['action' => 'print', 'start_date' => $startDate, 'end_date' => $endDate],
                ['class' => 'btn btn-info mr-3', 'escape' => false, 'target' => '_blank']
            ) ?>
            <?= $this->Html->link(
                '<i class="fa fa-download"></i> Export CSV',
                ['action' => 'export', 'start_date' => $startDate, 'end_date' => $endDate],
                ['class' => 'btn btn-success mr-3', 'escape' => false]
            ) ?>
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
function setDateRange(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'week':
            startDate = new Date(today);
            startDate.setDate(today.getDate() - 7);
            endDate = today;
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = today;
            break;
        case 'semester':
            startDate = new Date(today.getFullYear(), 0, 1); // January 1st
            endDate = today;
            break;
    }
    
    document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
}
</script>
