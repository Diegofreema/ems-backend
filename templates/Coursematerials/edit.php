<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Coursematerial $coursematerial
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $coursematerial->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $coursematerial->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Coursematerials'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="coursematerials form content">
            <?= $this->Form->create($coursematerial) ?>
            <fieldset>
                <legend><?= __('Edit Coursematerial') ?></legend>
                <?php
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                    echo $this->Form->control('teacher_id', ['options' => $teachers]);
                    echo $this->Form->control('title');
                    echo $this->Form->control('fileurl');
                    echo $this->Form->control('date_created');
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('comment');
                    echo $this->Form->control('updatedon');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
