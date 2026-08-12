<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">My Invoices</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Invoice</h6>
            </div>
            <div class="card-body"> 
              <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                  <thead>
                      
            <tr>
                  <div class="float-right">Please Click to Pay Fees </div>
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
            <?php foreach ($myinvoices as $invoice): ?>
            <tr>
                
                <td><?= h($invoice->fee->name) ?></td>
                   <td><?= h($invoice->invoiceid) ?></td>
                <td>₦<?= number_format($invoice->amount) ?></td>
<!--                <td><?= h($invoice->fee->startdate) ?></td>-->
               <td><?= h($invoice->session->name) ?></td>
               <td ><?php if($invoice->paystatus=="Unpaid"){
               echo (' <span class="badge badge-warning">'.$invoice->paystatus.'</span>');}
                   
                   else{
                        echo (' <span class="badge badge-success">Paid</span>');
                  
                          }?>
               </td>
               <td><?php if(!empty($invoice->payday)){echo $invoice->payday;}  ?></td>
               
        
                <td class="actions">
                    
                    <?php 
                    if($invoice->paystatus=="success"){ 
                        //echo $this->Html->link(__(' Paid'), ['controller'=>'Students','action' => 'generatepayeeid', $invoice->id,$invoice->student_id],
                          //  ['class'=>'btn btn-round btn-primary fa fa-money disabled','title'=>'pay online']);
                  
                    echo $this->Html->link(__(' Get Receipt'), ['controller'=>'Invoices','action' => 'studentreceipt', $invoice->id,$invoice->student_id],
                            ['class'=>'btn btn-round btn-success fa fa-money','title'=>'print receipt']);  
                        
              
                    }
                    else{
                   echo $this->Html->link(__(' Get Invoice'), ['controller'=>'Students','action' => 'getmypayeeid', $invoice->id,$invoice->student_id],
                           ['class'=>'btn btn-round btn-primary fa fa-money','title'=>'pay online']);    
                   
                      echo $this->Html->link(__('Pay Online'), ['controller'=>'Students','action' => 'gotopaystacktest', $invoice->student_id,$invoice->fee_id,$invoice->id],
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
              </div>
            </div>
          </div>

        </div>




