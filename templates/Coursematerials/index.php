<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Coursematerial[]|\Cake\Collection\CollectionInterface $coursematerials
 */
?>
<div class="coursematerials index content">
    <?= $this->Html->link(__('New Coursematerial'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Coursematerials') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th><?= $this->Paginator->sort('teacher_id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('fileurl') ?></th>
                    <th><?= $this->Paginator->sort('date_created') ?></th>
                    <th><?= $this->Paginator->sort('department_id') ?></th>
                    <th><?= $this->Paginator->sort('comment') ?></th>
                    <th><?= $this->Paginator->sort('updatedon') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coursematerials as $coursematerial): ?>
                <tr>
                    <td><?= $this->Number->format($coursematerial->id) ?></td>
                    <td><?= $coursematerial->has('subject') ? $this->Html->link($coursematerial->subject->name, ['controller' => 'Subjects', 'action' => 'view', $coursematerial->subject->id]) : '' ?></td>
                    <td><?= $coursematerial->has('teacher') ? $this->Html->link($coursematerial->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $coursematerial->teacher->id]) : '' ?></td>
                    <td><?= h($coursematerial->title) ?></td>
                    <td><?= h($coursematerial->fileurl) ?></td>
                    <td><?= h($coursematerial->date_created) ?></td>
                    <td><?= $coursematerial->has('department') ? $this->Html->link($coursematerial->department->name, ['controller' => 'Departments', 'action' => 'view', $coursematerial->department->id]) : '' ?></td>
                    <td><?= h($coursematerial->comment) ?></td>
                    <td><?= h($coursematerial->updatedon) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $coursematerial->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $coursematerial->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $coursematerial->id], ['confirm' => __('Are you sure you want to delete # {0}?', $coursematerial->id)]) ?>
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
