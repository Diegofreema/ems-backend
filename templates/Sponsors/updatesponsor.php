<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
<!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
          <div class="col-lg-12">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Update Sponsor</h1>
              </div>
                 <?= $this->Form->create($sponsor) ?>
            
                <div class="form-group row">
                 
                  <div class="col-sm-6">
                  <?=$this->Form->control('name', ['label' => "Name", 'class' => 'form-control form-control-user2',
                    'id'=>  'Name','placeholder'=>'Name'])?>
                    
                  </div>
                     <div class="col-sm-6">
                   <?=$this->Form->control('phone', ['label' => "Phone", 'class' => 'form-control form-control-user2','id'=>'phone'])?>
               
                  </div>
                </div>
                
                  <div class="form-group row">
                  <div class="col-sm-6">
                  <?= $this->Form->control('emailaddress', ['label' => "Email Address", 'class' => 'form-control form-control-user2','placeholder'=>'email address','type'=>'email'])?>
               
                  </div>
                 
                      
                      <div class="col-sm-6">

                  <?= $this->Form->control('address', [ 'label' => "Address", 'class' => 'form-control form-control-user2'])?>

               
                  </div>
                      
                </div>
        
              <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
             
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
