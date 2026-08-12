<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quizquestion $quizquestion
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Quizquestions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="quizquestions form content">
            <?= $this->Form->create($quizquestion) ?>
            <fieldset>
                <legend><?= __('Add Quizquestion') ?></legend>
                <?php
                    echo $this->Form->control('quiz_id', ['options' => $quizzes]);
                    echo $this->Form->control('question');
                    echo $this->Form->control('op1');
                    echo $this->Form->control('op2');
                    echo $this->Form->control('op3');
                    echo $this->Form->control('op4');
                    echo $this->Form->control('correctans');
                    echo $this->Form->control('mark');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
