<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Department $department
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $department->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $department->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Departments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departments form content">
            <?= $this->Form->create($department) ?>
            <fieldset>
                <legend><?= __('Edit Department') ?></legend>
                <?php
                    echo $this->Form->control('faculty_id', ['options' => $faculties]);
                    echo $this->Form->control('name');
                    echo $this->Form->control('deptcode');
                    echo $this->Form->control('description');
                    echo $this->Form->control('subjects._ids', ['options' => $subjects]);
                    echo $this->Form->control('fees._ids', ['options' => $fees]);
                    echo $this->Form->control('levels._ids', ['options' => $levels]);
                    echo $this->Form->control('programes._ids', ['options' => $programes]);
                    echo $this->Form->control('semesters._ids', ['options' => $semesters]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
