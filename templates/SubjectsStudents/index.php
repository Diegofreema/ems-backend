<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SubjectsStudent[]|\Cake\Collection\CollectionInterface $subjectsStudents
 */
?>
<div class="subjectsStudents index content">
    <?= $this->Html->link(__('New Subjects Student'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Subjects Students') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjectsStudents as $subjectsStudent): ?>
                <tr>
                    <td><?= $this->Number->format($subjectsStudent->id) ?></td>
                    <td><?= $subjectsStudent->has('subject') ? $this->Html->link($subjectsStudent->subject->name, ['controller' => 'Subjects', 'action' => 'view', $subjectsStudent->subject->id]) : '' ?></td>
                    <td><?= $subjectsStudent->has('student') ? $this->Html->link($subjectsStudent->student->id, ['controller' => 'Students', 'action' => 'view', $subjectsStudent->student->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $subjectsStudent->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $subjectsStudent->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $subjectsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjectsStudent->id)]) ?>
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
