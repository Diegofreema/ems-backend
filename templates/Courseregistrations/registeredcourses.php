<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
           
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Registered Courses</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Courses</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
            <tr>
                
                <th >Session</th>
                <th> Semester</th>
                <th>Level</th>
                <th>Date</th>
                <th>Action</th>
               
              
            </tr>
            
                  <tfoot>
                      <tr>
                
                 <th >Session</th>
                <th> Semester</th>
                <th>Level</th>
                <th>Date</th>
                <th>Action</th>
               
              
            </tr>
                  </tfoot>
        </thead>
        <tbody>
             <?php foreach ($registeredcourses as $courseregistration): ?>
            <tr>
               
               
                <td><?= $courseregistration->has('session') ? $courseregistration->session->name : '' ?></td>
                <td><?= $courseregistration->has('semester') ? $courseregistration->semester->name : '' ?></td>
                <td><?= $courseregistration->has('level') ? $courseregistration->level->name : '' ?></td>
                <td><?= h($courseregistration->date_created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__(' View Courses'), ['action' => 'viewcourses', $courseregistration->id,$this->generateurl('courses for'.$courseregistration->session->name)],['class'=>'fa fa-eye']) ?>
                     </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
                
              </div>
            </div>
          </div>

        </div>

