<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsPrograme $departmentsPrograme
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $departmentsPrograme->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsPrograme->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Departments Programes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsProgrames form content">
            <?= $this->Form->create($departmentsPrograme) ?>
            <fieldset>
                <legend><?= __('Edit Departments Programe') ?></legend>
                <?php
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('programe_id', ['options' => $programes]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
