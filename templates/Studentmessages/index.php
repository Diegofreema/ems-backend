<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Studentmessage[]|\Cake\Collection\CollectionInterface $studentmessages
 */
?>
<div class="studentmessages index content">
    <?= $this->Html->link(__('New Studentmessage'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Studentmessages') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('messages') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('date_created') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('mfor') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentmessages as $studentmessage): ?>
                <tr>
                    <td><?= $this->Number->format($studentmessage->id) ?></td>
                    <td><?= h($studentmessage->title) ?></td>
                    <td><?= h($studentmessage->messages) ?></td>
                    <td><?= $studentmessage->has('student') ? $this->Html->link($studentmessage->student->id, ['controller' => 'Students', 'action' => 'view', $studentmessage->student->id]) : '' ?></td>
                    <td><?= $studentmessage->has('user') ? $this->Html->link($studentmessage->user->id, ['controller' => 'Users', 'action' => 'view', $studentmessage->user->id]) : '' ?></td>
                    <td><?= h($studentmessage->date_created) ?></td>
                    <td><?= h($studentmessage->status) ?></td>
                    <td><?= h($studentmessage->mfor) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $studentmessage->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $studentmessage->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $studentmessage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $studentmessage->id)]) ?>
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
