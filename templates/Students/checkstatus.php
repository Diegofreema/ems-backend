<br />
<?php if(!empty($applicant)){
    
    if($applicant->status ==='Applied'){
 ?>
<div class="alert alert-info alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Hold On!</strong> Your Application is still being considered. We will get back to you shortly.
</div>
<?php }
elseif($applicant->status ==='Admitted'){ ?>
<div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Congratulations!</strong> You have been offered a provisional admission in our school. Please login with your username
  and password to pay the required fees and get your Registration Number<br />
  username : <?=$applicant->email?><br />
  Default Password : student123<br /> Please remember to change your password.
</div>

<?php }else{?>
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Hey! </strong> You are probably our student already : <?=$applicant->status?>.
</div>
<?php }}else{ ?>
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Sorry! :  </strong> We could not find your details. Please ensure you are entering the right application number.
</div>
<?php } ?>




