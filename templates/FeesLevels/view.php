<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesLevel $feesLevel
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fees Level'), ['action' => 'edit', $feesLevel->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fees Level'), ['action' => 'delete', $feesLevel->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feesLevel->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fees Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fees Level'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesLevels view content">
            <h3><?= h($feesLevel->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $feesLevel->has('fee') ? $this->Html->link($feesLevel->fee->name, ['controller' => 'Fees', 'action' => 'view', $feesLevel->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Level') ?></th>
                    <td><?= $feesLevel->has('level') ? $this->Html->link($feesLevel->level->name, ['controller' => 'Levels', 'action' => 'view', $feesLevel->level->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($feesLevel->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
