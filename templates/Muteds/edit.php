<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muted $muted
 * @var string[]|\Cake\Collection\CollectionInterface $students
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $muted->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $muted->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Muteds'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="muteds form content">
            <?= $this->Form->create($muted) ?>
            <fieldset>
                <legend><?= __('Edit Muted') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('datemuted');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
