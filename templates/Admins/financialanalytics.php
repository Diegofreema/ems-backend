<!-- Page Content -->
  <?php
                  $user = $this->request->getSession()->read('usersinfo');
                  $settings = $this->request->getSession()->read('settings');
                
                ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <div class="content container-fluid">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="page-title">Financial Analytics</h3>
                                <ul class="breadcrumb">
                    
                                <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                                <li class="breadcrumb-item active">Financial Analytics</li>
                            </ul>
                        </div>
                    </div>
                    <!-- /Page Header -->   
				 <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Financial Performance Analysis </h1>
                        </div>
    <?= $this->Form->create(null) ?>
    <fieldset>
         <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('session_id', ['options' => $sessions, 'label' => 'Select Session', 'empty' => 'Select Session', 'class' => 'select2_multiple form-control form-control-user']) ?>
                                </div>  
  
         </div>
       
        
    </fieldset>
   <br /> <br />
                    <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
                        <?= $this->Form->end() ?>
                    </div>
					
					
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6 text-center">
									<div class="card">
										<div class="card-body">
											<h3 class="card-title">Financial Analytics - Current Session</h3>
											<canvas id="myChart" width="100" height="73"></canvas>
										</div>
									</div>
								</div>
<div class="col-md-6 text-center">
									<div class="card">
										<div class="card-body">
											<h3 class="card-title">Financial Analytics - Previous Session</h3>
											<canvas id="myChart2" width="100" height="73"></canvas>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
	
				
				</div>
				<!-- /Page Content -->
                    <?php
            $months_array = [];
            $sales_array = [];
           if(isset($trsnactions_graph)){
            
            foreach ($trsnactions_graph as $data){
                
                array_push($months_array, "'".date('M',  strtotime($data->duration))."'"); 
                array_push($sales_array, $data->totalvalue); 
               // '{ y:' . "'".date('M',  strtotime($data->duration))."'".','.' a:' .$data->totalvalue.','. ' b:' .$data->count; },
            }
           $dm = implode(", ", $months_array); 
           $dd = implode(", ", $sales_array);   
          // debug(json_encode( $months_array , JSON_PRETTY_PRINT)); exit; 
           //data for previouse session financial records
            $months_array2 = [];
            $sales_array2 = [];
           
            
            foreach ($trsnactions_graph2 as $data2){
                
                array_push($months_array2, "'".date('M',  strtotime($data2->duration))."'"); 
                array_push($sales_array2, $data2->totalvalue); 
              }
           $dm2 = implode(", ", $months_array2); 
           $dd2 = implode(", ", $sales_array2); 
           }
            ?>                         
<script>
var ctx = document.getElementById('myChart');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [ <?= $dm?>],
        datasets: [{
            label: '# Revenue Volum For The Current Session',
            data:  <?= '[ ' .$dd. ' ] '?> ,
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
            label: '# Revenue Volum For The Previous Session',
            data:  <?= '[ ' .$dd2. ' ] '?> ,
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
