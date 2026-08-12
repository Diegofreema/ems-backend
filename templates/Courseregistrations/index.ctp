<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Courseregistration[]|\Cake\Collection\CollectionInterface $courseregistrations
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Courseregistration'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Sessions'), ['controller' => 'Sessions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Session'), ['controller' => 'Sessions', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Semesters'), ['controller' => 'Semesters', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Semester'), ['controller' => 'Semesters', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Levels'), ['controller' => 'Levels', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Level'), ['controller' => 'Levels', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Subjects'), ['controller' => 'Subjects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Subject'), ['controller' => 'Subjects', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="courseregistrations index large-9 medium-8 columns content">
    <h3><?= __('Courseregistrations') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('student_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('session_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('semester_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('level_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('date_created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courseregistrations as $courseregistration): ?>
            <tr>
                <td><?= $this->Number->format($courseregistration->id) ?></td>
                <td><?= $courseregistration->has('student') ? $this->Html->link($courseregistration->student->fname, ['controller' => 'Students', 'action' => 'view', $courseregistration->student->id]) : '' ?></td>
                <td><?= $courseregistration->has('session') ? $this->Html->link($courseregistration->session->name, ['controller' => 'Sessions', 'action' => 'view', $courseregistration->session->id]) : '' ?></td>
                <td><?= $courseregistration->has('semester') ? $this->Html->link($courseregistration->semester->name, ['controller' => 'Semesters', 'action' => 'view', $courseregistration->semester->id]) : '' ?></td>
                <td><?= $courseregistration->has('level') ? $this->Html->link($courseregistration->level->name, ['controller' => 'Levels', 'action' => 'view', $courseregistration->level->id]) : '' ?></td>
                <td><?= h($courseregistration->date_created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $courseregistration->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $courseregistration->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $courseregistration->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseregistration->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
