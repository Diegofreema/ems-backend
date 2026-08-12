<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesSponsorship $feesSponsorship
 * @var string[]|\Cake\Collection\CollectionInterface $fees
 * @var string[]|\Cake\Collection\CollectionInterface $sponsorships
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $feesSponsorship->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $feesSponsorship->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Fees Sponsorships'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesSponsorships form content">
            <?= $this->Form->create($feesSponsorship) ?>
            <fieldset>
                <legend><?= __('Edit Fees Sponsorship') ?></legend>
                <?php
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                    echo $this->Form->control('sponsorship_id', ['options' => $sponsorships]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
