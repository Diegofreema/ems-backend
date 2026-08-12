<div class="content container-fluid">
<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!--          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>-->
                <div class="col-lg-12">
                    <div class="p-5"><?= $this->Html->image($admin->adminphoto, ['alt' => 'EMS', 'class' => 'img-profile rounded-circle','height'=>90,'width'=>90])?>
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Admin : <?= h($admin->surname.' '.$admin->lastname) ?></h1>
                        </div>

<div class="admins view large-9 medium-8 columns content">
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Username') ?></th>
            <td><?= $admin->has('user') ? $admin->user->username : '' ?></td>
        </tr>
       
        
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= h($admin->status) ?></td>
        </tr>
<!--        <tr>
            <th scope="row"><?= __('Adminphoto') ?></th>
            <td><?= $this->Html->image($admin->adminphoto, ['alt' => 'EMS', 'class' => 'img-profile rounded-circle','height'=>90,'width'=>90])?></td>
        </tr>-->
        <tr>
            <th scope="row"><?= __('Gender') ?></th>
            <td><?= h($admin->gender) ?></td>
        </tr>
       
        <tr>
            <th scope="row"><?= __('Phone') ?></th>
            <td><?= h($admin->phone) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Address') ?></th>
            <td><?= h($admin->address) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Dob') ?></th>
            <td><?= h($admin->dob) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Profile') ?></th>
            <td><?= h($admin->profile) ?></td>
        </tr>
        
        <tr>
            <th scope="row"><?= __('Date Added') ?> </th>
            <td>&nbsp;<?= h(date('D d M Y', strtotime($admin->date_created))) ?></td>
        </tr>
    </table>
    <div class="related"><br />
        <h4><?= __('Privileges') ?></h4>
        <?php if (!empty($admin->privileges)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
               
                <th scope="col"><?= __('Name') ?></th>
                
            </tr>
            <?php foreach ($admin->privileges as $privileges): ?>
            <tr>
              
                <td><?= h($privileges->name) ?></td>
               
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div> </div>
</div> </div>
</div> </div>
</div></div>

