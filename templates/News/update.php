<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">News</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <?= $this->Html->link(__(' Dashboard '), ['controller' => 'Admins', 'action' => 'dashboard'], [ 'title' => 'addmit dashboard'])
                    ?>
                </li>
                <li class="breadcrumb-item active">Update News</li>
            </ul>
        </div>
       
    </div>
</div>

<div class="row">
    <div class="col-xl-12 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h4 class="card-title mb-0">Update News</h4>
            </div>
            <div class="card-body">

                <?= $this->Form->create($news,['type'=>'file']) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>News Title</label>
                            <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required']); ?>

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <?= $this->Form->control('details', ['label' => false, 'class' => 'ckeditor', 'required']); ?>
<!--                                <textarea class="form-control"></textarea>-->
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Image</label>
                            <?= $this->Form->control('nimage', ['label' => false, 'type' => 'file', 'required', 'class' => 'form-control']); ?>

                        </div>
                    </div>
                </div>
                <div class="submit-section">
                    <button class="btn btn-primary submit-btn float-right">Submit</button>
                </div>
                <?= $this->Form->end() ?>
    </div>
                    </div>
                </div> </div> </div>