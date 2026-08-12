<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Admins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="admins form content">
            <?= $this->Form->create($admin) ?>
            <fieldset>
                <legend><?= __('Add Admin') ?></legend>
                <?php
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('surname');
                    echo $this->Form->control('lastname');
                    echo $this->Form->control('status');
                    echo $this->Form->control('date_created');
                    echo $this->Form->control('adminphoto');
                    echo $this->Form->control('gender');
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('phone');
                    echo $this->Form->control('address');
                    echo $this->Form->control('dob');
                    echo $this->Form->control('profile');
                    echo $this->Form->control('privileges._ids', ['options' => $privileges]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
