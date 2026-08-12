<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Coursematerial $coursematerial
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Coursematerial'), ['action' => 'edit', $coursematerial->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Coursematerial'), ['action' => 'delete', $coursematerial->id], ['confirm' => __('Are you sure you want to delete # {0}?', $coursematerial->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Coursematerials'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Coursematerial'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="coursematerials view content">
            <h3><?= h($coursematerial->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $coursematerial->has('subject') ? $this->Html->link($coursematerial->subject->name, ['controller' => 'Subjects', 'action' => 'view', $coursematerial->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Teacher') ?></th>
                    <td><?= $coursematerial->has('teacher') ? $this->Html->link($coursematerial->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $coursematerial->teacher->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($coursematerial->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fileurl') ?></th>
                    <td><?= h($coursematerial->fileurl) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $coursematerial->has('department') ? $this->Html->link($coursematerial->department->name, ['controller' => 'Departments', 'action' => 'view', $coursematerial->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Comment') ?></th>
                    <td><?= h($coursematerial->comment) ?></td>
                </tr>
                <tr>
                    <th><?= __('Updatedon') ?></th>
                    <td><?= h($coursematerial->updatedon) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($coursematerial->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date Created') ?></th>
                    <td><?= h($coursematerial->date_created) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
