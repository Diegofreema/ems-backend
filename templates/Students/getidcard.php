<?php
$user = $this->request->getSession()->read('usersinfo');
?>
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Student Manager</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Admins', 'action' => 'dashboard'], ['title' => 'dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Student Profile Details</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
 <div class="card mb-1">
        <div class="card-body">
            <div class="col-md-7">
            <div class="row">
                        <div class="col-md-12">
                    <div class="profile-view" id="printableArea">  
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="#">
                                   <?=  $this->Html->image($student->passporturl, ['alt' => $student->regno,'style'=>'padding: 1px;'])?>
                                   
                                </a>
                                
                            </div>
                            
                        </div>
                        <div class="profile-basic">
                            
                            <div class="row" style="margin-left: 20px;">
                                <div class="col-md-12">
                                    
                                    <div class="profile-info-left">
                                       <h3 class="user-name m-t-0 mb-10" style="font-size: 22px;">Name :<?=  ucwords($student->fname.' '.$student->lname .' '.$student->mname)  ?></h3>
                                        <h6 class="text-muted">Registration Number : <?=$student->regno  ?></h6>
                                         <small class="text-muted">Faculty : <?=$student->faculty->name?></small>
                                     <br />   <small class="text-muted">Department : <?=$student->department->name?></small>
                                      <span style="margin-left: -10px; float: right;"> <?= $this->Qr->text($student->regno.','.$student->fname.' '.$student->lname .' '.$student->mname );?></span>
                                        <div class="staff-id text-muted">Programme : <?=$student->programe->name  ?></div>
                                        <div class="staff-id text-muted">Class : <?=$student->level->name  ?></div>
                                      <div class="small doj text-muted">Date of Birth : <?=$student->dob  ?></div>
                                      <div class="small doj text-muted">Phone : <?=$student->dob  ?></div>
                                      
                                      
                                     
                                    </div>
                                </div>
                 
                            </div>
                        </div>
                      
                    </div>
                             &nbsp;&nbsp;&nbsp;  <input class="btn btn-custom float-right" type="button" style="margin: 10px;" onclick="printDiv('printableArea')" value="Print a Card" />
                </div>
                    </div>
            </div>
                </div>
   

  

</div></div>

<script>
    
    function printDiv(divName) { //alert('am called');
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
 }

    </script>



