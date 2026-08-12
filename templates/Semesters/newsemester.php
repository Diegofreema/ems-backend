<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">New Term</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Terms', ['controller' => 'Semesters', 'action' => 'managesemesters'], ['title' => 'manage terms']) ?></li>
                    <li class="breadcrumb-item active">New Term</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">New Term</h1>
                        </div>
    <?= $this->Form->create($semester) ?>
    <fieldset>
      
        <?php
            echo $this->Form->control('name',['label'=>'Term Name','required','placeholder'=>'Term name'
                                            ,'class'=>'form-control','required']) ?>
       
    </fieldset>
      <br /> <br />
                            <?= $this->Form->button('Submit', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
