<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Sponsorships'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sponsorships form content">
            <?= $this->Form->create($sponsorship) ?>
            <fieldset>
                <legend><?= __('Add Sponsorship') ?></legend>
                <?php
                    echo $this->Form->control('sponsor_id', ['options' => $sponsors]);
                    echo $this->Form->control('session_id', ['options' => $sessions]);
                    echo $this->Form->control('student_id');
                    echo $this->Form->control('admin_id', ['options' => $admins]);
                    echo $this->Form->control('datecreated');
                    echo $this->Form->control('students._ids', ['options' => $students]);
                    echo $this->Form->control('fees._ids', ['options' => $fees]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
