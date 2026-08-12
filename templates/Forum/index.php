<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Forum[]|\Cake\Collection\CollectionInterface $forum
 */
?>
<div class="forum index content">
    <?= $this->Html->link(__('New Forum'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Forum') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('category_id') ?></th>
                    <th><?= $this->Paginator->sort('dateadded') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('views') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forum as $forum): ?>
                <tr>
                    <td><?= $this->Number->format($forum->id) ?></td>
                    <td><?= h($forum->title) ?></td>
                    <td><?= $forum->has('user') ? $this->Html->link($forum->user->username, ['controller' => 'Users', 'action' => 'view', $forum->user->id]) : '' ?></td>
                    <td><?= $forum->has('category') ? $this->Html->link($forum->category->name, ['controller' => 'Categories', 'action' => 'view', $forum->category->id]) : '' ?></td>
                    <td><?= h($forum->dateadded) ?></td>
                    <td><?= h($forum->status) ?></td>
                    <td><?= $this->Number->format($forum->views) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $forum->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $forum->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $forum->id], ['confirm' => __('Are you sure you want to delete # {0}?', $forum->id)]) ?>
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
