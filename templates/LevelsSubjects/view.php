<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LevelsSubject $levelsSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Levels Subject'), ['action' => 'edit', $levelsSubject->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Levels Subject'), ['action' => 'delete', $levelsSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $levelsSubject->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Levels Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Levels Subject'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="levelsSubjects view content">
            <h3><?= h($levelsSubject->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $levelsSubject->has('subject') ? $this->Html->link($levelsSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $levelsSubject->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Level') ?></th>
                    <td><?= $levelsSubject->has('level') ? $this->Html->link($levelsSubject->level->name, ['controller' => 'Levels', 'action' => 'view', $levelsSubject->level->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($levelsSubject->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
