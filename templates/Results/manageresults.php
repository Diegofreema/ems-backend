<?php
ob_start();
  $past_gpoint = 0;  // for cumulative grades
  $ptgp = 0; //past total grade point
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$settings = $this->request->getSession()->read('settings');
// $semesta = "";
// $dept = "";
// $lev = "";
// $session = "";
// $faculty = "";
// $persons_with_f = 0;
// $pwfs = [];
// $h_cgpa = 1.00;
// $l_cgpa = 5.00;
// $s_h_cgpa;
// $s_l_cgpa;
// $cgpa_less1 = 0;
// $firstclass = '';
// $secondclassupper = '';
// $secondclasslower = '';
// $thirdclass = '';
// $pass = '';
// $failed = '';
// //set values for class of degree in current semester
// $c_s_firstclass = 0;
// $c_s_secondclassupper = 0;
// $c_s_secondclasslower = 0;
// $c_s_thirdclass = 0;
// $c_s_pass = 0;
// $c_s_failed = 0;
// if ($courses != NULL) {
//     $iscarryo = 0; $carry_over = 0;
//     foreach ($courses as $cours) {
//         $semesta = $cours->semester->name;

//         $lev = $cours->level->name;
//         $session = $cours->session->name;
//         $faculty = $cours->faculty->name;
//         if($cours->iscarryover=='yes'){
//          $iscarryo++;  
         
//         }
//     }

  
// }
?>

<style>
    .rotateit{
        transform: rotate(90deg);
    }
</style>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Results</h3>
                <ul class="breadcrumb">
    
                    <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item active">Manage Results</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="donotprint" style="padding-bottom: 10px; margin-bottom: 20px;">
        <!--<?= $this->Html->link(__(' '), ['action' => 'newresult'],
        ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'add student result'])
?>
        -->
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>
    <div class="col-lg-12 donotprint">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Result</h1>
            </div>
                        <?= $this->Form->create(null) ?>
            <fieldset>

                <div class="form-group row">
                    <div class="col-sm-4 mb-3 mb-sm-0">
<?=
$this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class',
    'empty' => 'Select Class', 'class' => 'select2_multiple form-control', 'id' => 'dept1', 'onChange' => 'getstudents(this.value)'])
?>
                    </div>

                    <div class="col-sm-4">
                        <?=
                        $this->Form->control('subject_id', ['options' => $subjects, 'label' => 'Select Subject', 'empty' => 'Select Subject'
                            , 'class' => 'select2_multiple form-control'])
                        ?>
                    </div>
                    <div class="col-sm-4">
                        <?=
                        $this->Form->control('semester_id', ['options' => $semesters, 'label' => 'Select Term', 'empty' => 'Select Term', 'placeholder' => 'Select Term'
                            , 'class' => 'form-control'])
                        ?>
                    </div>  
                </div>

                <div class="form-group row">
                    
                    <div class="col-sm-4">
            <?=
            $this->Form->control('session_id', ['options' => $sessions, 'label' => 'Select Session', 'empty' => 'Select Session', 'placeholder' => 'Select Session'
                , 'class' => 'form-control','required'])
            ?>
                    </div>
                    <div class="col-sm-4">
<?= $this->Form->control('student_id', ['options' => $students, 'label' => 'Select Student', 'empty' => 'Select Student'
    , 'class' => 'select2_multiple form-control', 'id' => 'studentss'])
?>

                    </div>
                </div>
                
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search Result', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>

        </div>
        <br /> <br />

    </div>
   




    <!-- DataTales Example -->
    <?php if(!empty($results)){   ?>
                   <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Results Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                 <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
              
                <th>Student</th>
                 <th>Regno</th>
               
                <th>Department</th>
                <th>Subject</th>
                <th >Term</th>
                <th >Session</th>
                <th>Total</th>
                <th>Grade</th>
               <th >Remark</th>
<!--                <th scope="col" class="actions"><?= __('Actions') ?></th>-->
            </tr>
                  <tfoot>
                       <tr>
              
               <th>Student</th>
                 <th>Regno</th>
                <th>Department</th>
                <th>Subject</th>
                <th >Term</th>
                <th >Session</th>
                <th>Total</th>
                <th>Grade</th>
               <th >Remark</th>
<!--                <th scope="col" class="actions"><?= __('Actions') ?></th>-->
            </tr>
                  </tfoot>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
            <tr>
              
                <td><?= $result->has('student') ? $result->student->fname . ' ' . $result->student->lname : '' ?></td>
                <td><?= h($result->regno) ?></td>
                <td><?= $result->has('department') ? $result->department->name . (!empty($result->student->class_arm) ? ' - ' . $result->student->class_arm->arm_name : '') : '' ?></td>
                <td><?= $result->has('subject') ? $result->subject->name : '' ?></td>
                <td><?= $result->has('semester') ? $result->semester->name : '' ?></td>
                <td><?= $result->has('session') ? $result->session->name : '' ?></td>
                <td><?= $this->Number->format($result->total) ?></td>
                <td><?= h($result->grade) ?></td>
                <td><?= h($result->remark) ?></td>
               
<!--                <td class="actions">
                  
                    <?= $this->Html->link(__(' '), ['action' => 'updateresult', $result->id],['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'update result']) ?>
                    <?= $this->Form->postLink(__(' '), ['action' => 'delete', $result->id], ['confirm' => __('Are you sure you want to delete # {0}?', $result->id),'class'=>'btn btn-round btn-danger fa fa-times-circle','title'=>'delete result']) ?>
                </td>-->
            </tr>
            <?php endforeach; ?>
        </tbody>
     </table>
              </div>
            </div>
          </div>
 
               <?php } ?>

</div>

<script>

    function getdepartments(facultyid) {

        $.ajax({
            url: '../Results/getdaepts/' + facultyid,
            method: 'GET',
            dataType: 'text',
            success: function (response) {
                // console.log(response);
                document.getElementById('dept1').innerHTML = "";
                document.getElementById('dept1').innerHTML = response;
                //location.href = redirect;
            }
        });

    }

    function getstudents(deptid) {

        $.ajax({
            url: '../Results/studentsindept/' + deptid,
            method: 'GET',
            dataType: 'text',
            success: function (response) {
                // console.log(response);
                document.getElementById('studentss').innerHTML = "";
                document.getElementById('studentss').innerHTML = response;
                //location.href = redirect;
            }
        });

    }

</script>

<script>

    function printDiv(divName) { //alert('am called');
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }

</script>

