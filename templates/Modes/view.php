<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mode $mode
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Mode'), ['action' => 'edit', $mode->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Mode'), ['action' => 'delete', $mode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mode->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Modes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Mode'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="modes view content">
            <h3><?= h($mode->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($mode->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($mode->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
