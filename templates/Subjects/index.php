<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subject[]|\Cake\Collection\CollectionInterface $subjects
 */
?>
<div class="subjects index content">
    <?= $this->Html->link(__('New Subject'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Subjects') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('subjectcode') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('creditload') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('created_date') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('semester_id') ?></th>
                    <th><?= $this->Paginator->sort('level_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?= $this->Number->format($subject->id) ?></td>
                    <td><?= h($subject->name) ?></td>
                    <td><?= h($subject->subjectcode) ?></td>
                    <td><?= $this->Number->format($subject->department_id) ?></td>
                    <td><?= $this->Number->format($subject->creditload) ?></td>
                    <td><?= $subject->has('user') ? $this->Html->link($subject->user->username, ['controller' => 'Users', 'action' => 'view', $subject->user->id]) : '' ?></td>
                    <td><?= h($subject->created_date) ?></td>
                    <td><?= $this->Number->format($subject->status) ?></td>
                    <td><?= $this->Number->format($subject->semester_id) ?></td>
                    <td><?= $this->Number->format($subject->level_id) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $subject->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $subject->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $subject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subject->id)]) ?>
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
