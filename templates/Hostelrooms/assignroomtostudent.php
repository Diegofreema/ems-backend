<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Assign Hostel Room</h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
           <div class="form-group row">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?= $this->Form->control('hostel_id', ['label'=>'Select Hostel','options' => $hostels, 'empty'=>'Select Hostel','required','onChange'=>'getrooms(this.value)','class' => 'form-control form-control-user2'])?>
                                  
                                </div>
               <div class="col-sm-4 mb-3 mb-sm-0">
                   <?= $this->Form->control('hostelroom_id', ['label'=>'Select Hostel Room','options' => $hostelrooms,'required','empty'=>'Select Room','class' => 'form-control form-control-user2','id'=>'rooms'])?>
                                  
               </div>
               
          
            <div class="col-sm-4 mb-3 mb-sm-0">
                  <?php
            echo $this->Form->control('student_id', ['options' => $students,'label'=>'Assign Students','class'=>'select2_multiple form-control form-control-user']);
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
    
        function getrooms(roomid){ 

    $.ajax({
        url: '../../../Hostelrooms/getrooms/'+roomid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('rooms').innerHTML = "";
            document.getElementById('rooms').innerHTML = response;
            //location.href = redirect;
        }
    });

}
    </script>