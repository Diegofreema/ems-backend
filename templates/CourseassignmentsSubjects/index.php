<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseassignmentsSubject[]|\Cake\Collection\CollectionInterface $courseassignmentsSubjects
 */
?>
<div class="courseassignmentsSubjects index content">
    <?= $this->Html->link(__('New Courseassignments Subject'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Courseassignments Subjects') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('courseassignment_id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courseassignmentsSubjects as $courseassignmentsSubject): ?>
                <tr>
                    <td><?= $this->Number->format($courseassignmentsSubject->id) ?></td>
                    <td><?= $courseassignmentsSubject->has('courseassignment') ? $this->Html->link($courseassignmentsSubject->courseassignment->id, ['controller' => 'Courseassignments', 'action' => 'view', $courseassignmentsSubject->courseassignment->id]) : '' ?></td>
                    <td><?= $courseassignmentsSubject->has('subject') ? $this->Html->link($courseassignmentsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $courseassignmentsSubject->subject->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $courseassignmentsSubject->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $courseassignmentsSubject->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $courseassignmentsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseassignmentsSubject->id)]) ?>
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
