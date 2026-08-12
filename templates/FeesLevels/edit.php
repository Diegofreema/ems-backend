<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesLevel $feesLevel
 * @var string[]|\Cake\Collection\CollectionInterface $fees
 * @var string[]|\Cake\Collection\CollectionInterface $levels
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $feesLevel->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $feesLevel->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Fees Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesLevels form content">
            <?= $this->Form->create($feesLevel) ?>
            <fieldset>
                <legend><?= __('Edit Fees Level') ?></legend>
                <?php
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                    echo $this->Form->control('level_id', ['options' => $levels]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
