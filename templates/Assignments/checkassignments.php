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
                    <h3 class="page-title">My Assignments</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">My Assignments</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
            <div class="card shadow mb-4" id="printableArea">
                  
          <!-- DataTales Example --><br />
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Registered Courses</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTabl" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> COURSE TITLE</th>
                       <th>CODE</th>
                       <th> UNIT</th>
                      <th> ACTION</th>
                      
                    </tr>
                  </thead>
                 
                  <tbody>
                      <?php $unit = 0; foreach ($assignment as $subjects): 
                          $unit+= $subjects->creditload;
                          ?>
                                        <tr>

                                            <td>
             <?= $subjects->subject->name ?>
                     </td>
                                            <td><?= h($subjects->subject->subjectcode) ?></td>
                                            <td><?= $subjects->subject->creditload ?></td>
                                            
                                            <td><?php 
                                           // echo strtotime($subjects->closedate).' '.strtotime(date("Y-m-d")); exit;
                                            
                                            if(strtotime($subjects->closedate)> (strtotime(date("Y-m-d")))){
                                   
                                            echo $this->Html->link(' View', ['controller'=>'Assignments','action' => 'view',$subjects->id,$subjects->subject->id,$subjects->subject->name],['title'=>'view assignments']);}
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