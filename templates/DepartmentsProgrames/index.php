<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsPrograme[]|\Cake\Collection\CollectionInterface $departmentsProgrames
 */
?>
<div class="departmentsProgrames index content">
    <?= $this->Html->link(__('New Departments Programe'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Departments Programes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('programe_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departmentsProgrames as $departmentsPrograme): ?>
                <tr>
                    <td><?= $this->Number->format($departmentsPrograme->id) ?></td>
                    <td><?= $departmentsPrograme->has('department') ? $this->Html->link($departmentsPrograme->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsPrograme->department->id]) : '' ?></td>
                    <td><?= $departmentsPrograme->has('programe') ? $this->Html->link($departmentsPrograme->programe->programecode, ['controller' => 'Programes', 'action' => 'view', $departmentsPrograme->programe->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $departmentsPrograme->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $departmentsPrograme->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $departmentsPrograme->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsPrograme->id)]) ?>
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
