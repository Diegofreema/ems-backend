<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SemestersSubject[]|\Cake\Collection\CollectionInterface $semestersSubjects
 */
?>
<div class="semestersSubjects index content">
    <?= $this->Html->link(__('New Semesters Subject'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Semesters Subjects') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('semester_id') ?></th>
                    <th><?= $this->Paginator->sort('subject_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($semestersSubjects as $semestersSubject): ?>
                <tr>
                    <td><?= $this->Number->format($semestersSubject->id) ?></td>
                    <td><?= $semestersSubject->has('semester') ? $this->Html->link($semestersSubject->semester->name, ['controller' => 'Semesters', 'action' => 'view', $semestersSubject->semester->id]) : '' ?></td>
                    <td><?= $semestersSubject->has('subject') ? $this->Html->link($semestersSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $semestersSubject->subject->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $semestersSubject->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $semestersSubject->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $semestersSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $semestersSubject->id)]) ?>
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
