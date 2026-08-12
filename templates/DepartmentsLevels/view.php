<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsLevel $departmentsLevel
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Departments Level'), ['action' => 'edit', $departmentsLevel->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Departments Level'), ['action' => 'delete', $departmentsLevel->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsLevel->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Departments Level'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsLevels view content">
            <h3><?= h($departmentsLevel->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $departmentsLevel->has('department') ? $this->Html->link($departmentsLevel->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsLevel->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Level') ?></th>
                    <td><?= $departmentsLevel->has('level') ? $this->Html->link($departmentsLevel->level->name, ['controller' => 'Levels', 'action' => 'view', $departmentsLevel->level->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($departmentsLevel->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
