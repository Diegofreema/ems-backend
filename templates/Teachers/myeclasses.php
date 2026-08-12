<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>
<!-- Begin Page Content -->
        <div class="container-fluid">

         <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">My Meeting Links</h1>
         <?= $this->Html->link(__(' '), ['controller'=>'Liveclasses','action' => 'addlink'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'Create a live class'])
?>
         </div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Meeting Links</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> Date</th>
                       <th>Link</th> 
                       <th>ACTIONS</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                      <?php foreach ($myclasses as $meeeting): ?>
                                        <tr>

                                            <td><?= date('d D M Y, H:i:sa', strtotime($meeeting->datecreated)) ?></td>
                                            <td><?= h($meeeting->meetinglink) ?></td>
                                            <td class="actions">
                                       <!--?=$this->Html->link(__(' Live Class'), ['controller' => 'Liveclasses', 'action' => 'livelecture',$meeeting->id], ['title' => 'join online class'])  ?-->
                                     
                                                 <?= $this->Html->link( ' Join Class', $meeeting->meetinglink, ['class'=>'btn fa fa-video-camera m-r-5 dropdown-item ','title'=>'join live class','target' => '_blank']) ?> 
                                               					 
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



