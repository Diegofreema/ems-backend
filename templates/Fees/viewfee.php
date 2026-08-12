<?php
$user = $this->request->getSession()->read('usersinfo');
?>

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Fee Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' Manage Fees', ['controller' => 'Fees', 'action' => 'managefees'], ['title' => 'Manage Fees']) ?></li>
                    <li class="breadcrumb-item active">Fee Details</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <!-- Fee Information Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-money-bill-wave text-primary"></i> Fee Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Fee Name:</label>
                                <p class="form-control-static"><?= h($fee->name) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Amount:</label>
                                <p class="form-control-static text-success font-weight-bold">₦<?= number_format($fee->amount, 2) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Fee Type:</label>
                                <p class="form-control-static">
                                    <?php if (!empty($fee->feetype)): ?>
                                        <span class="badge badge-info"><?= h($fee->feetype) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Item Code:</label>
                                <p class="form-control-static">
                                    <?php if (!empty($fee->itemcode)): ?>
                                        <code><?= h($fee->itemcode) ?></code>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Status:</label>
                                <p class="form-control-static">
                                    <?php if ($fee->status == 1): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted">Created By:</label>
                                <p class="form-control-static">
                                    <?php if ($fee->has('user')): ?>
                                        <i class="fa fa-user text-primary"></i> <?= h($fee->user->username) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown</span>
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
                        <?= $this->Html->link('<i class="fa fa-edit"></i> Edit Fee', ['action' => 'editfee', $fee->id], ['class' => 'btn btn-primary btn-block', 'escape' => false]) ?>
                        
                        <?php if ($fee->status == 1): ?>
                            <?= $this->Form->postLink('<i class="fa fa-pause"></i> Deactivate', ['action' => 'deactivatefee', $fee->id], ['class' => 'btn btn-warning btn-block', 'escape' => false, 'confirm' => 'Are you sure you want to deactivate this fee?']) ?>
                        <?php else: ?>
                            <?= $this->Form->postLink('<i class="fa fa-play"></i> Activate', ['action' => 'activatefee', $fee->id], ['class' => 'btn btn-success btn-block', 'escape' => false, 'confirm' => 'Are you sure you want to activate this fee?']) ?>
                        <?php endif; ?>
                        
                        <?= $this->Html->link('<i class="fa fa-arrow-left"></i> Back to List', ['action' => 'managefees'], ['class' => 'btn btn-secondary btn-block', 'escape' => false]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Departments Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-building text-primary"></i> Assigned Departments
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($fee->departments)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Department Name</th>
                                        <th>Department Code</th>
                                        <th>Description</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; ?>
                                    <?php foreach ($fee->departments as $department): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <strong><?= h($department->name) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= h($department->deptcode) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($department->description)): ?>
                                                <?= h($department->description) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No description</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $this->Html->link('<i class="fa fa-eye"></i>', ['controller' => 'Departments', 'action' => 'viewdepartment', $department->id], ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'View Department', 'escape' => false]) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa fa-info-circle text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No Departments Assigned</h5>
                            <p class="text-muted">This fee is not currently assigned to any departments.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->
