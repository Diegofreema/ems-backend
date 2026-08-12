<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>



<!-- Begin Page Content -->
        <div class="container-fluid">

         <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Sponsors','action' => 'newsponsor'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new sponsor']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Sponsors</h1></div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Sponsors Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                         <th> #</th>
                        <th> NAME</th>
                          <th>PHONE</th>
                            <th>ADDRESS</th>
                       <th>EMAIL</th>
                     
                       <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tfoot>
                     <tr>
                         <th> #</th>
                        <th> NAME</th>
                          <th>PHONE</th>
                            <th>ADDRESS</th>
                       <th>EMAIL</th>
                     
                       <th>ACTIONS</th>
                    </tr>
                  </tfoot> 
                  <tbody>
                      <?php $count = 0; foreach ($sponsors as $sponsor): $count++;?>
                                        <tr>
                                             <td><?= $this->Number->format($count) ?></td>
                                             <td><?= h($sponsor->name) ?></td>
                    <td><?= h($sponsor->phone) ?></td>
                    <td><?= h($sponsor->address) ?></td>
                    <td><?= h($sponsor->emailaddress) ?></td>


                                            <td class="actions">
                                                
                                               
 <?php if($userdata['role_id'] == 5){ 
     echo $this->Html->link(__(' Update'), ['action' => 'updatesponsor', $sponsor->id, $this->GenerateUrl($sponsor->name)], ['class'=>'btn btn-round btn-primary fa fa-edit']);
                                                 
echo '&nbsp'. $this->Form->postLink(__(' Delete'), ['action' => 'delete', $sponsor->id], 
        ['confirm' => __('Are you sure you want to delete # {0}?', $sponsor->name),'class'=>'btn btn-round btn-danger fa fa-times']);
 }?>
										
												 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->



<div class="sponsors index content">
    <?= $this->Html->link(__('New Sponsor'), ['action' => 'newsponsor'], ['class' => 'button float-right']) ?>
 
 
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
