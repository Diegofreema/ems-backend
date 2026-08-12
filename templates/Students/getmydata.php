<?php $settings = $this->request->getSession()->read('settings') ?>
<!-- Page Content -->
<div class="container">
<div class="content container-fluid">

    <div class="row" id="printableArea">

        <div class="col-md-12">
      
            
            <div class="card" style="padding-left: 20px;"><br /><br /><br />
                <div class="row">
                        
                        <div class="col-sm-3 m-b-20">
                            <?= $this->Html->image("android-chrome-512x512.png", ['alt' => 'LOGO', 'class' => 'inv-logo', 'height' => 100, 'width' => 130]) ?>

                            <br /><br /><br />


                        </div>
                        <div class="col-sm-6 m-b-20 text-center">


                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?= $settings->name ?></strong><br />
                                <b style="font-size: 23px;">  <?= $settings->address ?><br />
                                    <?= $settings->email ?><br /></b>

                            <br />   
                       
                        </div>
                        

                    </div>
                <div class="card-body">
                    <div >
         <?= $this->Form->create(null) ?>
          <div class="form-group row">
                                 <div class="col-md-12 mb-3 mb-sm-0">
<?= $this->Form->control('application_no', ['label' => false, 'placeholder' => ' Enter your application number to retrieve your details',
      'class' => 'form-control form-control-user2', 'required','id'=>'application_id'])
?>
                                </div>
  </div>
          
       <br />
          <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-lg']) ?>
<?= $this->Form->end() ?> <br /> <br />
      </div>   </div>   </div>
 <br /> <br />
      
        </div>
    </div> 
</div>
    </div>
<!-- /Page Content -->
<script>

    function printDiv(divName) { //alert('am called');
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;

    }

</script>

