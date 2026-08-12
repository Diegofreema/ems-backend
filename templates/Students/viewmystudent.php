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
                <h3 class="page-title">My Kid Profile - <?= $student->fname.' '.$student->lname ?></h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Sparents', 'action' => 'dashboard', $this->GenerateUrl('Parent dashboard')], ['title' => 'Parent dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">My Kid Profile</li>
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
                                     
<!--                                      <div class="staff-msg"> <?= $this->Html->link(__('Get ID Card'), ['controller' => 'Students', 'action' => 'getidcard',$student->id,$this->generateurl($student->fname)],
               ['class'=>'btn btn-custom']) ?></div>-->
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


    <div class="tab-content">

        <!-- Profile Info Tab -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-6 d-flex">
                    
                </div>
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
                <th>Action</th>
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
               <td>
               <?php 
                    if($invoice->paystatus=="success"){ 
                        //echo $this->Html->link(__(' Paid'), ['controller'=>'Students','action' => 'generatepayeeid', $invoice->id,$invoice->student_id],
                          //  ['class'=>'btn btn-round btn-primary fa fa-money disabled','title'=>'pay online']);
                  
                    echo $this->Html->link(__(' Get Receipt'), ['controller'=>'Invoices','action' => 'mystudentreceipt', $invoice->id,$invoice->student_id],
                            ['class'=>'btn btn-round btn-success fa fa-money','title'=>'print receipt']);  
                        
              
                    }
                    else{
                   echo $this->Html->link(__(' Get Invoice'), ['controller'=>'Sparents','action' => 'getmystudentpayeeid', $invoice->id,$invoice->student_id],
                           ['class'=>'btn btn-round btn-primary fa fa-money','title'=>'pay online']);    
                   
                      echo $this->Html->link(__('Pay Online'), ['controller'=>'Sparents','action' => 'gotopaystacktest', $invoice->student_id,$invoice->fee_id,$invoice->id],
                          ['class'=>'btn btn-round btn-info fa fa-money','title'=>'pay online','style'=>'margin:2px;']); 
                    
                      // echo ' '. $this->Html->link(__(' Pay With Credo'), ['controller'=>'Students','action' => 'gotocredo', $invoice->student_id,$invoice->fee_id,$invoice->id],
                      //     ['class'=>'btn btn-round btn-info fa fa-money','title'=>'pay online','style'=>'margin: 5px;']); 
                      // echo $this->Html->link(__(' Pay With Remita'), ['controller'=>'Students','action' => 'remitasplit',$invoice->id, $invoice->student_id],
                      //     ['class'=>'btn btn-round btn-info fa fa-money','title'=>'pay online']); 
                    
                    }
                    ?>
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
                                                <a href="#/" class="name">Father : <?= isset($student->sparent) ? h($student->sparent->fathersname) : 'Not Available' ?></a>
                                                <div>Phone : <?= isset($student->sparent) ? h($student->sparent->fatherphone) : 'Not Available' ?></div>
                                            </div>
                                        </div>
                                    </li>
                                 <li>
                                        <div class="experience-user">
                                            <div class="before-circle"></div>
                                        </div>
                                        <div class="experience-content">
                                            <div class="timeline-content">
                                                <a href="#/" class="name">Mother : <?= isset($student->sparent) ? h($student->sparent->mothersname) : 'Not Available' ?></a>
                                                <div>Phone : <?= isset($student->sparent) ? h($student->sparent->motherphone) : 'Not Available' ?></div>
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






