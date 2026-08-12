<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subcategory[]|\Cake\Collection\CollectionInterface $subcategory
 */
?>
<div class="subcategory index content">
    <?= $this->Html->link(__('New Subcategory'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Subcategory') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('s_c_id') ?></th>
                    <th><?= $this->Paginator->sort('category_id') ?></th>
                    <th><?= $this->Paginator->sort('subcategory_name') ?></th>
                    <th><?= $this->Paginator->sort('subcategory_status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subcategory as $subcategory): ?>
                <tr>
                    <td><?= $this->Number->format($subcategory->s_c_id) ?></td>
                    <td><?= $this->Number->format($subcategory->category_id) ?></td>
                    <td><?= h($subcategory->subcategory_name) ?></td>
                    <td><?= $this->Number->format($subcategory->subcategory_status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $subcategory->s_c_id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $subcategory->s_c_id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $subcategory->s_c_id], ['confirm' => __('Are you sure you want to delete # {0}?', $subcategory->s_c_id)]) ?>
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
