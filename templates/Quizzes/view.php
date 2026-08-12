<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Quiz $quiz
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Quiz'), ['action' => 'edit', $quiz->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Quiz'), ['action' => 'delete', $quiz->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quiz->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Quizzes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Quiz'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="quizzes view content">
            <h3><?= h($quiz->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Faculty') ?></th>
                    <td><?= $quiz->has('faculty') ? $this->Html->link($quiz->faculty->name, ['controller' => 'Faculties', 'action' => 'view', $quiz->faculty->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $quiz->has('department') ? $this->Html->link($quiz->department->name, ['controller' => 'Departments', 'action' => 'view', $quiz->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $quiz->has('semester') ? $this->Html->link($quiz->semester->name, ['controller' => 'Semesters', 'action' => 'view', $quiz->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $quiz->has('session') ? $this->Html->link($quiz->session->name, ['controller' => 'Sessions', 'action' => 'view', $quiz->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $quiz->has('subject') ? $this->Html->link($quiz->subject->name, ['controller' => 'Subjects', 'action' => 'view', $quiz->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Quizname') ?></th>
                    <td><?= h($quiz->quizname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($quiz->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Quizquestions') ?></h4>
                <?php if (!empty($quiz->quizquestions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Quiz Id') ?></th>
                            <th><?= __('Question') ?></th>
                            <th><?= __('Op1') ?></th>
                            <th><?= __('Op2') ?></th>
                            <th><?= __('Op3') ?></th>
                            <th><?= __('Op4') ?></th>
                            <th><?= __('Correctans') ?></th>
                            <th><?= __('Mark') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($quiz->quizquestions as $quizquestions) : ?>
                        <tr>
                            <td><?= h($quizquestions->id) ?></td>
                            <td><?= h($quizquestions->quiz_id) ?></td>
                            <td><?= h($quizquestions->question) ?></td>
                            <td><?= h($quizquestions->op1) ?></td>
                            <td><?= h($quizquestions->op2) ?></td>
                            <td><?= h($quizquestions->op3) ?></td>
                            <td><?= h($quizquestions->op4) ?></td>
                            <td><?= h($quizquestions->correctans) ?></td>
                            <td><?= h($quizquestions->mark) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Quizquestions', 'action' => 'view', $quizquestions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Quizquestions', 'action' => 'edit', $quizquestions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Quizquestions', 'action' => 'delete', $quizquestions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quizquestions->id)]) ?>
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
