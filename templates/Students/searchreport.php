<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Search Students/Applicants</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Search Students/Applicants</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newstudent'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'addmit new student'])
?>
        <!-- Page Heading -->
         <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search Students/Applicants </h1>
                        </div>
    <?= $this->Form->create(null,['url'=>['controller'=>'Students','action'=>'searchreport']]) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('startdate',['label' => 'Start Date', 'empty' => 'Selecet Start Date',
      'class'=>'form-control floating datetimepicker','type'=>'text', 'id' => 'datepicker']);
            
            ?>
        </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('enddate',['label' => 'End Date', 'empty' => 'Select End Date',
      'class'=>'form-control floating datetimepicker','type'=>'text', 'id' => 'datepicker2']);
            
            ?>
        </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
                 
<?php $status = ['Applied'=>'Pending','Admitted'=>'Admited'];
        echo $this->Form->control('status', ['options' => $status, 'label' => 'Select Status', 'empty' => 'Select Status', 'class' => 'form-control form-control-user']) ?>
                                </div>
       
            </div>
        
        <div class="form-group row">
            
            <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Class', 'empty' => 'Select Class', 'class' => 'form-control form-control-user']) ?>
                                </div>
            
            
            
               </div>
    </fieldset>
   <br /> <br />
                    <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
        <h1 class="h3 mb-2 text-gray-800">Manage Students/Applicants</h1></div>


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

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>



                            <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                             <th>Class</th>
                            <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>

                            <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Autonomous Community</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>App_No</th>
                            <th>Admission Date</th>
                              <th>Status</th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>


                    <tfoot>
                        <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>



                            <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                          
                              <th>Class</th>
                            <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>

                            <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Autonomous Community</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>App_No</th>
                            <th>Admission Date</th>
                              <th>Status</th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </tfoot>


                    <tbody>
<?php  foreach ($students as $student): ?>
                              <tr>

                                  <td>
                                     <?= $this->Html->link($student->fname . ' ' . $student->lname, ['controller' => 'Students', 'action' => 'viewstudent', $student->id,$this->generateurl($student->lname)])?>
   </td>



                                  <td><?php if($student->status == "Applied"){echo "Not Admited Yet";}
                                  else { echo h($student->regno);} ?></td>
                                  <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                                
                          
                                  <td> <?=  $this->Html->image('../student_files/'.$student->passporturl, ['alt' => $student->regno, 'class' => 'img-circle profile_img',
          'style' => 'width:80px;height:80px;'])?>
                                   
                                  </td>
                                  <td><?= h($student->dob) ?></td>
                                  <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
                                  <td><?= h($student->lga->name ?? '') ?></td>
                                  <td><?= h($student->community) ?></td>
                                  <td><?= h($student->phone) ?></td>
                                  <td><?= h($student->gender) ?></td>
                                  <td><?= h($student->user->username) ?></td>
                                  <td><?= h($student->application_no) ?></td>
                                  <td><?= h($student->admissiondate) ?></td>
                                   <td><?= h($student->status) ?></td>
                                  <td class="actions">
                                      
                                      <?= $this->Html->link(__(' '), ['action' => 'updatestudent', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'view student details'])
                                      ?>
                                      &nbsp;<?= $this->Html->link(__(' Update Email'), ['action' => 'validateemail', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-info fa fa-edit', 'title' => 'update username'])
                                      ?>
                                       &nbsp;<?= $this->Html->link(__(' Reset Password'), ['action' => 'resetpassword', $student->user_id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-warning fa fa-edit', 'title' => 'reset password'])
                                      ?>
                                     &nbsp;<?= $this->Html->link(__(' Get Photo Card'), ['controller'=>'Students','action' => 'summarypage', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary', 'title' => 'get photo card'])
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