<!-- Page Wrapper -->

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">e-Examination</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <?= $this->Html->link(' Dashboard', ['controller' => 'Admins', 'action' => 'dashboard'], ['title' => 'my dashboard']) ?>
                    </li>
                    <li class="breadcrumb-item active">New Exam</li>
                </ul>
            </div>
        </div>
    </div>


  <div class="row">
        <div class="col-md-12">
            <div class="card mb-0">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create New Exam</h4>
                </div>
                <div class="card-body">
            <?= $this->Form->create($exam) ?>
          
                 <div class="row">

               <div class="col-xl-6">
                <div class="form-group row">
			<label class="col-lg-3 col-form-label">Select Course</label>
			<div class="col-lg-9">
                            <?= $this->Form->control('subject_id', ['options' => $subjects,'label'=>false,'class'=>'form-control select2_multiple','multiple'=>false])  ?>
                            
                </div>
	</div>
                <div class="form-group row">
			<label class="col-lg-3 col-form-label">Select Faculty</label>
			<div class="col-lg-9">
                            <?= $this->Form->control('faculty_id', ['options' => $faculties,'label'=>false,'class'=>'form-control'])  ?>
                            
                </div>
	</div>
                <div class="form-group row">
			<label class="col-lg-3 col-form-label">Select Department</label>
			<div class="col-lg-9">
                            <?= $this->Form->control('department_id', ['options' => $departments,'label'=>false,'class'=>'form-control'])  ?>
                            
                </div>
	</div>
                
              <div class="form-group row">
			<label class="col-lg-3 col-form-label">Select Semester</label>
			<div class="col-lg-9">
                            <?= $this->Form->control('semester_id', ['options' => $semesters,'label'=>false,'class'=>'form-control'])  ?>
                            
                </div>
	</div>  
                
                <div class="form-group row">
			<label class="col-lg-3 col-form-label">Select Session</label>
			<div class="col-lg-9">
                            <?= $this->Form->control('session_id', ['options' => $sessions,'label'=>false,'class'=>'form-control'])  ?>
                            
                </div>
	</div> 
               </div>
                     
                
            <div class="col-xl-6">
                 <div class="form-group row">
                                    
                   <label class="col-lg-3 col-form-label">Exam Name</label>
		<div class="col-lg-9"> 
                    <?= $this->Form->control('examname',['label'=>false,'class'=>'form-control']); ?>
                    
                </div>
                    </div>
                    
                     <div class="form-group row">
                                    
                   <label class="col-lg-3 col-form-label">Exam Date</label>
		<div class="col-lg-9"> 
                          <?= $this->Form->control('examdate', ['label' => false, 'empty' => 'Exam Date', 'class' => 'form-control']) ?>
																	
		</div>
                </div>
                       <div class="form-group row">
                                    
                   <label class="col-lg-3 col-form-label">Exam Time</label>
		<div class="col-lg-9"> 
                   <?= $this->Form->control('examtime', ['label' => false, 'empty' => 'Exam Time', 'class' => 'form-control']) ?>	
																	
		</div>
																
		</div>
                        <div class="form-group row">
                                    
                   <label class="col-lg-3 col-form-label">Exam Venue</label>
		<div class="col-lg-9"> 
                      <?=$this->Form->control('examvenue',['label'=>false,'class'=>'form-control','type' => 'text'])?>  
																	
		</div>
		
		</div>
                    
                </div>
             </div>
                     </div>
                       </div>
                
           
            <div class="text-right">
		<button type="submit" class="btn btn-primary">Submit</button>
		</div>
		<?= $this->Form->end() ?>
        </div>
    </div>
</div>
