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
                        <h3 class="page-title">My Subjects (Current Term)</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">My Subjects (Current Term)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">My Subjects</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th >Subject Name</th>
                <th>Subject Code</th>
                
                <th >Action</th>
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                 <th >Subject Name</th>
                <th>Subject Code</th>
                
                <th >Action</th>
            </tr>
              </tfoot>
            
        
         <tbody>
           
             <?php 
             if (!empty($subjects)): 
              foreach ($subjects as $subject): ?>
            <tr>
                
                <td><?= h($subject->name) ?></td>
                <td><?= h($subject->subjectcode) ?></td>
              
                <td class="actions">
                    
                    <?= $this->Html->link(__(' '), ['controller'=>'Topics','action' => 'viewcoursecontent', $subject->id,$this->generateurl($subject->name)],
                            ['class'=>'btn btn-round btn-primary fa fa-eye','title'=>'view course contents']) ?>
                    </td>
            </tr>
            <?php endforeach; 
             else: ?>
            <tr>
                <td colspan="3" class="text-center">No subjects found for your department.</td>
            </tr>
            <?php endif; ?>
            
        </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>





