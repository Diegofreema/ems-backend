<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Events</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
       <?= $this->Html->link(__(' Dashboard '), ['controller'=>'Admins','action' => 'dashboard'], [ 'title' => 'addmit dashboard'])
?>
                    </li>
                    <li class="breadcrumb-item active">Preview Event - <?=$event->eventtitle ?></li>
                </ul>
            </div>
           
        </div>
    </div>
    
    <div class="small-container">
						<div class="inner-header text-center">
							<h1><?=$event->eventtitle?></h1>
						</div>
						<div class="inner-content">
                                                    <p class="lead">
                                                      <?=$event->details ?>  
                                                        
                                                    </p>
                                        Posted by : <?=$event->admin->lastname.' '.$event->admin->surname  ?>     <br />   
                                        Post Date : <?= date('D d M, Y h:i a',  strtotime($event->datecreated)) ?><br /> 
                                        Page Views : <?= $event->viewscount ?><br /> 
                                        Venue : <?= $event->venue ?><br />
                                        Event date : <?= date('D d M, Y h:i a',  strtotime($event->eventdate)) ?>
                                                    </div>

					</div					
                </div>