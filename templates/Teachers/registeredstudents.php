<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                  
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
                                               if(!empty($result->ca) && !empty($result->score)){
                                                
                                              echo $result->ca.'/'.$result->score;}?>
                                            </td>


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
                    
                    
                   
                </div>
            </div>
        </div>
    </div>

</div>
