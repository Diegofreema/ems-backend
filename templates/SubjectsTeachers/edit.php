<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SubjectsTeacher $subjectsTeacher
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $subjectsTeacher->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $subjectsTeacher->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Subjects Teachers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subjectsTeachers form content">
            <?= $this->Form->create($subjectsTeacher) ?>
            <fieldset>
                <legend><?= __('Edit Subjects Teacher') ?></legend>
                <?php
                    echo $this->Form->control('teacher_id', ['options' => $teachers]);
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                    echo $this->Form->control('created_date');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
