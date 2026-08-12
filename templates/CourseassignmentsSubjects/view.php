<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseassignmentsSubject $courseassignmentsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Courseassignments Subject'), ['action' => 'edit', $courseassignmentsSubject->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Courseassignments Subject'), ['action' => 'delete', $courseassignmentsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseassignmentsSubject->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Courseassignments Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Courseassignments Subject'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="courseassignmentsSubjects view content">
            <h3><?= h($courseassignmentsSubject->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Courseassignment') ?></th>
                    <td><?= $courseassignmentsSubject->has('courseassignment') ? $this->Html->link($courseassignmentsSubject->courseassignment->id, ['controller' => 'Courseassignments', 'action' => 'view', $courseassignmentsSubject->courseassignment->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $courseassignmentsSubject->has('subject') ? $this->Html->link($courseassignmentsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $courseassignmentsSubject->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($courseassignmentsSubject->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
