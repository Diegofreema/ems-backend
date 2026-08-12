<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Courseregistration $courseregistration
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Courseregistration'), ['action' => 'edit', $courseregistration->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Courseregistration'), ['action' => 'delete', $courseregistration->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseregistration->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Courseregistrations'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Courseregistration'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="courseregistrations view content">
            <h3><?= h($courseregistration->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $courseregistration->has('student') ? $this->Html->link($courseregistration->student->id, ['controller' => 'Students', 'action' => 'view', $courseregistration->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $courseregistration->has('session') ? $this->Html->link($courseregistration->session->name, ['controller' => 'Sessions', 'action' => 'view', $courseregistration->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $courseregistration->has('semester') ? $this->Html->link($courseregistration->semester->name, ['controller' => 'Semesters', 'action' => 'view', $courseregistration->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Level') ?></th>
                    <td><?= $courseregistration->has('level') ? $this->Html->link($courseregistration->level->name, ['controller' => 'Levels', 'action' => 'view', $courseregistration->level->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($courseregistration->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date Created') ?></th>
                    <td><?= h($courseregistration->date_created) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Subjects') ?></h4>
                <?php if (!empty($courseregistration->subjects)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Subjectcode') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Creditload') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Created Date') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($courseregistration->subjects as $subjects) : ?>
                        <tr>
                            <td><?= h($subjects->id) ?></td>
                            <td><?= h($subjects->name) ?></td>
                            <td><?= h($subjects->subjectcode) ?></td>
                            <td><?= h($subjects->department_id) ?></td>
                            <td><?= h($subjects->creditload) ?></td>
                            <td><?= h($subjects->user_id) ?></td>
                            <td><?= h($subjects->created_date) ?></td>
                            <td><?= h($subjects->status) ?></td>
                            <td><?= h($subjects->semester_id) ?></td>
                            <td><?= h($subjects->level_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Subjects', 'action' => 'view', $subjects->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Subjects', 'action' => 'edit', $subjects->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Subjects', 'action' => 'delete', $subjects->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjects->id)]) ?>
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
