<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\FeesSponsorship> $feesSponsorships
 */
?>
<div class="feesSponsorships index content">
    <?= $this->Html->link(__('New Fees Sponsorship'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Fees Sponsorships') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('fee_id') ?></th>
                    <th><?= $this->Paginator->sort('sponsorship_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feesSponsorships as $feesSponsorship): ?>
                <tr>
                    <td><?= $this->Number->format($feesSponsorship->id) ?></td>
                    <td><?= $feesSponsorship->has('fee') ? $this->Html->link($feesSponsorship->fee->name, ['controller' => 'Fees', 'action' => 'view', $feesSponsorship->fee->id]) : '' ?></td>
                    <td><?= $feesSponsorship->has('sponsorship') ? $this->Html->link($feesSponsorship->sponsorship->id, ['controller' => 'Sponsorships', 'action' => 'view', $feesSponsorship->sponsorship->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $feesSponsorship->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $feesSponsorship->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $feesSponsorship->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feesSponsorship->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
