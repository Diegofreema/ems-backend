<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Events</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <?= $this->Html->link(__(' Dashboard '), ['controller' => 'Admins', 'action' => 'dashboard'], [ 'title' => 'addmit dashboard'])
                    ?>
                </li>
                <li class="breadcrumb-item active">Post Event</li>
            </ul>
        </div>
       
    </div>
</div>

<div class="row">
    <div class="col-xl-12 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h4 class="card-title mb-0">Post Event</h4>
            </div>
            <div class="card-body">

                <?= $this->Form->create($event) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Event Title</label>
                            <?= $this->Form->control('eventtitle', ['label' => false, 'class' => 'form-control', 'required']); ?>

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <?= $this->Form->control('details', ['label' => false,  'class' => 'ckeditor', 'type'=>'textarea','required']); ?>
<!--                                <textarea class="form-control"></textarea>-->
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Venue</label>
                            <?= $this->Form->control('venue', ['label' => false, 'class' => 'form-control','required']); ?>

                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            <label>Event Date</label>
                            <?= $this->Form->control('eventdate', ['label' => false, 'class' => 'form-control datetimepicker','required']); ?>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                            <label>Event Time</label>
                            <?= $this->Form->control('eventtime', ['label' => false, 'class' => 'form-control','required']); ?>

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

