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
                <h3 class="page-title">Take Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['title' => 'Teacher dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Attendance', ['action' => 'index'], ['title' => 'Attendance Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link($previousPage, $previousUrl, ['title' => $previousPage]) ?></li>
                    <li class="breadcrumb-item active">Take Attendance</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Attendance Form -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-calendar-check"></i> 
                        Take Attendance - 
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
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['url' => ['action' => 'take']]) ?>
                    
                    <!-- Date Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="attendance_date">Attendance Date</label>
                                <?= $this->Form->control('attendance_date', [
                                    'type' => 'date',
                                    'class' => 'form-control',
                                    'value' => $attendanceDate,
                                    'required' => true,
                                    'label' => false
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quick Actions</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success" onclick="markAllPresent()">Mark All Present</button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="markAllAbsent()">Mark All Absent</button>
                                    <button type="button" class="btn btn-sm btn-info" onclick="clearAll()">Clear All</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Attendance -->
                    <?php if (!empty($students)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Reg. No.</th>
                                        <th width="25%">Student Name</th>
                                        <th width="20%">Status</th>
                                        <th width="35%">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $student): ?>
                                        <?php
                                        $existingAttendance = null;
                                        if (isset($attendanceData[$student->id])) {
                                            $existingAttendance = $attendanceData[$student->id];
                                        }
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
                                                <?= $this->Form->control("attendance.{$student->id}", [
                                                    'type' => 'select',
                                                    'class' => 'form-control attendance-status',
                                                    'options' => [
                                                        'present' => 'Present',
                                                        'absent' => 'Absent',
                                                        'late' => 'Late',
                                                        'excused' => 'Excused'
                                                    ],
                                                    'value' => $existingAttendance ? $existingAttendance->status : 'present',
                                                    'label' => false,
                                                    'data-student-id' => $student->id
                                                ]) ?>
                                            </td>
                                            <td>
                                                <?= $this->Form->control("notes.{$student->id}", [
                                                    'type' => 'text',
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Optional notes...',
                                                    'value' => $existingAttendance ? $existingAttendance->notes : '',
                                                    'label' => false
                                                ]) ?>
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

                    <!-- Form Actions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <?= $this->Form->button(__('Save Attendance'), [
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary btn-lg mr-3'
                                ]) ?>
                                <?= $this->Html->link(__('Cancel'), 
                                    ['action' => 'index'], 
                                    ['class' => 'btn btn-secondary btn-lg']
                                ) ?>
                            </div>
                        </div>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Attendance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Present</h6>
                                <h4 id="present-count">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Absent</h6>
                                <h4 id="absent-count">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Late</h6>
                                <h4 id="late-count">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-info">
                                <h6>Excused</h6>
                                <h4 id="excused-count">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

.table th {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
    font-weight: 600;
}

.form-control {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}
</style>

<script>
// Function to mark all students as present
function markAllPresent() {
    const selects = document.querySelectorAll('.attendance-status');
    selects.forEach(select => {
        select.value = 'present';
    });
    updateSummary();
}

// Function to mark all students as absent
function markAllAbsent() {
    const selects = document.querySelectorAll('.attendance-status');
    selects.forEach(select => {
        select.value = 'absent';
    });
    updateSummary();
}

// Function to clear all selections
function clearAll() {
    const selects = document.querySelectorAll('.attendance-status');
    selects.forEach(select => {
        select.value = 'present'; // Default to present
    });
    updateSummary();
}

// Function to update attendance summary
function updateSummary() {
    const counts = {
        present: 0,
        absent: 0,
        late: 0,
        excused: 0
    };

    const selects = document.querySelectorAll('.attendance-status');
    selects.forEach(select => {
        counts[select.value]++;
    });

    document.getElementById('present-count').textContent = counts.present;
    document.getElementById('absent-count').textContent = counts.absent;
    document.getElementById('late-count').textContent = counts.late;
    document.getElementById('excused-count').textContent = counts.excused;
}

// Add event listeners to all attendance status selects
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.attendance-status');
    selects.forEach(select => {
        select.addEventListener('change', updateSummary);
    });
    
    // Initial summary update
    updateSummary();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const dateInput = document.querySelector('input[name="attendance_date"]');
    if (!dateInput.value) {
        e.preventDefault();
        alert('Please select an attendance date.');
        return false;
    }
    
    const presentCount = document.getElementById('present-count').textContent;
    const absentCount = document.getElementById('absent-count').textContent;
    const lateCount = document.getElementById('late-count').textContent;
    const excusedCount = document.getElementById('excused-count').textContent;
    
    const total = parseInt(presentCount) + parseInt(absentCount) + parseInt(lateCount) + parseInt(excusedCount);
    
    if (total === 0) {
        e.preventDefault();
        alert('Please mark attendance for at least one student.');
        return false;
    }
    
    return confirm('Are you sure you want to save this attendance?');
});
</script>
