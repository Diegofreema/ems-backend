<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>


<div class="container-fluid">
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
  <?= $this->Html->link(__(' '), ['action' => 'addresource'], ['class' => 'btn-circle btn-lg fa fa-plus float-right', 'title' => 'addmit new resource'])
?>
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Eresources </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">
                    
                    <div class="col-sm-6 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Department', 'empty' => 'Select Department', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>
                     <div class="col-sm-6 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('author', ['label' => 'Author', 'placeholder' => 'Author',
                              'class' => 'form-control form-control-user2']);
                        ?>
                    </div>
 
                </div>
              
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        </div>
        <h1 class="h3 mb-2 text-gray-800">Manage E-resources</h1>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">E-Resources Manager</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                            <th >Author</th>
                            <th>Title</th>
                            <th >Department</th>
                             <th>Pub Date</th>
                               <th>ISBN</th>
                             <th>View Count</th>

                           
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>

<tbody>
                <?php foreach ($eresources as $eresource): ?>
                <tr>
                   <td><?= h($eresource->author) ?></td>
                    <td><?= h($eresource->title) ?></td>
                   <td><?= $eresource->has('department') ? $this->Html->link($eresource->department->name, ['controller' => 'Departments', 'action' => 'view', $eresource->department->id]) : '' ?></td>
                   
                    <td><?= h($eresource->pubdate) ?></td>
                    <td><?= h($eresource->isbn) ?></td>
                    <td><?= $eresource->viewcount === null ? '' : $this->Number->format($eresource->viewcount) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $eresource->id],['class' => 'btn btn-warning']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $eresource->id,['class' => 'btn btn-primary']]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $eresource->id], 
                                ['confirm' => __('Are you sure you want to delete # {0}?', $eresource->title),'class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

                  
                </table>
            </div>
        </div>
    </div>

</div>


<div class="eresources index content">
 
   
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
