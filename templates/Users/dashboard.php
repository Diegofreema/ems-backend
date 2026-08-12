<!-- Page Content -->
<?php
$user = $this->request->getSession()->read('usersinfo');
$settings = $this->request->getSession()->read('settings');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <h3 class="page-title">Welcome back, <?= ucfirst($user['fname']) ?>!</h3>
                <p class="page-subtitle">Here's what's happening at <?= h($settings->name) ?> today</p>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Admin Dashboard</li>
                </ul>
            </div>
            <div class="col-sm-4 text-right">
                <div class="btn-group" role="group">
                    <?= $this->Html->link(__('<i class="fa fa-plus"></i> Quick Add'), 
                        ['controller' => 'Students', 'action' => 'newstudent'], 
                        ['class' => 'btn btn-primary', 'title' => 'Add New Student', 'escape' => false]
                    ) ?>
                    <?= $this->Html->link(__('<i class="fa fa-money"></i> Collect Fee'), 
                        ['controller' => 'CollectFees', 'action' => 'index'], 
                        ['class' => 'btn btn-success', 'title' => 'Collect Student Fees', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Main Statistics Row -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-primary">
                        <i class="fa fa-graduation-cap"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $students ?></h3>
                        <span>
                            <?= $this->Html->link('Active Students', [
                                'controller' => 'Students', 
                                'action' => 'managestudents'
                            ], ['title' => 'manage students', 'class' => 'text-primary']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-warning">
                        <i class="fa fa-user-plus"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $applied ?></h3>
                        <span>
                            <?= $this->Html->link('New Applicants', [
                                'controller' => 'Students', 
                                'action' => 'manageapplicants'
                            ], ['title' => 'manage applicants', 'class' => 'text-warning']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-info">
                        <i class="fa fa-users"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $parents ?></h3>
                        <span>
                            <?= $this->Html->link('Parents', [
                                'controller' => 'Admins', 
                                'action' => 'viewparents'
                            ], ['title' => 'manage parents', 'class' => 'text-info']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-success">
                        <i class="fa fa-user"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $teachers ?></h3>
                        <span>
                            <?= $this->Html->link('Teachers', [
                                'controller' => 'Teachers', 
                                'action' => 'manageteachers'
                            ], ['title' => 'manage teachers', 'class' => 'text-success']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Academic & Financial Overview Row -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-secondary">
                        <i class="fa fa-building"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $classes ?></h3>
                        <span>
                            <?= $this->Html->link('Classes/Departments', [
                                'controller' => 'Departments', 
                                'action' => 'managedepartments'
                            ], ['title' => 'manage departments', 'class' => 'text-secondary']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-dark">
                        <i class="fa fa-book"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $subjects ?></h3>
                        <span>
                            <?= $this->Html->link('Subjects', [
                                'controller' => 'Subjects', 
                                'action' => 'managesubjects'
                            ], ['title' => 'manage subjects', 'class' => 'text-dark']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-danger">
                        <i class="fa fa-file-text"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><i class="fa fa-file-text"></i></h3>
                        <span>
                            <?= $this->Html->link('Exams & Results', [
                                'controller' => 'Results', 
                                'action' => 'uploadresults'
                            ], ['title' => 'upload results', 'class' => 'text-danger']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-success">
                        <i class="fa fa-money"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3>₦<?= isset($total_revenue) ? number_format($total_revenue) : '0' ?></h3>
                        <span>
                            <?= $this->Html->link('Total Revenue', [
                                'controller' => 'Transactions', 
                                'action' => 'index'
                            ], ['title' => 'view transactions', 'class' => 'text-success']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & System Management Row -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-primary">
                        <i class="fa fa-star"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= $admins ?></h3>
                        <span>
                            <?= $this->Html->link('Administrators', [
                                'controller' => 'Users', 
                                'action' => 'manageadmins'
                            ], ['title' => 'manage admins', 'class' => 'text-primary']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-info">
                        <i class="fa fa-cogs"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3>⚙️</h3>
                        <span>
                            <?= $this->Html->link('School Settings', [
                                'controller' => 'Settings', 
                                'action' => 'editsettings', 
                                1
                            ], ['title' => 'manage school settings', 'class' => 'text-info']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-warning">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= isset($attendance_count) ? $attendance_count : '0' ?></h3>
                        <span>
                            <?= $this->Html->link('Attendance', [
                                'controller' => 'Attendances', 
                                'action' => 'index'
                            ], ['title' => 'manage attendance', 'class' => 'text-warning']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="card dash-widget">
                <div class="card-body">
                    <span class="dash-widget-icon bg-success">
                        <i class="fa fa-credit-card"></i>
                    </span>
                    <div class="dash-widget-info">
                        <h3><?= isset($fees_collected) ? $fees_collected : '0' ?></h3>
                        <span>
                            <?= $this->Html->link('Fee Collection', [
                                'controller' => 'CollectFees', 
                                'action' => 'index'
                            ], ['title' => 'collect fees', 'class' => 'text-success']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Analytics Section -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-chart-line"></i> Revenue Overview (Last 6 Months)
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="myChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-tasks"></i> Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?= $this->Html->link(__('<i class="fa fa-user-plus"></i> Add New Student'), 
                            ['controller' => 'Students', 'action' => 'newstudent'], 
                            ['class' => 'list-group-item list-group-item-action', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-money"></i> Collect Fees'), 
                            ['controller' => 'CollectFees', 'action' => 'index'], 
                            ['class' => 'list-group-item list-group-item-action', 'escape' => false]
                        ) ?>
                        <!-- <?= $this->Html->link(__('<i class="fa fa-calendar-check"></i> Take Attendance'), 
                            ['controller' => 'Attendances', 'action' => 'index'], 
                            ['class' => 'list-group-item list-group-item-action', 'escape' => false]
                        ) ?> -->
                        <?= $this->Html->link(__('<i class="fa fa-upload"></i> Upload Results'), 
                            ['controller' => 'Results', 'action' => 'uploadresults'], 
                            ['class' => 'list-group-item list-group-item-action', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(__('<i class="fa fa-search"></i> Search Student'), 
                            ['controller' => 'CollectFees', 'action' => 'search'], 
                            ['class' => 'list-group-item list-group-item-action', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-clock-o"></i> Recent Activity
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Welcome to your dashboard!</strong> 
                        Use the quick action buttons above to manage students, collect fees, and track attendance. 
                        All your school management tools are just a click away.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- /Page Content -->

<?php
$months_array = [];
$sales_array = [];

foreach ($trsnactions_graph as $data) {
    array_push($months_array, "'" . date('M', strtotime($data->duration)) . "'"); 
    array_push($sales_array, $data->totalvalue); 
    // '{ y:' . "'".date('M',  strtotime($data->duration))."'".','.' a:' .$data->totalvalue.','. ' b:' .$data->count; },
}

$dm = implode(", ", $months_array); 
$dd = implode(", ", $sales_array);

// debug(json_encode( $months_array , JSON_PRETTY_PRINT)); exit; 
foreach ($trsnactions_graph as $graph) { 
    // echo '{ y: '. "'".date('M',  strtotime($graph->duration))."'", ' a:' . $graph->totalvalue .', b:' . $graph->count .'},'; 
}
// exit;
?>                         

<script>
var ctx = document.getElementById('myChart');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?= $dm ?>],
        datasets: [{
            label: '# Revenue Volum For The Past Six Months',
            data: <?= '[ ' . $dd . ' ] ' ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<script>
// Bar Chart
Morris.Bar({
    element: 'bar-charts',
    data: [  
        { y: '2006', a: 100, b: 90 },
        { y: '2007', a: 75,  b: 65 },
        { y: '2008', a: 50,  b: 40 },
        { y: '2009', a: 75,  b: 65 },
        { y: '2010', a: 50,  b: 40 },
        { y: '2011', a: 75,  b: 65 },
        { y: '2012', a: 100, b: 90 }
    ],
    xkey: 'y',
    ykeys: ['a', 'b'],
    labels: ['Total Income', 'Total Outcome'],
    lineColors: ['#ff9b44','#fc6075'],
    lineWidth: '3px',
    barColors: ['#ff9b44','#fc6075'],
    resize: true,
    redraw: true
});
</script>

<style>
/* Enhanced Dashboard Styling */
.dash-widget {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 20px;
}

.dash-widget:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.dash-widget-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white !important;
    margin-right: 15px;
    flex-shrink: 0;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.dash-widget-icon i {
    color: white !important;
    font-size: 24px !important;
    display: block !important;
}

.dash-widget-icon.bg-primary { background: rgba(0, 0, 128, 0.95); }
.dash-widget-icon.bg-success { background: linear-gradient(135deg, rgba(0, 0, 128, 0.8) 0%, #20c997 100%); }
.dash-widget-icon.bg-info { background: linear-gradient(135deg, rgba(0, 0, 128, 0.7) 0%, #6f42c1 100%); }
.dash-widget-icon.bg-warning { background: linear-gradient(135deg, rgba(0, 0, 128, 0.6) 0%, #fd7e14 100%); }
.dash-widget-icon.bg-danger { background: linear-gradient(135deg, rgba(0, 0, 128, 0.5) 0%, #e83e8c 100%); }
.dash-widget-icon.bg-secondary { background: linear-gradient(135deg, rgba(0, 0, 128, 0.4) 0%, #495057 100%); }
.dash-widget-icon.bg-dark { background: linear-gradient(135deg, rgba(0, 0, 128, 0.3) 0%, #212529 100%); }

.dash-widget-info h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
}

.dash-widget-info span a {
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.3s ease;
}

.dash-widget-info span a:hover {
    text-decoration: underline;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

.card-header h4 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.list-group-item-action {
    border: none;
    border-radius: 5px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.list-group-item-action i {
    color: #007bff !important;
    margin-right: 8px;
    font-size: 14px;
    display: inline-block !important;
    width: 16px;
    text-align: center;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.page-header {
    background: rgba(0, 0, 128, 0.95);
    color: white;
    border-radius: 10px;
    margin-bottom: 30px;
    padding: 30px;
}

.page-header h3 {
    color: white;
    margin: 0;
    font-weight: 600;
    font-size: 1.8rem;
}

.page-subtitle {
    color: rgba(255,255,255,0.95) !important;
    margin: 8px 0 0 0;
    font-size: 1rem;
    font-weight: 400;
}

.breadcrumb {
    background: transparent !important;
    margin: 0;
    padding: 0;
}

.breadcrumb-item {
    color: white !important;
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.8) !important;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: white !important;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.page-header .breadcrumb-item {
    color: white !important;
}

.page-header .breadcrumb-item.active {
    color: white !important;
    font-weight: 600;
}

/* Force breadcrumb visibility */
.page-header ul.breadcrumb li.breadcrumb-item.active {
    color: white !important;
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5) !important;
}

.page-header ul.breadcrumb li.breadcrumb-item {
    color: white !important;
}

.btn-group .btn {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 500;
    margin-left: 10px;
}

.btn-primary {
    background: rgba(0, 0, 128, 0.95) !important;
    border-color: rgba(0, 0, 128, 0.95) !important;
}

.btn-primary:hover {
    background: rgba(0, 0, 128, 1) !important;
    border-color: rgba(0, 0, 128, 1) !important;
}

.alert-info {
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(0, 0, 128, 0.1) 0%, rgba(0, 0, 128, 0.05) 100%);
    border-left: 4px solid rgba(0, 0, 128, 0.95);
}

/* Chart container styling */
#myChart {
    max-height: 300px;
}

/* Ensure FontAwesome icons are visible */
.fa {
    font-family: FontAwesome !important;
    font-style: normal !important;
    font-weight: normal !important;
    text-decoration: none !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dash-widget-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .dash-widget-info h3 {
        font-size: 1.5rem;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .btn-group .btn {
        margin: 5px 0;
        width: 100%;
    }
}
</style>