<?php
$userdata = $this->request->getSession()->read('usersinfo');
$settings = $this->request->getSession()->read('settings');
?>
<!-- Begin Page Content -->
        <div class="content container-fluid">
          <!-- Page Header -->
           <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">View Responses</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('Manage Assignments', ['controller' => 'Setassignments', 'action' => 'index', $this->GenerateUrl('Manage Assignments')], ['title' => 'Manage Assignments'])
                            ?></li>
                        <li class="breadcrumb-item active">View Responses</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
            <div class="card shadow mb-4" id="printableArea">
                  
          <!-- DataTales Example --><br />
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Registered Subjects</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTabl" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> Subject</th>
                        <th>Subject Code</th>
                       <th>Student</th>
                     
                      <th> ACTION</th>
                      
                    </tr>
                  </thead>
                 
                  <tbody>
                      <?php $unit = 0; foreach ($assignments as $subjects): 
                       
                          ?>
                                        <tr>

                                            <td>
             <?= $subjects->subject->name ?>
                     </td>
                                            <td><?= h($subjects->subject->subjectcode) ?></td>
                                            <td><?= $subjects->student->regno ?></td>
                                            
                                            <td><?php 
                                            echo $this->Html->link(' View', ['controller'=>'Assignments','action' => 'viewres',$subjects->id,$subjects->subject->id,$subjects->subject->name],['title'=>'view assignments']);

                                            ?></td>          

                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
              
                </table>
                     
              </div>
            </div>
          </div>
<br />

                <br /><br />
        </div>
            </div>
        <!-- /.container-fluid -->



<script>
    
    function printDiv(divName) { //alert('am called');
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
 }

    </script>