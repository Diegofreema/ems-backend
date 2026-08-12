<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>

<!-- Begin Page Content -->
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Student Promotion</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                                ?></li>
                            <li class="breadcrumb-item active">Student Promotion</li>
                        </ul>
                    </div>
                </div>
            <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newstudent'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'addmit new student']) ?>
          <!-- Page Heading -->
           <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Promote Students </h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
       <?php
            echo $this->Form->control('department_id',['options'=>$departments,'label'=>'Select Class','onChange'=>"getClassArms(this.value)",'empty'=>'Select Class','class' => 'form-control form-control-user2']);
        ?>
        </div>
        <div class="col-sm-4 mb-3 mb-sm-0" id="classArms">
            <label for="class_arm_id">Select Class Arm</label>
            <select name="class_arm_id" class="form-control form-control-user2" onchange="getstudents()">
                <option value="">Select Class Arm</option>
            </select>
        </div>
        <div class="col-sm-4 mb-3 mb-sm-0">
            <label>&nbsp;</label>
            <div>
                <button type="button" class="btn btn-secondary btn-user btn-block" onclick="clearFilters()">Clear Filters</button>
            </div>
        </div>
            </div>
    </fieldset>
   <br /> <br />
                    <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
         
          
          <?php if(!empty($students)){ ?>
          
          <h1 class="h3 mb-2 text-gray-800">Students Promotion</h1></div>
          
 <?= $this->Form->create(null,['url'=>['controller' => 'Students','action' => 'promotestudents'], 'id' => 'promotionForm']); ?>
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Promote Students</h6>
            </div>
            <div class="card-body">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <?php
                  echo $this->Form->control('department_id',['options'=>$departments,'required','label'=>'Select New Class','empty'=>'Select New Class','class' => 'form-control form-control-user2', 'onChange' => 'getTargetClassArms(this.value)']);
              ?>
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0" id="targetClassArms">
                <?= $this->Form->control('target_class_arm_id', ['label' => 'Select New Class Arm', 'empty' => 'Select New Class Arm', 'class' => 'form-control form-control-user2']) ?>
            </div>
              <div class="table-responsive">
                <table id="datatable-button" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
		            style="margin-top: 23px;">
                  <thead>
            <tr>
           <th ><input type="checkbox" onclick="toggleAllApplicants(this);" name="parentCheck" /> </th>
           <th>S/N</th>
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
         <th scope="col"><?= $this->Paginator->sort('Class') ?></th>
               
               
                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
               
            </tr>
                  </thead>
            
            
             <tfoot>
            <tr>
           <th ><input type="checkbox" onclick="toggleAllApplicants(this);" name="parentCheck" /> </th>
            <th>S/N</th>
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
             
                <th scope="col"><?= $this->Paginator->sort('Class') ?></th>
              
                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
               
            </tr>
              </tfoot>
            
     
            <tbody>
            <?php $count =0; foreach ($students as $student): $count++; ?>
            <tr>
                 <td><?php 
	    echo $this->Form->checkbox('studentids[]', ['id' => $student->id,'hiddenField' => 'N','value' => $student->id]);
	    
	    ?>
                    
	     </td>
             <td><?=$count?></td>
                <td><?= h($student->fname.' '.$student->lname.' '.$student->mname) ?></td>  
                <td><?= $student->has('department') ? $this->Html->link($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : ''), ['controller' => 'Departments', 'action' => 'view', $student->department->id]) : '' ?></td>
                <td> <?= $this->Html->image('../student_files/'.$student->passporturl, ['alt' => 'passport', 'class' => 'img-circle profile_img',
                                    'style' => 'width:80px;height:80px;']) ?>
               </td>
              
                <td><?= h($student->regno) ?></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
                   <?= $this->Form->button(' Promote ',['type' => 'submit','class'=>'btn btn-large btn-success pull-right','onclick'=>'transferEmails(this)']) ?>
                 
                  <?= $this->Form->end() ?>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>

<script>
function getClassArms(departmentid){ 
    $.ajax({
        url: '../ClassArms/getArmsForDepartment/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            // Preserve the label and only replace the select element
            var label = '<label for="class_arm_id">Select Class Arm</label>';
            document.getElementById('classArms').innerHTML = label + response;
        }
    });
}

function getTargetClassArms(departmentid){ 
    $.ajax({
        url: '../ClassArms/getTargetArmsForDepartment/'+departmentid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            // Preserve the label and only replace the select element
            var label = '<label for="target_class_arm_id">Select New Class Arm</label>';
            document.getElementById('targetClassArms').innerHTML = label + response;
        }
    });
}

function clearFilters() {
    document.querySelector('select[name="department_id"]').value = '';
    document.querySelector('select[name="class_arm_id"]').value = '';
    document.getElementById('classArms').innerHTML = '<label for="class_arm_id">Select Class Arm</label><select name="class_arm_id" class="form-control form-control-user2"><option value="">Select Class Arm</option></select>';
}

function getstudents() {
    // This function can be used to trigger search when class arm is selected
    // For now, it's just a placeholder
}

</script>
