<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsProgramme $departmentsProgramme
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Departments Programme'), ['action' => 'edit', $departmentsProgramme->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Departments Programme'), ['action' => 'delete', $departmentsProgramme->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsProgramme->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments Programmes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Departments Programme'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsProgrammes view content">
            <h3><?= h($departmentsProgramme->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $departmentsProgramme->has('department') ? $this->Html->link($departmentsProgramme->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsProgramme->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Programme') ?></th>
                    <td><?= $departmentsProgramme->has('programme') ? $this->Html->link($departmentsProgramme->programme->programecode, ['controller' => 'Programmes', 'action' => 'view', $departmentsProgramme->programme->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($departmentsProgramme->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
