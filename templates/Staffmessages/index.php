<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffmessage[]|\Cake\Collection\CollectionInterface $staffmessages
 */
?>
<div class="staffmessages index content">
    <?= $this->Html->link(__('New Staffmessage'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Staffmessages') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('message') ?></th>
                    <th><?= $this->Paginator->sort('datecreated') ?></th>
                    <th><?= $this->Paginator->sort('teacher_id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffmessages as $staffmessage): ?>
                <tr>
                    <td><?= $this->Number->format($staffmessage->id) ?></td>
                    <td><?= h($staffmessage->title) ?></td>
                    <td><?= h($staffmessage->message) ?></td>
                    <td><?= h($staffmessage->datecreated) ?></td>
                    <td><?= $staffmessage->has('teacher') ? $this->Html->link($staffmessage->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $staffmessage->teacher->id]) : '' ?></td>
                    <td><?= $staffmessage->has('user') ? $this->Html->link($staffmessage->user->id, ['controller' => 'Users', 'action' => 'view', $staffmessage->user->id]) : '' ?></td>
                    <td><?= h($staffmessage->status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $staffmessage->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $staffmessage->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $staffmessage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $staffmessage->id)]) ?>
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
