<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quizquestion $quizquestion
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Quizquestion'), ['action' => 'edit', $quizquestion->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Quizquestion'), ['action' => 'delete', $quizquestion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quizquestion->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Quizquestions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Quizquestion'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="quizquestions view content">
            <h3><?= h($quizquestion->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Quiz') ?></th>
                    <td><?= $quizquestion->has('quiz') ? $this->Html->link($quizquestion->quiz->id, ['controller' => 'Quizzes', 'action' => 'view', $quizquestion->quiz->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Question') ?></th>
                    <td><?= h($quizquestion->question) ?></td>
                </tr>
                <tr>
                    <th><?= __('Op1') ?></th>
                    <td><?= h($quizquestion->op1) ?></td>
                </tr>
                <tr>
                    <th><?= __('Op2') ?></th>
                    <td><?= h($quizquestion->op2) ?></td>
                </tr>
                <tr>
                    <th><?= __('Op3') ?></th>
                    <td><?= h($quizquestion->op3) ?></td>
                </tr>
                <tr>
                    <th><?= __('Op4') ?></th>
                    <td><?= h($quizquestion->op4) ?></td>
                </tr>
                <tr>
                    <th><?= __('Correctans') ?></th>
                    <td><?= h($quizquestion->correctans) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($quizquestion->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mark') ?></th>
                    <td><?= $this->Number->format($quizquestion->mark) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Attemptedquizzes') ?></h4>
                <?php if (!empty($quizquestion->attemptedquizzes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Quizquestion Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Sanswer') ?></th>
                            <th><?= __('Correctans') ?></th>
                            <th><?= __('Quizdate') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($quizquestion->attemptedquizzes as $attemptedquizzes) : ?>
                        <tr>
                            <td><?= h($attemptedquizzes->id) ?></td>
                            <td><?= h($attemptedquizzes->quizquestion_id) ?></td>
                            <td><?= h($attemptedquizzes->student_id) ?></td>
                            <td><?= h($attemptedquizzes->sanswer) ?></td>
                            <td><?= h($attemptedquizzes->correctans) ?></td>
                            <td><?= h($attemptedquizzes->quizdate) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Attemptedquizzes', 'action' => 'view', $attemptedquizzes->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Attemptedquizzes', 'action' => 'edit', $attemptedquizzes->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Attemptedquizzes', 'action' => 'delete', $attemptedquizzes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $attemptedquizzes->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
