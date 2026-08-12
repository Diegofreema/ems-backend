<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Studentmessage $studentmessage
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Studentmessage'), ['action' => 'edit', $studentmessage->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Studentmessage'), ['action' => 'delete', $studentmessage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $studentmessage->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Studentmessages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Studentmessage'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="studentmessages view content">
            <h3><?= h($studentmessage->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($studentmessage->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Messages') ?></th>
                    <td><?= h($studentmessage->messages) ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $studentmessage->has('student') ? $this->Html->link($studentmessage->student->id, ['controller' => 'Students', 'action' => 'view', $studentmessage->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $studentmessage->has('user') ? $this->Html->link($studentmessage->user->id, ['controller' => 'Users', 'action' => 'view', $studentmessage->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($studentmessage->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mfor') ?></th>
                    <td><?= h($studentmessage->mfor) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($studentmessage->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date Created') ?></th>
                    <td><?= h($studentmessage->date_created) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
