<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Admission/Acceptance Conditions</h1>
                        </div>
<?= $this->Form->create($admisioncondition) ?>
    <fieldset>
       
             <?= $this->Form->control('conditiond', ['label' => 'Conditions  * :', 'type' => 'textarea'
                              , 'placeholder' => 'your message', 'class'=>'ckeditor','required'])?>
                        
           
         
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
<?=
  $this->Html->script(['jquery.min', 'ckeditor/ckeditor'])
?>

<?= $this->fetch('script') ?>


