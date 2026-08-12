<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Courseassignment $courseassignment
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Courseassignments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="courseassignments form content">
            <?= $this->Form->create($courseassignment) ?>
            <fieldset>
                <legend><?= __('Add Courseassignment') ?></legend>
                <?php
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('semester_id', ['options' => $semesters]);
                    echo $this->Form->control('level_id', ['options' => $levels]);
                    echo $this->Form->control('assignedon');
                    echo $this->Form->control('updatedon');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('subjects._ids', ['options' => $subjects]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
