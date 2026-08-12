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
                        <h3 class="page-title">My Sponsored Invoices</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">My Sponsored Invoices</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Invoices</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th >Fee Name</th>
                <th>Amount</th>
                <th>date Paid</th>
                 <th>Session</th>
                <th>Status</th>
                <th >Action</th>
               
            </tr>
                  </thead>
         <tbody>
            <?php foreach ($sponsored_fees as $invoice): ?>
            <tr>
                
                <td><?= h($invoice->fee->name) ?></td>
                <td>₦<?= number_format($invoice->amount) ?></td>
                <td><?= date('D d M, Y', strtotime($invoice->transdate)) ?></td>
               <td><?= h($invoice->session->name) ?></td>
               <td ><?= $invoice->paystatus ?>
               </td>
               
        
                <td class="actions">
                    
                    <?php if($invoice->paystatus=="completed"){
                         echo $this->Html->link(__(' Get Receipt'), ['controller'=>'Invoices','action' => 'studentreceipt', $invoice->invoice_id,$invoice->student_id],
                            ['class'=>'btn btn-round btn-success fa fa-money','title'=>'print receipt']);  
                        
               
                    }
                    else{
            
                    echo $this->Html->link(__(' Pay Online'), ['controller'=>'Students','action' => 'gotopaystack', $invoice->student_id,$invoice->fee_id,$invoice->id],
                           ['class'=>'btn btn-round btn-info fa fa-money','title'=>'pay online']); 

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





