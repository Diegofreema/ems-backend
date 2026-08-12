<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Teacher[]|\Cake\Collection\CollectionInterface $teachers
 */
?>
<div class="teachers index content">
    <?= $this->Html->link(__('New Teacher'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Teachers') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('gender') ?></th>
                    <th><?= $this->Paginator->sort('address') ?></th>
                    <th><?= $this->Paginator->sort('country_id') ?></th>
                    <th><?= $this->Paginator->sort('state_id') ?></th>
                    <th><?= $this->Paginator->sort('phone') ?></th>
                    <th><?= $this->Paginator->sort('profile') ?></th>
                    <th><?= $this->Paginator->sort('cv') ?></th>
                    <th><?= $this->Paginator->sort('qualification') ?></th>
                    <th><?= $this->Paginator->sort('date_created') ?></th>
                    <th><?= $this->Paginator->sort('passport') ?></th>
                    <th><?= $this->Paginator->sort('firstname') ?></th>
                    <th><?= $this->Paginator->sort('lastname') ?></th>
                    <th><?= $this->Paginator->sort('middlename') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('staffgrade_id') ?></th>
                    <th><?= $this->Paginator->sort('staffdepartment_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?= $this->Number->format($teacher->id) ?></td>
                    <td><?= $teacher->has('user') ? $this->Html->link($teacher->user->id, ['controller' => 'Users', 'action' => 'view', $teacher->user->id]) : '' ?></td>
                    <td><?= h($teacher->gender) ?></td>
                    <td><?= h($teacher->address) ?></td>
                    <td><?= $teacher->has('country') ? $this->Html->link($teacher->country->name, ['controller' => 'Countries', 'action' => 'view', $teacher->country->id]) : '' ?></td>
                    <td><?= $teacher->has('state') ? $this->Html->link($teacher->state->name, ['controller' => 'States', 'action' => 'view', $teacher->state->id]) : '' ?></td>
                    <td><?= h($teacher->phone) ?></td>
                    <td><?= h($teacher->profile) ?></td>
                    <td><?= h($teacher->cv) ?></td>
                    <td><?= h($teacher->qualification) ?></td>
                    <td><?= h($teacher->date_created) ?></td>
                    <td><?= h($teacher->passport) ?></td>
                    <td><?= h($teacher->firstname) ?></td>
                    <td><?= h($teacher->lastname) ?></td>
                    <td><?= h($teacher->middlename) ?></td>
                    <td><?= $teacher->has('department') ? $this->Html->link($teacher->department->name, ['controller' => 'Departments', 'action' => 'view', $teacher->department->id]) : '' ?></td>
                    <td><?= $teacher->has('staffgrade') ? $this->Html->link($teacher->staffgrade->name, ['controller' => 'Staffgrades', 'action' => 'view', $teacher->staffgrade->id]) : '' ?></td>
                    <td><?= $teacher->has('staffdepartment') ? $this->Html->link($teacher->staffdepartment->name, ['controller' => 'Staffdepartments', 'action' => 'view', $teacher->staffdepartment->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $teacher->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $teacher->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $teacher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $teacher->id)]) ?>
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
