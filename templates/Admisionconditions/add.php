<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admisioncondition $admisioncondition
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Admisionconditions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="admisionconditions form content">
            <?= $this->Form->create($admisioncondition) ?>
            <fieldset>
                <legend><?= __('Add Admisioncondition') ?></legend>
                <?php
                    echo $this->Form->control('conditiond');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('lastupdate');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
