<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Course Materials</h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">My Course Materials</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                  <th >Course</th>
                <th>Lecturer</th>
               
                 <th>Title</th>
                <th>Department</th>
                 <th>Comment</th>
                <th >Action</th>
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                  <th >Course</th>
                <th>Lecturer</th>
                
                 <th>Title</th>
                <th>Department</th>
                <th>Comment</th>
                <th >Action</th>
            </tr>
              </tfoot>
            
        
        <tbody>
            <?php foreach ($materials as $coursematerial): ?>
            <tr>
              
                <td><?= $coursematerial->has('subject') ? $coursematerial->subject->name: '' ?></td>
                <td><?= $coursematerial->has('teacher') ? $coursematerial->teacher->firstname : '' ?></td>
                <td><?= h($coursematerial->title) ?></td>
              
                <td><?= $coursematerial->has('department') ? $coursematerial->department->name : '' ?></td>
                <td><?= h($coursematerial->comment) ?></td>
                <td class="actions">
                    
                    <?= $this->Form->postLink(__(' Download'), ['action' => 'downloadmaterial', $coursematerial->id], 
                            ['confirm' => __('Are you sure you want to download # {0}?', $coursematerial->title),'class'=>'btn btn-success','title'=>'download material']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>





