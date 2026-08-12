<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                   
                    
                    
                    
                    
                    
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Student's Course Registration</h1>
                        </div>
                  
                         <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Registered Courses</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTabl" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        <th> #</th>
                        <th> NAME</th>
                       <th>CODE</th>
                         <th>SEMESTER</th>
                       <th>CREDIT UNIT</th>
                       <th>DEPARTMENT</th>
                       <th>LECTURER</th>
                       
                    </tr>
                  </thead>
                
                  <tbody>
                      <?php $count = 0; $creditload = 0; foreach ($courseregistration->subjects as $subject): 
                           $count++; $creditload+= $subject->creditload; ?>
                                        <tr>
                                             <td> <?= $count?> <span class="fa fa-check text-success"> </span></td>
                                            <td><?= h($subject->name) ?></td>
                                            <td><?= h($subject->subjectcode) ?></td>
                                             <td><?= h($subject->semester->name) ?></td>
                                            <td><?= $subject->creditload ?></td>
                                             <td><?= $subject->department->name ?></td>
                                             <td><?php foreach ($subject->teachers as $teacher){
                                              
                                             echo   ucwords($teacher->firstname.' '.$teacher->lastname).'<br />';
                                             }?></td>

                                       
                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
                </table>
              </div>
            </div>
          </div>
                         <br /> <br />
                         
                      Total Credit Load(Registered):   <?= $creditload.' units <br />'.'Max Unit(Do not exceed) : '. $subject->department->maxunit ?> 
                       <?php  $this->request->getSession()->write('creditload_registered', $creditload); ?>
    <?= $this->Form->create($courseregistration) ?>
    <br /> <fieldset>
        <div class="form-group row">       
            <div class="col-sm-4 mb-3 mb-sm-0">
        <?php 
          echo $this->Form->control('department_id', ['options' => $departments,'Label'=>'Department','class' => 'form-control form-control-user','required','default'=>$student->department_id,'id'=>'dept','onChange'=>'getdeptcourses(this.value)'])?>
           </div>
         
                  <div class="col-sm-4 mb-3 mb-sm-0">
            <?= $this->Form->control('level_id', ['options' => $levels,'label'=>'Select Level','required','empty'=>'Select Level','class' => 'form-control form-control-user','onChange'=>'getcourses(this.value)'
               ,'id'=>'levelid' ])?>
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
           
           <?= $this->Form->control('semester_id', ['options' => $semesters,'Label'=>'Select Semester','required','empty'=>'Select Semester','class' => 'form-control form-control-user','onChange'=>'getcourses(this.value)','id'=>'semesterid'])?>
            </div>                       
          
        </div>
            <div class="form-group row"> 
                                      
           
   
                <div class="col-sm-4 mb-3 mb-sm-0">
          
           <?= $this->Form->control('subjects._ids', ['options' => $subjects,'label' => 'Select Courses', 'class' => 'select2_multiple form-control form-control-user','id'=>'extracourses']) ?> 
                </div>
                </div>
    </fieldset>
     <br /> <br />
     <div class="col-md-12">
<?= $this->Form->button(' Add Courses', ['class' => 'btn btn-primary float-left']) ?>
<!--            <?= $this->Form->postLink(__(' Clear All'), ['controller'=>'Courseregistrations','action' => 'deletecourses', $courseregistration->id], ['confirm' => __('Are you sure you want to delete # {0}?', 'this course registration data'),'class'=>'float-right','style'=>'margin-left: 5px;','class' => 'btn btn-danger float-left']) ?>       -->
          <?= $this->Html->link(__(' Done'), ['action' => 'registeredcourses'], ['class' => 'btn btn-success float-right']) ?>
     </div>
<?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>  
    
       function getcourses(semid){ 
     deptid =     document.getElementById('dept').value; 
     level =     document.getElementById('levelid').value; 
     semesterid = document.getElementById('semesterid').value; 
     if( level!=='' && deptid!=='' && semesterid !=='' ) {getcourseswithallparams(semesterid,deptid,level);} 
     else if(level!=='' && deptid!==''){
     
     getcoursesbasedondeptandlevel(deptid,level);}

}  

    function getcourseswithallparams(semid,deptid,level){ 
//alert('all p');
    $.ajax({
        url: '../../../Courseregistrations/getdeptcourses/'+semid +'/'+ deptid +'/'+ level,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('extracourses').innerHTML = "";
            document.getElementById('extracourses').innerHTML = response;
            //location.href = redirect;
        }
    });

} 

function getdeptcourses(deptid){
  
      $.ajax({
        url: '../../../Courseregistrations/getcoursesindept/'+ deptid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('extracourses').innerHTML = "";
            document.getElementById('extracourses').innerHTML = response;
            //location.href = redirect;
        }
    });
}


//gets courses based on department and level
function getcoursesbasedondeptandlevel(deptid,levelid){
  //alert(deptid);
     $.ajax({
        url: '../../../Courseregistrations/getdeptcoursesindeptandlevel/'+ deptid +'/'+ levelid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('extracourses').innerHTML = "";
            document.getElementById('extracourses').innerHTML = response;
            //location.href = redirect;
        }
    });
    
}
    
    </script>