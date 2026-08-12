<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="container-fluid">
    <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'assigncourses'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'Assign courses'])
?>
        
 <h1 class="h3 mb-2 text-gray-800">Manage Course Allocations</h1></div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Manage Course Allocations</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>
            
                <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Semester') ?></th>
                <th scope="col"><?= $this->Paginator->sort('level') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Assigned On') ?></th>
               
                <th scope="col">Admin</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courseassignments as $courseassignment): ?>
            <tr>
                <td><?= $courseassignment->has('department') ? $courseassignment->department->name : '' ?></td>
                <td><?= $courseassignment->has('semester') ? $courseassignment->semester->name : '' ?></td>
                <td><?= $courseassignment->has('level') ? $courseassignment->level->name : '' ?></td>
                <td><?= h($courseassignment->assignedon) ?></td>
             
                <td><?= $courseassignment->has('user') ? $courseassignment->user->username: '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View Courses'), ['action' => 'view', $courseassignment->id],['class'=>'btn btn-round btn-primary fa fa-eye']) ?>
                    <?= $this->Html->link(__('Update'), ['action' => 'editassigncourses', $courseassignment->id],['class'=>'btn btn-round btn-success fa fa-edit']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $courseassignment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseassignment->id),'class'=>'btn btn-round btn-danger']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
      

            </div>
        </div>
    </div>

</div>
