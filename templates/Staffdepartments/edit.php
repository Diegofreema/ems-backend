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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $staffdepartment->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $staffdepartment->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Staffdepartments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffdepartments form content">
            <?= $this->Form->create($staffdepartment) ?>
            <fieldset>
                <legend><?= __('Edit Staffdepartment') ?></legend>
                <?php
                    echo $this->Form->control('name');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
