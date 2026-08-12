<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <h3 class="page-title">Welcome back, <?= ucfirst($student->user->fname ?? $student->user->username ?? 'Student') ?>!</h3>
                <p class="page-subtitle">Here's your academic overview at <?= h($settings->name) ?></p>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Student Dashboard</li>
                </ul>
            </div>
            <div class="col-sm-4 text-right">
                <div class="btn-group" role="group">
                    <?= $this->Html->link(__('<i class="fa fa-tasks"></i> My Assignments'), 
                        ['controller' => 'Assignments', 'action' => 'myassignments'], 
                        ['class' => 'btn btn-primary', 'title' => 'View My Assignments', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(__('<i class="fa fa-file-text"></i> My Invoices'), 
                        ['action' => 'myinvoices'], 
                        ['class' => 'btn btn-success', 'title' => 'View My Invoices', 'escape' => false]
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
                    <i class="fa fa-book"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $subjectsCount ?></h3>
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
                    <h3><?= $availableAssignments ?></h3>
                    <span>Available Assignments</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-success">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $completedAssignments ?></h3>
                    <span>Completed Assignments</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-info">
                    <i class="fa fa-graduation-cap"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= h($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '')) ?></h3>
                    <span>My Class</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Student Info & Quick Actions -->
        <div class="col-lg-4">
            <!-- Student Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-user"></i> My Profile
                    </h6>
                </div>
                <div class="card-body">
                    <div class="student-profile">
                        <div class="profile-avatar">
                            <?php if (!empty($student->passporturl)): ?>
                                <?= $this->Html->image('../student_files/' . $student->passporturl, [
                                    'alt' => $student->fname . ' ' . $student->lname,
                                    'class' => 'img-fluid rounded-circle'
                                ]) ?>
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <i class="fa fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-info">
                            <h5 class="student-name"><?= h($student->fname . ' ' . $student->lname) ?></h5>
                            <p class="student-regno">
                                <i class="fa fa-id-card"></i> <?= h($student->regno ?? 'No Reg. No.') ?>
                            </p>
                            <p class="student-class">
                                <i class="fa fa-graduation-cap"></i> <?= h($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '') ?? 'No Class') ?>
                            </p>
                            <p class="student-session">
                                <i class="fa fa-calendar"></i> <?= h($settings->session->name ?? 'Current Session') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <?= $this->Html->link(__('<i class="fa fa-tasks"></i> My Assignments'), 
                            ['controller' => 'Assignments', 'action' => 'myassignments'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-file-text"></i> My Invoices'), 
                            ['action' => 'myinvoices'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-user"></i> Update Profile'), 
                            ['action' => 'updateprofile'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-chart-bar"></i> View Results'), 
                            ['controller' => 'Results', 'action' => 'myresults'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Academic Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-chart-pie"></i> Academic Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="academic-summary">
                        <div class="summary-item">
                            <div class="summary-icon bg-primary">
                                <i class="fa fa-book"></i>
                            </div>
                            <div class="summary-content">
                                <h6>Total Subjects</h6>
                                <p class="text-primary mb-0"><?= $subjectsCount ?> subjects</p>
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-icon bg-success">
                                <i class="fa fa-check"></i>
                            </div>
                            <div class="summary-content">
                                <h6>Completed</h6>
                                <p class="text-success mb-0"><?= $completedAssignments ?> assignments</p>
                            </div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-icon bg-warning">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div class="summary-content">
                                <h6>Pending</h6>
                                <p class="text-warning mb-0"><?= $availableAssignments ?> assignments</p>
                            </div>
                        </div>
                    </div>
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
                            <?= $this->Html->link(__('<i class="fa fa-list"></i> View All'), 
                                ['controller' => 'Assignments', 'action' => 'myassignments'], 
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
                                        <?= $this->Html->link(__('View Assignment'), 
                                            ['controller' => 'Assignments', 'action' => 'myassignments'], 
                                            ['class' => 'btn btn-sm btn-outline-primary']
                                        ) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <?= $this->Html->link(__('View All Assignments'), 
                                ['controller' => 'Assignments', 'action' => 'myassignments'], 
                                ['class' => 'btn btn-outline-primary btn-sm']
                            ) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-tasks fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No assignments available</h5>
                            <p class="text-gray-400">Check back later for new assignments from your teachers.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Fee Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-file-text"></i> Fee Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="fee-status-item">
                                <div class="fee-icon bg-success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="fee-content">
                                    <h6>Paid Invoices</h6>
                                    <p class="text-success mb-0"><?= $paidInvoices ?> invoices paid</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fee-status-item">
                                <div class="fee-icon bg-danger">
                                    <i class="fa fa-exclamation"></i>
                                </div>
                                <div class="fee-content">
                                    <h6>Unpaid Invoices</h6>
                                    <p class="text-danger mb-0"><?= $unpaidInvoices ?> invoices pending</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <?= $this->Html->link(__('View All Invoices'), 
                            ['action' => 'myinvoices'], 
                            ['class' => 'btn btn-outline-primary btn-sm']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Student Dashboard -->
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

.student-profile {
    text-align: center;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #e9ecef;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 40px;
}

.student-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.student-regno, .student-class, .student-session {
    color: #6c757d;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.student-regno i, .student-class i, .student-session i {
    width: 16px;
    margin-right: 8px;
    color: #007bff;
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

.academic-summary {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.summary-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
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

.summary-icon.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.summary-icon.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.summary-icon.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.summary-icon i {
    color: white;
    font-size: 20px;
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

.fee-status-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.fee-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.fee-icon.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.fee-icon.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.fee-icon i {
    color: white;
    font-size: 20px;
}

.fee-content h6 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #2c3e50;
}

.fee-content p {
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