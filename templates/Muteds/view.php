<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Muted $muted
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Muted'), ['action' => 'edit', $muted->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Muted'), ['action' => 'delete', $muted->id], ['confirm' => __('Are you sure you want to delete # {0}?', $muted->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Muteds'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Muted'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="muteds view content">
            <h3><?= h($muted->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $muted->has('student') ? $this->Html->link($muted->student->regno, ['controller' => 'Students', 'action' => 'view', $muted->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($muted->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datemuted') ?></th>
                    <td><?= h($muted->datemuted) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
