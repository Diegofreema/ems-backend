<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffmessage $staffmessage
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Staffmessages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffmessages form content">
            <?= $this->Form->create($staffmessage) ?>
            <fieldset>
                <legend><?= __('Add Staffmessage') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('message');
                    echo $this->Form->control('datecreated');
                    echo $this->Form->control('teacher_id', ['options' => $teachers]);
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
