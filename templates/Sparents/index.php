<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sparent[]|\Cake\Collection\CollectionInterface $sparents
 */
?>
<div class="sparents index content">
    <?= $this->Html->link(__('New Sparent'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Sparents') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('fathersname') ?></th>
                    <th><?= $this->Paginator->sort('mothersname') ?></th>
                    <th><?= $this->Paginator->sort('fatherphone') ?></th>
                    <th><?= $this->Paginator->sort('motherphone') ?></th>
                    <th><?= $this->Paginator->sort('fathersjob') ?></th>
                    <th><?= $this->Paginator->sort('mothersjob') ?></th>
                    <th><?= $this->Paginator->sort('pemailaddress') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('address') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sparents as $sparent): ?>
                <tr>
                    <td><?= $this->Number->format($sparent->id) ?></td>
                    <td><?= h($sparent->fathersname) ?></td>
                    <td><?= h($sparent->mothersname) ?></td>
                    <td><?= h($sparent->fatherphone) ?></td>
                    <td><?= h($sparent->motherphone) ?></td>
                    <td><?= h($sparent->fathersjob) ?></td>
                    <td><?= h($sparent->mothersjob) ?></td>
                    <td><?= h($sparent->pemailaddress) ?></td>
                    <td><?= $sparent->has('user') ? $this->Html->link($sparent->user->id, ['controller' => 'Users', 'action' => 'view', $sparent->user->id]) : '' ?></td>
                    <td><?= h($sparent->address) ?></td>
                    <td><?= h($sparent->status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $sparent->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $sparent->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sparent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sparent->id)]) ?>
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
