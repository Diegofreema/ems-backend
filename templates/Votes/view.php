<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Vote $vote
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Vote'), ['action' => 'edit', $vote->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Vote'), ['action' => 'delete', $vote->id], ['confirm' => __('Are you sure you want to delete # {0}?', $vote->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Votes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Vote'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="votes view content">
            <h3><?= h($vote->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Candidate') ?></th>
                    <td><?= $vote->has('candidate') ? $this->Html->link($vote->candidate->id, ['controller' => 'Candidates', 'action' => 'view', $vote->candidate->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $vote->has('student') ? $this->Html->link($vote->student->regno, ['controller' => 'Students', 'action' => 'view', $vote->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($vote->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Vote') ?></th>
                    <td><?= $this->Number->format($vote->vote) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
