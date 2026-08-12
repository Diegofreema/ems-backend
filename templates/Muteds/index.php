<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muted[]|\Cake\Collection\CollectionInterface $muteds
 */
?>
<div class="muteds index content">
    <?= $this->Html->link(__('New Muted'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Muteds') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('datemuted') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($muteds as $muted): ?>
                <tr>
                    <td><?= $this->Number->format($muted->id) ?></td>
                    <td><?= $muted->has('student') ? $this->Html->link($muted->student->regno, ['controller' => 'Students', 'action' => 'view', $muted->student->id]) : '' ?></td>
                    <td><?= h($muted->datemuted) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $muted->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $muted->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $muted->id], ['confirm' => __('Are you sure you want to delete # {0}?', $muted->id)]) ?>
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
