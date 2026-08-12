

<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
//get session data
          $settings = $this->request->getSession()->read('settings');
         //get session for y3
          foreach ($year3 as $result3){
          $session3 = $result3->session->name;}
           //get session for y1
          foreach ($year1 as $result1){
          $session1 = $result1->session->name;}
           //get session for y4
          foreach ($year4 as $result4){
          $session4 = $result4->session->name;}
           //get session for y2
          foreach ($year2 as $result2){
          $session2 = $result2->session->name;}
?>


<!-- Begin Page Content -->
<div class="container-fluid" id="printableArea" style="font-size: 10px;">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"></h1></div>
         

          <!-- DataTales Example -->
          <div >
            
             
              <div class="card-body">
                  
                   <div style="padding:30px 15px">
        <div style="text-align:center">
            <?php echo $this->Html->image($settings->logo, ['alt' => 'EMS',  'style' => 'height:140px; position:relative; bottom:5px']) ?>

            <label style=" font-size:30px; margin-left:20px ; font-family: 'Caprasimo', cursive;">CLARETIAN UNIVERSITY OF NIGERIA</label>
            
        </div>
        <div style="margin-top: -20px;text-align:center; font-size:18px;  padding: 0px;font-family: 'Caprasimo', cursive;">
Maryland Nekede, Old Nekede Rd Umualum, Imo State.</div>
    </div>
                  <div style="text-align:center; font-size:13px;  padding: 0px;font-family: 'Caprasimo', cursive;">
                      (EXAMS’ STATISTICS AND RECORD UNIT)<br>
STUDENT’S ACADEMIC RECORD</div>
    </div>
    <div style="padding:5px 5px; font-size:14px; font-weight:bold">
        <table style="width:100%">
            <tr>
                <td style="padding:5px">
                    NAME OF STUDENT
                </td>
                <td style="padding:5px">
                    SEX
                </td>
                <td style="padding:5px">
                    DATE OF BIRTH
                </td>
                <td style="padding:5px">
                    REG. NO
                </td>
            </tr>
            <tr>
                <td style="padding:5px">
                    <label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->fname . " " . $student->lname . " " . $student->mname); ?></label>
                   
                </td>
                <td style="padding:5px">
                   <label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->gender ); ?></label>
                   
                </td>
                <td style="padding:5px">
                    <label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->dob ); ?></label>
                   
                </td>
                <td style="padding:5px">
                    <label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->regno ); ?></label>
                   
                </td>
            </tr>
        </table>
    </div>
    <div style="padding:5px 10px; font-size:14px; font-weight:bold; text-align: center">
       PERMANENT HOME ADDRESS <br />
<label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->address ); ?></label>
                   
    </div>
    <div style="padding:5px 5px; font-size:14px; font-weight:bold">
        <table style="width:100%">
            <tr>
                <td style="padding:5px">
                    FACULTY
                </td>
                <td style="padding:5px">
                    DEPARTMENT
                </td>
                
            </tr>
            <tr>
                <td style="padding:5px">
                    <label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->faculty->name ); ?></label>
                   
                </td>
                <td style="padding:5px">
                   <label style="border: 2px solid #1F90BF; padding:2px 5px" ><?php echo ucfirst($student->department->name ); ?></label>
                    
                </td>
                
            </tr>
        </table>
    </div>
                  
             <?= $this->Flash->render() ?>
                        <?php
                        if (!empty($year1->toArray())) {
                            ?>



                            <div class="col-md-12 col-sm-9 col-xs-12">
                                <style>
                                    .table td,.table th,.table tr, .table thead{
                                        border: 2px solid #1F90BF !important; padding: 5px !important;
                                    }
                                </style>
                                <table id="atatable-buttons" class="table table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                                       style="margin-top: 23px; ">
                                   
                                    <thead> 
                                        <tr style="border: 2px solid blue">

                                           
                                            <th style="width: 130px;">COURSE CODE</th>
                                            <th>COURSE TITLE</th>
                                          <th style="width: 150px;">CREDIT HOURS</th>
<!--                                            <th>SCORE</th>-->
                                            <th style="width: 150px;">LETTER GRADE</th>
                                           <th style="width: 150px;">GRADE POINT</th>
                                           

                                        </tr>
                                         <tr><td></td><td> <b> Year 1,    Session : <?=$session1?></b><br />
                          </td><td></td><td></td><td></td></tr>
                                    </thead>
                                    <tbody>
    <?php 
    $grade_point1 = 0; $unit1 = 0; $unit2 = 0; $unit3 = 0; $unit4 = 0;
     $grade_point2 = 0;
      $grade_point3 = 0;
       $grade_point4 = 0;
       $total_unit = 0;
    foreach ($year1 as $result): 
        $session = $result->session->name;

        if(($result->grade != "F")&& ($result->level_id==1)){
       $grade_point1 += $this->getgradepoint($result->subject_id,$result->grade);
       $unit1 += $result->subject->creditload;
       
        ?>
                                            <tr style="border: 2px solid blue">
                                               
                                                <td><?= h(strtoupper($result->subject->subjectcode)) ?></td>
                                                <td><?= h(ucwords(strtolower($result->subject->name))) ?></td>
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
                                       

                                            </tr>
        <?php }  
        endforeach; ?>
      <tr><td></td><td> 
              <b>  Year 1 GPA = <?= number_format(($grade_point1/$unit1),2)?></b></td><td><b><?=$unit1 ?></b></td><td></td><td><b><?= number_format($grade_point1,2) ?></b> </td></tr>
                                   
                                    </tbody>
                          
                                </table>
                            </div>
         <?php if(!empty($year2->toArray())){ ?>   
     <div class="col-md-12 col-sm-9 col-xs-12">
                                <table id="atatable-buttons" class="table table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                                       style="margin-top: 23px;">
                                   
                                    <thead> 
                                         <tr style="border: 2px solid blue">

                                           
                                            <th style="width: 130px;">COURSE CODE</th>
                                            <th>COURSE TITLE</th>
                                          <th style="width: 150px;">CREDIT HOURS</th>
<!--                                            <th>SCORE</th>-->
                                            <th style="width: 150px;">LETTER GRADE</th>
                                           <th style="width: 150px;">GRADE POINT</th>
                                           

                                        </tr>
                                        <tr><td></td><td> <b> Year 2,    Session : <?=$session2?></b><br />
                          </td><td></td><td></td><td></td></tr>
                                    </thead>
                                    <tbody>
    <?php foreach ($year2 as $result): 
       // $session2 = $result->session->name;

        if(($result->grade != "F")&& ($result->level_id==2)){
          $grade_point2 += $this->getgradepoint($result->subject_id,$result->grade);
         // echo $result->subject->creditload.'<br />';
         $unit2 +=$result->subject->creditload;
         //echo $unit4;
        ?>
                                            <tr>
                                               
                                                <td><?= h(strtoupper($result->subject->subjectcode)) ?></td>
                                                <td><?= h(ucwords(strtolower($result->subject->name))) ?></td>
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
                                       

                                            </tr>
                                           
        <?php }  
        endforeach; ?>
          <tr><td></td><td> 
                                                    <b>  Year 2 GPA = <?= number_format(($grade_point2/$unit2),2)?></b></td><td><b><?=$unit2 ?></b></td><td></td><td><b><?= number_format($grade_point2,2) ?></b> </td></tr>
                                   
                                    </tbody>
                               
                                </table>
                            </div> 
         <?php }  if(!empty($year3->toArray())){ ?> 
              
                              <div class="col-md-12 col-sm-9 col-xs-12">
                                <table id="atatable-buttons" class="table table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                                       style="margin-top: 23px;">
                                   
                                    <thead> 
                                         <tr style="border: 2px solid blue">

                                           
                                            <th style="width: 130px;">COURSE CODE</th>
                                            <th>COURSE TITLE</th>
                                          <th style="width: 150px;">CREDIT HOURS</th>
<!--                                            <th>SCORE</th>-->
                                            <th style="width: 150px;">LETTER GRADE</th>
                                           <th style="width: 150px;">GRADE POINT</th>
                                           

                                        </tr>
                                         <tr><td></td><td> <b> Year 3,    Session : <?=$session3?></b><br />
                          </td><td></td><td></td><td></td></tr>
                                    </thead>
                                    <tbody>
    <?php foreach ($year3 as $result3): 
        $session3 = $result3->session->name;

        if(($result3->grade != "F")&& ($result3->level_id==3)){
         $grade_point3 += $this->getgradepoint($result3->subject_id,$result3->grade);
       $unit3 += $result3->subject->creditload;
        ?>
                                            <tr>
                                               
                                                <td><?= h(strtoupper($result3->subject->subjectcode)) ?></td>
                                                <td><?= h(ucwords(strtolower($result3->subject->name))) ?></td>
                                                 <td>
                            <?php
                            //$grade_point = $this->getcreditunit($result->grade);
                            //echo $grade_point->value;
                            echo h($result3->subject->creditload);
                            ?>
                                                </td>
<!--                                                <td><?= $this->Number->format($result3->score) ?></td>-->
                                                <td><?= h($result3->grade) ?></td>
                                                <td> <?= $this->getgradepoint($result3->subject->id,$result3->grade) ?>  </td>
                                       

                                            </tr>
                                            
        <?php }  
        endforeach; ?>
             <tr><td></td><td> 
                                                    <b>  Year 3 GPA = <?= number_format(($grade_point3/$unit3),2)?></b></td><td><b><?=$unit3 ?></b></td><td></td><td><b><?= number_format($grade_point3,2) ?></b> </td></tr>
                                   
                                    </tbody>
                         
                                </table>
                            </div>
         <?php }  if(!empty($year4->toArray())){ ?> 
                            
                              <div class="col-md-12 col-sm-9 col-xs-12">
                                <table id="atatable-buttons" class="table table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                                       style="margin-top: 23px;">
                                   
                                    <thead> 
                                         <tr style="border: 2px solid blue">

                                           
                                            <th style="width: 130px;">COURSE CODE</th>
                                            <th>COURSE TITLE</th>
                                          <th style="width: 150px;">CREDIT HOURS</th>
<!--                                            <th>SCORE</th>-->
                                            <th style="width: 150px;">LETTER GRADE</th>
                                           <th style="width: 150px;">GRADE POINT</th>
                                           

                                        </tr>
                                        <tr><td></td><td>  <b>Year 4,    Session : <?=$session4?></b><br />
                          </td><td></td><td></td><td></td></tr>
                                    </thead>
                                    <tbody>
    <?php foreach ($year4 as $result): 
        $session4 = $result->session->name;

        if(($result->grade != "F")&& ($result->level_id==4)){
           // echo $this->getgradepoint($result->subject_id,$result->grade).'<br />';
          $grade_point4 += $this->getgradepoint($result->subject_id,$result->grade);
         // echo $result->subject->creditload.'<br />';
         $unit4 +=$result->subject->creditload;
         //echo $unit4;
        ?>
                                            <tr>
                                              
                                                <td><?= h(strtoupper($result->subject->subjectcode)) ?></td>
                                                 <td><?= h(ucwords(strtolower($result->subject->name))) ?></td>
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
                                       

                                            </tr>
        <?php }  
        endforeach; ?>
                                            <tr><td></td><td> 
                                                    <b>  Year 4 GPA = <?= number_format(($grade_point4/$unit4),2)?></b></td><td><b><?=$unit4 ?></b></td><td></td><td><b><?= number_format($grade_point4,2) ?></b> </td></tr>
                                    </tbody>
                        
                                </table>
                            </div>
         <?php } ?>

                            <div >
                                <span class="office-name pull-right"> <b>FINAL CUMMULATIVE G.P.A: <?php $final_gp = $this->calculateCGPA($student->regno).
                                        '<br /> FOR THE AWARD OF BACHELORS DEGREE';
                                       if($final_gp==0){ echo 'Sorry, No Results Found';}else{echo $final_gp;}
                                        ?></b></span><br>
                            </div>
              <table style="width:100%; border:none">
                  <tr>
                      <td valign="top" style="width:50%">
                          <table id="transtab" style="border:none; width:300px">
                                            
                                            <tbody>
                                                <tr style="font-weight: bold">
                                                    <td>SCORE</td>
                                                    <td>GRADE QUALITY</td>
                                                    <td>POINT</td>
                                                </tr>
                                                <tr>
                                                    <td>70 - ABOVE</td>
                                                    <td>A</td>
                                                    <td>5.00</td>
                                                </tr>
                                                <tr>
                                                    <td>60 - 69</td>
                                                    <td>B</td>
                                                    <td>4.00</td>
                                                </tr>
                                                <tr>
                                                    <td>50 - 59</td>
                                                    <td>C</td>
                                                    <td>3.00</td>
                                                </tr>
                                                <tr>
                                                    <td>45 - 49</td>
                                                    <td>D</td>
                                                    <td>2.00</td>
                                                </tr>
                                                <tr>
                                                    <td>40 - 45</td>
                                                    <td>E</td>
                                                    <td>1.00</td>
                                                </tr>
                                           <tr>
                                                    <td>00 - 39</td>
                                                    <td>F</td>
                                                    <td>0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                      </td>
                      <td >
                          <div style="padding: 5px;">
                              <p style="font-weight: bold">DATE OF GRADUATION</p>
                              <label style="padding: 3px 5px; border:1px solid #1F90BF"> OCTOBER, <?=$graduation_years+4?></label>
                          </div>
                          <div style="padding: 5px;">
                              <p style="font-weight: bold">CERTIFICATE AWARDED</p>
                              <label style="padding: 3px 5px; border:1px solid #1F90BF"> <?=$student->programme->name?></label>
                          </div>
                          <div style="padding: 5px;">
                              <p style="font-weight: bold">CLASS/ DIVISION</p>
                              <label style="padding: 3px 5px; border:1px solid #1F90BF"> <?php if($final_gp==0){     echo 'Sorry, No Result Was Found For This Student :'. str_replace('-', '/', $student->regno);}
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
                                                ?></label>
                          </div>
                          
                      </td>
                  </tr>
                  <tr>
                      <td style="width:50%">
                          <table id="transtab" style="width: 350px; border:none;">
                                            <thead><b>CLASSFICATION OF RESULT
                                                CUMMULATIVE</b><br><b> QUALITY POINT AVERAGE
CLASS/ADIVISION</b></thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width:100px">4.50 - 5.00</td>
                                                    <td style="width:30px"> - </td>
                                                    <td>FIRST CLASS</td>
                                                </tr>
                                                <tr>
                                                    <td>3.50 - 4.49</td>
                                                    <td> - </td>
                                                    <td>SECOND CLASS UPPER</td>
                                                </tr>
                                                <tr>
                                                    <td>2.49 - 3.49</td>
                                                    <td> - </td>
                                                    <td>SECOND CLASS LOWER</td>
                                                </tr>
                                                <tr>
                                                    <td>2.00 - 2.49</td>
                                                    <td> - </td>
                                                    <td>PASS</td>
                                                </tr>
                                                <tr>
                                                    <td>0.00 - 1.99</td>
                                                    <td> - </td>
                                                    <td>FAIL</td>
                                                </tr>
                                            </tbody>
                                        </table>
                      </td>
                      <td align="center">
                          <strong>CERTIFIED BY
                              <br><br><br>
                              ........................................
                              <br>
                              FOR: REGISTRAR</strong>
                      </td>
                  </tr>
              </table>
                                
                           

                            <!--/. ends create here-->
                            <div class="col-md-6 col-xs-6">
                                <div class="row">
                                    <div class="col-md-6 col-xs-6">
                                        
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12 hidden-xs" style="margin-top: 5px;">
                              
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
<br /><br /><br />
 <input class="btn btn-success float-right" type="button" onclick="printDiv('printableArea')" value="Print" />
         <br /><br /><br /><br />   </div>
        

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