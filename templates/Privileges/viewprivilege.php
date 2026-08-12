 <div class="content container-fluid">
  <!-- Page Header -->
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Privilege Details</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><?= $this->Html->link('Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
          <li class="breadcrumb-item"><?= $this->Html->link('Privileges', ['controller' => 'Privileges', 'action' => 'index'], ['title' => 'Privileges']) ?></li>
          <li class="breadcrumb-item active">Privilege Details</li>
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
                            <h1 class="h4 text-gray-900 mb-4">Privilege : <?= h($privilege->name) ?></h1>
                        </div>
<div class="privileges view large-9 medium-8 columns content">

   
    <div class="related">
        <h4><?= __('Admins With This Privilege') ?></h4>
        <?php if (!empty($privilege->admins)): ?>
        <table cellpadding="10" cellspacing="10">
            <tr>
                <th scope="col"><?= __('Surname') ?></th>
                <th scope="col"><?= __('Lastname') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col"><?= __('Photo') ?></th>
                <th scope="col"><?= __('Gender') ?></th>
                <th scope="col"><?= __('Phone') ?></th>
                <th scope="col"><?= __('Address') ?></th>
<!--                <th scope="col" class="actions"><?= __('Actions') ?></th>-->
            </tr>
            <?php foreach ($privilege->admins as $admins): ?>
            <tr>
     
                <td><?= h($admins->surname) ?></td>
                <td><?= h($admins->lastname) ?></td>
                <td><?= h($admins->status) ?></td>
                <td><?= $this->Html->image($admins->adminphoto, ['alt' => $admins->surname, 'class' => 'img-circle profile_img',
          'style' => 'width:100px;height:100px;'])
      ?></td>
                <td><?= h($admins->gender) ?></td>
     
                <td><?= h($admins->phone) ?></td>
                <td><?= h($admins->address) ?></td>
<!--                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Admins', 'action' => 'view', $admins->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Admins', 'action' => 'edit', $admins->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Admins', 'action' => 'delete', $admins->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admins->id)]) ?>
                </td>-->
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
                        </div>
                    </div></div></div></div></div></div>
