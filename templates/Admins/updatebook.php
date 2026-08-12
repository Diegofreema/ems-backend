<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Update Book</h1>
                        </div>
    <?= $this->Form->create($book,['type'=>'file']) ?>
    <fieldset>
       <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('title',['label'=>'Title','class' => 'form-control form-control-user','required','placeholder'=>'book title'])?>
                                </div>
           <div class="col-sm-6 mb-6 mb-sm-0">
            <?= $this->Form->control('author',['label'=>'Author','class' => 'form-control form-control-user','required','placeholder'=>'author'])?>
           </div>
           
       </div>
        <div class="form-group row">
            <div class="col-sm-4 mb-3 mb-sm-0">
            <?= $this->Form->control('pubdate',['label'=>'Date Published','class' => 'form-control form-control-user','required','placeholder'=>'date published'])?>
           </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
            <?php $avail = ['Available'=>'Available','Unavailable'=>'Unavailable'];
                  echo  $this->Form->control('isavailable',['label'=>'Availability','options'=>$avail,'class' => 'form-control form-control-user','required'])?>
           </div>
                                    <div class="col-sm-4 mb-3 mb-sm-0">
            <?= $this->Form->control('isbn',['label'=>'ISBN','class' => 'form-control form-control-user','required','placeholder'=>'isbn'])?>
           </div>
                                    </div>
         <div class="form-group row">
             <div class="col-sm-4 mb-3 mb-sm-0">
             <?= $this->Form->control('bookimage',['label'=>'Cover Photo','class' => 'form-control form-control-user','type'=>'file'])?>
               
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


