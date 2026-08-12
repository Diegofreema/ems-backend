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
                <h3 class="page-title">Manage Admins</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                  <li class="breadcrumb-item active">Manage Admins</li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /Page Header -->

         <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Admins','action' => 'newadmin'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new admin']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Admin Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> NAME</th>
                       <th>ROLE</th>
                       <th>USERNAME</th>
                       <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th> NAME</th>
                       <th>ROLE</th>
                       <th>USERNAME</th>
                       <th>ACTIONS</th>
                    </tr>
                  </tfoot> 
                  <tbody>
                      <?php foreach ($alldmins as $dadmin): ?>
                                        <tr>

                                            <td><?= h($dadmin->surname . ' ' . $dadmin->lastname) ?></td>
                                            <td><?= h($dadmin->user->role->role_name) ?></td>
                                            <td><?= $dadmin->user->username ?></td>


                                            <td class="actions">
                                                <?= $this->Html->link(__('View'), ['action' => 'viewadmin', $dadmin->id, $this->GenerateUrl($dadmin->lastname)], ['class'=>'btn btn-round btn-info fa fa-eye']) ?>
                                               
<?php 
     echo $this->Html->link(__('Edit'), ['action' => 'updateadmin', $dadmin->id, $this->GenerateUrl($dadmin->lastname)], ['class'=>'btn btn-round btn-primary fa fa-edit']);
     
     // Prevent admin from disabling themselves
     if ($dadmin->user->id != $userdata['id']) {
         if ($dadmin->user->userstatus == "Enabled"){
             echo '&nbsp;&nbsp;'. $this->Form->postLink(__('Disable'), ['action' => 'changeuserstatus', $dadmin->user->id,'Disabled'], ['confirm' => __('Are you sure you want disable user # {0}?', $dadmin->surname),'class'=>'btn btn-round btn-danger']);
         } else{
             echo '&nbsp;&nbsp;'. $this->Form->postLink(__('Enable'), ['action' => 'changeuserstatus', $dadmin->user->id,'Enabled'], ['confirm' => __('Are you sure you want to enable # {0}?', $dadmin->surname),'class'=>'btn btn-round btn-success']); 
         }
     }
     
     echo '&nbsp;&nbsp;'. $this->Html->link(__(' Logs'), ['controller'=>'Admins','action' => 'viewactivitylogs', $dadmin->id, $this->GenerateUrl($dadmin->lastname)], ['class'=>'btn btn-round btn-warning fa fa-eye']);
                                                        
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
        <!-- /.container-fluid -->


