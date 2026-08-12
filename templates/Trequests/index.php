<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trequest[]|\Cake\Collection\CollectionInterface $trequests
 */
?>
<div class="trequests index content">
    <?= $this->Html->link(__('New Trequest'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Trequests') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('orderdate') ?></th>
                    <th><?= $this->Paginator->sort('institution') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('continent_id') ?></th>
                    <th><?= $this->Paginator->sort('country_id') ?></th>
                    <th><?= $this->Paginator->sort('state_id') ?></th>
                    <th><?= $this->Paginator->sort('address') ?></th>
                    <th><?= $this->Paginator->sort('courier_id') ?></th>
                    <th><?= $this->Paginator->sort('amount') ?></th>
                    <th><?= $this->Paginator->sort('deliverystatus') ?></th>
                    <th><?= $this->Paginator->sort('fee_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trequests as $trequest): ?>
                <tr>
                    <td><?= $this->Number->format($trequest->id) ?></td>
                    <td><?= $trequest->has('student') ? $this->Html->link($trequest->student->id, ['controller' => 'Students', 'action' => 'view', $trequest->student->id]) : '' ?></td>
                    <td><?= h($trequest->orderdate) ?></td>
                    <td><?= h($trequest->institution) ?></td>
                    <td><?= h($trequest->status) ?></td>
                    <td><?= $trequest->has('continent') ? $this->Html->link($trequest->continent->name, ['controller' => 'Continents', 'action' => 'view', $trequest->continent->id]) : '' ?></td>
                    <td><?= $trequest->has('country') ? $this->Html->link($trequest->country->name, ['controller' => 'Countries', 'action' => 'view', $trequest->country->id]) : '' ?></td>
                    <td><?= $trequest->has('state') ? $this->Html->link($trequest->state->name, ['controller' => 'States', 'action' => 'view', $trequest->state->id]) : '' ?></td>
                    <td><?= h($trequest->address) ?></td>
                    <td><?= $trequest->has('courier') ? $this->Html->link($trequest->courier->name, ['controller' => 'Couriers', 'action' => 'view', $trequest->courier->id]) : '' ?></td>
                    <td><?= h($trequest->amount) ?></td>
                    <td><?= h($trequest->deliverystatus) ?></td>
                    <td><?= $trequest->has('fee') ? $this->Html->link($trequest->fee->name, ['controller' => 'Fees', 'action' => 'view', $trequest->fee->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $trequest->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $trequest->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $trequest->id], ['confirm' => __('Are you sure you want to delete # {0}?', $trequest->id)]) ?>
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
