<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$status = ['success'=>'Paid', 'Unpaid'=>'Unpaid'];
?>

<!-- Begin Page Content -->
        <div class="content container-fluid">
          <!-- Page Header -->
          <div class="page-header">
            <div class="row">
              <div class="col-sm-12">
                <h3 class="page-title">Manage Invoices</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                  <li class="breadcrumb-item active">Manage Invoices</li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /Page Header -->

               <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search Invoices </h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-3 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('startdate',['label' => 'Start Date', 'placeholder' => 'Start Date',
     'class'=>'form-control floating datetimepicker','type'=>'text']);
            
            ?>
        </div>
             <div class="col-sm-3 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('enddate',['label' => 'End Date', 'placeholder' => 'End Date',
      'class'=>'form-control floating datetimepicker','type'=>'text']);
            
            ?>
        </div>
             <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('fee_id', ['options' => $fees, 'label' => 'Select Fee Head', 'empty' => 'Select Fee Head', 'class' => 'form-control form-control-user']) ?>
                                </div>
             <div class="col-sm-3 mb-3 mb-sm-0">
<?= $this->Form->control('status', ['options' => $status, 'label' => 'Select status', 'empty' => 'Select Status', 'class' => 'form-control form-control-user']) ?>
                                </div>
       
            </div>
    </fieldset>
   <br /> <br />
                    <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Invoices</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Invoice</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
              <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
           <th> Student </th>
                 <th>Fee Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Pay day</th>
                 <th>Session</th>
               
                 <th>Class</th>
                   <th>Action</th>
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          <th> Student</th>
                  <th >Fee Name </th>
                <th>Amount</th>
                <th>Status</th>
                <th>Pay day</th>
                 <th>Session</th>
              <th>Class</th>
              <th>Action</th>
            </tr>
              </tfoot>
            
        
         <tbody>
            <?php $paidsum = 0; foreach ($invoices as $invoice): ?>
            <tr>
                 <td><?= $this->Html->link($invoice->student->fname.' '.$invoice->student->lname, ['controller' => 'Students', 'action' => 'viewstudent', $invoice->student->id,$this->generateurl($invoice->student->fname)]) ?></td>
                <td><?= h($invoice->fee->name) ?></td>
                <td>₦<?= number_format($invoice->amount) ?></td>
               <td ><?php if($invoice->paystatus=="Unpaid"){
               echo (' <span class="badge badge-warning">'.$invoice->paystatus.'</span>');}
                   
                   else{ $paidsum +=$invoice->fee->amount;
                        echo (' <span class="badge badge-success">Paid</span>');
                   }?>
               </td>
                <td><?= h($invoice->payday) ?></td>
               <td><?= h($invoice->session->name) ?></td>
              
             <td><?= h($invoice->student->department->name . (!empty($invoice->student->class_arm) ? ' - ' . $invoice->student->class_arm->arm_name : '')) ?></td>
                <td class="actions">
                     <?= $this->Html->link(' View', ['action' => 'viewinvoice', $invoice->id,$invoice->student->fname],
                             ['class'=>'btn btn-info fa fa-eye']) ?>
                    <?= $this->Html->link(' Update', ['action' => 'editinvoice', $invoice->id,$invoice->student->fname],
                            ['class'=>'btn btn-primary fa fa-edit']) ?>
                
                  <!-- <?= $this->Form->postLink(__(' Delete'), ['controller'=>'Invoices','action' => 'delete', $invoice->id],
                          ['confirm' => __('Are you sure you want to delete this invoice belonging to # {0}?', $invoice->student->fname),
                              'class'=>'btn btn-danger fa fa-times']) ?> -->
                    
                    <?php if((($invoice->fee_id==2) || ($invoice->fee_id==22)) && ($invoice->paystatus!="success")){
                     $payref =  $this->getrefno($invoice->student->id,$invoice->id);
                   echo $this->Html->link(__(' Retry Payment'), ['controller'=>'Admins','action' => 'retrypayment',$invoice->amount, $payref, $this->Generateurl($invoice->student->fname)], ['class' => 'btn btn-round btn-primary fa fa-check', 'title' => 'Requerry this payment']);}  ?>
                    
                </td>
                
            </tr>
            <?php endforeach; ?>
            
        </tbody>
        
                </table>
                  Total Paid : <span class="text-info" style="text-decoration: underline #00c292 solid;"> <?= number_format($paidsum)?></span>   
              </div>
            </div>
          </div>

        </div>
