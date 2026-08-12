
<!-- Begin Page Content -->
<div class="content container-fluid">
    
<?= $this->Html->link(__('New Assignment'), ['action' => 'addassignment'], ['class' => 'button float-right']) ?>
    
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Manage Assignments</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Manage Assignments</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    <h1 class="h3 mb-2 text-gray-800">&nbsp;</h1>


   
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assignments Manager</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>
                 
                    <th><?= $this->Paginator->sort('Subject') ?></th>
                    <th><?= $this->Paginator->sort('Test Title') ?></th>
               
                    <th><?= $this->Paginator->sort('Term') ?></th>
                    <th><?= $this->Paginator->sort('Status') ?></th>
                    <th><?= $this->Paginator->sort('Questions') ?></th>
                    <th><?= $this->Paginator->sort('Time Limit') ?></th>
                    <th><?= $this->Paginator->sort('Closing Date') ?></th>
                    <th><?= $this->Paginator->sort('Date Created') ?></th>
                    <th> Submissions </th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($setassignments as $setassignment): ?>
                <tr>
                
                    <td><?= $setassignment->has('subject') ? $setassignment->subject->name . ' (' . (isset($setassignment->subject->department) ? $setassignment->subject->department->name : 'No Class') . ')' : '' ?></td>
                    <td><strong><?= h($setassignment->title ?? 'Untitled Test') ?></strong></td>

                    <td><?= $setassignment->has('semester') ? $setassignment->semester->name : '' ?></td>
                    <td>
                        <span class="badge badge-<?= $setassignment->status === 'active' ? 'success' : 'secondary' ?>">
                            <?= h($setassignment->status) ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        $questionCount = $questionCounts[$setassignment->id] ?? 0;
                        echo $questionCount > 0 ? $questionCount . ' questions' : '<span class="text-warning">No questions</span>';
                        ?>
                    </td>
                    <td><?= h($setassignment->time_limit ?? 'N/A') ?> min</td>
                    <td><?= h($setassignment->closedate) ?></td>
                    <td><?= h($setassignment->datecreated) ?></td>
                    <td><?= $this->getsubmittedass($setassignment->subject->id,$setassignment->id) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__(' '), ['action' => 'view', $setassignment->id],['class'=>'btn btn-primary fa fa-eye','title'=>'view test']) ?>
                         &nbsp;&nbsp;  <?= $this->Html->link(__(' '), ['action' => 'editassignment', $setassignment->id],['class'=>'btn btn-success fa fa-edit','title'=>'edit test']) ?>
                       &nbsp;&nbsp;   <?= $this->Form->postLink(__(' '), ['action' => 'delete', $setassignment->id,'title'=>'delete test'], 
                                ['confirm' => __('Are you sure you want to delete # {0}?', $setassignment->id),'class'=>'btn btn-danger fa fa-times']) ?>
                  &nbsp;&nbsp;  <?= $this->Html->link(__(' '), ['action' => 'managequestions', $setassignment->id],
                          ['class'=>'btn btn-info fa fa-list','title'=>'manage questions']) ?>
                  &nbsp;&nbsp;  <?= $this->Html->link(__(' '), ['action' => 'viewsubmissions', $setassignment->id],
                          ['class'=>'btn btn-warning fa fa-users','title'=>'view submissions']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
         </table>
            </div>
        </div>
    </div>


    
            <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
