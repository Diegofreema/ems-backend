
 <?php
        $user = $this->request->getSession()->read('usersinfo');
 
        ?>
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Forum</h3>
                <ul class="breadcrumb">

                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard'], ['title' => 'students dashboard', 'class' => 'breadcrumb-item']) ?></li>
                    <li class="breadcrumb-item active">Posts</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">

                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_job"><i class="fa fa-plus"></i> Add Post</a>
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
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date Posted</th>
                            <th>Post By</th>
                           <th>Comments</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0; foreach( $posts as $post): $count ++; ?>
                        <tr>
                            <td><?=$count  ?></td>
                            <td><?= $this->Html->link(__($post->title), ['action' => 'readpost',$post->id,$this->generateurl($post->title)]) ?></td>
                            <td><?=$post->postcategory->name?></td>
                            <td><?=date('D d M, Y h:i', strtotime($post->dateadded))?></td>
                           <td><?=$post->user->username  ?></td>
                            <td><?=count($post->comments)  ?></td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                      <?php if($post->user_id==$user['id']){?>
                                     <?= $this->Html->link(__(' Edit Post'), ['action' => 'editpost',$post->id,$this->generateurl($post->title)],['class'=>'dropdown-item fa fa-pencil m-r-5']) ?>
                                       <?= $this->Form->postLink(
                __(' Delete'),
                ['action' => 'delete', $post->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $post->title), 'class' => 'dropdown-item fa fa-trash-o m-r-5']
            ) ?>
                                       
                                      <?php } ?>  </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                       
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
                <h5 class="modal-title">Add Post</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <?= $this->Form->create($post,['url'=> ['controller'=>'Posts','action' => 'addpost']]) ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Post Title</label>
                              <?=  $this->Form->control('title',['label'=>false,'class'=>'form-control','required'])?>
                            </div>
                        </div>
                      
                    </div>
                   
                
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                              <?= $this->Form->control('postdetails', ['label' => false, 'class' => 'form-control', 'required', 'type' => 'textarea', 'class' => 'summernote']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                
                                <label>Category</label>
                              <?=
                     $this->Form->control('postcategory_id', ['label'=>false,'options' => $postcategories],['class'=>'form-control','required'])?>
                           
                            </div>
                        </div>
                        
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
               <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<!-- /Add Job Modal -->




