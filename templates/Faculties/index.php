<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Faculty[]|\Cake\Collection\CollectionInterface $faculties
 */
?>
<div class="faculties index content">
    <?= $this->Html->link(__('New Faculty'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Faculties') ?></h3>
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
                <?php foreach ($faculties as $faculty): ?>
                <tr>
                    <td><?= $this->Number->format($faculty->id) ?></td>
                    <td><?= h($faculty->name) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $faculty->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $faculty->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $faculty->id], ['confirm' => __('Are you sure you want to delete # {0}?', $faculty->id)]) ?>
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
