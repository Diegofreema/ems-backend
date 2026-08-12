<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Result $result
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Result'), ['action' => 'edit', $result->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Result'), ['action' => 'delete', $result->id], ['confirm' => __('Are you sure you want to delete # {0}?', $result->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Results'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Result'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="results view content">
            <h3><?= h($result->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $result->has('student') ? $this->Html->link($result->student->id, ['controller' => 'Students', 'action' => 'view', $result->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Faculty') ?></th>
                    <td><?= $result->has('faculty') ? $this->Html->link($result->faculty->name, ['controller' => 'Faculties', 'action' => 'view', $result->faculty->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $result->has('department') ? $this->Html->link($result->department->name, ['controller' => 'Departments', 'action' => 'view', $result->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $result->has('subject') ? $this->Html->link($result->subject->name, ['controller' => 'Subjects', 'action' => 'view', $result->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $result->has('semester') ? $this->Html->link($result->semester->name, ['controller' => 'Semesters', 'action' => 'view', $result->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $result->has('session') ? $this->Html->link($result->session->name, ['controller' => 'Sessions', 'action' => 'view', $result->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Grade') ?></th>
                    <td><?= h($result->grade) ?></td>
                </tr>
                <tr>
                    <th><?= __('Remark') ?></th>
                    <td><?= h($result->remark) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $result->has('user') ? $this->Html->link($result->user->id, ['controller' => 'Users', 'action' => 'view', $result->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Regno') ?></th>
                    <td><?= h($result->regno) ?></td>
                </tr>
                <tr>
                    <th><?= __('Level') ?></th>
                    <td><?= $result->has('level') ? $this->Html->link($result->level->name, ['controller' => 'Levels', 'action' => 'view', $result->level->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($result->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Score') ?></th>
                    <td><?= $this->Number->format($result->score) ?></td>
                </tr>
                <tr>
                    <th><?= __('Creditload') ?></th>
                    <td><?= $this->Number->format($result->creditload) ?></td>
                </tr>
                <tr>
                    <th><?= __('Uploaddate') ?></th>
                    <td><?= h($result->uploaddate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
