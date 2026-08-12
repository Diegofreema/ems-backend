<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Feeallocation $feeallocation
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Feeallocation'), ['action' => 'edit', $feeallocation->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Feeallocation'), ['action' => 'delete', $feeallocation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feeallocation->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Feeallocations'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Feeallocation'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feeallocations view content">
            <h3><?= h($feeallocation->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $feeallocation->has('fee') ? $this->Html->link($feeallocation->fee->name, ['controller' => 'Fees', 'action' => 'view', $feeallocation->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $feeallocation->has('department') ? $this->Html->link($feeallocation->department->name, ['controller' => 'Departments', 'action' => 'view', $feeallocation->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Startdate') ?></th>
                    <td><?= h($feeallocation->startdate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Enddate') ?></th>
                    <td><?= h($feeallocation->enddate) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $feeallocation->has('user') ? $this->Html->link($feeallocation->user->id, ['controller' => 'Users', 'action' => 'view', $feeallocation->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($feeallocation->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
