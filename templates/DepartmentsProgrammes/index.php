<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsProgramme[]|\Cake\Collection\CollectionInterface $departmentsProgrammes
 */
?>
<div class="departmentsProgrammes index content">
    <?= $this->Html->link(__('New Departments Programme'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Departments Programmes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('programme_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departmentsProgrammes as $departmentsProgramme): ?>
                <tr>
                    <td><?= $this->Number->format($departmentsProgramme->id) ?></td>
                    <td><?= $departmentsProgramme->has('department') ? $this->Html->link($departmentsProgramme->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsProgramme->department->id]) : '' ?></td>
                    <td><?= $departmentsProgramme->has('programme') ? $this->Html->link($departmentsProgramme->programme->programecode, ['controller' => 'Programmes', 'action' => 'view', $departmentsProgramme->programme->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $departmentsProgramme->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $departmentsProgramme->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $departmentsProgramme->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsProgramme->id)]) ?>
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
