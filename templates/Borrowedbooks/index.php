<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Borrowedbook[]|\Cake\Collection\CollectionInterface $borrowedbooks
 */
?>
<div class="borrowedbooks index content">
    <?= $this->Html->link(__('New Borrowedbook'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Borrowedbooks') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('book_id') ?></th>
                    <th><?= $this->Paginator->sort('date') ?></th>
                    <th><?= $this->Paginator->sort('datetoreturn') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowedbooks as $borrowedbook): ?>
                <tr>
                    <td><?= $this->Number->format($borrowedbook->id) ?></td>
                    <td><?= $borrowedbook->has('student') ? $this->Html->link($borrowedbook->student->id, ['controller' => 'Students', 'action' => 'view', $borrowedbook->student->id]) : '' ?></td>
                    <td><?= $borrowedbook->has('book') ? $this->Html->link($borrowedbook->book->title, ['controller' => 'Books', 'action' => 'view', $borrowedbook->book->id]) : '' ?></td>
                    <td><?= h($borrowedbook->date) ?></td>
                    <td><?= h($borrowedbook->datetoreturn) ?></td>
                    <td><?= h($borrowedbook->status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $borrowedbook->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $borrowedbook->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $borrowedbook->id], ['confirm' => __('Are you sure you want to delete # {0}?', $borrowedbook->id)]) ?>
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
