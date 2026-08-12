<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">My Timetable</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">My Timetable</li>
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
                    <div class="p-5">
              <?php if(!empty($timetable->session->name )){  ?>
            <div class="timetables view content">
         
          
                    <?= __('Session') ?> : <?=$timetable->session->name ?><br />
                   
             
        <?= __('Class') ?> :   <?= $timetable->department->name ?> <br />
                 
               <!-- <?= __('Level') ?> :  <?=  $timetable->level->name ?><br /> -->
                
                   <?= __('Semester') ?> : <?=$timetable->semester->name ?> <br /> 
              
                 
                <br />  <br />   <?= $timetable->timetable ?>
               
               
              
           
        </div> 
         <?php } ?>     
              
          </div>
            </div>
                 </div> 
              
              
          </div>
           </div>    </div>