<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Exam $exam
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Exam'), ['action' => 'edit', $exam->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Exam'), ['action' => 'delete', $exam->id], ['confirm' => __('Are you sure you want to delete # {0}?', $exam->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Exams'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Exam'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="exams view content">
            <h3><?= h($exam->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $exam->has('semester') ? $this->Html->link($exam->semester->name, ['controller' => 'Semesters', 'action' => 'view', $exam->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $exam->has('session') ? $this->Html->link($exam->session->name, ['controller' => 'Sessions', 'action' => 'view', $exam->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Examname') ?></th>
                    <td><?= h($exam->examname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Examdate') ?></th>
                    <td><?= h($exam->examdate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Examtime') ?></th>
                    <td><?= h($exam->examtime) ?></td>
                </tr>
                <tr>
                    <th><?= __('Admin') ?></th>
                    <td><?= $exam->has('admin') ? $this->Html->link($exam->admin->id, ['controller' => 'Admins', 'action' => 'view', $exam->admin->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($exam->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dateadded') ?></th>
                    <td><?= h($exam->dateadded) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
