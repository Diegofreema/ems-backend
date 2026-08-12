<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesStudent[]|\Cake\Collection\CollectionInterface $feesStudents
 */
?>
<div class="feesStudents index content">
    <?= $this->Html->link(__('New Fees Student'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Fees Students') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('fee_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feesStudents as $feesStudent): ?>
                <tr>
                    <td><?= $this->Number->format($feesStudent->id) ?></td>
                    <td><?= $feesStudent->has('fee') ? $this->Html->link($feesStudent->fee->name, ['controller' => 'Fees', 'action' => 'view', $feesStudent->fee->id]) : '' ?></td>
                    <td><?= $feesStudent->has('student') ? $this->Html->link($feesStudent->student->id, ['controller' => 'Students', 'action' => 'view', $feesStudent->student->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $feesStudent->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $feesStudent->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $feesStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feesStudent->id)]) ?>
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
