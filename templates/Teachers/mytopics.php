<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>
<!-- Begin Page Content -->
        <div class="content container-fluid">
          <!-- Page Header -->
           <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Manage Topics</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Manage Topics</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
            
          <h1 class="h3 mb-2 text-gray-800">&nbsp;</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Topic Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
              
                        <th >Subject</th>
                        <th>Topic</th>
                      <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
                 <tr>
              
                        <th >Subject</th>
                        <th>Topic</th>
                   
                        <th>Actions</th>
            </tr>
        </tfoot>
        <tbody>
            <?php if ($mytopics->count() == 0): ?>
            <tr>
                <td colspan="3" class="text-center">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>No Subjects Assigned</strong><br>
                        You don't have any subjects assigned to your account yet. Please contact the school administration to assign subjects to your teacher account before managing topics.
                        <br><br>
                        <?= $this->Html->link('Back to Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($mytopics as $topic): ?>
                <tr>
                  
                    <td><?= $topic->has('subject') ? $this->Html->link($topic->subject->name . ' (' . (isset($topic->subject->department) ? $topic->subject->department->name : 'No Class') . ')', ['controller' => 'Teachers', 'action' => 'viewtopic', $topic->id,$this->GenerateUrl($topic->title)]) : '' ?></td>
                   <td><?= h($topic->title) ?></td>
                 <td class="actions">
                       <?= $this->Html->link(__(' '), ['action' => 'updatetopic', $topic->id,$this->GenerateUrl($topic->title)],['class'=>'btn btn-primary fa fa-edit','title'=>'update topic']) ?>
                       <?= $this->Html->link(__(' '), ['action' => 'viewtopic', $topic->id,$this->GenerateUrl($topic->title)],['class'=>'btn btn-info fa fa-eye','title'=>'view topics']) ?>
                      <!--  <?= $this->Form->postLink(__(' '), ['action' => 'delete', $topic->id], ['confirm' => __('Are you sure you want to delete # {0}?', $topic->title),'class'=>'fa fa-times btn btn-danger']) ?>
                   
                 -->
                 </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
               
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
