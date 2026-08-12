<div class="container" id="printableArea">
 <?php $settings = $this->request->getSession()->read('settings')?>
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <br />
             
                <div class="col-lg-12">
                    <div class="p-5">
                      
                       <div class="text-center">
                           <div class="col-md-3">
                <?=$this->Html->image($settings->logo, ['alt' => 'LOGO', 'class' => 'img-responsive float-left','height'=>100,'width'=>130])?>
                </div>  
                            <h1 class="h4 text-gray-900 mb-4"><strong style="font-size: 28px;"><?=$settings->name?></strong><br />
                                <b style="font-size: 23px;">  <?=$settings->address?></b><br />
                               
                                <b style="font-size: 21px;"> Fee Payment Invoice- <?= $fee->name?> </b></h1>
                        
                        </div>
                       
Name : <?=$student->fname.' '.$student->lname  ?><br />
Registration Number : <?=$student->regno ?><br />
Department : <?=$student->department->name ?><br />
Class : <?=$student->level->name ?><br />
Program : <?=$student->programe->name ?><br />
 Address :  <?=$student->address  ?>      <br /> 
 Amount : ₦<?= number_format($transaction->amount)  ?><br />
 Payee ID : <strong><?=$transaction->payref  ?></strong><br />
 Transaction Date : <?= date('D d M, Y')?>
                        <br />
                          
                        <?= $this->Form->create() ?>
<input name="product_id" type="hidden" value="6207" />
<input name="pay_item_id" type="hidden" value="103" />
<input name="amount" type="hidden" value="50000" />
<input name="currency" type="hidden" value="566" />
<input name="site_redirect_url" type="hidden" value="http://netpro.ng" />
<input name="txn_ref" type="hidden" value="AB-12385_TT" />
<input name="cust_id" type="hidden" value="AD99" />
<input name="hash" type="hidden" value="B48FEE432779ED2B2532677AD49C45EC03541B4555D6F439C1174B8F3D7A83E4B9B40223538E1EB5D834A7C1D04A32E984A41DBE2D8565B129B013BE1D1456DF" />

<!-- <div class="form-group">
	<div class="col-sm-4 float-left">
		<input type="submit" class="btn btn-success" name="submit" value="Pay Online" />
	</div>
     
</div>-->
<br />
Note : To pay through the bank, please print this page or copy the PAYEE ID (<?=$transaction->payref ?>) go to the bank and make an 
InterSwitch Imopoly Payment.	      
                         <br /> <br />
                     <?= $this->Form->end() ?>
                       
                    </div>
                  </div>
                <div class="col-sm-2 float-left">
		<input class="btn btn-primary float-right" type="button" onclick="printDiv('printableArea')" value="Print Slip" />
          
	</div>
                  </div>
                  </div>
       
    <br />
                </div>
   
           </div>
       
<script>
    
    function printDiv(divName) { //alert('am called');
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
 }

    </script>

