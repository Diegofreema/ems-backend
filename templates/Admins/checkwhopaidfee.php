<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
    <!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Students Fee Payments</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?>
                    </li>
                    <li class="breadcrumb-item active">Payments</li>
                </ul>
            </div>

        </div>
    </div>
    <?= $this->Form->create(null,['url'=>['controller'=>'Admins','action'=>'checkwhopaidfee']]) ?>
   
        <div class="row filter-row">
             
       
     
       
            
          <div class="col-sm-6 col-md-3 col-lg-4 col-xl-3 col-12">  
            <div class="form-group form-focus select-focus">
               <?= $this->Form->control('session_id', ['options' => $sessions, 'label' => false, 'empty' => 'Select Session', 'class' => 'form-control form-control-user']) ?>
 
          <label class="focus-label">Select Session</label>
            </div>
        </div>
            
             <div class="col-sm-6 col-md-3 col-lg-4 col-xl-3 col-12">  
            <div class="form-group form-focus select-focus">
               <?php $gateways = ['InterSwitch'=>'InterSwitch','PayStack'=>'PayStack','eTransact'=>'eTransact','FlutterWave'=>'FlutterWave'];
               echo $this->Form->control('gateway', ['options' => $gateways, 'label' => false, 'empty' => 'Payment Gateway', 'class' => 'form-control form-control-user']) ?>
 
           <label class="focus-label">Payment Gateway</label>
            </div>
        </div>
            
   
                   <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-12"> 
            <?= $this->Form->button(__('Search'), ['class' => 'btn btn-success btn-block']) ?>

        </div> 
                    </div>
     <?= $this->Form->end() ?>
    
           <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                 <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>
                
                <th>Name</th>
                <th>Reg. Number</th>
                <th>Date</th>
                <th>Session</th>
                <th>Fee</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Ref</th>
                  
               <th>Payment Gateway</th>
             <th>Action</th>
              
            </tr>
            </thead>
            
                 
        <tbody>
            <?php $paidsum = 0; foreach ($transactions as $transaction): ?>
            <tr>
              
                <td><?= $transaction->has('student') ? $this->Html->link($transaction->student->fname.' '.$transaction->student->lname, ['controller' => 'Students', 'action' => 'viewstudent', $transaction->student->id,$this->generateurl($transaction->student->fname)]) : '' ?></td>
               <td><?= h($transaction->student->regno) ?></td>
                <td><?= h(date('D d M Y H:i a',strtotime($transaction->transdate))) ?></td>
                <td><?= h($transaction->session->name) ?></td>
                 <td><?= h($transaction->fee->name) ?></td>
                <td>₦<?= number_format($transaction->amount-500) ?></td>
                <td>
                    <?php if($transaction->paystatus=="completed"){
                        $paidsum +=($transaction->amount-500);
               echo (' <span class="badge badge-success">'.$transaction->paystatus.'</span>');}
                   
                   else{ 
                        echo (' <span class="badge badge-info">'.$transaction->paystatus.'</span>');
                   }?>
               </td>
                <td><?= h($transaction->payref) ?></td>
            
                  <td><?= h($transaction->pgateway) ?></td>
               <td> <?= $this->Html->link(__('View Receipt'), ['controller'=>'Invoices','action' => 'viewpayment',$transaction->id, $transaction->student->id, $this->Generateurl($transaction->student->fname)], ['class' => 'btn btn-round btn-success fa fa-eye', 'title' => 'view and print details'])
                           ?>
                        </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
       Total Completed : <span class="text-info" style="text-decoration: underline #00c292 solid;"> <?= number_format($paidsum)?></span>   
           
              </div>

        </div></div></div>

    
