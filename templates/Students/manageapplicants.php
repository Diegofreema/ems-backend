<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Manage Applicants</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">Manage Applicants</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
<?= $this->Html->link(__(' '), ['action' => 'newstudent'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'addmit new student']) ?>
          <!-- Page Heading -->
           <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search Applicants </h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('startdate',['label' => 'Start Date', 'placeholder' => 'Start Date',
     'class'=>'form-control floating datetimepicker','type'=>'text', 'id' => 'datepicker']);
            
            ?>
        </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('enddate',['label' => 'End Date', 'placeholder' => 'End Date',
     'class'=>'form-control floating datetimepicker','type'=>'text', 'id' => 'datepicker2']);
            
            ?>
        </div>
             <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                </div>
       
            </div>
         <div class="form-group row">
           <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('session_id', ['options' => $sessions, 'label' => 'Select Session', 'empty' => 'Select Session', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                </div>  
         </div>
       
        
    </fieldset>
   <br /> <br />
                    <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Applicants Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
          
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
               
               
              
                <th scope="col"><?= $this->Paginator->sort('Date Applied') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Class') ?></th>
                 <th>Status</th>
                  <th>Email</th>
                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
                 <th>Payment</th>
                  <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                 <th>State</th>
                 <th>LGA</th>
                  <th>Autonomous Community</th>
               <th>Phone</th>
               <th>Gender</th>
               <th>Email</th>
              <th>App_No</th>
              <!-- <th>Mode</th> -->
<!--                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>-->
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                
               
              
                <th scope="col"><?= $this->Paginator->sort('Date Applied') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Class') ?></th>
                <th>Status</th>
                <th>Email</th>
                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
                   <th>Payment</th>
                  <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                <th>State</th>
                <th>LGA</th>
                  <th>Autonomous Community</th>
               <th>Phone</th>
               <th>Gender</th>
               <th>Email</th>
               <th>App_No</th>
              <!-- <th>Mode</th> -->
<!--                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>-->
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
              </tfoot>
            
     
         <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                
                <td><?= h($student->fname.' '.$student->lname) ?></td>
             
             
               
                <td><?= h(date('D, d M Y', strtotime($student->joindate))) ?></td>
                <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                <td><?= h($student->status) ?></td>
                <td><?= h($student->email) ?></td>
                <td> <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
                                    'style' => 'width:80px;height:80px;']) ?>
               </td>
                
              <td><?php foreach($student->transactions as $payment){
                  if(($payment->fee_id==2 || $payment->fee_id==22 || $payment->fee_id == 24 ||
                          $payment->fee_id==28 ||  $payment->fee_id==10) && ($payment->paystatus=="completed")){
              echo '<span class="badge badge-success">'.$payment->paystatus.'</span>';}
//               else{ 
//                        echo ' <span class="badge badge-danger">'.$payment->paystatus.'</span>';
//                   }
                  } ?></td>
              <td><?= h($student->dob) ?></td>
              <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
              <td><?php if(!empty($student->lga->name)){ echo $student->lga->name;} ?></td>
              <td><?= h($student->community) ?></td>
              <td><?= h($student->phone) ?></td>
              <td><?= h($student->gender) ?></td>
              <td><?= h($student->user->username) ?></td>
              <td><?= h($student->application_no) ?></td>
                <td >
                 
                    
                      <?php foreach($student->transactions as $payment){
                  if(($payment->fee_id==2 || $payment->fee_id==22 || $payment->fee_id == 24 ||
                          $payment->fee_id==28 ||  $payment->fee_id==10) && ($payment->paystatus=="completed")){ 
                        echo $this->Html->link(__('Admit'), ['action' => 'viewapplicant', $student->id,$this->Generateurl($student->fname)],
                  ['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'view applicant details']);}} ?>
                   &nbsp;<?= $this->Html->link(__('Photo Card'), ['controller'=>'Students','action' => 'applicationsummary', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-warning', 'title' => 'get photo card'])?>
                &nbsp;<?= $this->Html->link(__('Get Payeeid'), ['controller'=>'Students','action' => 'getapplicantpayeeid', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-info', 'title' => 'get payee id'])?>
              &nbsp;<?= $this->Html->link(__('Update Data'), ['controller'=>'Students','action' => 'updateapplicantdata', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary', 'title' => 'update applcant data'])?>     
              
               <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Students','action' => 'deleteapplicant', $student->id],
                          ['confirm' => __('Are you sure you want to delete this applicant # {0}?',$student->fname),
                              'class'=>'btn btn-danger fa fa-times']) ?> &nbsp;
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
