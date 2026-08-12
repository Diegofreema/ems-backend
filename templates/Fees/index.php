<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Fee> $fees
 */
?>
<div class="fees index content">
    <?= $this->Html->link(__('New Fee'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Fees') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('amount') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('startdate') ?></th>
                    <th><?= $this->Paginator->sort('enddate') ?></th>
                    <th><?= $this->Paginator->sort('feetype') ?></th>
                    <th><?= $this->Paginator->sort('itemcode') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $fee): ?>
                <tr>
                    <td><?= $this->Number->format($fee->id) ?></td>
                    <td><?= h($fee->name) ?></td>
                    <td><?= $this->Number->format($fee->amount) ?></td>
                    <td><?= $fee->has('user') ? $this->Html->link($fee->user->username, ['controller' => 'Users', 'action' => 'view', $fee->user->id]) : '' ?></td>
                    <td><?= $this->Number->format($fee->status) ?></td>
                    <td><?= h($fee->startdate) ?></td>
                    <td><?= h($fee->enddate) ?></td>
                    <td><?= h($fee->feetype) ?></td>
                    <td><?= h($fee->itemcode) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $fee->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $fee->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $fee->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fee->id)]) ?>
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
