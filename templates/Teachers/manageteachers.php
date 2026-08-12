<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>

<div class="content container-fluid">
     
        <?php $this->Form->button(' ',['href'=>'#','data-toggle'=>'modal','data-target'=>'#assignsubjects',
            'class'=>'btn btn-success fa fa-check-circle float-lg-right','title'=>'assign subjects to teachers']) ?>
         
  <?= $this->Html->link(__(' '), ['controller' => 'Teachers', 'action' => 'newteacher'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'Add new teacher'])
?>
        <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Manage Teachers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Manage Teachers</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Manage lecturers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th >name</th>
                            <th scope="col"><?= $this->Paginator->sort('email') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('gender') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('address') ?></th>
<!--                            <th scope="col"><?= $this->Paginator->sort('country') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('state') ?></th>-->
                            <th scope="col"><?= $this->Paginator->sort('phone') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Cert') ?></th>
                           
                            <th >Actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th >name</th>
                            <th scope="col"><?= $this->Paginator->sort('email') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('gender') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('address') ?></th>
<!--                            <th scope="col"><?= $this->Paginator->sort('country') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('state') ?></th>-->
                            <th scope="col"><?= $this->Paginator->sort('phone') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Cert') ?></th>
                          
                            <th >Action</th>
                        </tr>  
                    </tfoot>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                              <tr>
                                  <td><?= $teacher->user->fname . ' ' . $teacher->user->lname ?></td>
                                  <td><?= $teacher->has('user') ? $teacher->user->username : '' ?></td>
                                  <td><?= h($teacher->gender) ?></td>
                                  <td><?= h($teacher->address) ?></td>
      <!--                                  <td><?= $teacher->has('country') ? $teacher->country->name : '' ?></td>
                                  <td><?= $teacher->has('state') ? $teacher->state->name : '' ?></td>-->
                                  <td><?= h($teacher->phone) ?></td>
      <!--                                  <td><?= h($teacher->profile) ?></td>-->

                                  <td><?= h($teacher->qualification) ?></td>
                              
                                  <td class="actions" width="20px;">
<!--                                      <a href="#" data-toggle="modal" data-target="#assignsubjects">

                                          <button class="btn btn-success fa fa-check-circle" title="assign subjects">  </button>
                                      </a>-->
                                      <?= $this->Html->link(__(' '), ['action' => 'viewteacher', $teacher->id, $this->generateurl($teacher->user->fname)], ['class' => 'fa fa-eye btn btn-info', 'title' => 'view teacher']) ?>
                                      <?= $this->Html->link(__(' '), ['action' => 'updateteacher', $teacher->id, $this->generateurl($teacher->user->fname)], ['class' => 'fa fa-edit btn btn-primary', 'title' => 'update teacher']) ?>
                                      <?= $this->Form->postLink(__(' '), ['action' => 'delete', $teacher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $teacher->id), 'class' => 'fa fa-times-circle btn btn-danger', 'title' => 'delete teacher']) ?>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<!-- assign subjects  Modal-->
<div class="modal fade" id="assignsubjects" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Subjects/Teacher Assignment</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="col-lg-12">
                <div class="p-">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Assign Course(s) To Lecturer</h1>
                    </div>
                    <?= $this->Form->create(null, ['url' => ['controller' => 'Teachers', 'action' => 'assignsubjects']]) ?>
                    <fieldset>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?= $this->Form->control('user_id', ['options' => $users, 'label' => 'Select Lecturer', 'empty' => 'Select Teacher', 'class' => 'form-control form-control-user2']) ?>
                                <br />
                            </div>

                            <div class="col-sm-12">
                                <label for="subjects-ids">Assign Courses</label>
                                <select name="subjects[_ids][]" multiple="multiple" class="form-control form-control-user2" id="subjects-ids">
                                    <?php foreach ($subjects as $id => $subject): ?>
                                        <option value="<?= h($id) ?>"><?= h($subject) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple subjects.</small>
                            </div>
                        </div>
                    </fieldset>
                    <br /> <br />
                    <?= $this->Form->button('Assign', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                    <?= $this->Form->end() ?> <br /> <br />
                </div>
            </div>
        </div>
    </div>
</div>