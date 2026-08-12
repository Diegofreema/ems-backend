<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Liveclass $liveclass
 * @var string[]|\Cake\Collection\CollectionInterface $teachers
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $liveclass->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $liveclass->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Liveclasses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="liveclasses form content">
            <?= $this->Form->create($liveclass) ?>
            <fieldset>
                <legend><?= __('Edit Liveclass') ?></legend>
                <?php
                    echo $this->Form->control('meetinglink');
                    echo $this->Form->control('teacher_id', ['options' => $teachers]);
                    echo $this->Form->control('datecreated');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
