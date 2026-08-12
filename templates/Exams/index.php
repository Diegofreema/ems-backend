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
                        <li class="breadcrumb-item active">Exams Manager</li>
                    </ul>
                </div>
                <div class="col-auto float-right ml-auto">
                    <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leave"><i class="fa fa-plus"></i> Add Exam</a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

      

        <!-- Search Filter -->
        <div class="row filter-row">
           
            <div class="col-sm-6 col-md-4 col-lg-4 col-xl-4 col-12">  
                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Select Session<span style="color: red">*</span></label>
                                    <div class="col-lg-9">
                                        <?= $this->Form->control('session_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $sessions]) ?>  
                                    </div>
                                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-4 col-xl-4 col-12"> 
                <div class="form-group row">
                                    <label class="col-lg-5 col-form-label">Select Semester<span style="color: red">*</span></label>
                                    <div class="col-lg-9">
                                        <?= $this->Form->control('semester_id', ['label' => false, 'class' => 'form-control', 'required', 'options' => $semesters]) ?>  
                                    </div>
                                </div>
            </div>
           
            
            <div class="col-sm-6 col-md-4 col-lg-4 col-xl-2 col-12">  <br />
                <a href="#" class="btn btn-success btn-block"> Search </a>  
            </div>     
        </div>
        <!-- /Search Filter -->

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
                                <th>Created By</th>
                               
                                <th class="text-center">Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
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
                                <td><?=$exam->admin->surname?></td>
                              
                                <td class="text-center">
                                    <div class="dropdown action-label">
                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-dot-circle-o text-purple"></i> New
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-purple"></i> New</a>
                                            <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i> Pending</a>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#approve_leave"><i class="fa fa-dot-circle-o text-success"></i> Approved</a>
                                            <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i> Declined</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                           <?= $this->Html->link(__(' Manage Questions'), ['controller'=>'Examquestions','action' => 'index'], ['class' => 'fa fa-pencil m-r-5 dropdown-item','title'=>'manage exam questions']) ?>
                                            <?= $this->Html->link(__(' Update'), ['action' => 'updateexam',$exam->id,$exam->examname], ['class' => 'fa fa-pencil m-r-5 dropdown-item','title'=>'update exam']) ?>  
                                           <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Exams','action' => 'delete',$exam->id], 
    ['confirm' => __('Are you sure you want to delete # {0}?', $exam->examname),'class' => 'dropdown-item fa fa-trash-o m-r-5','title'=>'delete exam']) ?>
                                           
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