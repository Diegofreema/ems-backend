<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Liveclass $liveclass
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Liveclass'), ['action' => 'edit', $liveclass->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Liveclass'), ['action' => 'delete', $liveclass->id], ['confirm' => __('Are you sure you want to delete # {0}?', $liveclass->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Liveclasses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Liveclass'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="liveclasses view content">
            <h3><?= h($liveclass->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Meetinglink') ?></th>
                    <td><?= h($liveclass->meetinglink) ?></td>
                </tr>
                <tr>
                    <th><?= __('Teacher') ?></th>
                    <td><?= $liveclass->has('teacher') ? $this->Html->link($liveclass->teacher->firstname, ['controller' => 'Teachers', 'action' => 'view', $liveclass->teacher->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($liveclass->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datecreated') ?></th>
                    <td><?= h($liveclass->datecreated) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
