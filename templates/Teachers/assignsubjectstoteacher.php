<div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Assign Subjects to Teacher</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
          <!-- <li class="breadcrumb-item"><?= $this->Html->link('Teachers', ['controller' => 'Teachers', 'action' => 'manageteachers'], ['title' => 'Teachers']) ?></li> -->
          <li class="breadcrumb-item active">Assign Subjects</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- /Page Header -->

<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Assign Subjects To Teacher</h1>
                        </div>
                        <?= $this->Form->create(null, ['url' => ['controller' => 'Teachers', 'action' => 'assignsubjects']]) ?>
                    <fieldset>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?= $this->Form->control('user_id', ['options' => $users, 'label' => 'Select Teacher', 'empty' => 'Select Teacher', 'class' => 'form-control form-control-user2']) ?>
                                <br />
                            </div>

                            <div class="col-sm-12">
                                <label for="subjects-ids">Assign Subjects</label>
                                <select name="subjects[_ids][]" multiple="multiple" class="form-control form-control-user2" id="subjects-ids">
                                    <?php foreach ($subjects as $id => $subject): ?>
                                        <option value="<?= h($id) ?>"><?= h($subject) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple subjects.</small>
                            </div>
                        </div>
                    </fieldset>
                    <br /> <br />
                    <?= $this->Form->button('Assign', ['class' => 'btn btn-primary btn-user btn-block']) ?>
                    <?= $this->Form->end() ?> <br /> <br />
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
    </div>


