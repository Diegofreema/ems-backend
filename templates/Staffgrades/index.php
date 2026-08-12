<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffgrade[]|\Cake\Collection\CollectionInterface $staffgrades
 */
?>
<div class="staffgrades index content">
    <?= $this->Html->link(__('New Staffgrade'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Staffgrades') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('basicsalary') ?></th>
                    <th><?= $this->Paginator->sort('tax') ?></th>
                    <th><?= $this->Paginator->sort('deduction') ?></th>
                    <th><?= $this->Paginator->sort('allowance') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffgrades as $staffgrade): ?>
                <tr>
                    <td><?= $this->Number->format($staffgrade->id) ?></td>
                    <td><?= h($staffgrade->name) ?></td>
                    <td><?= $this->Number->format($staffgrade->basicsalary) ?></td>
                    <td><?= $this->Number->format($staffgrade->tax) ?></td>
                    <td><?= $this->Number->format($staffgrade->deduction) ?></td>
                    <td><?= $this->Number->format($staffgrade->allowance) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $staffgrade->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $staffgrade->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $staffgrade->id], ['confirm' => __('Are you sure you want to delete # {0}?', $staffgrade->id)]) ?>
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
