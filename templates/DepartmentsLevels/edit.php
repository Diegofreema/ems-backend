<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsLevel $departmentsLevel
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $departmentsLevel->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsLevel->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Departments Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsLevels form content">
            <?= $this->Form->create($departmentsLevel) ?>
            <fieldset>
                <legend><?= __('Edit Departments Level') ?></legend>
                <?php
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('level_id', ['options' => $levels]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
