<?php    debug(json_encode($student, JSON_PRETTY_PRINT)); exit; if(!empty($student)){?>
<div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Name : </strong> <?=$student->fname .' '.$student->lname. ' '. $student->mname?>
</div>
<?php }
else{
?>
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Error : </strong> Unknown Student data
</div>
<?php } ?>





