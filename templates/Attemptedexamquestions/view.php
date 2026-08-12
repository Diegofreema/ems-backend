<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attemptedexamquestion $attemptedexamquestion
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Attemptedexamquestion'), ['action' => 'edit', $attemptedexamquestion->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Attemptedexamquestion'), ['action' => 'delete', $attemptedexamquestion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attemptedexamquestion->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Attemptedexamquestions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Attemptedexamquestion'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="attemptedexamquestions view content">
            <h3><?= h($attemptedexamquestion->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Examquestion') ?></th>
                    <td><?= $attemptedexamquestion->has('examquestion') ? $this->Html->link($attemptedexamquestion->examquestion->id, ['controller' => 'Examquestions', 'action' => 'view', $attemptedexamquestion->examquestion->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $attemptedexamquestion->has('student') ? $this->Html->link($attemptedexamquestion->student->fname, ['controller' => 'Students', 'action' => 'view', $attemptedexamquestion->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Sanswer') ?></th>
                    <td><?= h($attemptedexamquestion->sanswer) ?></td>
                </tr>
                <tr>
                    <th><?= __('Correctans') ?></th>
                    <td><?= h($attemptedexamquestion->correctans) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($attemptedexamquestion->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Examdate') ?></th>
                    <td><?= h($attemptedexamquestion->examdate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
