<?php
  $userdata = $this->request->getSession()->read('usersinfo');
?>
<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">View Teacher</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Teachers', ['controller' => 'Teachers', 'action' => 'manageteachers', $this->GenerateUrl('Manage Teachers')], ['title' => 'Manage Teachers'])
                            ?></li>
                        <li class="breadcrumb-item active">View Teacher</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
    
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"> Profile</h1>
</div><!--/end d-sm-flex-->
<div class="row">
    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"> Photo</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body text-center">
                <?= $this->Html->image('../staff_files/'.$teacher->passport, ['alt' => 'Teacher Photo', 'class' => 'img-fluid rounded-circle', 'style' => 'max-width: 200px; max-height: 200px; width: 100%; height: auto; object-fit: cover;']) ?>
            </div>
            <!--/end card body-->
        </div>
        <!--/end card-->
    </div>
    <!--/end col-xl-4-->

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"> Overview</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Name</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->user->fname . " " . $teacher->user->lname . " " . $teacher->user->mname ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->user->username ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-envelope fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Phone</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->phone ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-phone fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Address</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->address ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-map-marker fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>

                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Join Date</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"> <?= h(date('d/M/Y', strtotime($teacher->date_created))) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-map-marker fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Class/Class Arm</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($teacher->department) && $teacher->department ? $teacher->department->name : 'No Class Assigned' ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-home fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>

                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Profile</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= h($teacher->profile) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-home fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">State</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($teacher->state) && $teacher->state ? $teacher->state->name : 'Not Set' ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Country</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($teacher->country) && $teacher->country ? $teacher->country->name : 'Not Set' ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
              
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">CV</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">

                            <?php if(!empty($teacher->cv)){ echo  $this->Html->link(__('Download CV'), ['action' => 'downloadcv', $teacher->id], ['title' => 'download cv']);} ?> 
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Assigned Subjects</div>
                        <?php if (!empty($teacher->subjects)): ?>
                              <table>
                                  <tr>

                                      <th style="margin: 5px; padding: 5px;">Subject Name </th>
                                      <th style="margin: 5px; padding: 5px;"> Class</th>
                                      <th style="margin: 5px; padding: 5px;"> Subject Code</th>

                                      <th style="margin: 5px; padding: 5px;"> Credit Unit </th>

                                  </tr>
                                  <?php foreach ($teacher->subjects as $subjects): ?>
                                      <tr>

                                          <td><?= h($subjects->name) ?></td>
                                          <td><?= h($subjects->department->name ?? 'No Class') ?></td>
                                          <td><?= h($subjects->subjectcode) ?></td>

                                          <td><?= h($subjects->creditload) ?></td>

                                      </tr>
                                  <?php endforeach; ?>
                              </table>
                          <?php endif; ?>
<!--            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->state->name ?></div>-->
                    </div>
                    <div class="col-auto">
<a href="#" data-toggle="modal" data-target="#assignsubjecttoteachers">

      <button class="btn btn-success fa fa-check-circle float-md-right" title="assign subjects">  </button>
                                      </a>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
            </div>
        </div>
        <!--/end card-->
    </div>
    <!--/end col-xl-8-->
</div>
<!-- assign subjects  Modal-->
<div class="modal fade" id="assignsubjecttoteachers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Subjects/Lecturer Assignment</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="col-lg-12">
                <div class="p-">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Assign Subjects To : <?= $teacher->user->fname . " " . $teacher->user->lname ?></h1>
                    </div>
                    <?= $this->Form->create(null, ['url' => ['controller' => 'Teachers', 'action' => 'assignsubjects']]) ?>
                    <fieldset>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?= $this->Form->control('teacher_id', ['value' => $teacher->id, 'type' => 'hidden']) ?>
                                <br />
                            </div>

                            <div class="col-sm-12">
                                <label for="subjects-ids">Assign Subjects</label>
                                <select name="subjects[_ids][]" multiple="multiple" class="form-control form-control-user2" id="subjects-ids">
                                    <?php 
                                    // Get the teacher's currently assigned subject IDs
                                    $assignedSubjectIds = [];
                                    if (!empty($teacher->subjects)) {
                                        foreach ($teacher->subjects as $assignedSubject) {
                                            $assignedSubjectIds[] = $assignedSubject->id;
                                        }
                                    }
                                    
                                    foreach ($subjectlists as $id => $subject): 
                                        $isSelected = in_array($id, $assignedSubjectIds) ? 'selected' : '';
                                    ?>
                                        <option value="<?= h($id) ?>" <?= $isSelected ?>><?= h($subject) ?></option>
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
</div>


