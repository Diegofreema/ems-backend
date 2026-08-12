<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffmessage $staffmessage
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Staffmessage'), ['action' => 'edit', $staffmessage->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Staffmessage'), ['action' => 'delete', $staffmessage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $staffmessage->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Staffmessages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Staffmessage'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffmessages view content">
            <h3><?= h($staffmessage->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($staffmessage->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Message') ?></th>
                    <td><?= h($staffmessage->message) ?></td>
                </tr>
                <tr>
                    <th><?= __('Teacher') ?></th>
                    <td><?= $staffmessage->has('teacher') ? $this->Html->link($staffmessage->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $staffmessage->teacher->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $staffmessage->has('user') ? $this->Html->link($staffmessage->user->id, ['controller' => 'Users', 'action' => 'view', $staffmessage->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($staffmessage->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($staffmessage->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datecreated') ?></th>
                    <td><?= h($staffmessage->datecreated) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
