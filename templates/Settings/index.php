<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>


<!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Settings</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Settings Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                        
                                        <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('description') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('regfee') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('address') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('email') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('phone') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('invoiceprefix') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('adminprefix') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('logo') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('staffprefix') ?></th>
                                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       
                                        <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('description') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('regfee') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('address') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('email') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('phone') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('invoiceprefix') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('adminprefix') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('logo') ?></th>
                                        <th scope="col"><?= $this->Paginator->sort('staffprefix') ?></th>
                                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                  </tfoot>
                <tbody>
                                    <?php foreach ($settings as $setting): ?>
                                        <tr>
                                           
                                            <td><?= h($setting->type) ?></td>
                                            <td><?= h($setting->description) ?></td>
                                            <td><?= $this->Number->format($setting->regfee) ?></td>
                                            <td><?= h($setting->name) ?></td>
                                            <td><?= h($setting->address) ?></td>
                                            <td><?= h($setting->email) ?></td>
                                            <td><?= h($setting->phone) ?></td>
                                            <td><?= h($setting->invoiceprefix) ?></td>
                                            <td><?= h($setting->adminprefix) ?></td>
                                            <td><?=
                                                $this->Html->image($setting->logo, ['alt' => 'EMS', 'class' => 'img-circle profile_img',
                                                    'style' => 'width:50px;height:60px;padding: 5px;'])
                                                ?>
                                            </td>
                                            <td><?= h($setting->staffprefix) ?></td>
                                            <td class="actions">
                                            <!--    <?= $this->Html->link(__('View'), ['action' => 'view', $setting->id]) ?>-->
                                                <?= $this->Html->link(__('Update'), ['action' => 'editsettings', $setting->id],
                                                        ['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'update session']) ?>
  <!--  <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $setting->id], ['confirm' => __('Are you sure you want to delete # {0}?', $setting->id)]) ?>-->
                                            </td>
                                        </tr>
<?php endforeach; ?>
                                </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

