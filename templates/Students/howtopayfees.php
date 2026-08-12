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
                               
                               <br /> <b style="font-size: 21px;"> Fee Payment Process</b></h1>
                        
                        </div>
                        
      <div class="col-sm-12">   <br />                    


                      <br />
                          
<?=$howtopayfee->conditiond ?>

 <div class="form-group">
	
     <div class="col-sm-4 float-right">
		<input class="btn btn-primary float-right" type="button" onclick="printDiv('printableArea')" value="Print Slip" />
          
	</div>
</div>
<br /><br /><br />
 <br />
                  
                       
                    </div>
                  </div>
                  </div>
                  </div>
       
    <br />
                </div>
   
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

