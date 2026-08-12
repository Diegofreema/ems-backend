<?php
$user = $this->request->getSession()->read('usersinfo');
$settings = $this->request->getSession()->read('settings');
?>

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Profile Manager</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Student Profile Details</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card mb-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-view">
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="#">
                                   <?=  $this->Html->image('../student_files/'.$student->passporturl, ['alt' => $student->regno])?>
                                   
<!--                                    <img alt="" src="assets/img/profiles/avatar-02.jpg">-->
                                </a>
                            </div>
                        </div>
                        <div class="profile-basic">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="profile-info-left">
                                    <h3 class="user-name m-t-0 mb-0"><?=$student->fname.' '.$student->lname .' '.$student->mname  ?></h3>
                                        <h6 class="staff-id "> Registration No: <span class="text-muted"><?=$student->regno  ?></span></h6>
                                        <div class="staff-id">Class : <span class="text-muted"><?=$student->department->name  ?></span></div>
                                      <div class="staff-id">Date of Birth : <span class="text-muted"><?=$student->dob  ?></span></div>
                                      <div class="staff-id">Current Term : <span class="text-muted"><?=$settings->semester->name  ?></span></div>
                                      <div class="staff-id">Current Session : <span class="text-muted"><?=$settings->session->name  ?></span></div>
                                     
                                     
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Phone:</div>
                                            <div class="text"><?=$student->phone?></div>
                                        </li>
                                        <li>
                                            <div class="title">Email:</div>
                                            <div class="text"><?=$student->user->username?></div>
                                        </li>
                                        
                                        <li>
                                            <div class="title">Address:</div>
                                            <div class="text"><?=$student->address?></div>
                                        </li>
                                        <li>
                                            <div class="title">Gender:</div>
                                            <div class="text"><?=$student->gender?></div>
                                        </li>
                                         <li>
                                            <div class="title">State of origin:</div>
                                            <div class="text"><?=$student->state->name?></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item"><a href="#emp_profile" data-toggle="tab" class="nav-link active">Profile</a></li>
                    <li class="nav-item"><a href="#emp_projects" data-toggle="tab" class="nav-link">Projects</a></li>
                    <li class="nav-item"><a href="#bank_statutory" data-toggle="tab" class="nav-link">Bank & Statutory <small class="text-danger">(Admin Only)</small></a></li>
                </ul>
            </div>
        </div>
    </div>-->

    <div class="tab-content">

        <!-- Profile Info Tab -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Payment Information</h3>
                            <div class="table-responsive">
                                 <?php if (!empty($student->invoices)) : ?> 
                                <table class="table table-nowrap">
                                    <thead>
                                         <tr>
          
                 <th >Fee Name</th>
                <th>Amount</th>
              
                <th>Payment Date</th>
                 <th>Session</th>
                <th>Status</th>
              
               
            </tr>
                                    </thead>
                                    <tbody>
                                       <?php foreach ($student->invoices as $invoice): ?>
                                        
                                           <tr>
                
                <td><?= h($invoice->fee->name) ?></td>
                <td>₦<?= number_format($invoice->fee->amount) ?></td>
          
                <td><?= h($invoice->payday) ?></td>
               <td><?= h($invoice->session->name) ?></td>
               <td ><?php if($invoice->paystatus=="Unpaid"){
               echo (' <span class="badge badge-warning">'.$invoice->paystatus.'</span>');}
                   
                   else{
                        echo (' <span class="badge badge-success">'.$invoice->paystatus.'</span>');
                   }?>
               </td>
               
        
                
            </tr>
                                          <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="row">
               
                <div class="col-md-6 d-flex">
                    
                </div>
            </div>
            <div class="row">
              
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        <div class="card-body">
                            <h3 class="card-title">Parental Information</h3>
                            <div class="experience-box">
                                 
                                <ul class="experience-list">
                                  
                                     
                                    <li>
                                        <div class="experience-user">
                                            <div class="before-circle"></div>
                                        </div>
                                        <div class="experience-content">
                                            <div class="timeline-content">
                                                <a href="#/" class="name">Father : <?= h($student->fathersname) ?></a>
                                                <div>Phone : <?= h($student->fatherphone) ?></div>
                                            </div>
                                        </div>
                                    </li>
                                 <li>
                                        <div class="experience-user">
                                            <div class="before-circle"></div>
                                        </div>
                                        <div class="experience-content">
                                            <div class="timeline-content">
                                                <a href="#/" class="name">Mother : <?= h($student->mothersname) ?></a>
                                                <div>Phone : <?= h($student->motherphone) ?></div>
<!--                                                <span class="time">₦<?= number_format($subprocesses->amount,2) ?></span>-->
                                            </div>
                                        </div>
                                    </li>
                                   
                                </ul>
                               
                            </div>
                        </div>
                    </div>
                    
                </div>
                
             
            </div>
        </div>
        <!-- /Profile Info Tab -->

   

    </div>
</div>
<!-- /Page Content -->






