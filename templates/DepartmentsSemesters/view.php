<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsSemester $departmentsSemester
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Departments Semester'), ['action' => 'edit', $departmentsSemester->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Departments Semester'), ['action' => 'delete', $departmentsSemester->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsSemester->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments Semesters'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Departments Semester'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsSemesters view content">
            <h3><?= h($departmentsSemester->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $departmentsSemester->has('department') ? $this->Html->link($departmentsSemester->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsSemester->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $departmentsSemester->has('semester') ? $this->Html->link($departmentsSemester->semester->name, ['controller' => 'Semesters', 'action' => 'view', $departmentsSemester->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($departmentsSemester->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
