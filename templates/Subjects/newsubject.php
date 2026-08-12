<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Add New Subject</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
  
                        ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Subjects', ['controller' => 'Subjects', 'action' => 'managesubjects'], ['title' => 'manage subjects']) ?></li>
                    <li class="breadcrumb-item active">Add New Subject</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Add New Subject</h1>
                        </div>
                        <?= $this->Form->create($subject) ?>
    <fieldset>
      <div class="form-group row">
          <div class="col-sm-6 mb-3 mb-sm-0">
              <?= $this->Form->control('name', ['label' => 'Subject Name', 'placeholder' => 'Subject Name', 'required',
                                          'class' => 'form-control form-control-user2']);?>
          </div>
          <div class="col-sm-6 mb-3 mb-sm-0">
                <?= $this->Form->control('departments._ids', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false]) ?>
                               
          </div>
      </div>
        <div class="form-group row">
          <!-- <div class="col-sm-4 mb-3 mb-sm-0">
        <?php
        
            echo $this->Form->control('classcategory_id',['options' => $classcategories,'label' => 'Class Category',  'required',
                                          'class' => 'form-control form-control-user2']);?>
      
          </div> -->
            <div class="col-sm-4 mb-3 mb-sm-0">
                <?= $this->Form->control('teachers._ids', ['options' => $teachers, 'label' => 'Select Teacher', 'empty' => 'Select Teacher', 'class' => 'select2_multiple form-control form-control-user', 'multiple' => false]) ?>
                              
          </div>
        </div>
        
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
