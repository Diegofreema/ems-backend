<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quiz[]|\Cake\Collection\CollectionInterface $quizzes
 */
?>
<div class="quizzes index content">
    <?= $this->Html->link(__('New Quiz'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Quizzes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('faculty_id') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('semester_id') ?></th>
                    <th><?= $this->Paginator->sort('session_id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th><?= $this->Paginator->sort('quizname') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?= $this->Number->format($quiz->id) ?></td>
                    <td><?= $quiz->has('faculty') ? $this->Html->link($quiz->faculty->name, ['controller' => 'Faculties', 'action' => 'view', $quiz->faculty->id]) : '' ?></td>
                    <td><?= $quiz->has('department') ? $this->Html->link($quiz->department->name, ['controller' => 'Departments', 'action' => 'view', $quiz->department->id]) : '' ?></td>
                    <td><?= $quiz->has('semester') ? $this->Html->link($quiz->semester->name, ['controller' => 'Semesters', 'action' => 'view', $quiz->semester->id]) : '' ?></td>
                    <td><?= $quiz->has('session') ? $this->Html->link($quiz->session->name, ['controller' => 'Sessions', 'action' => 'view', $quiz->session->id]) : '' ?></td>
                    <td><?= $quiz->has('subject') ? $this->Html->link($quiz->subject->name, ['controller' => 'Subjects', 'action' => 'view', $quiz->subject->id]) : '' ?></td>
                    <td><?= h($quiz->quizname) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $quiz->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $quiz->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $quiz->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quiz->id)]) ?>
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
