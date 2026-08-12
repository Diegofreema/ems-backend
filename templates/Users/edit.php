<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $user->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $user->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Users'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="users form content">
            <?= $this->Form->create($user) ?>
            <fieldset>
                <legend><?= __('Edit User') ?></legend>
                <?php
                    echo $this->Form->control('username');
                    echo $this->Form->control('password');
                    echo $this->Form->control('role_id', ['options' => $roles]);
                    echo $this->Form->control('fname');
                    echo $this->Form->control('lname');
                    echo $this->Form->control('mname');
                    echo $this->Form->control('gender');
                    echo $this->Form->control('address');
                    echo $this->Form->control('country_id', ['options' => $countries]);
                    echo $this->Form->control('state_id', ['options' => $states]);
                    echo $this->Form->control('phone');
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('profile');
                    echo $this->Form->control('dob');
                    echo $this->Form->control('created_date');
                    echo $this->Form->control('created_by');
                    echo $this->Form->control('passport');
                    echo $this->Form->control('useruniquid');
                    echo $this->Form->control('userstatus');
                    echo $this->Form->control('verification_key');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
