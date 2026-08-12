<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">News</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
       <?= $this->Html->link(__(' Dashboard '), ['controller'=>'Admins','action' => 'dashboard'], [ 'title' => 'addmit dashboard'])
?>
                    </li>
                    <li class="breadcrumb-item active">Preview News</li>
                </ul>
            </div>
           
        </div>
    </div>
    
    <div class="small-container">
						<div class="inner-header text-center">
							<h1><?=$news->title?></h1>
						</div>
						<div class="inner-content">
                                                    <p class="lead">
                                                      <?=$news->details ?>  
                                                        
                                                    </p>
                                        Posted by : Admin     <br />   
                                        Post Date : <?= date('D d M, Y h:i a',  strtotime($news->dateposted)) ?><br /> 
                                        Page Views : <?= $news->viewcount ?><br /> 
                                                    </div>

					</div					
                </div>