<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Feeallocation[]|\Cake\Collection\CollectionInterface $feeallocations
 */
?>
<div class="feeallocations index content">
    <?= $this->Html->link(__('New Feeallocation'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Feeallocations') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('fee_id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('startdate') ?></th>
                    <th><?= $this->Paginator->sort('enddate') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feeallocations as $feeallocation): ?>
                <tr>
                    <td><?= $this->Number->format($feeallocation->id) ?></td>
                    <td><?= $feeallocation->has('fee') ? $this->Html->link($feeallocation->fee->name, ['controller' => 'Fees', 'action' => 'view', $feeallocation->fee->id]) : '' ?></td>
                    <td><?= $feeallocation->has('department') ? $this->Html->link($feeallocation->department->name, ['controller' => 'Departments', 'action' => 'view', $feeallocation->department->id]) : '' ?></td>
                    <td><?= h($feeallocation->startdate) ?></td>
                    <td><?= h($feeallocation->enddate) ?></td>
                    <td><?= $feeallocation->has('user') ? $this->Html->link($feeallocation->user->id, ['controller' => 'Users', 'action' => 'view', $feeallocation->user->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $feeallocation->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $feeallocation->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $feeallocation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feeallocation->id)]) ?>
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
