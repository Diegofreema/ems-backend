<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsPrograme $departmentsPrograme
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Departments Programe'), ['action' => 'edit', $departmentsPrograme->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Departments Programe'), ['action' => 'delete', $departmentsPrograme->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsPrograme->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments Programes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Departments Programe'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsProgrames view content">
            <h3><?= h($departmentsPrograme->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $departmentsPrograme->has('department') ? $this->Html->link($departmentsPrograme->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsPrograme->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Programe') ?></th>
                    <td><?= $departmentsPrograme->has('programe') ? $this->Html->link($departmentsPrograme->programe->programecode, ['controller' => 'Programes', 'action' => 'view', $departmentsPrograme->programe->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($departmentsPrograme->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
