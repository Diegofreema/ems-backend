<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Admin'), ['action' => 'edit', $admin->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Admin'), ['action' => 'delete', $admin->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admin->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Admins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Admin'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="admins view content">
            <h3><?= h($admin->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $admin->has('user') ? $this->Html->link($admin->user->id, ['controller' => 'Users', 'action' => 'view', $admin->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Surname') ?></th>
                    <td><?= h($admin->surname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lastname') ?></th>
                    <td><?= h($admin->lastname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($admin->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Adminphoto') ?></th>
                    <td><?= h($admin->adminphoto) ?></td>
                </tr>
                <tr>
                    <th><?= __('Gender') ?></th>
                    <td><?= h($admin->gender) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $admin->has('department') ? $this->Html->link($admin->department->name, ['controller' => 'Departments', 'action' => 'view', $admin->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($admin->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($admin->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dob') ?></th>
                    <td><?= h($admin->dob) ?></td>
                </tr>
                <tr>
                    <th><?= __('Profile') ?></th>
                    <td><?= h($admin->profile) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($admin->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date Created') ?></th>
                    <td><?= h($admin->date_created) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Privileges') ?></h4>
                <?php if (!empty($admin->privileges)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($admin->privileges as $privileges) : ?>
                        <tr>
                            <td><?= h($privileges->id) ?></td>
                            <td><?= h($privileges->name) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Privileges', 'action' => 'view', $privileges->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Privileges', 'action' => 'edit', $privileges->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Privileges', 'action' => 'delete', $privileges->id], ['confirm' => __('Are you sure you want to delete # {0}?', $privileges->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
