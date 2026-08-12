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
                    <h3 class="page-title">My Students</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">My Students</li>
                    </ul>
                </div>
            </div>
        </div>
    <!-- /Page Header -->

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-primary">
                    <i class="fa fa-users"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $students->count() ?></h3>
                    <span>Total Students</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-info">
                    <i class="fa fa-graduation-cap"></i>
                </div>
                <div class="dash-widget-info">
                    <?php 
                    $classArmsCount = !empty($teacherClassArms) ? $teacherClassArms->count() : 0;
                    ?>
                    <h3><?= $classArmsCount ?></h3>
                    <span>Class Arm<?= $classArmsCount != 1 ? 's' : '' ?></span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-success">
                    <i class="fa fa-book"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= count($teacher->subjects ?? []) ?></h3>
                    <span>My Subjects</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-warning">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= date('M d') ?></h3>
                    <span>Today's Date</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fa fa-users"></i> Students in 
                <?php if (!empty($teacherClassArms)): ?>
                    <?php 
                    $classArmNames = [];
                    foreach ($teacherClassArms as $classArm) {
                        $classArmNames[] = $classArm->department->name . ' - ' . $classArm->arm_name;
                    }
                    echo h(implode(', ', $classArmNames));
                    ?>
                <?php else: ?>
                    <?= h($teacher->department->name ?? 'Your Class') ?>
                <?php endif; ?>
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <?= $this->Html->link(__('<i class="fa fa-download"></i> Export to Excel'), 
                        ['action' => 'mystudents', 'export' => 'excel'], 
                        ['class' => 'dropdown-item', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if ($students->count() > 0): ?>
                <!-- Basic Table Info -->
                <div class="row mb-3" id="basic-table-info">
                    <div class="col-sm-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            Showing <?= $students->count() ?> students in 
                            <?php if (!empty($teacherClassArms)): ?>
                                <?php 
                                $classArmNames = [];
                                foreach ($teacherClassArms as $classArm) {
                                    $classArmNames[] = $classArm->department->name . ' - ' . $classArm->arm_name;
                                }
                                echo h(implode(', ', $classArmNames));
                                ?>
                            <?php else: ?>
                                <?= h($teacher->department->name ?? 'your class') ?>
                            <?php endif; ?>.
                            <small class="text-muted">(Server-side pagination is active)</small>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="studentsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>S/N</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Registration No.</th>
                                <th>Class</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td>
                                        <div class="student-photo">
                                            <?php if (!empty($student->passporturl)): ?>
                                                <?= $this->Html->image('../student_files/' . $student->passporturl, [
                                                    'alt' => $student->fname . ' ' . $student->lname,
                                                    'class' => 'img-fluid rounded-circle',
                                                    'style' => 'width: 40px; height: 40px; object-fit: cover;'
                                                ]) ?>
                                            <?php else: ?>
                                                <div class="avatar-placeholder-small">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>
                                        <?php if (!empty($student->mname)): ?>
                                            <br><small class="text-muted"><?= h($student->mname) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= h($student->regno ?? 'No Reg. No.') ?></span>
                                    </td>
                                    <td>
                                        <?= $student->has('department') ? $student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '') : 'No Class' ?>
                                    </td>
                                    <td>
                                        <?= h($student->email ?? 'No Email') ?>
                                        <?php if (!empty($student->user->email)): ?>
                                            <br><small class="text-muted"><?= h($student->user->email) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= h($student->phone ?? 'No Phone') ?></td>
                                    <td>
                                        <?php if ($student->studentstatus === 'Active'): ?>
                                            <span class="badge badge-success">
                                                <i class="fa fa-check"></i> Active
                                            </span>
                                        <?php elseif ($student->studentstatus === 'Suspended'): ?>
                                            <span class="badge badge-danger">
                                                <i class="fa fa-ban"></i> Suspended
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">
                                                <i class="fa fa-clock-o"></i> <?= h($student->studentstatus) ?>
                                            </span>
                                        <?php endif; ?>
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

<!-- Custom CSS for My Students Page -->
<style>

.dash-widget {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid transparent;
}

.dash-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.dash-widget-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.dash-widget-icon.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.dash-widget-icon.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.dash-widget-icon.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.dash-widget-icon i {
    color: white !important;
    font-size: 24px !important;
    display: block !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.dash-widget-info h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
}

.dash-widget-info span {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.student-photo {
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-placeholder-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-primary {
    background: rgba(0, 0, 128, 0.95) !important;
    border-color: rgba(0, 0, 128, 0.95) !important;
}

.btn-primary:hover {
    background: rgba(0, 0, 128, 1) !important;
    border-color: rgba(0, 0, 128, 1) !important;
}

/* Ensure FontAwesome icons are loaded */
.fa {
    font-family: FontAwesome !important;
    font-style: normal !important;
    font-weight: normal !important;
    text-decoration: none !important;
}

@media (max-width: 768px) {
    .page-header {
        padding: 20px;
    }
    
    .dash-widget {
        margin-bottom: 20px;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>

<!-- DataTables JavaScript -->
<script>
// Wait for jQuery to be fully loaded
(function() {
    function initDataTable() {
        // Check if jQuery and DataTables are loaded
        if (typeof jQuery === 'undefined') {
            setTimeout(initDataTable, 100);
            return;
        }
        
        // DataTables is working - no need for debug logs
        
        // Use jQuery instead of $ to avoid conflicts
        jQuery(document).ready(function($) {
            // Initialize DataTable
            try {
                var table = $('#studentsTable').DataTable({
                    "responsive": true,
                    "pageLength": 25,
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "order": [[ 2, "asc" ]], // Sort by name
                    "columnDefs": [
                        { "orderable": false, "targets": [0, 1] } // Disable sorting on S/N and Photo columns
                    ],
                    "language": {
                        "search": "Search students:",
                        "lengthMenu": "Show _MENU_ students per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ students",
                        "infoEmpty": "No students found",
                        "infoFiltered": "(filtered from _MAX_ total students)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
                
                // DataTable initialized successfully
                
                // Show the DataTables controls and hide basic info
                $('#datatables-controls').show();
                $('#basic-table-info').hide();
                
            } catch (error) {
                console.error('Error initializing DataTable:', error);
                
                // Show a message that basic table is being used
                $('#studentsTable').before('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Using basic table view. DataTables features are not available.</div>');
            }
        });
    }
    
    // Start initialization
    initDataTable();
})();
</script>
