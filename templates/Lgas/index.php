<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Lga[]|\Cake\Collection\CollectionInterface $lgas
 */
?>
<div class="lgas index content">
    <?= $this->Html->link(__('New Lga'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Lgas') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('state_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lgas as $lga): ?>
                <tr>
                    <td><?= $this->Number->format($lga->id) ?></td>
                    <td><?= h($lga->name) ?></td>
                    <td><?= $lga->has('state') ? $this->Html->link($lga->state->name, ['controller' => 'States', 'action' => 'view', $lga->state->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $lga->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $lga->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $lga->id], ['confirm' => __('Are you sure you want to delete # {0}?', $lga->id)]) ?>
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
