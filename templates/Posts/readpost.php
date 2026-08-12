<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Forum</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard'], ['title' => 'students dashboard', 'class' => 'breadcrumb-item']) ?></li>
                    <li class="breadcrumb-item active">Post Details</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-8">
            <div class="job-info job-widget">
                <h3 class="job-title"><?= $post->title ?></h3>
                <span class="job-dept">Category: <?= $post->postcategory->name ?></span>
                <ul class="job-post-det">
                    <li><i class="fa fa-calendar"></i> Post Date: <span class="text-blue"><?= date('M d Y', strtotime($post->dateadded)) ?></span></li>
                    <li><i class="fa fa-calendar"></i> Last Update: <span class="text-blue"><?= date('M d Y', strtotime($post->lastedited)) ?></span></li>
                    <li><i class="fa fa-user-o"></i> Comments: <span class="text-blue"><?= $comments->count() ?></span></li>
                    <li><i class="fa fa-eye"></i> Views: <span class="text-blue"><?= $post->viewscount ?></span></li>
                </ul>
            </div>
            <div class="job-content job-widget">

                <div class="job-description">
                    <p><?= $post->postdetails ?></p>
                </div>
                <br />
                   <?php if ($post->allowcomments =="Yes"){ ?>
                <hr class="task-line">
 <?= $this->Form->create(null,['url'=> ['controller'=>'Comments','action' => 'addcomment']]) ?>
                  
                   
                <?= $this->Form->control('post_id', ['type' => 'hidden', 'value' => $post->id]) ?>    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Leave a Comment</label>
                              <?= $this->Form->control('comment', ['label' => false, 'class' => 'form-control', 'required', 'type' => 'textarea', 'class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>
                  
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
               <?= $this->Form->end() ?>
                     <?php } ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="job-det-info job-widget">
                <a class="btn job-btn" href="#" data-toggle="modal" data-target="#edit_job">Comments</a>
        
                <hr class="task-line">
             
              <?php foreach($comments as $comment) {?>
                 <?php  foreach ($comment->user->students as $student){
                     $name =  $student->fname.' '.$student->lname;
                     $photo = $student->passporturl;
                 }?>
                <div class="chat chat-left">
                    <div class="chat-avatar">
                        <a href="profile.html" class="avatar">
                            <?= $this->Html->image('../student_files/'.$photo, ['alt' => 'IMG', 'class' => 'avatar',
         ])?>
                   
                        </a>
                    </div>
                    <div class="chat-body">
                        <div class="chat-bubble">
                            <div class="chat-conte">
                                <span class="task-chat-user"><?=$name?></span> <span class="chat-time"><?=date('D d M, Y h:i', strtotime($comment->datecreated))?></span>
                                <p><?= $comment->comment ?></p>
                                
                            </div>
                        </div>
                    </div>
                </div>
              <?php  } ?>
             
            </div>

        </div>
       
    </div>
    <!-- /Page Content -->



</div>
<!-- /Page Wrapper -->

