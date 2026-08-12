<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Cafcredit> $cafcredit
 */
?>
<div class="cafcredit index content">
    <?= $this->Html->link(__('New Cafcredit'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Cafcredit') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('matricnum') ?></th>
                    <th><?= $this->Paginator->sort('amount') ?></th>
                    <th><?= $this->Paginator->sort('date1') ?></th>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cafcredit as $cafcredit): ?>
                <tr>
                    <td><?= h($cafcredit->matricnum) ?></td>
                    <td><?= $this->Number->format($cafcredit->amount) ?></td>
                    <td><?= h($cafcredit->date1) ?></td>
                    <td><?= $this->Number->format($cafcredit->id) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $cafcredit->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cafcredit->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cafcredit->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cafcredit->id)]) ?>
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
