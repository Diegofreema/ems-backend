 <div class="container-fluid">
<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Approved Result status</h1>
                        </div>
            <?= $this->Form->create($approvedresult) ?>
            <fieldset>
                  <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                <?= $this->Form->control('session_id', ['options' => $sessions, 'required', 'label' => 'Select Session',
                                        'placeholder' => 'Session', 'class' => 'form-control'])
                                    ?>     
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('semester_id', ['options' => $semesters, 'required', 'label' =>'Select Semester',
                                        'placeholder' => 'Select semester', 'class' => 'form-control'])
                                    ?>
                                </div>
<?php $approve_status = ['Approved'=>'Approved','Pending'=>'Pending'] ?>
                                <div class="col-sm-4">
                                    <?= $this->Form->control('status', ['options' => $approve_status ,'label' => 'Select Status', 'required', 'placeholder' => 'Select Status'
                                        , 'class' => 'form-control'])
                                    ?>
                                </div>
                            </div>
               
            </fieldset>
    <br /> <br />
<?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div></div>
