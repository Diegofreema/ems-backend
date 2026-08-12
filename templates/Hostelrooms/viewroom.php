<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Hostel Room Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' Manage Hostel Rooms', ['controller' => 'Hostelrooms', 'action' => 'index'], ['title' => 'manage hostel rooms']) ?></li>
                    <li class="breadcrumb-item active">Room Details</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <!-- Room Information Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-bed text-primary"></i> Room Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Hostel:</label>
                                <p class="form-control-static">
                                    <?php if ($hostelroom->has('hostel')): ?>
                                        <i class="fa fa-building text-primary"></i> <?= h($hostelroom->hostel->name) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not assigned</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Room Number:</label>
                                <p class="form-control-static">
                                    <span class="badge badge-primary"><?= h($hostelroom->room_number) ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Floor:</label>
                                <p class="form-control-static">
                                    <i class="fa fa-level-up-alt text-info"></i> <?= h($hostelroom->floor) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Description:</label>
                                <p class="form-control-static">
                                    <?php if (!empty($hostelroom->description)): ?>
                                        <?= h($hostelroom->description) ?>
                                    <?php else: ?>
                                        <span class="text-muted">No description available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Total Beds:</label>
                                <p class="form-control-static">
                                    <span class="badge badge-info"><?= $this->Number->format($hostelroom->available_beds) ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Available Beds:</label>
                                <p class="form-control-static">
                                    <span class="badge badge-success"><?= $this->Number->format($hostelroom->available_beds - $hostelroom->occupiedbeds) ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Occupied Beds:</label>
                                <p class="form-control-static">
                                    <span class="badge badge-warning"><?= $this->Number->format($hostelroom->occupiedbeds) ?></span>
                                    <?php 
                                    $actualStudentCount = !empty($hostelroom->students) ? count($hostelroom->students) : 0;
                                    if ($actualStudentCount != $hostelroom->occupiedbeds): 
                                    ?>
                                        <br><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> Actual students: <?= $actualStudentCount ?></small>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-cogs text-primary"></i> Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= $this->Html->link('<i class="fa fa-edit"></i> Edit Room', ['action' => 'editroom', $hostelroom->id], ['class' => 'btn btn-primary btn-block', 'escape' => false]) ?>
                        
                        <?= $this->Html->link('<i class="fa fa-user-plus"></i> Assign Student', ['action' => 'assignroom', $hostelroom->id], ['class' => 'btn btn-success btn-block', 'escape' => false]) ?>
                        
                        <?php 
                        // Check if there's a mismatch between bed count and actual students
                        $actualStudentCount = !empty($hostelroom->students) ? count($hostelroom->students) : 0;
                        $bedCountMismatch = $actualStudentCount != $hostelroom->occupiedbeds;
                        ?>
                        
                        <?php if ($bedCountMismatch): ?>
                            <?= $this->Form->postLink('<i class="fa fa-sync"></i> Sync Bed Count', ['action' => 'syncbedcount', $hostelroom->id], ['class' => 'btn btn-warning btn-block', 'escape' => false, 'confirm' => 'This will update the bed count to match the actual number of students (' . $actualStudentCount . '). Continue?']) ?>
                        <?php endif; ?>
                        
                        <?= $this->Html->link('<i class="fa fa-arrow-left"></i> Back to List', ['action' => 'index'], ['class' => 'btn btn-secondary btn-block', 'escape' => false]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupied Students Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-users text-primary"></i> Occupied Students
                        <?php if (!empty($hostelroom->students)): ?>
                            <span class="badge badge-info float-right"><?= count($hostelroom->students) ?> Student(s)</span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($hostelroom->students)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Registration No</th>
                                        <th>Class</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($hostelroom->students as $student): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <strong><?= h($student->fname . ' ' . $student->lname) ?></strong>
                                            <?php if (!empty($student->mname)): ?>
                                                <br><small class="text-muted"><?= h($student->mname) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary"><?= h($student->regno) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($student->has('department')): ?>
                                                <span class="badge badge-info"><?= h($student->department->name) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fa fa-phone text-primary"></i> <?= h($student->phone) ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($student->address)): ?>
                                                <i class="fa fa-map-marker-alt text-danger"></i> <?= h($student->address) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No address</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $this->Html->link('<i class="fa fa-eye"></i> View', ['controller' => 'Students', 'action' => 'viewstudent', $student->id], ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'View Student', 'escape' => false]) ?>
                                            
                                            <?= $this->Html->link('<i class="fa fa-sign-out"></i> Eject', ['action' => 'ejectstudent', $student->id, $hostelroom->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-sm btn-outline-warning', 'title' => 'Eject Student', 'escape' => false, 'confirm' => 'Are you sure you want to eject this student from the room?']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa fa-bed text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No Students Occupying This Room</h5>
                            <p class="text-muted">This room is currently empty. You can assign students to this room.</p>
                            <?= $this->Html->link('<i class="fa fa-user-plus"></i> Assign Student', ['action' => 'assignroom', $hostelroom->id], ['class' => 'btn btn-success', 'escape' => false]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->




