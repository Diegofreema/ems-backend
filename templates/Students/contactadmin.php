<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Contact Admin</h1>
                        </div>
                        <?= $this->Form->create() ?>
                        <div class="form-group row">
                         
                            <div class="col-sm-12">
                                <?= $this->Form->control('subject', ['label' => 'Title', 'required', 'class' => 'form-control form-control-user2', 'placeholder' => 'message title']) ?>
                            </div>    

                        </div>

                        <br />

                        <?= $this->Form->control('message', ['label' => 'Message  * :', 'type' => 'textarea'
                              , 'placeholder' => 'your message', 'class' => 'ckeditor','required'])
                        ?>




                        <br /> <br />
                        <?= $this->Form->button('Send', ['class' => 'btn btn-primary btn-user btn-block']) ?>
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




