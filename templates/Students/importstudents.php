<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Students Bulk Import</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Students Bulk Import</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center"> 
                            <?= $this->Html->link(__(' '), ['action' => 'downloadformat'],
                                    ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'download data format']) ?>
                            <h1 class="h4 text-gray-900 mb-4">Students Bulk Import </h1>
                        </div>
    <?= $this->Form->create(null,['type'=>'file']) ?>
    <fieldset>
        <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('department_id',['options'=>$departments,'required','label'=>'Select Class','empty'=>'Select Class','class' => 'form-control form-control-user2', 'onChange' => 'getClassArms(this.value)']);
        ?>
        </div>
        <div class="col-sm-4 mb-3 mb-sm-0" id="classArms">
            <label for="class_arm_id">Select Class Arm</label>
            <select name="class_arm_id" class="form-control form-control-user2">
                <option value="">Select Class Arm</option>
            </select>
        </div>
        <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('students',['required','label'=>'Upload File','type'=>'file','class' => 'form-control form-control-user']);
        ?>
        </div>
            </div>
    </fieldset>
   <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Define getClassArms function immediately (no jQuery dependency)
window.getClassArms = function(departmentid) { 
    // Use vanilla JavaScript fetch instead of jQuery AJAX
    fetch('<?= $this->Url->build(['controller' => 'ClassArms', 'action' => 'getArmsForDepartment']) ?>/' + departmentid)
        .then(response => response.text())
        .then(data => {
            // Preserve the label and only replace the select element
            var label = '<label for="class_arm_id">Select Class Arm</label>';
            document.getElementById('classArms').innerHTML = label + data;
        })
        .catch(error => {
            console.log('AJAX error:', error);
        });
};

function calldownloder(){
    // Use vanilla JavaScript fetch instead of jQuery AJAX
    fetch('../Students/downloadformat')
        .then(response => response.text())
        .then(data => {
            // Handle response if needed
        })
        .catch(error => {
            console.log('Error:', error);
        });
}



    
    
    
        
    
function getdepartments(facultyid){ 
    // Use vanilla JavaScript fetch instead of jQuery AJAX
    fetch('../../Students/getdapts/' + facultyid)
        .then(response => response.text())
        .then(data => {
            document.getElementById('depts').innerHTML = "";
            document.getElementById('depts').innerHTML = data;
        })
        .catch(error => {
            console.log('Error:', error);
        });
}
</script>