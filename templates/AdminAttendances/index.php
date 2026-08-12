<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attendance[]|\Cake\Collection\CollectionInterface $todayStats
 * @var \App\Model\Entity\Department[]|\Cake\Collection\CollectionInterface $departments
 */
?>

<!-- Begin Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Attendance Management</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard']) ?>
                </li>
                <li class="breadcrumb-item active">Attendance Management</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Attendance Dashboard</h4>
                <p class="card-text">Overview of student attendance across all classes</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Today's Date</h5>
                                <h3 class="text-white"><?= date('l, F j, Y', strtotime($today)) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Total Classes</h5>
                                <h3 class="text-white"><?= $departments->count() ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Today's Attendance by Class</h5>
                        <?php if (count($todayStats) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Total Students</th>
                                            <th>Present Today</th>
                                            <th>Attendance Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($todayStats as $stat): ?>
                                            <tr>
                                                <td><?= h($stat->department_name) ?></td>
                                                <td><?= h($stat->total_students) ?></td>
                                                <td><?= h($stat->present_count) ?></td>
                                                <td>
                                                    <?php 
                                                    $rate = $stat->total_students > 0 ? ($stat->present_count / $stat->total_students) * 100 : 0;
                                                    $badgeClass = $rate >= 80 ? 'badge-success' : ($rate >= 60 ? 'badge-warning' : 'badge-danger');
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= number_format($rate, 1) ?>%</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No attendance records found for today.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Quick Actions</h5>
                        <div class="btn-group" role="group">
                            <?= $this->Html->link(
                                '<i class="fa fa-chart-bar"></i> View Reports',
                                ['action' => 'report'],
                                ['class' => 'btn btn-primary', 'escape' => false]
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fa fa-print"></i> Print Reports',
                                ['action' => 'report'],
                                ['class' => 'btn btn-secondary', 'escape' => false]
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fa fa-download"></i> Export Data',
                                ['action' => 'report'],
                                ['class' => 'btn btn-success', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- End Page Content -->
