<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Search Library Books</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li>
                    <li class="breadcrumb-item active">Search Library Books</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
  
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Library Books </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">
                    
                    <div class="col-sm-4 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Class', 'empty' => 'Select Class', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>
                     <div class="col-sm-4 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('author', ['label' => 'Author', 'placeholder' => 'Author',
                              'class' => 'form-control form-control-user2']);
                        ?>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('title', ['label' => 'Title', 'placeholder' => 'title',
                              'class' => 'form-control form-control-user2']);
                        ?>
                    </div>
 
                </div>
              
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        </div>
       
    


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Library Books</h6>
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
                        
                            <th>Section</th> 
                           <th>Call No</th>
                           <th>Copies</th>
                        </tr>
                    </thead>

<tbody>
                <?php foreach ($books as $eresource): ?>
                <tr>
                   <td><?= h($eresource->author) ?></td>
                    <td><?= h($eresource->title) ?></td>
                   <td><?= $eresource->has('department') ? $eresource->department->name: '' ?></td>
                   
                    <td><?= h($eresource->pubdate) ?></td>
                    <td><?= h($eresource->isbn) ?></td>
                 
                    <td><?= h($eresource->section) ?></td>
                     <td><?= h($eresource->callno) ?></td>
                      <td><?= h($eresource->copies) ?></td>
                    
                </tr>
                <?php endforeach; ?>
            </tbody>

                  
                </table>
            </div>
        </div>
    </div>
</div>
</div>