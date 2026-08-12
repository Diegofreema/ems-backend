<div class="content container-fluid">
    <!-- Page Header -->
     <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">View Topic</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Teachers', 'action' => 'dashboard', $this->GenerateUrl('Teacher dashboard')], ['title' => 'Teacher dashboard'])
                            ?></li>
                        <li class="breadcrumb-item"><?= $this->Html->link('My Topics', ['controller' => 'Teachers', 'action' => 'mytopics', $this->GenerateUrl('My Topics')], ['title' => 'My Topics'])
                            ?></li>
                        <li class="breadcrumb-item active">View Topic</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    
                    <div class="card">
  <div class="card-header">Subject : <?=$topic->subject->name?> | Topic : <?=$topic->title?></div>
  <div class="card-body">
        <?=$topic->contents?>
  </div> 
  <div class="card-footer">Last Updated on : <?=$topic->updatedon?></div>
</div>
                    
                     </div>
            </div>
        </div>
    </div>

</div>
