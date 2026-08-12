<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Postcategory[]|\Cake\Collection\CollectionInterface $postcategories
 */
?>
<div class="postcategories index content">
    <?= $this->Html->link(__('New Postcategory'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Postcategories') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($postcategories as $postcategory): ?>
                <tr>
                    <td><?= $this->Number->format($postcategory->id) ?></td>
                    <td><?= h($postcategory->name) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $postcategory->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $postcategory->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $postcategory->id], ['confirm' => __('Are you sure you want to delete # {0}?', $postcategory->id)]) ?>
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
