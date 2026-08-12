<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseregistrationsSubject $courseregistrationsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Courseregistrations Subject'), ['action' => 'edit', $courseregistrationsSubject->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Courseregistrations Subject'), ['action' => 'delete', $courseregistrationsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseregistrationsSubject->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Courseregistrations Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Courseregistrations Subject'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="courseregistrationsSubjects view content">
            <h3><?= h($courseregistrationsSubject->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Courseregistration') ?></th>
                    <td><?= $courseregistrationsSubject->has('courseregistration') ? $this->Html->link($courseregistrationsSubject->courseregistration->id, ['controller' => 'Courseregistrations', 'action' => 'view', $courseregistrationsSubject->courseregistration->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $courseregistrationsSubject->has('subject') ? $this->Html->link($courseregistrationsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $courseregistrationsSubject->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($courseregistrationsSubject->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
