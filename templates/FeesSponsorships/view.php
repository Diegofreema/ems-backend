<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesSponsorship $feesSponsorship
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fees Sponsorship'), ['action' => 'edit', $feesSponsorship->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fees Sponsorship'), ['action' => 'delete', $feesSponsorship->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feesSponsorship->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fees Sponsorships'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fees Sponsorship'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesSponsorships view content">
            <h3><?= h($feesSponsorship->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $feesSponsorship->has('fee') ? $this->Html->link($feesSponsorship->fee->name, ['controller' => 'Fees', 'action' => 'view', $feesSponsorship->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Sponsorship') ?></th>
                    <td><?= $feesSponsorship->has('sponsorship') ? $this->Html->link($feesSponsorship->sponsorship->id, ['controller' => 'Sponsorships', 'action' => 'view', $feesSponsorship->sponsorship->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($feesSponsorship->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
