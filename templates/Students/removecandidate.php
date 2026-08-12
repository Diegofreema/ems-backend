<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
<?= $this->Html->link(__(' '), ['action' => 'newstudent'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'addmit new student']) ?>
          <!-- Page Heading -->
          
          <h1 class="h3 mb-2 text-gray-800">Manage Applicants</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Applicants Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="myTabl" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
          
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>          
                <th scope="col"><?= $this->Paginator->sort('Date Applied') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                 <th>Status</th>

                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                  
               
              <th>App_No</th>
              <th>Mode</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
                  </thead>
            
            
         <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                
                <td><?= h($student->fname.' '.$student->lname) ?></td>
             
             
               
                <td><?= h(date('D, d M Y', strtotime($student->joindate))) ?></td>
                <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                <td><?= h($student->status) ?></td>
>
                <td> <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
                                    'style' => 'width:80px;height:80px;']) ?>
               </td>
              <td><?= h($student->application_no) ?></td>
              <td><?= h($student->mode->name) ?></td>
                <td >
        
               <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Students','action' => 'deleteapplicant', $student->id],
                          ['confirm' => __('Are you sure you want to delete this applicant # {0}?',$student->fname),
                              'class'=>'btn btn-danger fa fa-times']) ?> &nbsp;
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
