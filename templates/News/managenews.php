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
                    <li class="breadcrumb-item active">Manage News</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                 <?= $this->Html->link(__(' Post News'), ['controller'=>'News','action' => 'postnews'], [ 'title' => 'post news','class'=>'btn add-btn fa fa-plus'])
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
                            <th>Image</th>
                            <th>Status</th>
                           
                            
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $news as $news){  ?>
                        <tr>
                          
                            <td> <?= $this->Html->link($news->title, ['controller' => 'News', 'action' => 'preview',$news->id,$this->generateurl($news->title)], ['title' => 'read news']) ?></td>
                            <td><?= date('D d M, Y h:i a',  strtotime($news->dateposted)) ?></td>
                            <td><?=$news->viewcount ?></td>
                            <td><?= $this->Html->image($news->newsimage, ['alt' =>$news->title,'hight'=>30,'width'=>30]) ?></td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-danger"></i> <?=$news->status ?>
                                    </a>

                                </div>
                            </td>
                            <td class="text-center">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-dot-circle-o text-danger"></i> admin
                                    </a>
<!--                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i> Open</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-success"></i> Closed</a>
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i> Cancelled</a>
                                    </div>-->
                                </div>
                            </td>
                          
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?= $this->Html->link(' Update', ['controller' => 'News', 'action' => 'update',$news->id,$this->generateurl($news->title)], ['title' => 'update news','class'=>'dropdown-item fa fa-pencil m-r-5']) ?>
                                     <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $news->id], ['confirm' => __('Are you sure you want to delete # {0}?', $news->title),'class'=>'dropdown-item fa fa-trash-o m-r-5']) ?>
                                   <?php if($news->status=='live'){
                                       echo $this->Html->link(' Take Down', ['controller' => 'News', 'action' => 'takedown',$news->id,$this->generateurl($news->title)], ['title' => 'put offline','class'=>'dropdown-item fa fa-pencil m-r-5']);    
                                       }elseif($news->status=='offline'){
                                     echo  $this->Html->link(' Go Live', ['controller' => 'News', 'action' => 'golive',$news->id,$this->generateurl($news->title)], ['title' => 'go live','class'=>'dropdown-item fa fa-pencil m-r-5']);    
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

<!-- Add Job Modal -->
<div id="add_job" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Post News</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
             <?= $this->Form->create(null,['url'=>['controller'=>'News','action'=>'postnews'],'type'=>'file']) ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>News Title</label>
                               <?= $this->Form->control('title',['label'=>false,'class'=>'form-control','required']); ?>
                               
                            </div>
                        </div>
                        
                    </div>
                   
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <?= $this->Form->control('details',['label'=>false,'class'=>'summernote','required']); ?>
<!--                                <textarea class="form-control"></textarea>-->
                            </div>
                        </div>
                    </div>
                     <div class="row">
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Image</label>
                                <?= $this->Form->control('nimage',['label'=>false,'type'=>'file','required','class'=>'form-control']); ?>
                              
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <?= $this->Form->button('Submit',['class'=>'btn btn-primary submit-btn']) ?>
                   
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<!-- /Add Job Modal -->




