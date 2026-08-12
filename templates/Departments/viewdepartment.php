 <div class="content container-fluid">
  <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View Class</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
  
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Classes', ['controller' => 'Departments', 'action' => 'managedepartments'], ['title' => 'manage classes']) ?></li>
                    <li class="breadcrumb-item active">View Class</li>
                </ul>
            </div>
        </div>
    </div>

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Class : <?= h($department->name) ?></h1>

          <div class="row">

            <div class="col-lg-12">

              <!-- Circle Buttons -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary"> 
            <!-- <?= $department->has('faculty') ? $this->Html->link($department->faculty->name, ['controller' => 'Faculties', 'action' => 'viewfaculty', $department->faculty->id,$this->generateurl($department->faculty->name)]) : '' ?></td> -->
     </h6>
                </div>
                <div class="card-body">
                  <!-- <p>List of Programmes  </p> -->
                  <!-- Circle Buttons (Default) -->
                   <!-- <?php if (!empty($department->programes)){
                  foreach ($department->programes as $programes){
          
                  echo h($programes->name.' / '.$programes->programecode).'<br />';
                  
                   }} ?> -->
                  <hr />
                   <p>Fees Applicable to this Class  </p>
                  <?php if (!empty($department->fees)){
                      
                  foreach ($department->fees as $fee){
          
                  echo h($fee->name.'  -  N'.number_format($fee->amount,2)).'<br />';
                  
                   }} ?>
                  
                  <?php if (!empty($department->class_arms)){?>
                   <br /> <strong>Class Arms</strong>
                   <table id="classArmsTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                       
                       <thead>
            <tr>
                <th>Arm Name</th>
                <th>Class Teacher</th>
                <th>Students</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
                  </thead>
                      <tbody>
                 <?php foreach ($department->class_arms as $classArm){?>
                          <tr>
                              <td>
                                  <?= h($classArm->arm_name) ?>
                              </td>
                              <td>
                                  <?php if (!empty($classArm->teacher)): ?>
                                      <?= h($classArm->teacher->user->fname . ' ' . $classArm->teacher->user->lname) ?>
                                  <?php else: ?>
                                      <span class="text-muted">Not Assigned</span>
                                  <?php endif; ?>
                              </td>
                              <td>
                                  <span class="badge badge-info"><?= count($classArm->students) ?> students</span>
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
                              <td>
                                  <?= $this->Html->link(__('View'), ['controller' => 'ClassArms', 'action' => 'view', $classArm->id],
                                      ['class'=>'btn btn-sm btn-primary fa fa-eye','title'=>'view class arm']) ?>
                                  <?= $this->Html->link(__('Students'), ['controller' => 'ClassArms', 'action' => 'manageStudents', $classArm->id],
                                      ['class'=>'btn btn-sm btn-info fa fa-users','title'=>'manage students']) ?>
                              </td>
                          </tr>
          
                  <?php }}?>
                      </tbody>
                   </table>
                   <hr />
               
                  <?php if (!empty($department->subjects)){?>
                   <br /> <strong>Courses</strong>
                   <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                       
                       <thead>
            <tr>
          
                <th >Name</th>
                <th >Course Code</th>
                <th>Semester</th>
                <th>Class</th>
                <th>Credit Load</th>
                
            </tr>
                  </thead>
                      <tbody>
                 <?php foreach ($department->subjects as $subject){?>
                          <tr>
                              <td>
                                  <?=$subject->name?>
                              </td>
                              <td>
                                  <?=$subject->subjectcode?>
                              </td>
                               <td>
                                  <?php if(!empty($subject->semester->name)){echo $subject->semester->name;}?>
                              </td>
                               <td>
                                  <?php if(!empty($subject->level->name)){echo $subject->level->name;}?>
                              </td>
                              <td>
                                  <?=$subject->creditload?>
                              </td>
                          </tr>
          
                  <?php }}?>
                      </tbody>
                   </table>
                   <hr />
                </div>
              </div>

            </div>


          </div>

        </div>
        <!-- /.container-fluid -->
