<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Approvedresult $approvedresult
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Approvedresult'), ['action' => 'edit', $approvedresult->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Approvedresult'), ['action' => 'delete', $approvedresult->id], ['confirm' => __('Are you sure you want to delete # {0}?', $approvedresult->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Approvedresults'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Approvedresult'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="approvedresults view content">
            <h3><?= h($approvedresult->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $approvedresult->has('session') ? $this->Html->link($approvedresult->session->name, ['controller' => 'Sessions', 'action' => 'view', $approvedresult->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $approvedresult->has('semester') ? $this->Html->link($approvedresult->semester->name, ['controller' => 'Semesters', 'action' => 'view', $approvedresult->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($approvedresult->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Admin') ?></th>
                    <td><?= $approvedresult->has('admin') ? $this->Html->link($approvedresult->admin->id, ['controller' => 'Admins', 'action' => 'view', $approvedresult->admin->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($approvedresult->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
