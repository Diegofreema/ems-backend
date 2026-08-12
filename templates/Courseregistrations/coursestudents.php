<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newstudent'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'addmit new student']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Students Course Registration</h1></div>
     <!-- DataTales Example -->
          <div class="card shadow mb-4" id="students">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Course Registration</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                <th >Session</th>
               

                <th > Semester</th>
                <th >Course</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
               <th >Session</th>
               

                <th > Semester</th>
                <th >Course</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
              </tfoot>
            
        </thead>
         <tbody>
            <?php foreach ($courseregistrations as $register):
               
                
                ?>
            <tr>
                
                <td>
                    <?=$register->courseregistration->session->name ?>
                
                </td>
                
               <td><?= h($register->courseregistration->semester->name) ?></td>
              
               <td><?php foreach ($register->courseregistration->subjects as $subject)
               {if(in_array($subject->id,  $teacher_subjects)) {echo $subject->name;?></td>
                
              
               
                <td class="actions">
                    
                    <?php echo $this->Html->link(__(' View Students'), ['action' => 'viewregisteredstudent',$subject->id,$this->generateurl($register->courseregistration->semester->name)],
                            ['class'=>'btn btn-round btn-primary fa fa-eye','title'=>'view students for this course']);
                    
               }} ?>
                   
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>


