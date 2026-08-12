<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="content container-fluid">
          <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Student Invoices</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">Student Invoices</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Invoice - <?=$student->regno?></h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th >Fee Name</th>
                 <th>Invoice ID</th>
                <th>Amount</th>
            
                 <th>Session</th>
                <th>Status</th>
                 <th>Date Paid</th>
                <th >Action</th>
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                  <th >Fee Name</th>
                     <th>Invoice ID</th>
                <th>Amount</th>
               
                 <th>Session</th>
                <th>Status</th>
                 <th>Date Paid</th>
                <th >Action</th>
            </tr>
              </tfoot>
            
        
         <tbody>
            <?php foreach ($invoices as $invoice): ?>
            <tr>
                
                <td><?= h($invoice->fee->name) ?></td>
                   <td><?= h($invoice->invoiceid) ?></td>
                <td>₦<?= number_format($invoice->fee->amount) ?></td>
              
               <td><?= h($invoice->session->name) ?></td>
               <td ><?php if($invoice->paystatus=="Unpaid"){
               echo (' <span class="badge badge-warning">'.$invoice->paystatus.'</span>');}
                   
                   else{
                        echo (' <span class="badge badge-success">Paid</span>');
                   }?>
               </td>
               <td><?php if(!empty($invoice->payday)){echo $invoice->payday;}  ?></td>
               
        
                <td class="actions">
                    
                    <?php if($invoice->paystatus=="success"){
                        //echo $this->Html->link(__(' Paid'), ['controller'=>'Students','action' => 'generatepayeeid', $invoice->id,$invoice->student_id],
                          //  ['class'=>'btn btn-round btn-primary fa fa-money disabled','title'=>'payment made']);
                    
                    echo $this->Html->link(__('Get Receipt'), ['controller'=>'Invoices','action' => 'viewinvoice', $invoice->id,$invoice->student_id],
                            ['class'=>'btn btn-round btn-success fa fa-money','title'=>'get receipt']);
                  
                    }
                    else{
                    echo $this->Html->link(__(' Get Invoice'), ['controller'=>'Students','action' => 'admingeneratepayeeid', $invoice->id,$invoice->student_id],
                            ['class'=>'btn btn-round btn-primary fa fa-money','title'=>'get payee ID']);    
                    
                  
                    // echo $this->Form->postLink(__('Update Invoice'), ['controller'=>'Invoices','action' => 'updateoldpayment', $invoice->id,$invoice->student_id],
                       //      ['confirm' => __('Are you sure you want to update this fee as paid # {0}?', $invoice->fee->name),'class'=>'btn btn-warning']);
                    }
                    if($invoice->paystatus!="success"){
                         echo $this->Form->postLink(__('Delete Invoice'), ['controller'=>'Invoices','action' => 'delete', $invoice->id],
                             ['confirm' => __('Are you sure you want to this invoice # {0}?', $invoice->fee->name),'class'=>'btn btn-danger']);
                  
                    }
                    ?>
                    </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>





