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
                    <li class="breadcrumb-item active">Manage Events</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                 <?= $this->Html->link(__(' Post Events'), ['controller'=>'Events','action' => 'postevents'], [ 'title' => 'post events','class'=>'btn add-btn fa fa-plus'])
?>
<!--                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_job"><i class="fa fa-plus"></i> Post News</a>-->
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                          
                            <th>Title</th>
                          
                            <th>Date</th>
                            <th>Views</th>
                                 <th class="text-center">Admin</th>
                            <th>Status</th>
                       
                            
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $events as $news){  ?>
                        <tr>
                          
                            <td> <?= $this->Html->link($news->eventtitle, ['controller' => 'Events', 'action' => 'preview',$news->id,$this->generateurl($news->eventtitle)], ['title' => 'preview event']) ?></td>
                            <td><?= date('D d M, Y h:i a',  strtotime($news->datecreated)) ?></td>
                            <td><?=$news->viewscount ?></td>
                             <td><?=$news->admin->surname ?></td>
<!--                            <td><?= $this->Html->image($news->newsimage, ['alt' =>$news->title,'hight'=>30,'width'=>30]) ?></td>-->
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-danger"></i> <?=$news->status ?>
                                    </a>
<!--                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i> Full Time</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-success"></i> Part Time</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i> Internship</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-warning"></i> Temporary</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-warning"></i> Other</a>
                                    </div>-->
                                </div>
                            </td>
                       
                          
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?= $this->Html->link(' Update', ['controller' => 'Events', 'action' => 'update',$news->id,$this->generateurl($news->eventtitle)], ['title' => 'update news','class'=>'dropdown-item fa fa-pencil m-r-5']) ?>
                                     <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $news->id], ['confirm' => __('Are you sure you want to delete # {0}?', $news->eventtitle),'class'=>'dropdown-item fa fa-trash-o m-r-5']) ?>
                                       <?php if($news->status=='live'){
                                       echo $this->Html->link(' Take Down', ['controller' => 'Events', 'action' => 'takedown',$news->id,$this->generateurl($news->eventtitle)], ['title' => 'put offline','class'=>'dropdown-item fa fa-pencil m-r-5']);    
                                       }elseif($news->status=='offline'){
                                     echo  $this->Html->link(' Go Live', ['controller' => 'Events', 'action' => 'golive',$news->id,$this->generateurl($news->eventtitle)], ['title' => 'go live','class'=>'dropdown-item fa fa-pencil m-r-5']);    
                                       }           
                                           ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php  } ?>
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->






