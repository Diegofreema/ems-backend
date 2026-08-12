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
                                    <?= $settings->email ?></b>

                          
                       
                        </div>
                        

                    </div>
                <div class="card-body">
                    If you have been screened and your name is on the list of successful 
                    candidates ready for admission, kindly follow the instructions below to generate
                    your fee invoices and make payment(s)
                    <br /> <br /> <br />
                    <ol>
                        <li>Enter you application number</li>   
                        <li>Select the fee you are meant to pay (Not sure? please ask call or whatsApp : 07036614567 or info@claretianuniversity.edu.ng)</li> 
                  <li>Click on "Generate Invoice"</li> 
                  <li>On the invoice page, you can instantly pay online using your card or visit a bank branch</li> 
                  <ol>
                      <li><b>Pay online?</b> Click on any of the "Pay Now" buttons</li>
                       <li>Enter your card details and pay</li> 
                        <li>Print and keep your receipt</li>  
                  </ol>
                   <ol>
                      <li><b>Pay at the bank?</b> Click on "Print Invoice"</li>
                       <li>Visit any commercial bank with the invoice and make a payment
                           in favour of Claretian University via Interswitch PayDirect platform </li> 
                        <li>keep your receipt as issued by the bank</li>  
                  </ol>
                    </ol>
                    <br /> <br /> <br />
                    <div >
         <?= $this->Form->create(null) ?>
          <div class="form-group row">
                                 <div class="col-md-6 mb-3 mb-sm-0">
<?= $this->Form->control('application_no', ['label' => 'Application Number', 'placeholder' => ' Enter your Application',
      'class' => 'form-control form-control-user2', 'required','id'=>'application_id'])
?>
                                </div>
              <div class="col-md-6 mb-3 mb-sm-0">
<?= $this->Form->control('fee_id', ['label' => 'Select Fee To Pay', 'options' => $fees,
      'class' => 'form-control form-control-user2', 'required'])
?>
                                </div>
  </div>
          
       <br />
          <?= $this->Form->button('Generate Invoice', ['class' => 'btn btn-primary btn-lg']) ?>
<?= $this->Form->end() ?> <br /> <br />
      </div>   </div>   </div>
 <br /> <br />
           
        </div>
    </div> 
</div>
    </div>


