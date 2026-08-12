<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
<div class="container-fluid">
     <div style="padding-bottom: 10px; margin-bottom: 20px;">
         
  <div class="p-5">
            <?= $this->Form->create($liveclass) ?>
            <fieldset>
               <div class="form-group row">
         <div class="col-sm-8 mb-4 mb-sm-0">
             <?php $vurl = "https://meet.jit.si/".uniqid('EBUSCED'); ?>                                       
                        <?php
                          echo $this->Form->control('meetinglink', ['label' => 'Meeting Link', 'placeholder' => 'gitsi link',
                              'class' => 'form-control form-control-user2','required'=>'required','value'=>$vurl]);
                        ?>
                    </div>          
                   
                   
               </div>
                
            </fieldset>
          <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>  
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
