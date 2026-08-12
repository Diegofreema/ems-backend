<?php
  $userdata = $this->request->getSession()->read('usersinfo');
?>
  <div class="container-fluid">
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
</div><!--/end d-sm-flex-->
<div class="row">
    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Photo</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <?= $this->Html->image('../staff_files/'.$teacher->passport, ['alt' => 'BUSCED', 'class' => 'img-responsive avatar-view', "width" => "100%", "height" => "300px"]) ?>
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
                <h6 class="m-0 font-weight-bold text-primary">Overview</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Name</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->fname . " " . $teacher->lname . " " . $teacher->mname ?></div>
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
                <?php if (!empty($teacherClassArms)): ?>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Assigned Class Arms</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            <?php foreach ($teacherClassArms as $classArm): ?>
                                <span class="badge badge-primary mr-1"><?= h($classArm->department->name) ?> - <?= h($classArm->arm_name) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <?php else: ?>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Class Arms</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">No class arms assigned</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <?php endif; ?>
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Country</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->country->name ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
                <!--/end no-gutters-->
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">State</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->state->name ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">CV</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">

                            <?php if(!empty($teacher->cv)) {echo $this->Html->link(__('Download CV'), ['action' => 'downloadcv', $teacher->id], ['title' => 'download cv']);} ?> 
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
                <hr/>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Assigned Courses</div>
                        <?php if (!empty($teacher->subjects)): ?>
                              <table>
                                  <tr>

                                      <th style="margin: 5px; padding: 5px;">Courses Name </th>
                                      <th style="margin: 5px; padding: 5px;"> Courses Code</th>

                                      <th style="margin: 5px; padding: 5px;"> Credit Load </th>

                                  </tr>
                                  <?php foreach ($teacher->subjects as $subjects): ?>
                                      <tr>

                                          <td><?= h($subjects->name) ?></td>
                                          <td><?= h($subjects->subjectcode) ?></td>

                                          <td><?= h($subjects->creditload) ?></td>

                                      </tr>
                                  <?php endforeach; ?>
                              </table>
                          <?php endif; ?>
<!--            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $teacher->state->name ?></div>-->
                    </div>
                    <div class="col-auto">
<!--                        <i class="fas fa-globe fa-2x text-gray-300"></i>-->
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
  </div>





