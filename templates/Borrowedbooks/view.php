<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Borrowedbook $borrowedbook
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Borrowedbook'), ['action' => 'edit', $borrowedbook->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Borrowedbook'), ['action' => 'delete', $borrowedbook->id], ['confirm' => __('Are you sure you want to delete # {0}?', $borrowedbook->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Borrowedbooks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Borrowedbook'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="borrowedbooks view content">
            <h3><?= h($borrowedbook->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $borrowedbook->has('student') ? $this->Html->link($borrowedbook->student->id, ['controller' => 'Students', 'action' => 'view', $borrowedbook->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Book') ?></th>
                    <td><?= $borrowedbook->has('book') ? $this->Html->link($borrowedbook->book->title, ['controller' => 'Books', 'action' => 'view', $borrowedbook->book->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Datetoreturn') ?></th>
                    <td><?= h($borrowedbook->datetoreturn) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($borrowedbook->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($borrowedbook->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date') ?></th>
                    <td><?= h($borrowedbook->date) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
