<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="container-fluid">
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
  <?= $this->Html->link(__(' '), ['action' => 'newstudent'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'addmit new student'])
?>
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Students </h1>
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
        <h1 class="h3 mb-2 text-gray-800">Manage Students</h1>
    </div>


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
                            <th >Department</th>
                             <th>Level</th>
                              <th>Programme</th>
                            <th >Passport</th>
                             <th >Faculty</th>
                            <th >DOB</th>
                            <th>State</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>QR-Code</th>
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
                                   <td><?= h($student->faculty->name) ?></td>
                                  <td><?= h($student->dob) ?></td>
                                  <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
                                 
                                  
                               
                                  <td><?= h($student->phone) ?></td>
                                  <td><?= h($student->gender) ?></td>  
                                   <td> <?=
                                            $this->Qr->text($student->fname . ' ' . $student->lname . ' '.$student->mname.
                                                    ' ' . $student->regno . ', ' . $student->programme->name . ', ' . $student->level->name. ', '
                                                    . $student->phone . ', ' . $student->user->username);
                                            ?></td>
                              </tr>
                          <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>






