<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Examquestion $examquestion
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $examquestion->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $examquestion->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Examquestions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="examquestions form content">
            <?= $this->Form->create($examquestion) ?>
            <fieldset>
                <legend><?= __('Edit Examquestion') ?></legend>
                <?php
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                    echo $this->Form->control('question');
                    echo $this->Form->control('op1');
                    echo $this->Form->control('op2');
                    echo $this->Form->control('op3');
                    echo $this->Form->control('op4');
                    echo $this->Form->control('correctans');
                    echo $this->Form->control('mark');
                    echo $this->Form->control('dateadded');
                    echo $this->Form->control('admin_id', ['options' => $admins]);
                    echo $this->Form->control('exam_id', ['options' => $exams]);
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('level_id', ['options' => $levels]);
                    echo $this->Form->control('faculty_id');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
