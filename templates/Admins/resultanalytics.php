<!-- Page Content -->
  <?php
                  $user = $this->request->getSession()->read('usersinfo');
                  $settings = $this->request->getSession()->read('settings');
                
                ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});
</script>
                <div class="content container-fluid">
                  <!-- Page Header -->
                  <div class="page-header">
                    <div class="row">
                      <div class="col-sm-12">
                        <h3 class="page-title">Results Analytics</h3>
                        <ul class="breadcrumb">
                          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                          <li class="breadcrumb-item active">Results Analytics</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!-- /Page Header -->

				 <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Academic Performance Analysis </h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
         <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('session_id', ['options' => $sessions, 'label' => 'Select Session', 'empty' => 'Select Session', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                </div>  
                    
                    <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('subject_id', ['options' => $subjects, 'label' => 'Select Subject', 'empty' => 'Select Subject', 'class' => 'select2 form-control form-control-user']) ?>
                    </div>
             
         </div>
       
        
    </fieldset>
 
                    <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
					
					
					<div class="row">
						<div class="col-md-12">
							<div class="row">
<!--								<div class="col-md-6 text-center">
									<div class="card">
										<div class="card-body">
											<h3 class="card-title">Total Revenue</h3>
											<div id="bar-charts"></div>
										</div>
									</div>
								</div>-->
								<div class="col-md-6 text-center">
									<div class="card">
										<div class="card-body">
											<h3 class="card-title">Results Analytics - Current Session</h3>
											<canvas id="myChart" width="100" height="73"></canvas>
										</div>
									</div>
								</div>
<div class="col-md-6 text-center">
									<div class="card">
										<div class="card-body">
											<h3 class="card-title">Results Analytics - Previous Session</h3>
											<canvas id="myChart2" width="100" height="73"></canvas>
										</div>
									</div>
								</div>

							</div>
                                                    <span style="font-weight: bold"> Subject: <?= $subject->name ?><br /></span>
						</div>
					</div>
	
				
				</div>
				<!-- /Page Content -->
                    <?php
            $grades_array = [];
            $results_array = [];
  if(isset($results_graph)){
            foreach ($results_graph as $data){
               
                array_push($grades_array, '"'.$data->grade.'"'); 
                array_push($results_array, $data->count); 
               // '{ y:' . "'".date('M',  strtotime($data->duration))."'".','.' a:' .$data->totalvalue.','. ' b:' .$data->count; },
            }
           $dm = implode(", ", $grades_array); 
           $dd = implode(", ", $results_array);
         //  echo "'[ $dm ]'"; exit;
         //debug(json_encode($dm, JSON_PRETTY_PRINT)); exit;
           //for the previouse session
           $grades_array2 = [];
            $results_array2 = [];
  
            foreach ($results_graph2 as $data2){
               
                array_push($grades_array2, '"'.$data2->grade.'"'); 
                array_push($results_array2, $data2->count); 
             }
           $dm2 = implode(", ", $grades_array2); 
           $dd2 = implode(", ", $results_array2);
           
  }
            ?>                         
<script>
var ctx = document.getElementById('myChart');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [ <?= $dm?>], 
        datasets: [{
            label: '# Results Analysis',
            data: <?= '[ ' .$dd. ' ] '?> ,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
	
</script>

<script>
var ctx2 = document.getElementById('myChart2');
var myChart2 = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [ <?= $dm2?>], 
        datasets: [{
            label: '# Results Analysis',
            data: <?= '[ ' .$dd2. ' ] '?> ,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
	
</script>