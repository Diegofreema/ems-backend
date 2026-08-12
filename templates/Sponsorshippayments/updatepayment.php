<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
<!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
          <div class="col-lg-12">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Update Sponsorship Payment</h1>
              </div>
                    <?= $this->Form->create($sponsorshippayment) ?>
            
                <div class="form-group row">
                 
                  <div class="col-sm-6">
                  <?=$this->Form->control('sref', ['label' => false, 'class' => 'form-control form-control-user2','readonly',
                    'id'=>  'exampleLastName'])?>
                    
                  </div>
                     <div class="col-sm-6">
                   <?=$this->Form->control('amount', ['label' => false, 'class' => 'form-control form-control-user2','id'=>'exampleFirstName'])?>
               
                  </div>
                </div>

                  <div class="col-sm-6">
                      <?php $pstatus = ['completed'=>'completed','pending'=>'pending','canceled'=>'canceled'] ?>
                  <?= $this->Form->control('paystatus', ['options' => $pstatus, 'label' => false, 'class' => 'form-control form-control-user2'])?>

               
                  </div>
                
              
                
              <br /> <br />
                        <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-sm float-right']) ?>
                        <?= $this->Form->end() ?>
              </div>
             
            </div>
          </div>
        </div>
      </div>
    </div>

 