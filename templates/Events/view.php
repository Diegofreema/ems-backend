<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Event $event
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Event'), ['action' => 'edit', $event->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Event'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Events'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Event'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="events view content">
            <h3><?= h($event->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Eventtitle') ?></th>
                    <td><?= h($event->eventtitle) ?></td>
                </tr>
                <tr>
                    <th><?= __('Details') ?></th>
                    <td><?= h($event->details) ?></td>
                </tr>
                <tr>
                    <th><?= __('Venue') ?></th>
                    <td><?= h($event->venue) ?></td>
                </tr>
                <tr>
                    <th><?= __('Eventdate') ?></th>
                    <td><?= h($event->eventdate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Eventtime') ?></th>
                    <td><?= h($event->eventtime) ?></td>
                </tr>
                <tr>
                    <th><?= __('Admin') ?></th>
                    <td><?= $event->has('admin') ? $this->Html->link($event->admin->id, ['controller' => 'Admins', 'action' => 'view', $event->admin->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($event->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Viewscount') ?></th>
                    <td><?= $this->Number->format($event->viewscount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datecreated') ?></th>
                    <td><?= h($event->datecreated) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
