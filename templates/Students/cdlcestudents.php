<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="container-fluid">
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
  
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search CDLCE Students </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">
                    <div class="col-sm-3 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('level_id', ['label' => 'Select Level', 'placeholder' => 'Select Level',
                              'class' => 'form-control form-control-user2', 'options' => $levels,'empty'=>'Select Level']);
                        ?>
                    </div>
                    <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Department', 'empty' => 'Select Department', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>
                    
                     <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('category_id', ['options' => $categories, 'label' => 'Select Category', 'empty' => 'Select Category', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>
                    
                    <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('programetype_id', ['options' => $programetypes, 'label' => 'Programe Type', 'empty' => 'Programe Type', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>

                </div>
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        </div>
        <h1 class="h3 mb-2 text-gray-800">Manage CDLCE Students</h1>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students CDLCE Manager</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                            <th >Name</th>
                            <th>RegNo</th>
                            <th >Department</th>
                             <th>Class</th>
                              <th>Programme</th>
                            <th >Passport</th>

                            <th >DOB</th>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Autonomous Community</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Hostel/Room</th>
                            <th>Admission Date</th>
                            <th>Mode</th>
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
                                  <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                                  <td><?= h($student->level->name) ?></td>
                                    <td><?= h($student->programme->name) ?></td>

                                  <td> <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
          'style' => 'width:80px;height:80px;'])
      ?>
                                  </td>
                                  <td><?= h($student->dob) ?></td>
                                  <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
                                  <td><?php if(!empty($student->lga->name)){
                                  echo h($student->lga->name);} ?></td>
                                  
                                  <td><?= h($student->community) ?></td>
                                  <td><?= h($student->phone) ?></td>
                                  <td><?= h($student->gender) ?></td>
                                  <td><?= h($student->user->username) ?></td>
                                  <td> <?php 
                                                   foreach($student->hostelrooms as $room ){
                                                       echo $room->hostel->name.' / '. $room->room_number;
                                                   }?></td>
                                  <td><?= h($student->admissiondate) ?></td>
                                  <td><?= h($student->mode->name) ?></td>
                                   <td><?= h($student->status) ?></td>
                                  
                                  <td class="actions">
                                      
                                      <?= $this->Html->link(__(' '), ['action' => 'updatestudent', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'view student details'])
                                      ?>
                                      &nbsp;<!--?= $this->Html->link(__(' Update Email'), ['action' => 'validateemail', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-info fa fa-edit', 'title' => 'update username'])
                                      ?-->
                                       &nbsp;<?= $this->Html->link(__(' Reset Password'), ['action' => 'resetpassword', $student->user_id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-warning fa fa-edit', 'title' => 'reset password'])
                                      ?>
                                     &nbsp;<!--?= $this->Html->link(__('Photo Card'), ['controller'=>'Students','action' => 'summarypage', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary', 'title' => 'get photo card'])
                                      ?-->
                                      &nbsp;<?= $this->Html->link(__(' A. Letter'), ['controller'=>'Students','action' => 'printacceptanceletter', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-success', 'title' => 'get acceptance letter'])?>
                                      &nbsp;<?php if($userdata['role_id']==5){
                                          echo $this->Html->link(' Generate Transcript', ['controller' => 'Admins', 'action' => 'generatetranscript',$student->id], ['title' => 'generate student transcript', 'class' => 'btn btn-success']);
                                      } ?>
                                      &nbsp;<?= $this->Html->link(__('Assign Room'), ['controller'=>'Hostelrooms','action' => 'assignroomtostudent', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary', 'title' => 'assign room to '.$student->fname])
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






