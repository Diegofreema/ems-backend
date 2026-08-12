<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Letters</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <?= $this->Html->link(__(' Dashboard '), ['controller' => 'Admins', 'action' => 'dashboard'], [ 'title' => 'addmit dashboard'])
                    ?>
                </li>
                <li class="breadcrumb-item active">Post Letter</li>
            </ul>
        </div>
       
    </div>
</div>

<div class="row">
    <div class="col-xl-12 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h4 class="card-title mb-0">Post Letter</h4>
            </div>
            <div class="card-body">

                  <?= $this->Form->create($letter) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Title</label>
                            <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required']); ?>

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <?= $this->Form->control('letterbody', ['label' => false, 'class' => 'ckeditor', 'required']); ?>
<!--                                <textarea class="form-control"></textarea>-->
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mode of Admission</label>
                            <?= $this->Form->control('mode_id', ['label' => false, 'options' => $modes, 'required', 'class' => 'form-control']); ?>

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

