<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsSubject $departmentsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Departments Subject'), ['action' => 'edit', $departmentsSubject->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Departments Subject'), ['action' => 'delete', $departmentsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsSubject->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Departments Subject'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsSubjects view content">
            <h3><?= h($departmentsSubject->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $departmentsSubject->has('department') ? $this->Html->link($departmentsSubject->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsSubject->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $departmentsSubject->has('subject') ? $this->Html->link($departmentsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $departmentsSubject->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($departmentsSubject->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
