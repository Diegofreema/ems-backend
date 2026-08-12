<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\DepartmentsFee> $departmentsFees
 */
?>
<div class="departmentsFees index content">
    <?= $this->Html->link(__('New Departments Fee'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Departments Fees') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('fee_id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departmentsFees as $departmentsFee): ?>
                <tr>
                    <td><?= $this->Number->format($departmentsFee->id) ?></td>
                    <td><?= $departmentsFee->has('fee') ? $this->Html->link($departmentsFee->fee->name, ['controller' => 'Fees', 'action' => 'view', $departmentsFee->fee->id]) : '' ?></td>
                    <td><?= $departmentsFee->has('department') ? $this->Html->link($departmentsFee->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsFee->department->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $departmentsFee->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $departmentsFee->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $departmentsFee->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsFee->id)]) ?>
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
