<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attemptedexamquestion $attemptedexamquestion
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Attemptedexamquestions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="attemptedexamquestions form content">
            <?= $this->Form->create($attemptedexamquestion) ?>
            <fieldset>
                <legend><?= __('Add Attemptedexamquestion') ?></legend>
                <?php
                    echo $this->Form->control('examquestion_id', ['options' => $examquestions]);
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('sanswer');
                    echo $this->Form->control('correctans');
                    echo $this->Form->control('examdate');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
