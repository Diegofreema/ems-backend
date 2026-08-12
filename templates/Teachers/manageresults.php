
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Manage Results</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('teacher dashboard')], ['title' => 'teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Manage Results</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
              
                    <div class="p-5">
                        <div class="text-center">
                             <h1 class="h4 text-gray-900 mb-4">Result Manager</h1>
                        </div>
                        <?= $this->Form->create(null) ?>
                        <fieldset>
      
                            <div class="form-group row">
                                 <div class="col-sm-4">
                                    <?= $this->Form->control('subject_id', ['options' => $subjects,'label' => 'Select Course', 'required', 'placeholder' => 'Select Course'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                           <div class="col-sm-4">
                                    <?= $this->Form->control('semester_id', ['options' => $semesters,'label' => 'Select Semester', 'required', 'placeholder' => 'Select Semester'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>  
                                <div class="col-sm-4">
                                    <?= $this->Form->control('session_id', ['options' => $sessions,'label' => 'Select Session', 'required', 'placeholder' => 'Select Session'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                                                
                                </div>
                          
                        </fieldset>
                        <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>

                    </div>
               
                
               <?php if(!empty($results)){   ?>
                   <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Results Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                 <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
              
                <th>Student</th>
                 <th>Regno</th>
               
                <th>Department</th>
                <th>Subject</th>
                <th >Term</th>
                <th >Session</th>
                <th>Total</th>
                <th>Grade</th>
               <th >Remark</th>
<!--                <th scope="col" class="actions"><?= __('Actions') ?></th>-->
            </tr>
                  <tfoot>
                       <tr>
              
               <th>Student</th>
                 <th>Regno</th>
                <th>Department</th>
                <th>Subject</th>
                <th >Term</th>
                <th >Session</th>
                <th>Total</th>
                <th>Grade</th>
               <th >Remark</th>
<!--                <th scope="col" class="actions"><?= __('Actions') ?></th>-->
            </tr>
                  </tfoot>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
            <tr>
              
                <td><?= $result->has('student') ? $result->student->fname . ' ' . $result->student->lname : '' ?></td>
                <td><?= h($result->regno) ?></td>
                <td><?= $result->has('department') ? $result->department->name : '' ?></td>
                <td><?= $result->has('subject') ? $result->subject->name : '' ?></td>
                <td><?= $result->has('semester') ? $result->semester->name : '' ?></td>
                <td><?= $result->has('session') ? $result->session->name : '' ?></td>
                <td><?= $this->Number->format($result->total) ?></td>
                <td><?= h($result->grade) ?></td>
                <td><?= h($result->remark) ?></td>
               
<!--                <td class="actions">
                  
                    <?= $this->Html->link(__(' '), ['action' => 'updateresult', $result->id],['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'update result']) ?>
                    <?= $this->Form->postLink(__(' '), ['action' => 'delete', $result->id], ['confirm' => __('Are you sure you want to delete # {0}?', $result->id),'class'=>'btn btn-round btn-danger fa fa-times-circle','title'=>'delete result']) ?>
                </td>-->
            </tr>
            <?php endforeach; ?>
        </tbody>
     </table>
              </div>
            </div>
          </div>
 
               <?php } ?>
                
            </div>
   

