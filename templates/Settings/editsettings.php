<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Update System Settings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Update System Settings</li>
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
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update System Settings</h1>
                        </div>

                        <?= $this->Form->create($setting, ['type' => 'file', 'class' => 'user']) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('name', ['label' => 'SCHOOL NAME', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('description', ['label' => 'DESCRIPTION', 'class' => 'form-control form-control-user']) ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                
                                <div class="col-sm-6">
                                    <?= $this->Form->control('address', ['label' => 'ADDRESS', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">

                                    <?= $this->Form->control('semester_id', ['options'=>$semesters,'label' => 'Select Term', 'class' => 'form-control form-control-user2']) ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('email', ['label' => 'EMAIL', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('phone', ['label' => 'PHONE', 'class' => 'form-control form-control-user']) ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('invoiceprefix', ['label' => 'INVOICE PREFIX', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('adminprefix', ['label' => 'ADMIN PREFIX', 'class' => 'form-control form-control-user']) ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('logos', ['label' => 'LOGO', 'class' => 'form-control form-control-user', 'type' => 'file']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $this->Form->control('staffprefix', ['label' => 'STAFF PREFIX', 'class' => 'form-control form-control-user']) ?>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <?= $this->Form->control('school_stamp', ['label' => 'SCHOOL STAMP', 'class' => 'form-control form-control-user', 'type' => 'file']) ?>
                                    <small class="form-text text-muted">Upload a school stamp image for result sheets (PNG, JPG, GIF - Max 2MB)</small>
                                </div>
                                <div class="col-sm-6">
                                    <?php if (!empty($setting->school_stamp)): ?>
                                        <label class="form-label">Current Stamp:</label><br>
                                        <img src="<?= $this->Url->build('/img/' . $setting->school_stamp) ?>" alt="Current Stamp" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                        <br><small class="text-muted">Upload a new file to replace</small>
                                    <?php else: ?>
                                        <label class="form-label">No stamp uploaded</label><br>
                                        <small class="text-muted">Upload a stamp image to display on result sheets</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <?= $this->Form->control('regnoformat', ['label' => 'STUDENT REGNO FORMAT', 'class' => 'form-control form-control-user']) ?>
                                </div> 
                               
                                <div class="col-sm-4">
                                    <?= $this->Form->control('application_no_prefix', ['label' => 'APPLICATION NO. PREFIX', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                
                                <div class="col-sm-4">
                                    <?= $this->Form->control('session_id', ['options'=>$sessions,'label' => 'Select Session', 'class' => 'form-control form-control-user2']) ?>
                                </div>
                            </div> 
                            
                             <div class="form-group row">
                                <div class="col-sm-6">
                                    <?= $this->Form->control('rector', ['label' => 'Principal', 'class' => 'form-control form-control-user']) ?>
                                </div> 
                               
                                <div class="col-sm-6">
                                    <?= $this->Form->control('rectorcerts', ['label' => 'Principal\'s Qualifications', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                </div>
                             <div class="form-group row">
                                <div class="col-sm-6">
                                    <?= $this->Form->control('registrar', ['label' => 'Secretary', 'class' => 'form-control form-control-user']) ?>
                                </div>
                                 <div class="col-sm-6">
                                    <?= $this->Form->control('registrarcerts', ['label' => 'Secretary\'s Qualifications', 'class' => 'form-control form-control-user']) ?>
                                </div>
                            </div> 
                            
                            
                            <?php
                            // Get values directly from database for display
                            $connection = \Cake\Datasource\ConnectionManager::get('default');
                            $sql = "SELECT currenttermends, nexttermbegins FROM settings WHERE id = ?";
                            $result = $connection->execute($sql, [$setting->id])->fetch('assoc');
                            
                            // Calculate the converted values
                            $currenttermends_value = !empty($result['currenttermends']) ? date('Y-m-d', strtotime(str_replace('/', '-', $result['currenttermends']))) : '';
                            $nexttermbegins_value = !empty($result['nexttermbegins']) ? date('Y-m-d', strtotime(str_replace('/', '-', $result['nexttermbegins']))) : '';
                            ?>
                            
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <?= $this->Form->control('currenttermends', [
                                        'label' => 'Current Term Ends', 
                                        'class' => 'form-control form-control-user2', 
                                        'type' => 'date',
                                        'value' => $currenttermends_value
                                    ]) ?>
                                </div>   
                                
                                <div class="col-sm-6">
                                    <?= $this->Form->control('nexttermbegins', [
                                        'label' => 'Next Term Begins', 
                                        'class' => 'form-control form-control-user2', 
                                        'type' => 'date',
                                        'value' => $nexttermbegins_value
                                    ]) ?>
                                </div>   
                            </div>
                            <br /> <br />
                            <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                            <?= $this->Form->end() ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


