<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Letter $letter
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Letter'), ['action' => 'edit', $letter->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Letter'), ['action' => 'delete', $letter->id], ['confirm' => __('Are you sure you want to delete # {0}?', $letter->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Letters'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Letter'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="letters view content">
            <h3><?= h($letter->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Mode') ?></th>
                    <td><?= $letter->has('mode') ? $this->Html->link($letter->mode->name, ['controller' => 'Modes', 'action' => 'view', $letter->mode->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($letter->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($letter->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datecreated') ?></th>
                    <td><?= h($letter->datecreated) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Letterbody') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($letter->letterbody)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
