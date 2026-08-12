<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\AdminsPrivilege $adminsPrivilege
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Admins Privilege'), ['action' => 'edit', $adminsPrivilege->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Admins Privilege'), ['action' => 'delete', $adminsPrivilege->id], ['confirm' => __('Are you sure you want to delete # {0}?', $adminsPrivilege->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Admins Privileges'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Admins Privilege'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="adminsPrivileges view content">
            <h3><?= h($adminsPrivilege->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Admin') ?></th>
                    <td><?= $adminsPrivilege->has('admin') ? $this->Html->link($adminsPrivilege->admin->id, ['controller' => 'Admins', 'action' => 'view', $adminsPrivilege->admin->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Privilege') ?></th>
                    <td><?= $adminsPrivilege->has('privilege') ? $this->Html->link($adminsPrivilege->privilege->name, ['controller' => 'Privileges', 'action' => 'view', $adminsPrivilege->privilege->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($adminsPrivilege->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
