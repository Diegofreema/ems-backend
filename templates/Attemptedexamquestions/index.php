<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attemptedexamquestion[]|\Cake\Collection\CollectionInterface $attemptedexamquestions
 */
?>
<div class="attemptedexamquestions index content">
    <?= $this->Html->link(__('New Attemptedexamquestion'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Attemptedexamquestions') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('examquestion_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('sanswer') ?></th>
                    <th><?= $this->Paginator->sort('correctans') ?></th>
                    <th><?= $this->Paginator->sort('examdate') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attemptedexamquestions as $attemptedexamquestion): ?>
                <tr>
                    <td><?= $this->Number->format($attemptedexamquestion->id) ?></td>
                    <td><?= $attemptedexamquestion->has('examquestion') ? $this->Html->link($attemptedexamquestion->examquestion->id, ['controller' => 'Examquestions', 'action' => 'view', $attemptedexamquestion->examquestion->id]) : '' ?></td>
                    <td><?= $attemptedexamquestion->has('student') ? $this->Html->link($attemptedexamquestion->student->fname, ['controller' => 'Students', 'action' => 'view', $attemptedexamquestion->student->id]) : '' ?></td>
                    <td><?= h($attemptedexamquestion->sanswer) ?></td>
                    <td><?= h($attemptedexamquestion->correctans) ?></td>
                    <td><?= h($attemptedexamquestion->examdate) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $attemptedexamquestion->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $attemptedexamquestion->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $attemptedexamquestion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attemptedexamquestion->id)]) ?>
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
