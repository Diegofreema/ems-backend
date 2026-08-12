<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Vote $vote
 * @var string[]|\Cake\Collection\CollectionInterface $candidates
 * @var string[]|\Cake\Collection\CollectionInterface $students
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $vote->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $vote->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Votes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="votes form content">
            <?= $this->Form->create($vote) ?>
            <fieldset>
                <legend><?= __('Edit Vote') ?></legend>
                <?php
                    echo $this->Form->control('candidate_id', ['options' => $candidates]);
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('vote');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
