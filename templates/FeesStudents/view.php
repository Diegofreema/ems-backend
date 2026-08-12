<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesStudent $feesStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fees Student'), ['action' => 'edit', $feesStudent->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fees Student'), ['action' => 'delete', $feesStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feesStudent->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fees Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fees Student'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesStudents view content">
            <h3><?= h($feesStudent->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $feesStudent->has('fee') ? $this->Html->link($feesStudent->fee->name, ['controller' => 'Fees', 'action' => 'view', $feesStudent->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $feesStudent->has('student') ? $this->Html->link($feesStudent->student->id, ['controller' => 'Students', 'action' => 'view', $feesStudent->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($feesStudent->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
