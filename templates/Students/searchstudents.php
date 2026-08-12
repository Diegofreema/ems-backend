<?php
$user = $this->request->getSession()->read('usersinfo');
?>
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Search Results</h1>
  </div><!--/end d-sm-flex-->
  <div class="row">
  <!-- Pie Chart -->
  <?php foreach ( $students as $student){  ?>
  <div class="col-xl-4 col-lg-5 col-sm-12 col-md-12 col-xs-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Dropdown -->
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Regno : <?=$student->regno?></h6>
      </div>
      <!-- Card Body -->
      <div class="card-body">
         <?=  $this->Html->image($student->passporturl, ['alt' => 'EMS', 'class' => 'img-responsive avatar-view', "width"=>"100%", "height"=>"300px"])?>
      </div>
      <!--/end card body-->
       <?= $this->Html->link(__($student->fname.' '.$student->lname), ['controller' => 'Students', 'action' => 'viewstudent',$student->id,$this->generateurl($student->fname)],
               ['class'=>'btn btn-success']) ?>
             
    </div>
    <!--/end card-->
  </div>
  <?php } ?>
  <!--/end col-xl-4-->

 
</div>
 


