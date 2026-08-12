<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Forum $forum
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Forum'), ['action' => 'edit', $forum->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Forum'), ['action' => 'delete', $forum->id], ['confirm' => __('Are you sure you want to delete # {0}?', $forum->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Forum'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Forum'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="forum view content">
            <h3><?= h($forum->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($forum->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $forum->has('user') ? $this->Html->link($forum->user->username, ['controller' => 'Users', 'action' => 'view', $forum->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Category') ?></th>
                    <td><?= $forum->has('category') ? $this->Html->link($forum->category->name, ['controller' => 'Categories', 'action' => 'view', $forum->category->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($forum->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($forum->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Views') ?></th>
                    <td><?= $this->Number->format($forum->views) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dateadded') ?></th>
                    <td><?= h($forum->dateadded) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Details') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($forum->details)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
