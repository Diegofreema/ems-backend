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
                         <?=$this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'admins dashboard'])?>
                            </li>
                        <li class="breadcrumb-item active">Students Exams Manager</li>
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
                                <th>Exam</th>
                                <th>Semester</th>
                                <th>Session</th>
                                <th>Date</th>

                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myexams as $exam): ?>
                            <tr>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="#" class="avatar">
                                             <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'height'=>'37','width'=>'37'])?>
<!--                                            <img alt="" src="assets/img/profiles/avatar-09.jpg">-->
                                        </a>
                                        <a href="#"><?=$exam->examname?> <span><?=$exam->examdate?></span></a>
                                    </h2>
                                </td>
                                <td><?=$exam->semester->name?></td>
                                <td><?=$exam->session->name?></td>
                                <td><?=date('D d M Y',  strtotime(str_ireplace('/', '-', $exam->examdate)))?></td>
                               
                              
                                
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                           <?= $this->Html->link(__(' View Courses'), ['controller'=>'Exams','action' => 'mycourses',$exam->id,$this->generateurl($exam->examname)], ['class' => 'fa fa-eye m-r-5 dropdown-item','title'=>'view exam courses/questions']) ?>
                                               
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
    
    <!-- /Page Content -->





    <!-- Add Leave Modal -->
				<div id="add_leave" class="modal custom-modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Create Exam</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								   <?= $this->Form->create(null,['url'=>['controller'=>'Exams','action'=>'createexam']]) ?>
									<div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Semester<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('semester_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $semesters]) ?>  
                                    </div>
                                </div>
									</div>
                                                           <div class="form-group">
										<div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Session<span style="color: red">*</span></label>
                                    <div class="col-lg-12">
                                        <?= $this->Form->control('session_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $sessions]) ?>  
                                    </div>
                                </div>
									</div>
									<div class="form-group">
										<label>Exam Date <span class="text-danger">*</span></label>
										<div class="cal-icon">
                                                                                    <input name="examdate" class="form-control datetimepicker" type="text">
										</div>
									</div>
									<div class="form-group">
										<label>Time <span class="text-danger">*</span></label>
										<div class="cal-icon">
                                                                                    <input name="examtime" class="form-control" type="text">
										</div>
									</div>
									
									
									<div class="form-group">
										<label>Exam Name <span class="text-danger">*</span></label>
                                                                                <textarea name="examname" rows="4" class="form-control"></textarea>
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
                                
                                <!-- Delete Leave Modal -->
				<div class="modal custom-modal fade" id="delete_approve" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete Exam?</h3>
									<p>Are you sure want to delete this Exam?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
										<div class="col-6">
                                                                                   <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Exams','action' => 'delete'], ['class' => 'btn btn-primary continue-btn','title'=>'delete exam']) ?>
											
										</div>
										<div class="col-6">
											<a href="javascript:void(0);" data-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Delete Leave Modal -->