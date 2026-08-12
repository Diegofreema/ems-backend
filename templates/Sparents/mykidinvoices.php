<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="content container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">My Kid Invoices</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Sparents', 'action' => 'dashboard', $this->GenerateUrl('Parent dashboard')], ['title' => 'Parent dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">My Kid Invoices</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Invoice</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          <th >Student</th>
                 <th >Fee Name</th>
                <th>Amount</th>
                 <th>Session</th>
                <th>Status</th>
                <th>Paid On</th>
                <th >Action</th>
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
             <th >Student</th>
                  <th >Fee Name</th>
                <th>Amount</th>
                 <th>Session</th>
                <th>Status</th>
                <th>Paid On</th>
                <th >Action</th>
            </tr>
              </tfoot>
            
        
         <tbody>
            <?php if (empty($invoices) || $invoices->count() == 0): ?>
            <tr>
                <td colspan="7" class="text-center">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>No Students Found</strong><br>
                        You don't have any students registered under your account yet. 
                        Please contact the school administration to link your student(s) to your parent account.
                    </div>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?= $this->Html->link($invoice->student->fname.' '.$invoice->student->lname, ['controller'=>'Students','action' => 'viewmystudent', 
                        $invoice->student->id,$this->generateurl($invoice->student->fname)],
                                ['title'=>'view profile'])?>
                    </td>
                    <td><?= h($invoice->fee->name) ?></td>
                    <td><?= number_format($invoice->amount) ?></td>
                   <td><?= h($invoice->session->name) ?></td>
                   <td><?php if($invoice->paystatus=="Unpaid"){
                   echo (' <span class="badge badge-warning">'.$invoice->paystatus.'</span>');}
                       
                       else{
                            echo (' <span class="badge badge-success">Paid</span>');
                       }?>
                   </td>
                    <td><?php if(!empty($invoice->payday)){ echo h($invoice->payday);} ?></td>
            
                    <td class="actions">
                        
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
            <?php endif; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>





