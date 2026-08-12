<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Assignment> $assignments
 */
?>
<div class="assignments index content">
    <?= $this->Html->link(__('New Assignment'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Assignments') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('details') ?></th>
                    <th><?= $this->Paginator->sort('datecreated') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('session_id') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): ?>
                <tr>
                    <td><?= $assignment->has('subject') ? $this->Html->link($assignment->subject->name, ['controller' => 'Subjects', 'action' => 'view', $assignment->subject->id]) : '' ?></td>
                    <td><?= $assignment->has('student') ? $this->Html->link($assignment->student->regno, ['controller' => 'Students', 'action' => 'view', $assignment->student->id]) : '' ?></td>
                    <td><?= h($assignment->details) ?></td>
                    <td><?= h($assignment->datecreated) ?></td>
                    <td><?= h($assignment->status) ?></td>
                    <td><?= $assignment->has('session') ? $this->Html->link($assignment->session->name, ['controller' => 'Sessions', 'action' => 'view', $assignment->session->id]) : '' ?></td>
                    <td><?= $this->Number->format($assignment->id) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $assignment->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $assignment->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $assignment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $assignment->id)]) ?>
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
