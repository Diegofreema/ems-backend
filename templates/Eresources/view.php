<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Eresource $eresource
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Eresource'), ['action' => 'edit', $eresource->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Eresource'), ['action' => 'delete', $eresource->id], ['confirm' => __('Are you sure you want to delete # {0}?', $eresource->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Eresources'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Eresource'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="eresources view content">
            <h3><?= h($eresource->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($eresource->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Pubdate') ?></th>
                    <td><?= h($eresource->pubdate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Isbn') ?></th>
                    <td><?= h($eresource->isbn) ?></td>
                </tr>
                <tr>
                    <th><?= __('Author') ?></th>
                    <td><?= h($eresource->author) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $eresource->has('department') ? $this->Html->link($eresource->department->name, ['controller' => 'Departments', 'action' => 'view', $eresource->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($eresource->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Viewcount') ?></th>
                    <td><?= $eresource->viewcount === null ? '' : $this->Number->format($eresource->viewcount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dateadded') ?></th>
                    <td><?= h($eresource->dateadded) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
