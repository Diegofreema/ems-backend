<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quizquestion[]|\Cake\Collection\CollectionInterface $quizquestions
 */
?>
<div class="quizquestions index content">
    <?= $this->Html->link(__('New Quizquestion'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Quizquestions') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('quiz_id') ?></th>
                    <th><?= $this->Paginator->sort('question') ?></th>
                    <th><?= $this->Paginator->sort('op1') ?></th>
                    <th><?= $this->Paginator->sort('op2') ?></th>
                    <th><?= $this->Paginator->sort('op3') ?></th>
                    <th><?= $this->Paginator->sort('op4') ?></th>
                    <th><?= $this->Paginator->sort('correctans') ?></th>
                    <th><?= $this->Paginator->sort('mark') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizquestions as $quizquestion): ?>
                <tr>
                    <td><?= $this->Number->format($quizquestion->id) ?></td>
                    <td><?= $quizquestion->has('quiz') ? $this->Html->link($quizquestion->quiz->id, ['controller' => 'Quizzes', 'action' => 'view', $quizquestion->quiz->id]) : '' ?></td>
                    <td><?= h($quizquestion->question) ?></td>
                    <td><?= h($quizquestion->op1) ?></td>
                    <td><?= h($quizquestion->op2) ?></td>
                    <td><?= h($quizquestion->op3) ?></td>
                    <td><?= h($quizquestion->op4) ?></td>
                    <td><?= h($quizquestion->correctans) ?></td>
                    <td><?= $this->Number->format($quizquestion->mark) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $quizquestion->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $quizquestion->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $quizquestion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quizquestion->id)]) ?>
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
