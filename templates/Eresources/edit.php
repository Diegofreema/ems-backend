<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Resource</h1>
                        </div>
                        <?= $this->Form->create($eresource,['type'=>'file']) ?>
                        <fieldset><legend>Update E-Resource</legend>
                          

                            <div class="form-group row">
                                   <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('title', ['label' => 'Title', 'placeholder' => 'title',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('pubdate', ['label' => 'Date Published', 'placeholder' => 'date ',
                                          'class' => 'form-control form-control-user2', 'required','type'=>'date'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('isbn', ['label' => 'ISBN', 'placeholder' => 'ISBN ',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                             
                            </div>
                            
             <div class="form-group row">
                                   <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('author', ['label' => 'Author', 'placeholder' => 'author',
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('department_id', ['label' => 'Department', 'options' => $departments,
                                          'class' => 'form-control form-control-user2', 'required'])
                                    ?>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('filenameurls', ['label' => 'File', 
                                          'class' => 'form-control form-control-user2', 'required','type'=>'file'])
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
