<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SparentsStudent[]|\Cake\Collection\CollectionInterface $sparentsStudents
 */
?>
<div class="sparentsStudents index content">
    <?= $this->Html->link(__('New Sparents Student'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Sparents Students') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('parent_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sparentsStudents as $sparentsStudent): ?>
                <tr>
                    <td><?= $this->Number->format($sparentsStudent->id) ?></td>
                    <td><?= $sparentsStudent->has('student') ? $this->Html->link($sparentsStudent->student->id, ['controller' => 'Students', 'action' => 'view', $sparentsStudent->student->id]) : '' ?></td>
                    <td><?= $sparentsStudent->has('parent_sparents_student') ? $this->Html->link($sparentsStudent->parent_sparents_student->id, ['controller' => 'SparentsStudents', 'action' => 'view', $sparentsStudent->parent_sparents_student->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $sparentsStudent->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $sparentsStudent->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sparentsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sparentsStudent->id)]) ?>
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
