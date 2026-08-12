<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="content container-fluid">
  <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Accommodation Registry</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Accommodation Registry</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Accommodation Registry</h6>
            <span class="pull-right">     <?= $this->Form->postLink(__('Eject All'), ['action' => 'ejectall'], ['confirm' => __('Are you sure you want to EJECT all the students from their hostels?')],['class' => 'btn btn-success btn-block pull-right']) ?></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>
                              <th >Passport</th>
                            <th >Name</th>
                            <th>RegNo</th>
                            <th >Class</th>
                             <th>Hostel</th>
                              <th>Room</th>
                          

                        </tr>
                    </thead>



                    <tbody>
<?php foreach ($hostelrooms as $room): ?>
                              <tr>
                                  
  <td> <?= $this->Html->image('../student_files/'.$room->student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
          'style' => 'width:80px;height:80px;'])
      ?>
                                  </td>
                                  <td>
                                     <?= $this->Html->link(ucfirst($room->student->fname . ' ' . $room->student->lname), ['controller' => 'Students', 'action' => 'viewstudent', $room->student->id,$this->generateurl($room->student->lname)])?>
   </td>
                                  <td><?= h($room->student->regno) ?></td>
                                  <td><?= $room->student->department->name ?></td>
                                  
                                  <td><?= h($room->hostelroom->hostel->name) ?></td>
                                    <td><?= h($room->hostelroom->room_number) ?></td>

                                
                   
                              </tr>
                          <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>






