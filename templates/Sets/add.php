<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Set $set
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Sets'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sets form content">
            <?= $this->Form->create($set) ?>
            <fieldset>
                <legend><?= __('Add Set') ?></legend>
                <?php
                    echo $this->Form->control('year');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
