<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsFee $departmentsFee
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Departments Fee'), ['action' => 'edit', $departmentsFee->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Departments Fee'), ['action' => 'delete', $departmentsFee->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsFee->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments Fees'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Departments Fee'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsFees view content">
            <h3><?= h($departmentsFee->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $departmentsFee->has('fee') ? $this->Html->link($departmentsFee->fee->name, ['controller' => 'Fees', 'action' => 'view', $departmentsFee->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $departmentsFee->has('department') ? $this->Html->link($departmentsFee->department->name, ['controller' => 'Departments', 'action' => 'view', $departmentsFee->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($departmentsFee->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
