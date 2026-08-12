<!-- Begin Page Content -->
    <div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <h3 class="page-title" style="color: white !important; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Welcome back, <?= ucfirst($parent->mothersname ?? 'Parent') ?>!</h3>
                <p class="page-subtitle">Here's an overview of your children's academic progress at <?= h($settings->name) ?></p>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Parent Dashboard</li>
                </ul>
            </div>
            <div class="col-sm-4 text-right">
                <div class="btn-group" role="group">
                    <?= $this->Html->link(__('<i class="fa fa-graduation-cap"></i> View Assignments'), 
                        ['action' => 'mykidsassignments'], 
                        ['class' => 'btn btn-primary', 'title' => 'View Children Assignments', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(__('<i class="fa fa-money"></i> Pay Fees'), 
                        ['action' => 'mykidinvoices'], 
                        ['class' => 'btn btn-success', 'title' => 'Pay School Fees', 'escape' => false]
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
                    <h3><?= $totalStudents ?></h3>
                    <span>Total Children</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-success">
                    <i class="fa fa-user"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $activeStudents ?></h3>
                    <span>Active Students</span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $suspendedStudents ?></h3>
                    <span>Suspended</span>
                </div>
            </div>
        </div>
                
                    <div class="col-xl-3 col-md-6 mb-4">
            <div class="dash-widget">
                <div class="dash-widget-icon bg-danger">
                    <i class="fa fa-file-text"></i>
                </div>
                <div class="dash-widget-info">
                    <h3><?= $unpaidInvoices ?></h3>
                    <span>Unpaid Invoices</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Children Cards -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-child"></i> Your Children
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <?= $this->Html->link(__('<i class="fa fa-eye"></i> View All'), 
                                ['controller' => 'Students', 'action' => 'index'], 
                                ['class' => 'dropdown-item', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                </div>
                            <div class="card-body">
                    <?php if ($kids->count() > 0): ?>
                        <div class="row">
                            <?php foreach ($kids as $kid): ?>
                                <div class="col-lg-6 col-md-6 mb-4">
                                    <div class="student-card <?= $kid->studentstatus === 'Suspended' ? 'suspended' : '' ?>">
                                        <div class="student-card-header">
                                            <div class="student-avatar">
                                                <?php if (!empty($kid->passport)): ?>
                                                    <?= $this->Html->image('student_passports/' . $kid->passport, [
                                                        'alt' => $kid->fname . ' ' . $kid->lname,
                                                        'class' => 'img-fluid rounded-circle'
                                                    ]) ?>
                                                <?php else: ?>
                                                    <div class="avatar-placeholder">
                                                        <i class="fa fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="student-status">
                                                <?php if ($kid->studentstatus === 'Suspended'): ?>
                                                    <span class="badge badge-danger">
                                                        <i class="fa fa-ban"></i> Suspended
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-check"></i> Active
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="student-card-body">
                                            <h5 class="student-name"><?= h($kid->fname . ' ' . $kid->lname) ?></h5>
                                            <p class="student-class">
                                                <i class="fa fa-graduation-cap"></i> 
                                                <?= h($kid->department->name . (!empty($kid->class_arm) ? ' - ' . $kid->class_arm->arm_name : '') ?? 'No Class') ?>
                                            </p>
                                            <p class="student-regno">
                                                <i class="fa fa-id-card"></i> 
                                                <?= h($kid->regno ?? 'No Reg. No.') ?>
                                            </p>
                                        </div>
                                        <div class="student-card-footer">
                                            <?php if ($kid->studentstatus === 'Suspended'): ?>
                                                <button class="btn btn-outline-danger btn-sm" disabled>
                                                    <i class="fa fa-ban"></i> Access Restricted
                                                </button>
                                            <?php else: ?>
                                                <?= $this->Html->link(__('<i class="fa fa-eye"></i> View Profile'), 
                                                    ['controller' => 'Students', 'action' => 'viewmystudent', $kid->id, $this->generateurl($kid->fname)], 
                                                    ['class' => 'btn btn-primary btn-sm', 'escape' => false]
                                                ) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-child fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No children registered</h5>
                            <p class="text-gray-400">Contact the school administration to register your children.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
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
                        <?= $this->Html->link(__('<i class="fa fa-graduation-cap"></i> View Assignments'), 
                            ['action' => 'mykidsassignments'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-money"></i> Pay Fees'), 
                            ['action' => 'mykidinvoices'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-calendar"></i> Check Attendance'), 
                            ['action' => 'childattendance'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-file-text-o"></i> View Results'), 
                            ['action' => 'mykidsresults'], 
                            ['class' => 'quick-action-item', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Recent Assignments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-tasks"></i> Recent Assignments
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($recentAssignments->count() > 0): ?>
                        <div class="recent-assignments">
                            <?php foreach ($recentAssignments as $assignment): ?>
                                <div class="assignment-item">
                                    <div class="assignment-title">
                                        <strong><?= h($assignment->title) ?></strong>
                                    </div>
                                    <div class="assignment-meta">
                                        <small class="text-muted">
                                            <i class="fa fa-book"></i> <?= h($assignment->subject->name) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa fa-calendar"></i> <?= $assignment->datecreated ? $assignment->datecreated->format('M d, Y') : 'No date' ?>
                                        </small>
                                </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        <div class="text-center mt-3">
                            <?= $this->Html->link(__('View All Assignments'), 
                                ['action' => 'mykidsassignments'], 
                                ['class' => 'btn btn-outline-primary btn-sm']
                            ) ?>
                            </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fa fa-tasks fa-2x text-gray-300 mb-2"></i>
                            <p class="text-gray-500 mb-0">No recent assignments</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fa fa-exclamation-triangle"></i> Important Notice
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Student Suspension Notice:</strong>
                        <p class="mb-0">If any of your children are suspended, please visit the school administration to resolve the issue. Suspended students cannot access their academic information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Parent Dashboard -->
<style>
.page-header {
    background: linear-gradient(135deg, rgba(0, 0, 128, 0.95) 0%, rgba(0, 0, 128, 0.85) 100%);
    color: white;
    border-radius: 10px;
    margin-bottom: 30px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.page-header .page-title {
    color: white !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.page-subtitle {
    color: rgba(255,255,255,0.95) !important;
    margin: 8px 0 0 0;
    font-size: 1rem;
    font-weight: 400;
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

.dash-widget-icon.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.dash-widget-icon i {
    color: white !important;
    font-size: 24px !important;
    display: block !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

/* Ensure FontAwesome icons are loaded */
.fa {
    font-family: FontAwesome !important;
    font-style: normal !important;
    font-weight: normal !important;
    text-decoration: none !important;
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

.student-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    height: 100%;
}

.student-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: rgba(0, 0, 128, 0.2);
}

.student-card.suspended {
    border-color: #dc3545;
    background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
}

.student-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.student-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #e9ecef;
}

.student-avatar img {
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
    font-size: 24px;
}

.student-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.student-class, .student-regno {
    color: #6c757d;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.student-class i, .student-regno i {
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

.assignment-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
    border-left: 4px solid #007bff;
}

.assignment-title {
    margin-bottom: 8px;
}

.assignment-meta {
    color: #6c757d;
}

.assignment-meta i {
    margin-right: 5px;
    width: 12px;
}

.btn-primary {
    background: rgba(0, 0, 128, 0.95) !important;
    border-color: rgba(0, 0, 128, 0.95) !important;
}

.btn-primary:hover {
    background: rgba(0, 0, 128, 1) !important;
    border-color: rgba(0, 0, 128, 1) !important;
}

.alert-warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
    border-left: 4px solid #ffc107;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .page-header {
        padding: 20px;
    }
    
    .dash-widget {
        margin-bottom: 20px;
    }
    
    .student-card {
        margin-bottom: 20px;
    }
}
</style>