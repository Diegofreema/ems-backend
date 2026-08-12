<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assign Fee</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Assign Fee</li>
                </ul>
            </div>
        </div>
    </div>
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Students </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">

                    <div class="col-sm-8 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>

                </div>
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        </div>
       <?php if(!empty($students)){  ?>
        
        <h1 class="h3 mb-2 text-gray-800">Manage Fee Assignment</h1></div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students Fee Assignment</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
               <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Department') ?></th>                            
                            <th scope="col"><?= $this->Paginator->sort('Passport') ?></th> 
                            <th>State</th>
                           
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>


                    <tfoot>
                         <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Passport') ?></th> 
                            <th>State</th>
                           
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </tfoot>


                    <tbody>
<?php foreach ($students as $student): ?>
                              <tr>

                                  <td>
                                     <?= $this->Html->link($student->fname . ' ' . $student->lname, ['controller' => 'Students', 'action' => 'viewstudent', $student->id,$this->generateurl($student->lname)])?>
   </td>



                                  <td><?= h($student->regno) ?></td>
                                  <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                                  
                                  <td> <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
          'style' => 'width:80px;height:80px;'])
      ?>
                                  </td>
                                  <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
                                  
                                  <td class="actions">
                                      
                                      <?= $this->Html->link(__('View Invoices '), ['action' => 'getstudentinvoices', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-success', 'title' => 'get student invoices'])
                                      ?>
                                      
                                      <?= $this->Html->link(__('Assign Fee '), ['action' => 'feeassignment', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary', 'title' => 'assign fee to this student'])
                                      ?>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
       <?php } ?>

</div>
