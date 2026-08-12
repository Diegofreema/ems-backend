<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseregistrationsSubject $courseregistrationsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Courseregistrations Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="courseregistrationsSubjects form content">
            <?= $this->Form->create($courseregistrationsSubject) ?>
            <fieldset>
                <legend><?= __('Add Courseregistrations Subject') ?></legend>
                <?php
                    echo $this->Form->control('courseregistration_id', ['options' => $courseregistrations]);
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
