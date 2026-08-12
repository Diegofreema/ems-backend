<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SubjectsTeacher $subjectsTeacher
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Subjects Teacher'), ['action' => 'edit', $subjectsTeacher->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Subjects Teacher'), ['action' => 'delete', $subjectsTeacher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjectsTeacher->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Subjects Teachers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Subjects Teacher'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subjectsTeachers view content">
            <h3><?= h($subjectsTeacher->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Teacher') ?></th>
                    <td><?= $subjectsTeacher->has('teacher') ? $this->Html->link($subjectsTeacher->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $subjectsTeacher->teacher->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $subjectsTeacher->has('subject') ? $this->Html->link($subjectsTeacher->subject->name, ['controller' => 'Subjects', 'action' => 'view', $subjectsTeacher->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($subjectsTeacher->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created Date') ?></th>
                    <td><?= h($subjectsTeacher->created_date) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
