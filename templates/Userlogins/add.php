<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Userlogin $userlogin
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Userlogins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="userlogins form content">
            <?= $this->Form->create($userlogin) ?>
            <fieldset>
                <legend><?= __('Add Userlogin') ?></legend>
                <?php
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('logintime');
                    echo $this->Form->control('logouttime', ['empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
