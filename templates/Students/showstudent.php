<div class="row">
<div class="card">
           <?= $this->Html->image($student->passporturl, ['alt' => $student->fname, 'class' => 'card-img-top','style'=>'height: 230px; width: 335px;'])?>
            
  <div class="card-body">
    <h4 class="card-title">Reg No : <?=$student->regno?></h4>
    <p class="card-text">Name : <?=$student->fname.' '.$student->lname?><br />
    Department : <?=$student->department->name?> </p>
                  
  </div>
</div>
    </div>
