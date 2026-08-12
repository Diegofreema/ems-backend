<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Donation $donation
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $donation->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $donation->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Donations'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="donations form content">
            <?= $this->Form->create($donation) ?>
            <fieldset>
                <legend><?= __('Edit Donation') ?></legend>
                <?php
                    echo $this->Form->control('donator');
                    echo $this->Form->control('donationdate');
                    echo $this->Form->control('phone');
                    echo $this->Form->control('email');
                    echo $this->Form->control('address');
                    echo $this->Form->control('amount');
                    echo $this->Form->control('rrr');
                    echo $this->Form->control('status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
