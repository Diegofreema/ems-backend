<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Vote> $votes
 */
?>
<div class="votes index content">
    <?= $this->Html->link(__('New Vote'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Votes') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('candidate_id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('vote') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($votes as $vote): ?>
                <tr>
                    <td><?= $this->Number->format($vote->id) ?></td>
                    <td><?= $vote->has('candidate') ? $this->Html->link($vote->candidate->id, ['controller' => 'Candidates', 'action' => 'view', $vote->candidate->id]) : '' ?></td>
                    <td><?= $vote->has('student') ? $this->Html->link($vote->student->regno, ['controller' => 'Students', 'action' => 'view', $vote->student->id]) : '' ?></td>
                    <td><?= $this->Number->format($vote->vote) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $vote->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $vote->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $vote->id], ['confirm' => __('Are you sure you want to delete # {0}?', $vote->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
