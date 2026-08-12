<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Level $level
 * @var string[]|\Cake\Collection\CollectionInterface $departments
 * @var string[]|\Cake\Collection\CollectionInterface $fees
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $level->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $level->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="levels form content">
            <?= $this->Form->create($level) ?>
            <fieldset>
                <legend><?= __('Edit Level') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('departments._ids', ['options' => $departments]);
                    echo $this->Form->control('fees._ids', ['options' => $fees]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
