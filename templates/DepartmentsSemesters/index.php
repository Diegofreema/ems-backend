<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsSemester[]|\Cake\Collection\CollectionInterface $departmentsSemesters
 */
?>
<div class="departmentsSemesters index content">
    <?= $this->Html->link(__('New Departments Semester'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Departments Semesters') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('semester_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departmentsSemesters as $departmentsSemester): ?>
                <tr>
                    <td><?= $this->Number->format($departmentsSemester->id) ?></td>
                    <td><?= $departmentsSemester->has('department') ? $this->Html->link($departmentsSemester->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsSemester->department->id]) : '' ?></td>
                    <td><?= $departmentsSemester->has('semester') ? $this->Html->link($departmentsSemester->semester->name, ['controller' => 'Semesters', 'action' => 'view', $departmentsSemester->semester->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $departmentsSemester->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $departmentsSemester->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $departmentsSemester->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsSemester->id)]) ?>
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
