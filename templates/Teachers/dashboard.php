<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <h3 class="page-title">Welcome back, <?= ucfirst($teacher->user->fname ?? $teacher->user->username ?? 'Teacher') ?>!</h3>
                <p class="page-subtitle">Here's your teaching overview at <?= h($settings->name) ?></p>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Teacher Dashboard</li>
                </ul>
            </div>
            <div class="col-sm-4 text-right">
                <div class="btn-group" role="group">
                    <?= $this->Html->link(__('<i class="fa fa-plus"></i> Create Assignment'), 
                        ['controller' => 'Setassignments', 'action' => 'addassignment'], 
                        ['class' => 'btn btn-primary', 'title' => 'Create New Assignment', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(__('<i class="fa fa-calendar"></i> Take Attendance'), 
                        ['controller' => 'Attendances', 'action' => 'index'], 
                        ['class' => 'btn btn-success', 'title' => 'Take Student Attendance', 'escape' => false]
                    ) ?>
                </div>
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
                    <h3><?= $myStudents ?></h3>
                    <span>My Students</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-success">
                    <i class="fa fa-book"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $mySubjects ?></h3>
                    <span>My Subjects</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-warning">
                    <i class="fa fa-tasks"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $pendingAssignments ?></h3>
                    <span>Pending Assignments</span>
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
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Quick Actions & Class Info -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <?= $this->Html->link(__('<i class="fa fa-plus"></i> Create Assignment'), 
                            ['controller' => 'Setassignments', 'action' => 'addassignment'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-calendar"></i> Take Attendance'), 
                            ['controller' => 'Attendances', 'action' => 'index'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-list"></i> View Assignments'), 
                            ['controller' => 'Setassignments', 'action' => 'index'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-users"></i> My Students'), 
                            ['controller' => 'Teachers', 'action' => 'mystudents'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Class Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-graduation-cap"></i> Class Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="class-info">
                        <?php if (!empty($teacherClassArms)): ?>
                            <div class="info-item">
                                <strong>Class Arms:</strong>
                                <ul class="class-arms-list">
                                    <?php foreach ($teacherClassArms as $classArm): ?>
                                        <li><?= h($classArm->department->name) ?> - <?= h($classArm->arm_name) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="info-item">
                                <strong>Class:</strong> <?= h($teacher->department->name) ?>
                            </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <strong>Students:</strong> <?= $myStudents ?> students
                        </div>
                        <div class="info-item">
                            <strong>Subjects:</strong> <?= $mySubjects ?> subjects
                        </div>
                        <div class="info-item">
                            <strong>Today's Attendance:</strong> 
                            <?php if ($attendanceTakenToday): ?>
                                <span class="badge badge-success">Taken</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Not Taken</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Subjects -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-book"></i> My Subjects
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($teacher->subjects)): ?>
                        <div class="subjects-list">
                            <?php foreach ($teacher->subjects as $subject): ?>
                                <div class="subject-item">
                                    <i class="fa fa-book text-primary"></i>
                                    <div class="subject-details">
                                        <span class="subject-name"><?= h($subject->name) ?></span>
                                        <small class="subject-class"><?= h($subject->department->name ?? 'No Class') ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fa fa-book fa-2x text-gray-300 mb-2"></i>
                            <p class="text-gray-500 mb-0">No subjects assigned</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Assignments & Activity -->
        <div class="col-lg-8">
            <!-- Recent Assignments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-tasks"></i> Recent Assignments
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <?= $this->Html->link(__('<i class="fa fa-plus"></i> Create New'), 
                                ['controller' => 'Setassignments', 'action' => 'addassignment'], 
                                ['class' => 'dropdown-item', 'escape' => false]
                            ) ?>
                            <?= $this->Html->link(__('<i class="fa fa-list"></i> View All'), 
                                ['controller' => 'Setassignments', 'action' => 'index'], 
                                ['class' => 'dropdown-item', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($recentAssignments->count() > 0): ?>
                        <div class="assignments-list">
                            <?php foreach ($recentAssignments as $assignment): ?>
                                <div class="assignment-item">
                                    <div class="assignment-header">
                                        <h6 class="assignment-title"><?= h($assignment->title) ?></h6>
                                        <span class="assignment-type badge badge-info"><?= h($assignment->test_type) ?></span>
                                    </div>
                                    <div class="assignment-meta">
                                        <small class="text-muted">
                                            <i class="fa fa-book"></i> <?= h($assignment->subject->name) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa fa-calendar"></i> <?= $assignment->datecreated ? $assignment->datecreated->format('M d, Y') : 'No date' ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa fa-clock-o"></i> <?= $assignment->time_limit ?> minutes
                                        </small>
                                    </div>
                                    <div class="assignment-actions">
                                        <?= $this->Html->link(__('View'), 
                                            ['controller' => 'Setassignments', 'action' => 'view', $assignment->id], 
                                            ['class' => 'btn btn-sm btn-outline-primary']
                                        ) ?>
                                        <?= $this->Html->link(__('Edit'), 
                                            ['controller' => 'Setassignments', 'action' => 'editassignment', $assignment->id], 
                                            ['class' => 'btn btn-sm btn-outline-secondary']
                                        ) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <?= $this->Html->link(__('View All Assignments'), 
                                ['controller' => 'Setassignments', 'action' => 'index'], 
                                ['class' => 'btn btn-outline-primary btn-sm']
                            ) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-tasks fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No assignments created yet</h5>
                            <p class="text-gray-400">Start by creating your first assignment for your students.</p>
                            <div class="mt-3">
                                <?= $this->Html->link(__('Create Assignment'), 
                                    ['controller' => 'Setassignments', 'action' => 'add'], 
                                    ['class' => 'btn btn-primary']
                                ) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Today's Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-calendar"></i> Today's Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-item">
                                <div class="summary-icon bg-success">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="summary-content">
                                    <h6>Attendance Status</h6>
                                    <?php if ($attendanceTakenToday): ?>
                                        <p class="text-success mb-0">Attendance has been taken today</p>
                                    <?php else: ?>
                                        <p class="text-warning mb-0">Attendance not taken yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <div class="summary-icon bg-info">
                                    <i class="fa fa-tasks"></i>
                                </div>
                                <div class="summary-content">
                                    <h6>Pending Assignments</h6>
                                    <p class="text-info mb-0"><?= $pendingAssignments ?> assignments pending</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Teacher Dashboard -->
<style>
.page-header {
    background: linear-gradient(135deg, rgba(0, 0, 128, 0.95) 0%, rgba(0, 0, 128, 0.85) 100%);
    color: white;
    border-radius: 10px;
    margin-bottom: 30px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.page-subtitle {
    color: rgba(255,255,255,0.95) !important;
    margin: 8px 0 0 0;
    font-size: 1rem;
    font-weight: 400;
}

.page-header .page-title {
    color: white !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.breadcrumb-item {
    color: white !important;
}

.breadcrumb-item.active {
    color: white !important;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

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

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.quick-action-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.quick-action-item:hover {
    background: rgba(0, 0, 128, 0.1);
    color: rgba(0, 0, 128, 0.95);
    text-decoration: none;
    border-left-color: rgba(0, 0, 128, 0.95);
    transform: translateX(5px);
}

.quick-action-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    color: #007bff !important;
    font-size: 16px !important;
    display: inline-block !important;
}

.class-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.subjects-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.subject-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.subject-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.subject-item i {
    margin-right: 12px;
    width: 20px;
    font-size: 16px;
}

.subject-details {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.subject-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
    margin-bottom: 2px;
}

.subject-class {
    color: #6c757d;
    font-size: 0.8rem;
    font-weight: 500;
}

.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.assignment-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.assignment-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.assignment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.assignment-title {
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.assignment-type {
    font-size: 0.75rem;
}

.assignment-meta {
    margin-bottom: 15px;
}

.assignment-meta i {
    margin-right: 5px;
    width: 12px;
    color: #6c757d;
}

.assignment-actions {
    display: flex;
    gap: 10px;
}

.summary-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.summary-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.summary-icon.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.summary-icon.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.summary-icon i {
    color: white !important;
    font-size: 20px !important;
    display: block !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.summary-content h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #2c3e50;
}

.summary-content p {
    margin: 0;
    font-size: 0.9rem;
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

/* Class arms list styling */
.class-arms-list {
    margin: 5px 0 0 0;
    padding-left: 20px;
    list-style-type: disc;
}

.class-arms-list li {
    margin-bottom: 3px;
    font-size: 0.9em;
    color: #666;
}

@media (max-width: 768px) {
    .page-header {
        padding: 20px;
    }
    
    .dash-widget {
        margin-bottom: 20px;
    }
    
    .assignment-item {
        margin-bottom: 20px;
    }
    
    .summary-item {
        margin-bottom: 15px;
    }
}
</style>