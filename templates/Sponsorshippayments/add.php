<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sponsorshippayment $sponsorshippayment
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Sponsorshippayments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sponsorshippayments form content">
            <?= $this->Form->create($sponsorshippayment) ?>
            <fieldset>
                <legend><?= __('Add Sponsorshippayment') ?></legend>
                <?php
                    echo $this->Form->control('sref');
                    echo $this->Form->control('sponsorship_id', ['options' => $sponsorships]);
                    echo $this->Form->control('amount');
                    echo $this->Form->control('datecreated');
                    echo $this->Form->control('paystatus');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
