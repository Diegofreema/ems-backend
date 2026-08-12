<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Liveclass> $liveclasses
 */
?>
<div class="liveclasses index content">
    <?= $this->Html->link(__('New Liveclass'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Liveclasses') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('meetinglink') ?></th>
                    <th><?= $this->Paginator->sort('teacher_id') ?></th>
                    <th><?= $this->Paginator->sort('datecreated') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($liveclasses as $liveclass): ?>
                <tr>
                    <td><?= $this->Number->format($liveclass->id) ?></td>
                    <td><?= h($liveclass->meetinglink) ?></td>
                    <td><?= $liveclass->has('teacher') ? $this->Html->link($liveclass->teacher->firstname, ['controller' => 'Teachers', 'action' => 'view', $liveclass->teacher->id]) : '' ?></td>
                    <td><?= h($liveclass->datecreated) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $liveclass->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $liveclass->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $liveclass->id], ['confirm' => __('Are you sure you want to delete # {0}?', $liveclass->id)]) ?>
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
