<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
//get session data
          $settings = $this->request->getSession()->read('settings');
?>


<!-- Begin Page Content -->
        <div class="container-fluid" id="printableArea">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"></h1></div>
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Transcript Ordering And Processing System </h6>
            </div>
              <div class="card-body">
                  
             <?= $this->Flash->render() ?>
                        <?php
                        if (!empty($student->results)) {
                            ?>
                  <div class="col-md-12 col-xs-12">
<!--                                <p>TOPS ID : <?php $transcript_id = "UAES/ESR/" . mt_rand();
                        echo $transcript_id; ?></p>--><br /><br />
                                  <?php echo $this->Html->image($settings->logo, ['alt' => 'EMS',  'style' => 'margin-top: 5px;','class'=>'float-right','height' => 100,]) ?>
                           <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'IMG', 'class' => 'img-responsive img-fluid float-left',
                                    'style' => 'height:100px; margin-top: 5px;']) ?>
                            </div>
                  
                            <div class="text-center" style="text-align: center; margin-bottom: 5px;">
                                <span class="school-name" style="font-weight: bolder; font-size: 20px;"><?=$settings->name?></span><br>
                                <span class="mail-bag"><?=$settings->address?><br /><?=$settings->email?><br />
                                    <?=$settings->phone?><br />
                                    <span style="margin-left: 20px;">Exams, Statistics and Records Unit</span>.
                                    </span><br>
  

                            </div>
                  <div class="row">
                            <div class="col-md-6 col-xs-6">
                                <br /><br /> <span style="margin-left: 15px;"><small>The Vice Chancellor</small></span><br>
                                <span style="margin-left: 15px;" class="school-name"><?=$settings->rector?></span><br>
                                <span style="margin-left: 15px;" ><small> </small></span><br>
                            </div>
                            <div class="col-md-6 text-center  col-xs-6">
                               <br /><br /> <span><small>The Registrar</small></span><br>
                                <span class="school-name"><?=$settings->registrar?></span><br>
                                <span><small><?=$settings->registrarcerts?></small></span><br>
                            </div>
                  </div>
                            <div class="col-md-4 col-xs-4 col-md-offset-4 col-xs-offset-4" style="text-left: center; margin-bottom: 5px;">
                            <br /><br />    <span class="office-name">OFFICE OF THE REGISTRAR</span><br>
                            </div>
                            <div class="col-md-4 col-xs-4 col-md-offset-8 col-xs-offset-8" style="text-align: right;">
                                <span class="mail-bag float-left"><?php echo date('M j, Y'); ?></span><br />
                            </div>
                            <div class="col-md-12 col-xs-12" style="text-align: left;">
    <?php foreach ( $trequest as $requestdetail): ?>
                                    <span class=""><?php echo ucfirst( $requestdetail->institution); ?></span><br>
                                    <span class="">
                                    <?php echo ucfirst( $requestdetail->address); ?>
                                    </span><br><br>
    <?php endforeach; ?>
                            </div>
                            <div class="col-md-6 col-xs-6" style="text-align: left;">
                                <span><small>ACADEMIC TRANSCRIPT OF</small></span>
                                <span class="school-name"><?php echo ucfirst($student->fname . " " . $student->mname . " " . $student->lname); ?></span><br>
                                <span class="office-name">REGISTRATION NO : <small><?php echo $student->regno ?></small></span><br>
                            </div>
                            <div class="col-md-6 col-xs-6" style="text-align: left;">
                                <span></span><br>
                                <span></span><br>
                                <span class="office-name">Department : <small>
    <?= $student->department->name ?>
                                    </small></span><br><br>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p>We have been asked to forward to you, the academic transcript of the above named student/ex-student of <?=SCHOOL ?>, Nigeria. The Candidate is/was of Our Department of <?php echo $student->department->name; ?>.
                                    <br>
                                    The Transcript is herewith enclosed. You will have to testify yourself that <strong>
    <?php echo ucfirst($student->fname . " " . $student->mname . " " . $student->lname); ?>
                                    </strong> of our record and the one requesting us to send the transcript is one and the same person
                                    <br>The <strong>TRANSCRIPT</strong> is confidential and on no account should the student or other unauthorised person(s) be allowed access to it
                                </p>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p>Please acknowledge receipt of this transcript by sending an email to <?=$settings->email?>, quoting the student's name and the Transcript ID/Student registration number. at the top of this page. 
                                </p>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p>Any other advisory document supplied by the candidate that may be enclosed should be treated as separate  from the transcript and attended at their merit.
                                </p>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p><?=$settings->registrar?><br>
                                    REGISTRAR<br><br><br>
                                </p>
                                <p>
                                    <span><strong>The Transcript Details : </strong></span><br>

                                    <?php
                                    //select distinct session for the student
//                        foreach ($results as $result){
//                            echo "<span>First Year :  ({$result->sessions->sessionname})</span>,";
//                        }
                                    ?>

                                </p>
                            </div>
                            <!--div class="col-md-12 col-xs-12">
                                    <p>TOPS ID : <?php echo $transcript_id; ?></p>
                            </div-->
                            <!--div class="col-md-4 col-xs-4 col-md-offset-4 col-xs-offset-4" style="text-align: center; margin-bottom: 5px;">
                                    <span class="school-name">Imo State Polytechnic</span><br>
                            <span class="mail-bag">P.M.B. 1472, Umuagwo, Imo State, 
              Office of the Registrar Statistic and Record Unit.
              Nigeria</span><br>
                                    <img src="images/logo-icon.png" style="margin-top: 5px;"><br><br><br>
                                    <span class="">Transcript Of Student Academic Records</span><br>
                            </div-->
                            <div class="col-md-6 col-xs-6">
                                <p><strong>FULL NAME : </strong><?php echo ucfirst($student->fname . " " . $student->lname . " " . $student->mname); ?></p>
                                <p><strong>STUDENT REGISTRATION NUMBER : </strong><?php echo  $student->regno ?></p>
                                <p><strong>NATIONALITY : </strong><?php echo  $student->country->name ?></p>
                               
                                <p><strong>PRESENT ADDRESS : </strong>
                                   <?=$student->address?>
                                </p>
                            </div>
                            <div class="col-md-6 col-xs-6" style="text-align: left">
                                <p><strong>SEX : </strong><?php echo $student->gender; ?></p>
                                <p><strong>DATE OF BIRTH : </strong><?= h(date("d-M-Y", strtotime($student->dob))) ?></p>
                                <p><strong>YEAR OF ENTRY : </strong> <?php echo h($student->admissiondate) ?>
                                    <?php
//                                    $regnumb_explode = explode("-", $student->regnumb);
//                                    echo $regnumb_explode[0];
                                    ?>
                                </p><br><br><br>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <div class="row">
                                   <div class="col-md-6 col-xs-6">
                                        <p><strong>SCHOOL /FACULTY: </strong>
                                            <?php echo strtoupper($student->faculty->name); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-xs-6" style="text-align: right">
                                        <p><strong>DEPARTMENT : </strong><?php echo strtoupper($student->department->name); ?></p>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 col-sm-9 col-xs-12">
                                <table id="atatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                                       style="margin-top: 23px;">
                                    <thead>
                                        <tr>

                                            <th>COURSE TITLE</th>
                                            <th>COURSE CODE</th>
                                           <th>UNITS</th>
<!--                                            <th>SCORE</th>-->
                                            <th>GRADE</th>
                                           <th>G POINT</th>
                                           
<!--                                           <th>REMARK</th>-->
                                           <th>SEMESTER</th>
                                            <th>SESSION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    <?php foreach ($student->results as $result): 
        if(($result->grade != "F")&& ($result->level_id==1)){
        ?>
                                            <tr>
                                                <td><?= h($result->subject->name) ?></td>
                                                <td><?= h($result->subject->subjectcode) ?></td>
                                                 <td>
                            <?php
                            //$grade_point = $this->getcreditunit($result->grade);
                            //echo $grade_point->value;
                            echo h($result->subject->creditload);
                            ?>
                                                </td>
<!--                                                <td><?= $this->Number->format($result->score) ?></td>-->
                                                <td><?= h($result->grade) ?></td>
                                                <td> <?= $this->getgradepoint($result->subject->id,$result->grade) ?>  </td>
                                       
<!--                                                <td><?= h($result->remark) ?></td>-->
                                                 <td><?= h($result->semester->name) ?></td>
                                                <td><?= h($result->session->name) ?></td>
                                            </tr>
        <?php } 
      
         if(($result->grade != "F")&& ($result->level_id==3)){?>
             
                                               <tr>
                                                <td><?= h($result->subject->name) ?></td>
                                                <td><?= h($result->subject->subjectcode) ?></td>
                                                 <td>
                            <?php
                            //$grade_point = $this->getcreditunit($result->grade);
                            //echo $grade_point->value;
                            echo h($result->subject->creditload);
                            ?>
                                                </td>
<!--                                                <td><?= $this->Number->format($result->score) ?></td>-->
                                                <td><?= h($result->grade) ?></td>
                                                <td> <?= $this->getgradepoint($result->subject->id,$result->grade) ?>  </td>
                                       
<!--                                                <td><?= h($result->remark) ?></td>-->
                                                 <td><?= h($result->semester->name) ?></td>
                                                <td><?= h($result->session->name) ?></td>
                                            </tr>
      <?php   }
        
           if(($result->grade != "F")&& ($result->level_id==4)){?>
             
                                               <tr>
                                                <td><?= h($result->subject->name) ?></td>
                                                <td><?= h($result->subject->subjectcode) ?></td>
                                                 <td>
                            <?php
                            //$grade_point = $this->getcreditunit($result->grade);
                            //echo $grade_point->value;
                            echo h($result->subject->creditload);
                            ?>
                                                </td>
<!--                                                <td><?= $this->Number->format($result->score) ?></td>-->
                                                <td><?= h($result->grade) ?></td>
                                                <td> <?= $this->getgradepoint($result->subject->id,$result->grade) ?>  </td>
                                       
<!--                                                <td><?= h($result->remark) ?></td>-->
                                                 <td><?= h($result->semester->name) ?></td>
                                                <td><?= h($result->session->name) ?></td>
                                            </tr>
      <?php   }
        
        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
            
                            <?php foreach ($student->results as $result):
                          if($student->level_id==1){ //year 1 results
                              ?>  
                            
                                
                                
                                
                          <?php }
                          if($student->level_id==2){
                              
                          }
                          
                          endforeach; ?>

                            <div class="col-md-12 col-xs-12">
                                <span class="office-name pull-right">FINAL CUMMULATIVE G.P.A : <b><?php $final_gp = $this->calculateCGPA($student->regno);
                                       if($final_gp==0){ echo 'Sorry, No Results Found';}else{echo $final_gp;}
                                        ?></b></span><br>
                                <table class="table table-bordered" id="transtab">
                                    <tbody>
                                        <tr>
                                            <th>Curriculum</th>
                                            <td><?php echo $student->department->name; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Degree</th>
                                            <td>
                                                <?php
                                                $department_explode = explode("/", $student->regno);
                                                //print_r($department_explode);
                                                if ($department_explode[1] == "ND") {
                                                    echo "National Diploma";
                                                } elseif ($department_explode[1] == "HND") {
                                                    echo "Higher National Diploma";
                                                } else {
                                                    echo "Degree Certificate";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Class Of Degree</th>
                                            <td>
                                                <?php if($final_gp==0){     echo 'Sorry, No Result Was Found For This Student :'. str_replace('-', '/', $student->regno);}
                                                elseif ($final_gp >= 4.50) {
                                                    echo "First Class";
                                                } elseif ($final_gp >= 3.50) {
                                                    echo "Second Class Upper";
                                                } elseif ($final_gp >= 2.49)  {
                                                    echo "Second Class Lower";
                                                } elseif ($final_gp >= 2.00 )  {
                                                    echo "Pass";
                                                } else {
                                                    echo "Fail";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Graduation Year</th>
                                            <td> OCTOBER, <?=date('Y')?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!--/. ends create here-->
                            <div class="col-md-6 col-xs-6">
                                <div class="row">
                                    <div class="col-md-6 col-xs-6">
                                        <table class="table table-bordered" id="transtab" style="margin-bottom: 0px !important;">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">CLASSIFICATION DEGREE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>4.50 - 5.00</th>
                                                    <td>FIRST CLASS</td>
                                                </tr>
                                                <tr>
                                                    <th>3.50 - 4.49</th>
                                                    <td>SECOND CLASS UPPER</td>
                                                </tr>
                                                <tr>
                                                    <th>2.49 - 3.49</th>
                                                    <td>SECOND CLASS LOWER</td>
                                                </tr>
                                                <tr>
                                                    <th>2.00 - 2.49</th>
                                                    <td>PASS</td>
                                                </tr>
                                                <tr>
                                                    <th>0.00 - 1.99</th>
                                                    <td>FAIL</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        <table class="table table-bordered" id="transtab" style="margin-bottom: 0px !important;">
                                            <thead>
                                                <tr>
                                                    <th colspan="3">GRADING SYSTEM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>70 - ABOVE</th>
                                                    <td>A</td>
                                                    <td>5.00</td>
                                                </tr>
                                                <tr>
                                                    <th>60 - 69</th>
                                                    <td>B</td>
                                                    <td>4.00</td>
                                                </tr>
                                                <tr>
                                                    <th>50 - 59</th>
                                                    <td>C</td>
                                                    <td>3.00</td>
                                                </tr>
                                                <tr>
                                                    <th>45 - 49</th>
                                                    <td>D</td>
                                                    <td>2</td>
                                                </tr>
                                                <tr>
                                                    <th>40 - 45</th>
                                                    <td>E</td>
                                                    <td>1.00</td>
                                                </tr>
                                           <tr>
                                                    <th>00 - 39</th>
                                                    <td>F</td>
                                                    <td>0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12 hidden-xs" style="margin-top: 5px;">
                               <input class="btn btn-success float-right" type="button" onclick="printDiv('printableArea')" value="Print" />
                <!--             <?= $this->Html->link(__('Download Transcript'), ['controller' => 'Admins', 'action' => 'downloadtranscript',$student->regnumb, $request_id],
                                     ['class'=>'btn btn-primary pull-left']) ?>
                                
                                <div id="editor"></div>
                                <button id="cmd" class="btn btn-primary pull-left">generate PDF</button>-->
                            </div>
                            <?php
                        } else {
                            ?>
                            <h2 style="text-align: center;">NO RESULT HAS BEEN UPLOADED FOR THIS STUDENT..</h2>
                        <?php } ?>
                             
                    </div>
                    <!--/.end x_content-->
                </div>
            </div>
        

<script language="javascript" type="text/javascript">
    
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;

        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = 
          "<html><head><title></title></head><body>" + 
          divElements + "</body>";

        //Print Page
        window.print();

        //Restore orignal HTML
        document.body.innerHTML = oldPage;
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