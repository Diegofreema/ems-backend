<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LevelsSubject $levelsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $levelsSubject->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $levelsSubject->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Levels Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="levelsSubjects form content">
            <?= $this->Form->create($levelsSubject) ?>
            <fieldset>
                <legend><?= __('Edit Levels Subject') ?></legend>
                <?php
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                    echo $this->Form->control('level_id', ['options' => $levels]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
