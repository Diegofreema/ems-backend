<!-- Begin Page Content -->
<div class="container-fluid">
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
        <!-- Page Heading -->
        <div class="p-5">
            
    <div class="column-responsive column-80">
        <div class="timetables view content">
         
          
                    <?= __('Session') ?> : <?=$timetable->session->name ?><br />
                   
             
        <?= __('Department') ?> :   <?= $timetable->has('department') ? $timetable->department->name : '' ?> <br />
                 
               <?= __('Level') ?>                    <?= $timetable->has('level') ? $timetable->level->name : '' ?><br />
                
                   <?= __('Semester') ?> :         <?= $timetable->has('semester') ? $timetable->semester->name : '' ?></td>
              
                 
                <br />   <?= $timetable->timetable ?>
               
               
              
           
        </div>
    </div>
</div></div>