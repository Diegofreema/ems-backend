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
                        <h3 class="page-title">Notifications</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">Notifications</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Notices</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th >Title</th>
                <th>Date</th>
                
                <th >Action</th>
               
            </tr>
                  </thead>
         <tbody>
            <?php foreach ($notifications as $notification): ?>
            <tr>
                
                <td><?= h($notification->title) ?></td>
                
                <td><?= date('D, M Y h:m:i',strtotime($notification->datecreated)) ?></td>
           
               <td> <?= $this->Html->link(__(' Read'), ['controller'=>'Notifications','action' => 'getnote', $notification->id,$this->generateurl($notification->title)],
                            ['class'=>'btn btn-round btn-success','title'=>'read'])?> 
                         </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>





