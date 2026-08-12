<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attemptedquiz $attemptedquiz
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $attemptedquiz->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $attemptedquiz->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Attemptedquizzes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="attemptedquizzes form content">
            <?= $this->Form->create($attemptedquiz) ?>
            <fieldset>
                <legend><?= __('Edit Attemptedquiz') ?></legend>
                <?php
                    echo $this->Form->control('quizquestion_id', ['options' => $quizquestions]);
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('sanswer');
                    echo $this->Form->control('correctans');
                    echo $this->Form->control('quizdate');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
