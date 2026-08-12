<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attendance[]|\Cake\Collection\CollectionInterface $attendanceRecords
 * @var \App\Model\Entity\Department[]|\Cake\Collection\CollectionInterface $departments
 */
?>

<!-- Begin Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Attendance Reports</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard']) ?>
                </li>
                <li class="breadcrumb-item"><?= $this->Html->link('Attendance Management', ['action' => 'index']) ?></li>
                <li class="breadcrumb-item active">Reports</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filter Attendance Records</h4>
            </div>
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row g-3']) ?>
                    <div class="col-md-3">
                        <?= $this->Form->control('department_id', [
                            'type' => 'select',
                            'label' => 'Class',
                            'class' => 'form-control select2_single',
                            'empty' => 'All Classes',
                            'options' => $departments->combine('id', 'name'),
                            'value' => $departmentId,
                            'onChange' => 'getClassArms(this.value)'
                        ]) ?>
                    </div>
                    <div class="col-md-2" id="classArms">
                        <label for="class_arm_id">Class Arm</label>
                        <select name="class_arm_id" class="form-control select2_single">
                            <option value="">All Arms</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('start_date', [
                            'type' => 'date',
                            'class' => 'form-control',
                            'value' => $startDate
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('end_date', [
                            'type' => 'date',
                            'class' => 'form-control',
                            'value' => $endDate
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('status', [
                            'type' => 'select',
                            'class' => 'form-control select2_single',
                            'empty' => 'All Status',
                            'options' => [
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'excused' => 'Excused'
                            ],
                            'value' => $status
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Filter
                        </button>
                        <?= $this->Html->link('<i class="fa fa-refresh"></i> Reset', [
                            'action' => 'report'
                        ], [
                            'class' => 'btn btn-secondary',
                            'escape' => false
                        ]) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($attendanceRecords)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Attendance Records</h4>
                <div class="card-tools">
                    <?= $this->Html->link(
                        '<i class="fa fa-print"></i> Print Report',
                        [
                            'action' => 'print',
                            '?' => [
                                'department_id' => $departmentId,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'status' => $status
                            ]
                        ],
                        [
                            'class' => 'btn btn-sm btn-primary',
                            'target' => '_blank',
                            'escape' => false
                        ]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="fa fa-download"></i> Export CSV',
                        [
                            'action' => 'export',
                            '?' => [
                                'department_id' => $departmentId,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'status' => $status
                            ]
                        ],
                        [
                            'class' => 'btn btn-sm btn-success',
                            'escape' => false
                        ]
                    ) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student Name</th>
                                <th>Registration No.</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Teacher</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceRecords as $record): ?>
                                <tr>
                                    <td><?= h($record->attendance_date->format('M d, Y')) ?></td>
                                    <td><?= h($record->student->fname . ' ' . $record->student->lname) ?></td>
                                    <td><?= h($record->student->regno) ?></td>
                                    <td><?= h($record->student->department->name . (!empty($record->student->class_arm) ? ' - ' . $record->student->class_arm->arm_name : '')) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = '';
                                        switch ($record->status) {
                                            case 'present':
                                                $badgeClass = 'badge-success';
                                                break;
                                            case 'absent':
                                                $badgeClass = 'badge-danger';
                                                break;
                                            case 'late':
                                                $badgeClass = 'badge-warning';
                                                break;
                                            case 'excused':
                                                $badgeClass = 'badge-info';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($record->status) ?></span>
                                    </td>
                                    <td><?= h($record->teacher->firstname . ' ' . $record->teacher->lastname) ?></td>
                                    <td><?= h($record->notes) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Attendance Statistics</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5>Present</h5>
                                <h3><?= isset($attendanceStats->present) ? $attendanceStats->present : 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h5>Absent</h5>
                                <h3><?= isset($attendanceStats->absent) ? $attendanceStats->absent : 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5>Late</h5>
                                <h3><?= isset($attendanceStats->late) ? $attendanceStats->late : 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5>Excused</h5>
                                <h3><?= isset($attendanceStats->excused) ? $attendanceStats->excused : 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center">
                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                <h4>No Records Found</h4>
                <p class="text-muted">No attendance records match your filter criteria. Try adjusting your search parameters.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

</div>
<!-- End Page Content -->

<script>
function getClassArms(departmentid){ 
    $.ajax({
        url: '../ClassArms/getArmsForDepartment/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            // Preserve the label and only replace the select element
            var label = '<label for="class_arm_id">Class Arm</label>';
            document.getElementById('classArms').innerHTML = label + response;
        }
    });
}
</script>
