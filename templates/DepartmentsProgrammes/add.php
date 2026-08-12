<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsProgramme $departmentsProgramme
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Departments Programmes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsProgrammes form content">
            <?= $this->Form->create($departmentsProgramme) ?>
            <fieldset>
                <legend><?= __('Add Departments Programme') ?></legend>
                <?php
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('programme_id', ['options' => $programmes]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
