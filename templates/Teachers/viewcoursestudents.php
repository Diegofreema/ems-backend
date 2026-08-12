<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">View Subject Students</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('My Subjects', ['controller' => 'Teachers', 'action' => 'assignedcourses', $this->GenerateUrl('My Subjects')], ['title' => 'My Subjects'])
                            ?></li>
                        <li class="breadcrumb-item active">View Subject Students</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <?php if(empty($registered)){ ?>
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search Students</h1>
                        </div>
                        <?= $this->Form->create(null) ?>
                        <fieldset>
                      
                         
                         
                         
                            <div class="form-group row">
                                <div class="col-sm-3">
<?= $this->Form->control('semester_id', ['label' => false, 'class' => 'form-control form-control-user2', 'options' => $semesters, 'empty' => 'Select Semester','required']) ?>

                                </div>
                                <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('session_id', ['label' => false, 'id'=>'states1','class' => 'form-control form-control-user2', 'options' => $sessions, 'empty' => 'Select Session','required']); ?>

                                </div>

                              <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('level_id', ['label' => false, 'id'=>'states1','class' => 'form-control form-control-user2', 'options' => $levels, 'empty' => 'Select level']); ?>

                                </div>
                                   <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('subject_id', ['label' => false, 'id'=>'states1','class' => 'form-control form-control-user2', 'options' => $subjects, 'empty' => 'Select Course','required']); ?>

                                </div>
                                
                            </div>
                      
                            
                        </fieldset>
                        <br /> <br />
<?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>
                    </div>
                    <?php } else{ ?>
                     <div class="p-5">
                    <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Students  <span class="float-right"><?= $subject->subjectcode.'-'.$subject->name?></span></h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> NAME</th>
                       <th>RegNo</th>
                       <th>Department</th>
                        <th>CA/Exam</th>
                        
                       <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th> NAME</th>
                       <th>RegNo</th>
                       <th>Department</th>
                       <th>CA/Exam</th>
                       <th>ACTIONS</th>
                    </tr>
                  </tfoot>
                  <tbody>
                      <?php $ca = 0; $exam = 0; foreach ($registered as $course): ?>
                                        <tr>

                                            <td><?= ucfirst($course->courseregistration->student->fname . ' ' . $course->courseregistration->student->lname) ?></td>
                                            <td><?= h($course->courseregistration->student->regno) ?></td>
                                            <td><?= $course->courseregistration->student->department->name ?></td>
                                            <td>
                                                <?php   
                                                $result = $this->checkandgetresult($course->courseregistration->student->department->id,$subject->id,$course->courseregistration->student->id);
                                              if(!empty($result->ca)|| !empty($result->score)){
                                        
                                            echo  $result->ca.'/'.$result->score;} ?> </td>


                                            <td class="actions">
                                                <?php if(!empty($course->ca || (!empty($course->exam)))){
                                                //entry exists so update
                                                    echo $this->Html->link(__(' Update CA/Exam Score'), ['controller'=>'Teachers','action' => 'updateca', 
                                                     $course->courseregistration->student->id,$course->subject->id, $this->GenerateUrl($course->courseregistration->student->lname),$course->id], 
                                                ['class'=>'btn btn-round btn-info fa fa-edit','title'=>'view add result/ca']);}
                                                else{ //no entry so add
                                                    echo $this->Html->link(__(' Add CA/Exam Score'), ['controller'=>'Teachers','action' => 'addca', 
                                                     $course->courseregistration->student->id,$course->subject->id, $this->GenerateUrl($course->courseregistration->student->lname),$course->id], 
                                                ['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'view add result/ca']);
                                                }?> 
                                               						 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
                </table>
              </div>
            </div>
          </div>     
                         
                     </div>
                    
                    
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    
        function getstates(stateid){ 

    $.ajax({
        url: '../Teachers/getstates/'+stateid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('states1').innerHTML = "";
            document.getElementById('states1').innerHTML = response;
            //location.href = redirect;
        }
    });

}
    </script>