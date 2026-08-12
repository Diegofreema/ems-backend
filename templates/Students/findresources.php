<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Search Resource</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Search Resource</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Search Resource</h1>
                        </div>
                        <?= $this->Form->create(null) ?>
<!--                        <fieldset><legend>Search E-Resource</legend>-->
                          

                            <div class="form-group row">
                                   <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('title', ['label' => 'Title', 'placeholder' => 'title',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                     <?=
                                      $this->Form->control('author', ['label' => 'Author', 'placeholder' => 'author',
                                          'class' => 'form-control form-control-user2'])
                                    ?>
                                </div>
                                 <div class="col-sm-4 mb-3 mb-sm-0">
                                    <?=
                                      $this->Form->control('department_id', ['label' => 'Class', 'options' => $departments,
                                          'class' => 'form-control form-control-user2','empty'=>'Select Class'])
                                    ?>
                                </div>
                                
                             
                            </div>
                            
                         
                 
                            </fieldset>
                               
                        <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>
<?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">E-Resources </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                            <th >Author</th>
                            <th>Title</th>
                            <th >Class</th>
                             <th>Pub Date</th>
                               <th>ISBN</th>
                           

                           
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>

<tbody>
                <?php foreach ($eresources as $eresource): ?>
                <tr>
                   <td><?= h($eresource->author) ?></td>
                    <td><?= h($eresource->title) ?></td>
                   <td><?= $eresource->has('department') ? $eresource->department->name : '' ?></td>
                   
                    <td><?= h($eresource->pubdate) ?></td>
                    <td><?= h($eresource->isbn) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__(' Download'), ['action' => 'downloadmaterial', $eresource->id],['class' => 'btn btn-warning','title'=>'download material']) ?>
                          </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

                  
                </table>
            </div>
        </div>
    </div>
    
</div>
