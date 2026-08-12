<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsSubject[]|\Cake\Collection\CollectionInterface $departmentsSubjects
 */
?>
<div class="departmentsSubjects index content">
    <?= $this->Html->link(__('New Departments Subject'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Departments Subjects') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departmentsSubjects as $departmentsSubject): ?>
                <tr>
                    <td><?= $this->Number->format($departmentsSubject->id) ?></td>
                    <td><?= $departmentsSubject->has('department') ? $this->Html->link($departmentsSubject->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsSubject->department->id]) : '' ?></td>
                    <td><?= $departmentsSubject->has('subject') ? $this->Html->link($departmentsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $departmentsSubject->subject->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $departmentsSubject->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $departmentsSubject->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $departmentsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsSubject->id)]) ?>
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
