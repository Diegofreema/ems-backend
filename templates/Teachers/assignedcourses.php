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
                    <h3 class="page-title">My Subjects</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">My Subjects</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Assigned Courses</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> NAME</th>
                       <th>CODE</th>
                       <th>CLASS</th>
                      
                       <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th> NAME</th>
                       <th>CODE</th>
                       <th>CLASS</th>
                       
                       <th>ACTIONS</th>
                    </tr>
                  </tfoot>
                  <tbody>
                      <?php foreach ($teacher->subjects as $subjects): ?>
                                        <tr>

                                            <td><?= h($subjects->name) ?></td>
                                            <td><?= h($subjects->subjectcode) ?></td>
                                            <td><?= h($subjects->department->name ?? 'Not Assigned') ?></td>
                                            


                                            <td class="actions">
                                                 <!-- <?= $this->Html->link(__(' Students'), ['controller'=>'Teachers','action' => 'viewcoursestudents', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-info fa fa-eye','title'=>'view students']) ?>  -->
                                                <?= $this->Html->link(__(' View Contents'), ['controller'=>'Topics','action' => 'teacherviewcontents', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-info fa fa-eye','title'=>'view contents']) ?>
                                                <?= $this->Html->link(__(' Add Assignment '), ['controller'=>'Setassignments','action' => 'addassignment', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'fa fa-upload btn btn-round btn-primary','title'=>'upload course material']) ?>
                                              <?= $this->Html->link('Add Content', ['controller'=>'Teachers','action' => 'teachertopics', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-success fa fa-eye','title'=>'add course contents']) ?> 
                                                 <!--?= $this->Html->link(__(' Contents'), ['controller'=>'Topics','action' => 'viewcontents', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-info fa fa-eye','title'=>'view contents']) ?--> 
									
												 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
                </table>
              </div>
            </div>

        </div>
        <!-- /.container-fluid -->



