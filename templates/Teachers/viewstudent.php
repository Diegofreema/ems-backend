<?php
$userdata = $this->request->getSession()->read('usersinfo');
?>
  <div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Student Profile</h1>
  </div><!--/end d-sm-flex-->
  <div class="row">
  <!-- Pie Chart -->
  <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Regno : <?= h($student->regno) ?></h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?=  $this->Html->image($student->passporturl, ['alt' => 'EMS', 'class' => 'img-responsive avatar-view', "width"=>"100%", "height"=>"300px"])?>
      </div>
      <!--/end card body-->
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-4-->

  <!-- Area Chart -->
  <div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Overview</h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Name</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->fname . " " . $student->lname ." ".$student->mname?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date Of Birth</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->dob?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-calendar fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        
         <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Father's Name</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= h($student->fathersname.'  - ('.$student->fathersjob.', '.$student->fatherphone.')') ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-user-plus fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mother's Name</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= h($student->mothersname.'  - ('.$student->mothersjob.', '.$student->motherphone.')') ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-user-plus fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->user->username ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-envelope fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Phone</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->phone ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-phone fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Address</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->address ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-map-marker fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Department</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->department->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-home fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
         <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">State</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->state->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-globe fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Country</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $student->country->name ?></div>
          </div>
          <div class="col-auto">
            <i class="fa fa-globe fa-2x text-gray-300"></i>
          </div>
        </div>
        <!--/end no-gutters-->
        <hr/>
       
      </div>
    </div>
    <!--/end card-->
  </div>
  <!--/end col-xl-8-->
</div>
  
  <div class="accordion" id="accordionExample">
  <div class="card z-depth-0 bordered">
    <div class="card-header bg-success" id="headingOne">
      <h5 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne" style="color: white">
          Courses
        </button>
      </h5>
    </div>
    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
      data-parent="#accordionExample">
      <div class="card-body">
         <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student's Courses</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th >Course Name</th>
                <th>Subject Code</th>
                
               
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                 <th >Course Name</th>
                <th>Subject Code</th>
                
               
            </tr>
              </tfoot>
            
        
         <tbody>
            <?php //debug(json_encode( $mycourses->department->subjects, JSON_PRETTY_PRINT));exit;
              foreach ($student->department->subjects as $subject): ?>
            <tr>
                
                <td><?= h($subject->name) ?></td>
                <td><?= h($subject->subjectcode) ?></td>
              
               
            </tr>
            <?php endforeach; ?>
            <!--  for any assigned course like carry over -->
             <?php //debug(json_encode( $mycourses->department->subjects, JSON_PRETTY_PRINT));exit;
              foreach ($student->subjects as $subject): ?>
            <tr>
                
                <td><?= h($subject->name) ?></td>
                <td><?= h($subject->subjectcode) ?></td>
               
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <div class="card z-depth-0 bordered">
    <div class="card-header bg-info" id="headingTwo">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
          data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="color: white">
          Invoices
        </button>
      </h5>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
         <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Invoices</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th >Fee Name</th>
                <th>Amount</th>
                <th>Deadline</th>
                 <th>Session</th>
                <th>Status</th>
              
               
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                  <th >Fee Name</th>
                <th>Amount</th>
                <th>Deadline</th>
                 <th>Session</th>
                <th>Status</th>
               
            </tr>
              </tfoot>
            
        
         <tbody>
            <?php foreach ($student->invoices as $invoice): ?>
            <tr>
                
                <td><?= h($invoice->fee->name) ?></td>
                <td><?= number_format($invoice->fee->amount) ?></td>
                <td><?= date('d M Y', strtotime($invoice->fee->startdate)) ?></td>
               <td><?= h($invoice->session->name) ?></td>
               <td ><?php if($invoice->paystatus=="Unpaid"){
               echo (' <span class="badge badge-warning">'.$invoice->paystatus.'</span>');}
                   
                   else{
                        echo (' <span class="badge badge-success">'.$invoice->paystatus.'</span>');
                   }?>
               </td>
               
        
                
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <div class="card z-depth-0 bordered">
    <div class="card-header bg-warning" id="headingThree">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
          data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="color: white">
          Results
        </button>
      </h5>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
           <?php if(!empty($student->results)){   ?>
                   <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Results</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
              
              
               
                <th>Course</th>
                <th >Semester</th>
                <th >Session</th>
                <th>Score</th>
                <th>Grade</th>
                <th >Remark</th>
                
                <th >Credit Load</th>
            </tr>
                  <tfoot>
                       <tr>
              
               <th>Course</th>
                <th >Semester</th>
                <th >Session</th>
                <th>Score</th>
                <th>Grade</th>
                <th >Remark</th>
                
                <th >Credit Load</th>
            </tr>
                  </tfoot>
        </thead>
        <tbody>
            <?php foreach ($student->results as $result): ?>
            <tr>
              
                <td><?= $result->subject->name?></td>
                <td><?= $result->semester->name?></td>
                <td><?= $result->session->name?></td>
                <td><?= $this->Number->format($result->score) ?></td>
                <td><?= h($result->grade) ?></td>
                <td><?= h($result->remark) ?></td>
               
                <td><?= $this->Number->format($result->creditload) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
     </table>
              </div>
            </div>
          </div>
    
               <?php }
               echo 'CGPA : '. $this->calculateCGPA($student->regno); ?>
          
      </div>
    </div>
  </div>
</div>
  </div>
