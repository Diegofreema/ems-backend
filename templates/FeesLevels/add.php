<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesLevel $feesLevel
 * @var \Cake\Collection\CollectionInterface|string[] $fees
 * @var \Cake\Collection\CollectionInterface|string[] $levels
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Fees Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesLevels form content">
            <?= $this->Form->create($feesLevel) ?>
            <fieldset>
                <legend><?= __('Add Fees Level') ?></legend>
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
