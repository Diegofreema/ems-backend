<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attemptedquiz[]|\Cake\Collection\CollectionInterface $attemptedquizzes
 */
?>
<div class="attemptedquizzes index content">
    <?= $this->Html->link(__('New Attemptedquiz'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Attemptedquizzes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('quizquestion_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('sanswer') ?></th>
                    <th><?= $this->Paginator->sort('correctans') ?></th>
                    <th><?= $this->Paginator->sort('quizdate') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attemptedquizzes as $attemptedquiz): ?>
                <tr>
                    <td><?= $this->Number->format($attemptedquiz->id) ?></td>
                    <td><?= $attemptedquiz->has('quizquestion') ? $this->Html->link($attemptedquiz->quizquestion->id, ['controller' => 'Quizquestions', 'action' => 'view', $attemptedquiz->quizquestion->id]) : '' ?></td>
                    <td><?= $attemptedquiz->has('student') ? $this->Html->link($attemptedquiz->student->fname, ['controller' => 'Students', 'action' => 'view', $attemptedquiz->student->id]) : '' ?></td>
                    <td><?= h($attemptedquiz->sanswer) ?></td>
                    <td><?= h($attemptedquiz->correctans) ?></td>
                    <td><?= h($attemptedquiz->quizdate) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $attemptedquiz->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $attemptedquiz->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $attemptedquiz->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attemptedquiz->id)]) ?>
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
