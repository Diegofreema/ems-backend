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
                    <li class="breadcrumb-item active">Update Exam</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-0">
                <div class="card-header">
                    <h4 class="card-title mb-0">Update Exam</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($exam) ?>
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
                         <?=$this->Form->control('examdate',['label'=>false,'class'=>'form-control datetimepicker','type'=>'text'])?>
                          
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Exam Time <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                         <?=$this->Form->control('examtime',['label'=>false,'class'=>'form-control','type'=>'text'])?>
                 
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Exam Name <span class="text-danger">*</span></label>
                    <?=$this->Form->control('examname',['label'=>false,'class'=>'form-control','rows'=>4])?>
                 
                      
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
