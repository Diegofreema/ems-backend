<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Lga $lga
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Lgas'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="lgas form content">
            <?= $this->Form->create($lga) ?>
            <fieldset>
                <legend><?= __('Add Lga') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('state_id', ['options' => $states,'class' => 'select2_multiple form-control form-control-user',]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
