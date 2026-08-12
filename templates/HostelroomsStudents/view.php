<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HostelroomsStudent $hostelroomsStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Hostelrooms Student'), ['action' => 'edit', $hostelroomsStudent->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Hostelrooms Student'), ['action' => 'delete', $hostelroomsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $hostelroomsStudent->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Hostelrooms Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Hostelrooms Student'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="hostelroomsStudents view content">
            <h3><?= h($hostelroomsStudent->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Hostelroom') ?></th>
                    <td><?= $hostelroomsStudent->has('hostelroom') ? $this->Html->link($hostelroomsStudent->hostelroom->id, ['controller' => 'Hostelrooms', 'action' => 'view', $hostelroomsStudent->hostelroom->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $hostelroomsStudent->has('student') ? $this->Html->link($hostelroomsStudent->student->id, ['controller' => 'Students', 'action' => 'view', $hostelroomsStudent->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($hostelroomsStudent->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
