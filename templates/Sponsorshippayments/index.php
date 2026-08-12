<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
 
        $admin = $this->request->getSession()->read('admin');
        // debug(json_encode($admin, JSON_PRETTY_PRINT)); exit;
        $privileg_ids = [];
        foreach ($admin->privileges as $privilege) {
            array_push($privileg_ids, $privilege->id);
        }    
?>

<!-- Begin Page Content -->
    <!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Sponsorship Payments and Invoices</h3>
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
    <?= $this->Form->create(null,['url'=>['controller'=>'Sponsorshippayments','action'=>'searchtransactions']]) ?>
   
        <div class="row filter-row">
             <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-12">  
            <div class="form-group form-focus">
                <?= $this->Form->control('sref',['label'=>false,'class'=>'form-control floating']);  ?>
                
                <label class="focus-label">Transaction Ref </label>
            </div>
        </div>
       
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-12">  
            <div class="form-group form-focus">
                <div class="cal-icon">
                   <?= $this->Form->control('startdate',['label'=>false,'class'=>'form-control floating datetimepicker','type'=>'text']);  ?>
                   
                </div>
                <label class="focus-label">From</label>
            </div>
        </div>
             <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-12">  
            <div class="form-group form-focus">
                <div class="cal-icon">
                   <?= $this->Form->control('enddate',['label'=>false,'class'=>'form-control floating datetimepicker','type'=>'text']);  ?>
                </div>
                <label class="focus-label">To</label>
            </div>
        </div>

            
          <div class="col-sm-6 col-md-3 col-lg-4 col-xl-3 col-12">  
            <div class="form-group form-focus select-focus">
               <?= $this->Form->control('session_id', ['options' => $sessions, 'label' => false, 'empty' => 'Select Session', 'class' => 'form-control form-control-user']) ?>
 
          <label class="focus-label">Select Session</label>
            </div>
        </div>

                   <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3 col-12"> 
            <?= $this->Form->button(__('Search'), ['class' => 'btn btn-success btn-block']) ?>

        </div> 
                    </div>
    <br /><br /><br />
     <?= $this->Form->end() ?>
    
           <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                 <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>
                 <th>Sponsor</th>
                <th scope="col"><?= $this->Paginator->sort('Date') ?></th>
                <th>Session</th>
            
                <th scope="col"><?= $this->Paginator->sort('Amount') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Ref') ?></th>
                  
               
             <th>Action</th>
              
            </tr>
            </thead>
            
                 
        <tbody>
              <?php $paidsum = 0; foreach ($sponsorshippayments as $sponsorshippayment): ?>
            <tr>
               <td><?= h($sponsorshippayment->sponsorship->sponsor->name) ?></td>
               <td><?= h(date('D d M Y H:i a',strtotime($sponsorshippayment->datecreated))) ?></td>
                
                <td><?= h($sponsorshippayment->sponsorship->session->name) ?></td>
               
                <td>₦<?= $this->Number->format($sponsorshippayment->amount) ?></td>
                <td>
                    <?php if($sponsorshippayment->paystatus=="completed"){
                        $paidsum +=$sponsorshippayment->amount;
               echo (' <span class="badge badge-success">'.$sponsorshippayment->paystatus.'</span>');}
                   
                   else{ 
                        echo (' <span class="badge badge-info">'.$sponsorshippayment->paystatus.'</span>');
                   }?>
               </td>
                <td><?= h($sponsorshippayment->sref) ?></td>
            
                
               <td> <?= $this->Html->link(__('View Receipt'), ['controller'=>'Sponsorshippayments','action' => 'viewpayment',$sponsorshippayment->id, $this->Generateurl('sponsorship payment')], ['class' => 'btn btn-round btn-success fa fa-eye', 'title' => 'view and print details'])
                           ?>&nbsp;
                           <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Sponsorshippayments','action' => 'delete', $sponsorshippayment->id],
                          ['confirm' => __('Are you sure you want to delete this payment record with REF # {0}?', $sponsorshippayment->sref),
                              'class'=>'btn btn-danger fa fa-times']) ?> &nbsp;
                              
                               <?php if (in_array(11, $privileg_ids)) {
                               echo $this->Html->link(__(' Update'), ['action' => 'updatepayment', $sponsorshippayment->id], ['class' => 'btn btn-round btn-primary fa fa-edit', 
                               'title' => 'Requerry this payment']);} ?>

               </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
       Total Completed : <span class="text-info" style="text-decoration: underline #00c292 solid;"> <?= number_format($paidsum)?></span>   
           
              </div>

        </div></div></div>

    

    