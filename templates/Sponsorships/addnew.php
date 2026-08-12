<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">

          <!-- Page Heading -->
           <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search Students </h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
       <?php
            echo $this->Form->control('department_id',['options'=>$departments,'label'=>'Select Department','empty'=>'Select Department',
                'class' => 'select2_multiple form-control form-control-user2','onChange'=>'getthestudents(this.value)']);
        ?>
        </div>
             <div class="col-sm-6 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('level_id',['options'=>$levels,'required','label'=>'Select Level','empty'=>'Select Level',
                'class' => 'form-control form-control-user2','onChange'=>"getlevelstudent(this.value)",]);
        ?>
        </div>
             
       
            </div>
    </fieldset>
   <br /> <br />
                   
                        <?= $this->Form->end() ?>
                    </div>
         
          
       
        </div>


<div style="padding-bottom: 10px; margin-bottom: 20px;">

          <!-- Page Heading -->
           <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Select Students </h1>
                        </div>
            <?= $this->Form->create($sponsorship) ?>
            <fieldset>
                <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
              
               <?= $this->Form->control('sponsor_id', ['label'=>'Select Sponsor','options' => $sponsors,'class' => 'form-control form-control-user2'])?>
                                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
              
               <?php 
               echo $this->Form->control('fees._ids', ['label'=>'Select Fees','options' => $fees,'class' => 'form-control select2_multiple'])?>
                                    </div>
                     <div class="col-sm-4 mb-3 mb-sm-0">
                   <?= $this->Form->control('session_id', ['label'=>'Select Session','options' => $sessions,'class' => 'form-control form-control-user2'])?>
                         </div>
                    
               <div class="col-sm-12 mb-6 mb-sm-0"><br />
                 <?=$this->Form->control('student_id', ['label'=>'Select Students','options' => $students, 'class' => 'select2_multiple form-control form-control-user',
                     'required','id'=>'students']);
                ?>
                   </div>
                                </div>
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user float-right']) ?>
<?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
    
        function getthestudents(deptid){ 

    $.ajax({
        url: '../Sponsorships/getstudentsindept/'+deptid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
            document.getElementById('students').innerHTML = "";
            document.getElementById('students').innerHTML = response;
            //location.href = redirect;
        }
    });

}


      function getlevelstudent(levelid){ 

    $.ajax({
        url: '../Sponsorships/getstudentsinlevel/'+levelid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
            document.getElementById('students').innerHTML = "";
            document.getElementById('students').innerHTML = response;
            //location.href = redirect;
        }
    });

}

</script>