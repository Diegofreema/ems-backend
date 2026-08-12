<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Userlogin[]|\Cake\Collection\CollectionInterface $userlogins
 */
?>
<div class="userlogins index content">
    <?= $this->Html->link(__('New Userlogin'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Userlogins') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('logintime') ?></th>
                    <th><?= $this->Paginator->sort('logouttime') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userlogins as $userlogin): ?>
                <tr>
                    <td><?= $this->Number->format($userlogin->id) ?></td>
                    <td><?= $userlogin->has('user') ? $this->Html->link($userlogin->user->id, ['controller' => 'Users', 'action' => 'view', $userlogin->user->id]) : '' ?></td>
                    <td><?= h($userlogin->logintime) ?></td>
                    <td><?= h($userlogin->logouttime) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $userlogin->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $userlogin->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $userlogin->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userlogin->id)]) ?>
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
