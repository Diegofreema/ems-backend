<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Constant $constant
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Constant'), ['action' => 'edit', $constant->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Constant'), ['action' => 'delete', $constant->id], ['confirm' => __('Are you sure you want to delete # {0}?', $constant->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Constants'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Constant'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="constants view content">
            <h3><?= h($constant->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($constant->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Value') ?></th>
                    <td><?= h($constant->value) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($constant->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
