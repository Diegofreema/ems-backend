<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SubjectsTeacher[]|\Cake\Collection\CollectionInterface $subjectsTeachers
 */
?>
<div class="subjectsTeachers index content">
    <?= $this->Html->link(__('New Subjects Teacher'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Subjects Teachers') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('teacher_id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th><?= $this->Paginator->sort('created_date') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjectsTeachers as $subjectsTeacher): ?>
                <tr>
                    <td><?= $this->Number->format($subjectsTeacher->id) ?></td>
                    <td><?= $subjectsTeacher->has('teacher') ? $this->Html->link($subjectsTeacher->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $subjectsTeacher->teacher->id]) : '' ?></td>
                    <td><?= $subjectsTeacher->has('subject') ? $this->Html->link($subjectsTeacher->subject->name, ['controller' => 'Subjects', 'action' => 'view', $subjectsTeacher->subject->id]) : '' ?></td>
                    <td><?= h($subjectsTeacher->created_date) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $subjectsTeacher->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $subjectsTeacher->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $subjectsTeacher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjectsTeacher->id)]) ?>
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
