<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HostelroomsStudent[]|\Cake\Collection\CollectionInterface $hostelroomsStudents
 */
?>
<div class="hostelroomsStudents index content">
    <?= $this->Html->link(__('New Hostelrooms Student'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Hostelrooms Students') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('hostelroom_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hostelroomsStudents as $hostelroomsStudent): ?>
                <tr>
                    <td><?= $this->Number->format($hostelroomsStudent->id) ?></td>
                    <td><?= $hostelroomsStudent->has('hostelroom') ? $this->Html->link($hostelroomsStudent->hostelroom->id, ['controller' => 'Hostelrooms', 'action' => 'view', $hostelroomsStudent->hostelroom->id]) : '' ?></td>
                    <td><?= $hostelroomsStudent->has('student') ? $this->Html->link($hostelroomsStudent->student->id, ['controller' => 'Students', 'action' => 'view', $hostelroomsStudent->student->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $hostelroomsStudent->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $hostelroomsStudent->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $hostelroomsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $hostelroomsStudent->id)]) ?>
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
