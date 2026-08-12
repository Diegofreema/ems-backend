<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Class Arms</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item active">Manage Class Arms</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
        <?= $this->Html->link(__(' '), ['action' => 'add'],
            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new class arm']) ?>
        <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Class Arms Manager</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col"><?= $this->Paginator->sort('Class') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Arm Name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Class Teacher') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Students') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th scope="col"><?= $this->Paginator->sort('Class') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Arm Name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Class Teacher') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Students') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach ($classArms as $classArm): 
                        ?>
                        <tr>
                            <td><?= h($classArm->has('department') ? $classArm->department->name : 'No Department') ?></td>
                            <td><?= h($classArm->arm_name) ?></td>
                            <td>
                                <?php if (!empty($classArm->teacher) && $classArm->teacher->has('user')): ?>
                                    <?= h($classArm->teacher->user->fname . ' ' . $classArm->teacher->user->lname) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= $classArm->has('students') ? count($classArm->students) : 0 ?> students</span>
                            </td>
                            <td>
                                <?php if ($classArm->status === 'active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php elseif ($classArm->status === 'inactive'): ?>
                                    <span class="badge badge-warning">Inactive</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Archived</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?= $this->Html->link(__(' View'), ['action' => 'view', $classArm->id],
                                    ['class'=>'btn btn-round btn-primary fa fa-eye','title'=>'view class arm details']) ?>
                                <?= $this->Html->link(__(' Edit'), ['action' => 'edit', $classArm->id],
                                    ['class'=>'btn btn-round btn-warning fa fa-edit','title'=>'edit class arm']) ?>
                                <?= $this->Html->link(__(' Students'), ['action' => 'manageStudents', $classArm->id],
                                    ['class'=>'btn btn-round btn-info fa fa-users','title'=>'manage students']) ?>
                                <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $classArm->id], 
                                    ['confirm' => __('Are you sure you want to delete class arm "{0}"?', $classArm->arm_name),
                                        'class'=>'btn btn-round btn-danger fa fa-times','title'=>'delete class arm']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ], [ 1, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": 5 }
        ]
    });
});
</script>
