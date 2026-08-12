<?php 
    echo '<div class="col-md-12">';
        //$cities = ['1'=>'Mpape','2'=>'Yola','3'=>'Uyo'];
        echo $this->Form->control(
            'student._ids', 
            ['options' =>  $students, 'empty' => 'Select Student(s)'],
            ['class'=>'select2_multiple form-control']
        );
    echo '</div>';
?>
