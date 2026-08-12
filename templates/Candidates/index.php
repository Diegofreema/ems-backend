<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Candidate> $candidates
 */
?>
<div class="candidates index content">
    <?= $this->Html->link(__('New Candidate'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Candidates') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('position_id') ?></th>
                    <th><?= $this->Paginator->sort('session_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $candidate): ?>
                <tr>
                    <td><?= $this->Number->format($candidate->id) ?></td>
                    <td><?= $candidate->has('student') ? $this->Html->link($candidate->student->regno, ['controller' => 'Students', 'action' => 'view', $candidate->student->id]) : '' ?></td>
                    <td><?= $candidate->has('position') ? $this->Html->link($candidate->position->name, ['controller' => 'Positions', 'action' => 'view', $candidate->position->id]) : '' ?></td>
                    <td><?= $candidate->has('session') ? $this->Html->link($candidate->session->name, ['controller' => 'Sessions', 'action' => 'view', $candidate->session->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $candidate->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $candidate->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $candidate->id], ['confirm' => __('Are you sure you want to delete # {0}?', $candidate->id)]) ?>
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
