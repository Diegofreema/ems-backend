<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffdepartment $staffdepartment
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Staffdepartments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffdepartments form content">
            <?= $this->Form->create($staffdepartment) ?>
            <fieldset>
                <legend><?= __('Add Staffdepartment') ?></legend>
                <?php
                    echo $this->Form->control('name');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
