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
                <h3 class="page-title">Manage Students</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Manage Students</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
  <?= $this->Html->link(__(' '), ['action' => 'newstudent'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'addmit new student'])
?>
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Students </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">
                    <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user', 'onChange' => 'getClassArms(this.value)']) ?>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0" id="classArms">
                        <label for="class_arm_id">Select Class Arm</label>
                        <select name="class_arm_id" class="form-control form-control-user">
                            <option value="">Select Class Arm</option>
                        </select>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('session_id', ['label' => 'Select Session', 'placeholder' => 'Select Session',
                              'class' => 'form-control form-control-user2', 'options' => $sessions,'empty'=>'Select Session']);
                        ?>
                    </div>
                </div>
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        <br />
        <h1 class="h3 mb-2 text-gray-800">&nbsp;</h1>
        <br />



    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students Manager</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                            <th >Name</th>
                            <th>RegNo</th>
                            <th >Class</th>
                            <th >Passport</th>
                            <th >DOB</th>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Admission Date</th>
                            <th>Status</th>
                           
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>



                    <tbody>
<?php foreach ($students as $student): ?>
                              <tr>

                                  <td>
                                     <?= $this->Html->link(ucfirst($student->fname . ' ' . $student->lname), ['controller' => 'Students', 'action' => 'viewstudent', $student->id,$this->generateurl($student->lname)])?>
   </td>



                                  <td><?= h($student->regno) ?></td>
                                  <td><?= $student->has('department') ? $this->Html->link($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : ''), ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                                  <td> <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
          'style' => 'width:80px;height:80px;'])
      ?>
                                  </td>
                                  <td><?= h($student->dob) ?></td>
                                  <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
                                  <td><?php if(!empty($student->lga->name)){
                                  echo h($student->lga->name);} ?></td>
                                  <td><?= h($student->phone) ?></td>
                                  <td><?= h($student->gender) ?></td>
                                  <td><?= h($student->user->username) ?></td>
                                  <td><?= h($student->admissiondate) ?></td>
                                   <td><?= h($student->status) ?></td>
                                  
                                  <td class="actions">
                                      
                                      <?= $this->Html->link(__(' '), ['action' => 'updatestudent', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'view student details'])
                                      ?>
                                      &nbsp;<!--?= $this->Html->link(__(' Update Email'), ['action' => 'validateemail', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-info fa fa-edit', 'title' => 'update username'])
                                      ?-->
                                       &nbsp;<?= $this->Html->link(__(' Reset Pwd'), ['action' => 'resetpassword', $student->user_id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-warning fa fa-edit', 'title' => 'reset password'])
                                      ?>
                                     <!-- &nbsp;<?= $this->Html->link(__('A. RegNo'), ['controller'=>'Students','action' => 'assignregno', $student->id, $student->department_id,$this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-success', 'title' => 'assign RegNo'])
                                      ?> -->
                                      <!-- &nbsp;<?= $this->Html->link(__(' A. Letter'), ['controller'=>'Students','action' => 'printacceptanceletter', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-success', 'title' => 'get acceptance letter'])?>
                                      &nbsp;<?php if($userdata['role_id']==5){
                                          echo $this->Html->link(' G. Transcript', ['controller' => 'Admins', 'action' => 'generatetranscript',$student->id], ['title' => 'generate student transcript', 'class' => 'btn btn-success']);
                                      } ?> -->
                                      <!-- &nbsp;<?= $this->Html->link(__('A. Room'), ['controller'=>'Hostelrooms','action' => 'assignroomtostudent', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary', 'title' => 'assign room to '.$student->fname])
                                      ?>  &nbsp; -->
                                      <?php if($student->studentstatus=="Active" || $student->studentstatus== null){
                                          echo $this->Html->link(' Susp Acc', ['controller' => 'Students', 'action' => 'suspendstudent',$student->id,$this->Generateurl($student->fname)], ['title' => 'suspend account', 'class' => 'btn btn-warning']);
                                      }
                                      elseif($student->studentstatus=="Suspended" || $student->studentstatus== null){
                                          echo $this->Html->link(' Re-Activate', ['controller' => 'Students', 'action' => 'unsuspendstudent',$student->id,$this->Generateurl($student->fname)], ['title' => 'suspend account', 'class' => 'btn btn-warning']);
                                      }
                            //    echo '&nbsp;'. $this->Html->link(' Resend L', ['controller' => 'Students', 'action' => 'sendadminletter',$student->id,$this->Generateurl($student->fname)], ['title' => 'resend admission letter', 'class' => 'btn btn-warning']);
                            //    echo '&nbsp;'. $this->Html->link(' Print A L', ['controller' => 'Students', 'action' => 'printadminletter',$student->id,$this->Generateurl($student->fname)], ['title' => 'print admission letter', 'class' => 'btn btn-warning']);
                                      ?>
                                     
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
function getClassArms(departmentid){ 
    $.ajax({
        url: '../ClassArms/getArmsForDepartment/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            // Preserve the label and only replace the select element
            var label = '<label for="class_arm_id">Select Class Arm</label>';
            document.getElementById('classArms').innerHTML = label + response;
        }
    });
}
</script>



