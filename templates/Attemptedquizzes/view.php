<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attemptedquiz $attemptedquiz
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Attemptedquiz'), ['action' => 'edit', $attemptedquiz->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Attemptedquiz'), ['action' => 'delete', $attemptedquiz->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attemptedquiz->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Attemptedquizzes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Attemptedquiz'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="attemptedquizzes view content">
            <h3><?= h($attemptedquiz->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Quizquestion') ?></th>
                    <td><?= $attemptedquiz->has('quizquestion') ? $this->Html->link($attemptedquiz->quizquestion->id, ['controller' => 'Quizquestions', 'action' => 'view', $attemptedquiz->quizquestion->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $attemptedquiz->has('student') ? $this->Html->link($attemptedquiz->student->fname, ['controller' => 'Students', 'action' => 'view', $attemptedquiz->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Sanswer') ?></th>
                    <td><?= h($attemptedquiz->sanswer) ?></td>
                </tr>
                <tr>
                    <th><?= __('Correctans') ?></th>
                    <td><?= h($attemptedquiz->correctans) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($attemptedquiz->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Quizdate') ?></th>
                    <td><?= h($attemptedquiz->quizdate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
