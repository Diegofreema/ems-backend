<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admisioncondition $admisioncondition
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Admisioncondition'), ['action' => 'edit', $admisioncondition->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Admisioncondition'), ['action' => 'delete', $admisioncondition->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admisioncondition->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Admisionconditions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Admisioncondition'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="admisionconditions view content">
            <h3><?= h($admisioncondition->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $admisioncondition->has('user') ? $this->Html->link($admisioncondition->user->id, ['controller' => 'Users', 'action' => 'view', $admisioncondition->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($admisioncondition->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lastupdate') ?></th>
                    <td><?= h($admisioncondition->lastupdate) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Conditiond') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($admisioncondition->conditiond)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
