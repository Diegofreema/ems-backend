<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsSemester $departmentsSemester
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Departments Semesters'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsSemesters form content">
            <?= $this->Form->create($departmentsSemester) ?>
            <fieldset>
                <legend><?= __('Add Departments Semester') ?></legend>
                <?php
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('semester_id', ['options' => $semesters]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
