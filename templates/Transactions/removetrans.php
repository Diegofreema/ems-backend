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
                <h3 class="page-title">Payments and Invoices</h3>
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
  
    
           <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                 <table id="myTabl" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>
                
                <th scope="col"><?= $this->Paginator->sort('Student') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Date') ?></th>
                <th>Session</th>
                <th>Fee</th>
                <th scope="col"><?= $this->Paginator->sort('Amount') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Ref') ?></th>
               
             <th>Action</th>
              
            </tr>
            </thead>
            
                 
        <tbody>
            <?php $paidsum = 0; foreach ($transactions as $transaction): ?>
            <tr>
              
                <td><?= $transaction->has('student') ? $this->Html->link($transaction->student->fname.' '.$transaction->student->lname, ['controller' => 'Students', 'action' => 'viewstudent', $transaction->student->id,$this->generateurl($transaction->student->fname)]) : '' ?></td>
               
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
            
               <td> <?= $this->Html->link(__('View Receipt'), ['controller'=>'Invoices','action' => 'viewpayment',$transaction->id, $transaction->student->id, $this->Generateurl($transaction->student->fname)], ['class' => 'btn btn-round btn-success fa fa-eye', 'title' => 'view and print details'])
                           ?>&nbsp;
                           <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Transactions','action' => 'delete', $transaction->id],
                          ['confirm' => __('Are you sure you want to delete this payment record with REF # {0}?', $transaction->payref),
                              'class'=>'btn btn-danger fa fa-times']) ?> &nbsp;
               <?php if((($transaction->fee_id==2) || ($transaction->fee_id==22)) && ($transaction->paystatus!="completed")){
                   echo $this->Html->link(__('Retry Payment'), ['controller'=>'Admins','action' => 'retrypayment',$teceiptransaction->amount, $transaction->payref, $this->Generateurl($transaction->student->fname)], ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'Requerry this payment']);}  ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
       Total Completed : <span class="text-info" style="text-decoration: underline #00c292 solid;"> <?= number_format($paidsum)?></span>   
           
              </div>

        </div></div></div>

    
