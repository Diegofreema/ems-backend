<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Result $result
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $result->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $result->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Results'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="results form content">
            <?= $this->Form->create($result) ?>
            <fieldset>
                <legend><?= __('Edit Result') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('faculty_id', ['options' => $faculties]);
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                    echo $this->Form->control('semester_id', ['options' => $semesters]);
                    echo $this->Form->control('session_id', ['options' => $sessions]);
                    echo $this->Form->control('score');
                    echo $this->Form->control('grade');
                    echo $this->Form->control('remark');
                    echo $this->Form->control('uploaddate');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('regno');
                    echo $this->Form->control('creditload');
                    echo $this->Form->control('level_id', ['options' => $levels]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
