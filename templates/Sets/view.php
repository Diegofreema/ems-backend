<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Set $set
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Set'), ['action' => 'edit', $set->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Set'), ['action' => 'delete', $set->id], ['confirm' => __('Are you sure you want to delete # {0}?', $set->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Sets'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Set'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sets view content">
            <h3><?= h($set->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Year') ?></th>
                    <td><?= h($set->year) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($set->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
