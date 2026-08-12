<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SubjectsStudent $subjectsStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Subjects Student'), ['action' => 'edit', $subjectsStudent->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Subjects Student'), ['action' => 'delete', $subjectsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjectsStudent->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Subjects Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Subjects Student'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subjectsStudents view content">
            <h3><?= h($subjectsStudent->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $subjectsStudent->has('subject') ? $this->Html->link($subjectsStudent->subject->name, ['controller' => 'Subjects', 'action' => 'view', $subjectsStudent->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $subjectsStudent->has('student') ? $this->Html->link($subjectsStudent->student->id, ['controller' => 'Students', 'action' => 'view', $subjectsStudent->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($subjectsStudent->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
