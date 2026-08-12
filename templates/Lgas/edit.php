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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $lga->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $lga->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Lgas'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="lgas form content">
            <?= $this->Form->create($lga) ?>
            <fieldset>
                <legend><?= __('Edit Lga') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('state_id', ['options' => $states]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
