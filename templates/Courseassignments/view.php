<?php
$userdata = $this->request->getSession()->read('usersinfo');
$settings = $this->request->getSession()->read('settings');
?>
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Courses Manager</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard'], ['title' => 'Admin dashboard']) ?></li>
                    <li class="breadcrumb-item active">Assigned Courses</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
<!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="card shadow mb-4" id="printableArea">
                 
        <div class="card-header py-3">
                <div class="col-sm-3 m-b-20">
                <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100,'width'=>140])?>
                </div>
              <center>
                            
                          
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 26px;"><?=$settings->name?></strong><br />
                                <b style="font-size: 22px;">  <?=$settings->address?><br />
                               <?=$settings->email?><br /></b>
                               
                               </h1>
                        
                  <br />   </center>
                
          
          
        <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Department') ?></th>
            <td><?= $courseassignment->has('department') ? $this->Html->link($courseassignment->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $courseassignment->department->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Semester') ?></th>
            <td><?= $courseassignment->has('semester') ? $courseassignment->semester->name : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Level') ?></th>
            <td><?= $courseassignment->has('level') ? $courseassignment->level->name : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Updatedon') ?></th>
            <td><?= h($courseassignment->updatedon) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Admin') ?></th>
            <td><?= $courseassignment->has('user') ? $courseassignment->user->username: '' ?></td>
        </tr>
       
        <tr>
            <th scope="row"><?= __('Assigned On') ?></th>
            <td><?= h($courseassignment->assignedon) ?></td>
        </tr>
    </table>     
            
           
            <br /> <br /> <center>Course Assignment</center>
       <div class="card-body">
              <div class="table-responsive">
                  
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                 
                  <thead>
                    <tr>
                        <th> COURSE</th>
                       <th>CODE</th>
                       <th> UNIT</th>
                      
                      
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th> COURSE</th>
                       <th>CODE</th>
                       <th>UNIT </th>
                       
                    
                    </tr>
                  </tfoot>
                  <tbody>
                      <?php $unit = 0; foreach ($courseassignment->subjects as $subjects): 
                          $unit+= $subjects->creditload;
                          ?>
                                        <tr>

                                            <td><?= h($subjects->name) ?></td>
                                            <td><?= h($subjects->subjectcode) ?></td>
                                            <td><?= $subjects->creditload ?></td>
                                            

                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
              
                </table>
                  Total Unit : <?=  number_format($unit,2)?>
              </div>
            </div>
           
           <br />
        </div>
            </div> </div>
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


<div class="container-fluid">

          <!-- Page Heading -->
        

          <div class="row">

            <div class="col-lg-12">

              <!-- Circle Buttons -->
            <div class="card-body">

                 <input class="btn btn-success float-right" type="button" onclick="printDiv('printableArea')" value="Print Course Form" />
                 </div>

            </div>


          </div>

        </div>
