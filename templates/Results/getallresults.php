<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$settings = $this->request->getSession()->read('settings');
?>

<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
         
               
              <div class="card shadow mb-4 PrintDis" id="printableArea">  <br /><br /><br />
                <?php if(!empty($results)){  ?>
           <div class="row"><br /><br />
               <div class="col-sm-3 m-b-20" >
                      <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100,'style'=>"margin-left: 15px;"])?>
                         
                            <br /><br /><br />

                            
                        </div>
                        <div class="col-sm-6 m-b-20 text-center">
                            
                          
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 30px;"><?=$settings->name?></strong><br />
                                <b style="font-size: 23px;">  <?=$settings->address?><br />
                               <?=$settings->email?><br /></b>
                               
                                <b style="font-size: 21px;"> Student Results </b></h1>
                        
                    <br />    </div>
                        <div class="col-sm-3 m-b-20">
                    <?=$this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'Passport', 'class' => 'img-responsive float-right','height'=>100,'style'=>'margin-right: 15px;'])?>
    
                        </div>
                        
                    </div>
           
             <div style="margin-left: 30px; font-size: 19px; font-family: sans-serif;">      
                <div>Name : <?=  ucfirst($student->fname. ' '.$student->lname.' '.$student->mname) ?></div>
                <div>Registration Number : <?=$student->regno?></div>
                <div>Department : <?=$student->department->name?></div>
                <?php foreach ($results as $result){
                    $chosenSession = $result->session->name;
                    $chosenSemester = $result->semester->name;
                }  ?>
                <div>Session : <?=$chosenSession?></div>
                <div>Semester : <?=$chosenSemester?></div>
             </div>
             <br />
                 <div class="card shadow mb-4">
                   
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold">Student Results </h6>
            </div>
                     
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
             <th>Course</th>
             <th >Course Code</th>
             
                <th>Score</th>
<!--                  <th>Class</th>-->
                <th>Grade</th>
               
                <th>Credit Load</th>
            </tr>
                  </thead>
                  <tfoot>
                       <tr>
              
                <th>Course</th>
               <th >Course Code</th>
            
                <th>Score</th>
<!--                 <th>Class</th>-->
                <th>Grade</th>
              
                <th >Credit Load</th>
                
            </tr>
                  </tfoot>
      
        <tbody>
            <?php $total_unit =0; $quality_point =0;
            foreach ($results as $result): $total_unit+=$result->creditload; ?>
            <tr>
              
                 <td><?=$result->subject->name?></td>
               <td><?= $result->subject->subjectcode?></td>
<!--                <td><?= $result->session->name ?></td>-->
                <td><?= $this->Number->format($result->total) ?></td>

                <td><?= h($result->grade) ?></td>
              
                <td><?= $this->Number->format($result->creditload) ?></td>
                <?php $quality_point += $this->calculategpa($result->grade,$result->creditload)   ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <div class="watermark"> 
  <p>WORKING COPY!</p>
</div>
        
     </table>
                   CGPA : <?= $this->calculateCGPA($student->regno) ?>
                  
                   <div class="pull-right"> GPA:<?=number_format($quality_point / $total_unit, 2);  ?> </div>
              </div>
            </div>
          </div>
                <?php }?>
             <br />  <button class="btn btn-primary float-left DontPrint" onclick="printDiv('printableArea')" ><i class="fa fa-print fa-lg"></i> Print Slip</button>
                <br />  <br />
            </div>
          
             </div>
              </div>

<script>

    function printDiv(divName) { //alert('am called');
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;

    }

</script>

<style>
/* (A) PAGE WATERMARK */
#watermark {
  /* STICK AT BOTTOM RIGHT */
  position: fixed;
  bottom: 10px;
  right: 10px;
  z-index: 999;
 
  /* TRANSPARENCY */
  opacity: 0.5;
 
  /* COSMETICS */
  text-align: right;
  color: red;
   font-size: 52px;
  /* disable select and copy  */
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}

.watermark {
    position: fixed;
    opacity: 0.2;
    /* Safari */
    -webkit-transform: rotate(-60deg);
    /* Firefox */
    -moz-transform: rotate(-60deg);
    /* IE */
    -ms-transform: rotate(-60deg);
    /* Opera */
    -o-transform: rotate(-60deg);
    /* Internet Explorer */
    filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
    position: absolute;
    font-size: 100px;
    margin-top: -50px;
    margin-left: -30px;
    white-space: nowrap;
 }
 
 @media Print
	{
            body {visibility: hidden}
		.PrintDis
		{
			visibility: visible;
                        position:fixed; top:0; left:0
		}
				
	}
</style>
        

