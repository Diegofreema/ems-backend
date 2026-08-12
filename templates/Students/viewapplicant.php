<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View/Update Applicant</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Applicant Manager', ['controller' => 'Students', 'action' => 'manageapplicants', $this->GenerateUrl('Applicant Manager')], ['title' => 'Applicant Manager'])
                        ?></li>
                    <li class="breadcrumb-item active">View/Update Applicant</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">View/Update Applicant</h1>
                        </div>
                        <?= $this->Form->create($student,['type'=>'file']) ?>
                        <fieldset>
                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fname', ['label' => 'First Name', 'placeholder' => 'First Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('lname', ['label' => 'Last Name', 'placeholder' => 'Last Name', 'required',
                                          'class' => 'form-control form-control-user2'])
                                    ?>

                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">    
<?= $this->Form->control('mname', ['label' => 'Middle Name', 'placeholder' => 'Middle Name',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">  
<?= $this->Form->control('dob', ['label' => 'Date Of Birth', 'placeholder' => 'Date Of Birth',
      'class' => 'form-control form-control-user2','type' => 'text', 'id' => 'datepicker'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0"> 

                                    <?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                </div>
<div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('email', ['label' => 'Email Address', 'placeholder' => 'Email Address',
                                          'class' => 'form-control form-control-user2', 'required', 'type' => 'email','disabled'])
                                    ?>
                                </div>
                               
                            </div>

                        
                            <div class="form-group row">
                               
                                
                                  <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('country_id', ['options' => $countries, 'label' => 'Select Country', 'empty' => 'Select Country', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false]) ?>

                                </div>

                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('state_id', [ 'label' => 'Select State', 'options' => $states,'empty' => 'Select State', 'class' => 'form-control select2_multiple', 'multiple' => false,'default'=>$student->state->id]) ?>
                                </div>
 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('lga_id', ['label' => 'LGA', 'options'=>$lgas,'placeholder' => 'local government of origin',
                                          'class' => 'form-control form-control-user2'])
                                    ?>

                                </div>
                            </div>

                            <div class="form-group row">        
                                <div class="col-sm-8 mb-3 mb-sm-0">
<?= $this->Form->control('address', ['label' => 'Address', 'placeholder' => 'Address',
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('nin', ['label' => 'NIN', 'placeholder' => 'nin',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                            </div>
<!--                        <div class="form-group row"> 
                            <div class="col-sm-8 mb-3 mb-sm-0">
                            <!--?= $this->Form->control('universitymail', ['label' => 'Assign University Email Address', 'placeholder' => 'School Email Address',
      'class' => 'form-control form-control-user2', 'required','type'=>'email'])
?>    
                            </div>  
                            
                          </div>    -->
                            

                            <div class="form-group row">  
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php echo $this->Form->control('phone', ['label' => 'Phone', 'placeholder' => 'Phone',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fathersname', ['label' => 'Father\'s Name', 'placeholder' => 'Father Name',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('mothersname', ['label' => 'Mother\'s Name', 'placeholder' => 'Mother Name',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">        
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('fatherphone', ['label' => 'Father\'s Phone', 'placeholder' => 'Father Phone',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('motherphone', ['label' => 'Mother\'s Phone', 'placeholder' => 'Mother Phone',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('fathersjob', ['label' => 'Father\'s Occupation', 'placeholder' => 'Father Occupation',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>

                            </div>

                            <div class="form-group row">        
                                <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('mothersjob', ['label' => 'Mother\'s Occupation', 'placeholder' => 'Mother Occupation',
      'class' => 'form-control form-control-user2'])
?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                   <?php $status = ['Admitted'=>'Selected','Applied'=>'Applied','Pending'=>'Pending','Rejected'=>'Rejected'];
                                           echo $this->Form->control('status',['options' => $status,'label'=>'Change Status','empty' => 'Select Status','class' => 'form-control form-control-user'])?>  
                                    
                                </div> 
                                 <div class="col-sm-4 mb-3 mb-sm-0">
<?=
  $this->Form->control('passporturls', ['label' => 'Upload Passport', 'placeholder' => 'Uplaod Passport',
      'class' => 'form-control form-control-user2', 'type' => 'file'])
?>
                                </div>
<!--                                <div class="col-sm-3 mb-3 mb-sm-0">
<!--?= $this->Form->control('fees._ids', ['options' => $fees, 'label' => 'Assign Fees(optional)', 'empty' => 'Select Fees', 'class' => 'select2_multiple form-control form-control-user', 'required']) ?>
                                </div>-->

                            </div>  
                             <div class="form-group row">        
                                
                             </div>

                            <div class="form-group row">        
<!--                                <div class="col-sm-6 mb-3 mb-sm-0">
<!--?= $this->Form->control('subjects._ids', ['options' => $subjects, 'label' => 'Assign Subjects(optional)', 'empty' => 'Select Subjects', 'class' => 'select2_multiple form-control form-control-user']) ?> 
                                </div>-->
                               
                                
                                
                            </div>


                            <!--   echo $this->Form->control('user_id', ['options' => $users]);
                               echo $this->Form->control('regno');
                             
                            -->
                            <div class="form-group row">    
                                <legend> <center>Download Files</center></legend>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?php if(!empty($student->olevelresulturl)){
                                       echo $this->Html->link(__(' O level'), ['controller' => 'Users', 'action' => 'downloadfiles',$student->olevelresulturl],['title'=>'Download o level result']); 
                                    }
                                    ?>

                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                     <?php if(!empty($student->jambresult)){
                                       echo $this->Html->link(__('JAMB Result'), ['controller' => 'Users', 'action' => 'downloadfiles',$student->jambresult],['title'=>'Download jamb result']); 
                                    }
                                    ?>

                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                     <?php if(!empty($student->birthcerturl)){
                                       echo $this->Html->link(__('Birth Cert'), ['controller' => 'Users', 'action' => 'downloadfiles',$student->birthcerturl],['title'=>'Download birth certificate']); 
                                    }
                                    ?>

                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                   <?php if(!empty($student->othercerts)){
                                       echo $this->Html->link(__('Other Certs'), ['controller' => 'Users', 'action' => 'downloadfiles',$student->othercerts],['title'=>'Download other certs']); 
                                    }
                                    ?>       
 
                                </div>
                                 </div>
                            <div class="form-group row"> 
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                   <?php if(!empty($student->jamb_notification)){
                                       echo $this->Html->link(__('JAMB Notification'), ['controller' => 'Users', 'action' => 'downloadfiles',$student->jamb_notification],['title'=>'Download JAMB Notification']); 
                                    }
                                    ?>       
 
                                </div> 
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                   <?php if(!empty($student->jamb_admin_letter)){
                                       echo $this->Html->link(__('JAMB Admission Letter'), ['controller' => 'Users', 'action' => 'downloadfiles',$student->jamb_admin_letter],['title'=>'Download JAMB Admission Letter']); 
                                    }
                                    ?>       
 
                                </div>
                            </div>
                        </fieldset>
                        <br /> <br />
<?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user float-right']) ?>
<?= $this->Form->end() ?>
                          <?= $this->Form->postLink(__('Delete'), ['action' => 'deleteapplicant', $student->id], 
    ['confirm' => __('Are you sure you want to delete # {0}?', $student->fname),'class' => 'btn btn-round btn-danger', 'title' => 'delete this applicant data'])?> 
             <br /> 
                    </div>
                     </div>
            </div>
        </div>
    </div>

</div>
