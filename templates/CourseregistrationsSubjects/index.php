<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseregistrationsSubject[]|\Cake\Collection\CollectionInterface $courseregistrationsSubjects
 */
?>
<div class="courseregistrationsSubjects index content">
    <?= $this->Html->link(__('New Courseregistrations Subject'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Courseregistrations Subjects') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('courseregistration_id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courseregistrationsSubjects as $courseregistrationsSubject): ?>
                <tr>
                    <td><?= $this->Number->format($courseregistrationsSubject->id) ?></td>
                    <td><?= $courseregistrationsSubject->has('courseregistration') ? $this->Html->link($courseregistrationsSubject->courseregistration->id, ['controller' => 'Courseregistrations', 'action' => 'view', $courseregistrationsSubject->courseregistration->id]) : '' ?></td>
                    <td><?= $courseregistrationsSubject->has('subject') ? $this->Html->link($courseregistrationsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $courseregistrationsSubject->subject->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $courseregistrationsSubject->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $courseregistrationsSubject->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $courseregistrationsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseregistrationsSubject->id)]) ?>
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
