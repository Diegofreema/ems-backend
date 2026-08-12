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
                        <h3 class="page-title">Notification Details</h3>
                          <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                                ?></li>
                            <li class="breadcrumb-item"><?= $this->Html->link('Notifications', ['controller' => 'Notifications', 'action' => 'notices', $this->GenerateUrl('Notifications')], ['title' => 'Notifications']) 
                                ?></li>
                            <li class="breadcrumb-item active">Notification Details</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Notices</h6>
            </div>
            <div class="card-body">
               <h3> <?= $notification->title?></h3>
                <p> <?= $notification->message?> </p>
            </div>
          </div>

        </div>





