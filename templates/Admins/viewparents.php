<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="content container-fluid">
                <?= $this->Html->link(__(' '), ['controller'=>'Sparents','action' => 'add'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new parent']) ?>
          <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Manage Parents</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Admin dashboard')], ['title' => 'Admin dashboard'])
                            ?></li>
                        <li class="breadcrumb-item active">Manage Parents</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
         

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Parents Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
              <table class="table table-bordered" id="myTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
            
                <th scope="col"><?= $this->Paginator->sort('Father') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Mother') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Father\'s Phone') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Mother\'s Phone') ?></th>
<!--                <th scope="col"><?= $this->Paginator->sort('father\'s Occupation') ?></th>
                <th scope="col"><?= $this->Paginator->sort('mother\'s Occupation') ?></th>-->
                <th style="width: 5%" ><?= $this->Paginator->sort('Email') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
            
                <th scope="col"><?= $this->Paginator->sort('Father') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Mother') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Father\'s Phone') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Mother\'s Phone') ?></th>
<!--                <th scope="col"><?= $this->Paginator->sort('father\'s Occupation') ?></th>
                <th scope="col"><?= $this->Paginator->sort('mother\'s Occupation') ?></th>-->
                <th style="width: 5%" ><?= $this->Paginator->sort('Email') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($parents as $sparent): ?>
            <tr>
               
                <td><?= $this->Html->link($sparent->fathersname, ['controller' => 'Admins', 'action' => 'parentdata',$sparent->id,$this->generateurl($sparent->fathersname)]) ?>
               </td>
                <td><?= $this->Html->link($sparent->mothersname, ['controller' => 'Admins', 'action' => 'parentdata',$sparent->id,$this->generateurl($sparent->mothersname)]) ?>
                   </td>
                <td><?= h($sparent->fatherphone) ?></td>
                <td><?= h($sparent->motherphone) ?></td>
<!--                <td><?= h($sparent->fathersjob) ?></td>
                <td><?= h($sparent->mothersjob) ?></td>-->
                <td style="width: 5%" ><?= h($sparent->pemailaddress) ?></td>
                <td class="actions">
                   
                    <?= $this->Html->link(__(' Update'), ['controller'=>'Sparents','action' => 'edit', $sparent->id,$this->generateurl($sparent->mothersname)],
                            ['class'=>'fa fa-edit btn btn-primary','title'=>'update parent']) ?>
                    <?php if($sparent->status=='active'){ echo $this->Form->postLink(__('Deactivate'), ['controller'=>'Sparents','action' => 'deactivate', $sparent->id], 
                    ['confirm' => __('Are you sure you want to deactivate # {0}?', $sparent->mothersname),'class'=>'btn btn-danger']);}
                    else{
                      echo $this->Form->postLink(__('Activate'), ['controller'=>'Sparents','action' => 'activate', $sparent->id], 
                    ['confirm' => __('Are you sure you want to activate # {0}?', $sparent->mothersname),'class'=>'btn btn-success']);
                    
                    }?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

 </div>
            </div>
          </div>

        </div>


