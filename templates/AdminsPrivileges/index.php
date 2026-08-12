<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AdminsPrivilege[]|\Cake\Collection\CollectionInterface $adminsPrivileges
 */
?>
<div class="adminsPrivileges index content">
    <?= $this->Html->link(__('New Admins Privilege'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Admins Privileges') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('admin_id') ?></th>
                    <th><?= $this->Paginator->sort('privilege_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adminsPrivileges as $adminsPrivilege): ?>
                <tr>
                    <td><?= $this->Number->format($adminsPrivilege->id) ?></td>
                    <td><?= $adminsPrivilege->has('admin') ? $this->Html->link($adminsPrivilege->admin->id, ['controller' => 'Admins', 'action' => 'view', $adminsPrivilege->admin->id]) : '' ?></td>
                    <td><?= $adminsPrivilege->has('privilege') ? $this->Html->link($adminsPrivilege->privilege->name, ['controller' => 'Privileges', 'action' => 'view', $adminsPrivilege->privilege->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $adminsPrivilege->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $adminsPrivilege->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $adminsPrivilege->id], ['confirm' => __('Are you sure you want to delete # {0}?', $adminsPrivilege->id)]) ?>
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
