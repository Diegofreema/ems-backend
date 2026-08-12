<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles'); 
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Students Transcript Requests</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Transcript Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                 <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
                
                <th scope="col"><?= $this->Paginator->sort('Student') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Institution') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Continent') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Country') ?></th>
                <th scope="col"><?= $this->Paginator->sort('State') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Address') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Courier') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Amount') ?></th>
                 <th>Payment</th>
                 <th>Delivery Status</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                
                 <th scope="col"><?= $this->Paginator->sort('Student') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Institution') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Continent') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Country') ?></th>
                <th scope="col"><?= $this->Paginator->sort('State') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Address') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Courier') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Amount') ?></th>
                <th>Payment</th>
                <th>Delivery Status</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($trequests as $trequest): ?>
            <tr>
               
                <td>
                    <?= $this->Html->link(h($trequest->student->fname. ' '.$trequest->student->lname), ['controller'=>'Students','action' => 'viewstudent', $trequest->student->id,$this->Generateurl($trequest->student->fname)],
                            ['class'=>'fa fa-eye','title'=>'view student details']) ?>
             
                </td>
                <td><?= h($trequest->orderdate) ?></td>
                <td><?= h($trequest->institution) ?></td>
                <td><?= h($trequest->status) ?></td>
                <td><?= $trequest->has('continent') ? $trequest->continent->name : '' ?></td>
                <td><?= $trequest->has('country') ? $trequest->country->name : '' ?></td>
                <td><?= $trequest->has('state') ? $trequest->state->name : '' ?></td>
                <td><?= h($trequest->address) ?></td>
                <td><?= $trequest->has('courier') ? $trequest->courier->name : '' ?></td>
                <td>₦<?= number_format($trequest->amount) ?></td>
                 
                 <td><?php
                  
                   if(!empty($trequest->student->invoices)){ foreach ($trequest->student->invoices as $invoice){
                       // debug(json_encode( $invoice, JSON_PRETTY_PRINT)); exit;
                   if(($invoice->fee_id == 52 || $invoice->fee_id == 53 || $invoice->fee_id == 54 ||
                           $invoice->fee_id == 23 || $invoice->fee_id == 55 ) && ($invoice->paystatus=='success')){
                echo ' <span class="badge badge-success">'.$invoice->paystatus.'</span>';}
                else{
                    ' <span class="badge badge-info">'.$invoice->paystatus.'</span>';
                }
                
                   } }?></td>
                 <td><?= h($trequest->deliverystatus) ?></td>
                <td class="actions">
                    <?php  
                    if((($invoice->fee_id == 52 )|| ($invoice->fee_id == 53) || ($invoice->fee_id == 54) ||
                           ($invoice->fee_id == 23) || ($invoice->fee_id == 55 )) && ($invoice->paystatus=='success')){
                     echo $this->Html->link(' Generate Transcript', ['controller' => 'Results', 'action' => 'gettranscript',$trequest->student->id], ['title' => 'generate student transcript', 'class' => 'btn btn-success']);
                       
                    }
                    ?>
                  <?php if($trequest->deliverystatus!="Delivered"){ echo $this->Html->Link(__('Set As Delivered'), ['controller'=>'Trequests','action' => 'deliverystatus', $trequest->id,$this->generateurl($trequest->student->fname)],
                  ['confirm' => __('This means that this transcript has been delivered to the courier company # {0}?', $trequest->id),'class'=>'btn btn-info','title'=>'set transcript as delivered to courier']);} ?>
                    <?= $this->Html->link(__('Update'), ['controller'=>'Trequests','action' => 'edit', $trequest->id,$this->generateurl($trequest->student->fname)],['class'=>'btn btn-primary']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $trequest->id], 
                            ['confirm' => __('Are you sure you want to delete the trasncript request # {0}?', $trequest->id),'class'=>'btn btn-danger']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
         </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
