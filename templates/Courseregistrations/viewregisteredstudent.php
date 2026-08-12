<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Course Registration</h1></div>
        

          <!-- DataTales Example -->
          <div class="card shadow mb-4" id="students">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Students Course Registration</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th>Name</th>
               <th>Regno</th>

                 <th >Level</th>
                 <th>Session</th>
                 <th>Semester</th>
             
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                  <th>Name</th>
               <th>Regno</th>

                 <th >Level</th>
                 <th>Session</th>
                 <th>Semester</th>
              
            </tr>
              </tfoot>
            
        </thead>
         <tbody>
            <?php foreach ($courseregistration as $student): 
                // debug(json_encode($student->courseregistration->student, JSON_PRETTY_PRINT)); exit;
                ?>
            <tr>
                
                <td>
                    <?= $this->Html->link(h(' '.$student->courseregistration->student->fname. ' '.$student->courseregistration->student->lname), ['action' => 'viewstudent', $student->courseregistration->student->id,$this->Generateurl($student->courseregistration->student->fname)],
                            ['class'=>'fa fa-eye','title'=>'view student details']) ?>
                
                </td>
                 <td><?= h($student->courseregistration->student->regno) ?></td>
               <td><?= h($student->courseregistration->level->name) ?></td>
                <td><?= h($student->courseregistration->session->name) ?></td>
                 <td><?= h($student->courseregistration->semester->name) ?></td>
              
               
               
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>

