<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subject $subject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subjects form content">
            <?= $this->Form->create($subject) ?>
            <fieldset>
                <legend><?= __('Add Subject') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('subjectcode');
                    echo $this->Form->control('department_id');
                    echo $this->Form->control('creditload');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('created_date');
                    echo $this->Form->control('status');
                    echo $this->Form->control('semester_id');
                    echo $this->Form->control('level_id');
                    echo $this->Form->control('departments._ids', ['options' => $departments]);
                    echo $this->Form->control('semesters._ids', ['options' => $semesters]);
                    echo $this->Form->control('levels._ids', ['options' => $levels]);
                    echo $this->Form->control('courseassignments._ids', ['options' => $courseassignments]);
                    echo $this->Form->control('courseregistrations._ids', ['options' => $courseregistrations]);
                    echo $this->Form->control('students._ids', ['options' => $students]);
                    echo $this->Form->control('teachers._ids', ['options' => $teachers]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
