<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsSubject $departmentsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $departmentsSubject->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsSubject->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Departments Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsSubjects form content">
            <?= $this->Form->create($departmentsSubject) ?>
            <fieldset>
                <legend><?= __('Edit Departments Subject') ?></legend>
                <?php
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
