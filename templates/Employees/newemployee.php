<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
<!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
          <div class="col-lg-12">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">New Employee</h1>
              </div>
            <?= $this->Form->create($employee,['type'=>'file']) ?>
            <fieldset>
                 <div class="form-group row">
                  <div class="col-sm-4 mb-3 mb-sm-0">
                    <?=  $this->Form->control('sname', ['label' => 'Surname', 'class' => 'form-control form-control-user2',
                        'placeholder'=>'surname']);?>
                      </div>
                    
                     <div class="col-sm-4">
                  <?=$this->Form->control('fname', ['label' =>'First Name', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'first name'])?>
                    
                  </div>
                    
                      <div class="col-sm-4">
                  <?=$this->Form->control('mname', ['label' => 'Middle Name', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'middle name'])?>
                    
                  </div>
               
                </div>
                
                <div class="form-group row">
                  <div class="col-sm-4 mb-3 mb-sm-0">
                    <?=  $this->Form->control('phone', ['label' => 'Phone', 'class' => 'form-control form-control-user2',
                        'placeholder'=>'phone']);?>
                      </div>
                    
                     <div class="col-sm-4">
                  <?=$this->Form->control('dod', ['label' =>'Date of Birth', 'class' => 'form-control datetimepicker',
                    'id'=>  'exampleLastName','placeholder'=>'date of birth'])?>
                    
                  </div>
                    
                      <div class="col-sm-4">
                  <?=$this->Form->control('email', ['label' => 'Email Address', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'email address','type'=>'email'])?>
                    
                  </div>
               
                </div>
                
                <div class="form-group row">
                  <div class="col-sm-4 mb-3 mb-sm-0">
                    <?=  $this->Form->control('state_id', ['label' => 'State of Origin', 'class' => 'form-control form-control-user2',
                        'placeholder'=>'state of origin','options' => $states,'onChange'=>'getlgas(this.value)']);?>
                      </div>
                    
                     <div class="col-sm-4">
                  <?=$this->Form->control('lga_id', ['label' =>'LGA', 'class' => 'form-control form-control-user2',
                    'id'=>  'lga','placeholder'=>'LGA','options' => $lgas])?>
                    
                  </div>
                    
                      <div class="col-sm-4">
                  <?=$this->Form->control('hqn', ['label' => 'Highest Qualification', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'highest qualification'])?>
                    
                  </div>
               
                </div>
                
                 <div class="form-group row">
                  <div class="col-sm-4 mb-3 mb-sm-0">
                    <?=  $this->Form->control('staffgrade_id', ['label' => 'State of Origin', 'class' => 'form-control form-control-user2',
                        'options' => $staffgrades]);?>
                      </div>
                    
                     <div class="col-sm-4">
                  <?=$this->Form->control('staffdepartment_id', ['label' =>'LGA', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','options' => $staffdepartments])?>
                    
                  </div>
                    
                      <div class="col-sm-4">
                  <?php $genders = ['Male'=>'Male','Female'=>'Female'];
                  echo $this->Form->control('gender', ['label' => 'Gender', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName','placeholder'=>'gender','options'=>$genders])?>
                    
                  </div>
               
                </div>
                 <div class="form-group row">
                  <div class="col-sm-4 mb-3 mb-sm-0">
                    <?=  $this->Form->control('photo', ['label' => 'Photo', 'class' => 'form-control form-control-user2',
                        'type' => 'file']);?>
                      </div>
                    
                     <div class="col-sm-8">
                  <?=$this->Form->control('address', ['label' =>'Address', 'class' => 'form-control form-control-user2',
                    'id'=>  'exampleLastName'])?>
                    
                  </div>
                    
                      
               
                </div>
                
                <div class="form-group row">
                  <div class="col-sm-12 mb-3 mb-sm-0">
                    <?=  $this->Form->control('profile', ['label' => 'Profile', 'class' => 'form-control form-control-user2',
                        'rows' =>6,'cols'=>8]);?>
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
    
    function getlgas(stateid){ 

    $.ajax({
        url: '../Students/getlgas/'+stateid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('lga').innerHTML = "";
            document.getElementById('lga').innerHTML = response;
            //location.href = redirect;
        }
    });

}

    
    </script>