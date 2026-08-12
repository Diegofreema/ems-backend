<?php
// Get the previous page from HTTP referer
$referer = $this->request->getHeaderLine('Referer');
$previousPage = '';

if (strpos($referer, 'dashboard') !== false) {
    $previousPage = 'Dashboard';
    $previousUrl = ['controller' => 'Sparents', 'action' => 'dashboard'];
} else {
    // Default fallback
    $previousPage = 'Dashboard';
    $previousUrl = ['controller' => 'Sparents', 'action' => 'dashboard'];
}
?>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Child Attendance Report</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Sparents', 'action' => 'dashboard'], ['title' => 'Parent dashboard']) ?></li>
                    <!-- <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li> -->
                    <li class="breadcrumb-item active">Child Attendance</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <?php if (empty($students)): ?>
        <div class="alert alert-info">
            <strong>No children found!</strong> You don't have any children registered in the system.
        </div>
    <?php else: ?>
        <!-- Student Selection and Date Range -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fa fa-calendar-alt"></i> 
                            Select Child and Date Range
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="<?= $this->Url->build(['action' => 'childattendance']) ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="student_id">Select Child</label>
                                        <select name="student_id" id="student_id" class="form-control" required>
                                            <option value="">Choose a child...</option>
                                            <?php foreach ($students as $student): ?>
                                                <option value="<?= $student->id ?>" <?= $studentId == $student->id ? 'selected' : '' ?>>
                                                    <?= h($student->fname . ' ' . $student->lname) ?> (<?= h($student->regno) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" 
                                               value="<?= h($startDate) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" 
                                               value="<?= h($endDate) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Quick Selection</label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="setDateRange('week')">This Week</button>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="setDateRange('month')">This Month</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> View Attendance Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($selectedStudent): ?>
            <!-- Student Information -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fa fa-user-graduate"></i> 
                                <?= h($selectedStudent->fname . ' ' . $selectedStudent->lname) ?> - Attendance Report
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Student Name:</strong> <?= h($selectedStudent->fname . ' ' . $selectedStudent->lname) ?></p>
                                    <p><strong>Registration Number:</strong> <?= h($selectedStudent->regno) ?></p>
                                    <p><strong>Class:</strong> <?= h($selectedStudent->department->name . (!empty($selectedStudent->class_arm) ? ' - ' . $selectedStudent->class_arm->arm_name : '') ?? 'Not specified') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Report Period:</strong> <?= date('M j, Y', strtotime($startDate)) ?> - <?= date('M j, Y', strtotime($endDate)) ?></p>
                                    <p><strong>Total Records:</strong> <?= $attendanceStats['total'] ?></p>
                                    <p><strong>Attendance Rate:</strong> 
                                        <span class="badge <?= $attendanceStats['rate'] >= 80 ? 'badge-success' : ($attendanceStats['rate'] >= 60 ? 'badge-warning' : 'badge-danger') ?>">
                                            <?= $attendanceStats['rate'] ?>%
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Statistics -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Attendance Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stats-info">
                                        <h6>Present</h6>
                                        <h4 class="text-success"><?= $attendanceStats['present'] ?></h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-info">
                                        <h6>Absent</h6>
                                        <h4 class="text-danger"><?= $attendanceStats['absent'] ?></h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-info">
                                        <h6>Late</h6>
                                        <h4 class="text-warning"><?= $attendanceStats['late'] ?></h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-info">
                                        <h6>Excused</h6>
                                        <h4 class="text-info"><?= $attendanceStats['excused'] ?></h4>
                                    </div>
                                </div>
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
                            <h5 class="card-title">Detailed Attendance Records</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($studentAttendance)): ?>
                                <div class="alert alert-info">
                                    <h5><i class="fa fa-info-circle"></i> No Records Found</h5>
                                    <p>No attendance records found for the selected date range.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="15%">Date</th>
                                                <th width="15%">Day</th>
                                                <th width="15%">Status</th>
                                                <th width="35%">Notes</th>
                                                <th width="20%">Recorded By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($studentAttendance as $record): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= date('M j, Y', strtotime($record->attendance_date)) ?></strong>
                                                    </td>
                                                    <td>
                                                        <?= date('l', strtotime($record->attendance_date)) ?>
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
                                                            <?= h($record->teacher->firstname . ' ' . $record->teacher->lastname) ?>
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
                    <!-- <?php if (!empty($studentAttendance)): ?>
                        <?= $this->Html->link(
                            '<i class="fa fa-print"></i> Print Report',
                            ['action' => 'printchildattendance', 'student_id' => $selectedStudent->id, 'start_date' => $startDate, 'end_date' => $endDate],
                            ['class' => 'btn btn-info mr-3', 'escape' => false, 'target' => '_blank']
                        ) ?>
                    <?php endif; ?> -->
                    <?= $this->Html->link(__('Back to Dashboard'), 
                        ['controller' => 'Sparents', 'action' => 'dashboard'], 
                        ['class' => 'btn btn-secondary']
                    ) ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
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
            // Get start of current week (Monday)
            startDate = new Date(today);
            const dayOfWeek = today.getDay();
            const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); // Adjust when day is Sunday
            startDate.setDate(diff);
            endDate = new Date(today);
            break;
        case 'month':
            // Get start of current month
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today);
            break;
        default:
            return;
    }
    
    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set the date values
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && endDateInput) {
        startDateInput.value = formatDate(startDate);
        endDateInput.value = formatDate(endDate);
        
        // Optional: Show a brief feedback
        console.log(`Date range set to: ${formatDate(startDate)} - ${formatDate(endDate)}`);
    } else {
        console.error('Date input elements not found');
    }
}

// Ensure the function is available when the page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Child attendance page loaded, date range functions ready');
});
</script>
