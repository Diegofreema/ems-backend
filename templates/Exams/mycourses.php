   <!-- Page Content -->
    <div class="content container-fluid">
 <?php $settings = $this->request->getSession()->read('settings')?>
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Exams</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                         <?=$this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard'], ['title' => 'my dashboard'])?>
                            </li>
                        <li class="breadcrumb-item active">Students Exams Manager/Registered Courses</li>
                    </ul>
                </div>
                
            </div>
        </div>
        <!-- /Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>Course</th>
                               <th>Course Code</th>
                                <th>Faculty</th>
                                <th>Department</th>
                                <th>Level</th>
                                <th>Semester</th>
                                <th>Session</th>
                               <th>Exam Date</th>
                               
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($examcourses as $examquestion): ?>
                            <tr>
                                <td>
                                    <?= $examquestion->has('subject') ? $examquestion->subject->name : '' ?>
                                </td>
                                <td><?=$examquestion->subject->subjectcode?></td>
                                <td><?=$examquestion->faculty->name?></td>
                                <td><?= $examquestion->has('department') ? $examquestion->department->name  : '' ?></td>
                    <td><?= $examquestion->has('level') ? $examquestion->level->name : '' ?></td>
                   <td><?=$examquestion->exam->semester->name?></td>
                  <td><?=$examquestion->exam->session->name?></td>
                  <td><?=date('D d M, Y',  strtotime($examquestion->exam->examdate))?>
                
                  </td>              
                               
                              
                               
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php if(date('dmy',strtotime($examquestion->exam->examdate))==date('dmy')) {
                                           echo $this->Html->link(__(' View Questions'), ['controller'=>'Examquestions','action' => 'myquestions',$examquestion->exam_id,$examquestion->level_id,$examquestion->subject_id,$examquestion->id,$this->generateurl($examquestion->subject->subjectcode)], ['class' => 'fa fa-pencil m-r-5 dropdown-item','title'=>'start exam']);
                                            }
                                            else{
                                            echo 'Not schedulled for today';    
                                            }
                                            ?>
                                           
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                           
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
       <!-- Add Leave Modal -->
				<div id="add_leave" class="modal custom-modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Upload Exam Questions</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								   <?= $this->Form->create(null,['url'=>['controller'=>'Examquestions','action'=>'uploadquestions'],'type'=>'file']) ?>
									<div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Faculty<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('faculty_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $faculties]) ?>  
                                    </div>
                                </div>
									</div>
                                                           <div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Department<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('department_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $departments]) ?>  
                                    </div>
                                </div>
									</div>
                                                             <div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Level<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('level_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $levels]) ?>  
                                    </div>
                                </div>
									</div>
                                                             <div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Course<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('subject_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $subjects]) ?>  
                                    </div>
                                </div>
									</div>
                                                             <div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Exam<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('exam_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $exams]) ?>  
                                    </div>
                                </div>
									</div>
									<div class="form-group">
										<label>Select File <span class="text-danger">*</span></label>
										<div class="file-list">
                                                                                   <?= $this->Form->control('questions', ['label' => false, 'class' => 'form-control', 'required', 'type' =>'file']) ?>  
                                                                                    
										</div>
									</div>
									
									
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn">Submit</button>
									</div>
								<?= $this->Form->end() ?>
							</div>
						</div>
					</div>
				</div>
				<!-- /Add Leave Modal -->

