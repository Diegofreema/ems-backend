<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SponsorshipsStudent[]|\Cake\Collection\CollectionInterface $sponsorshipsStudents
 */
?>
<div class="sponsorshipsStudents index content">
    <?= $this->Html->link(__('New Sponsorships Student'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Sponsorships Students') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('sponsorship_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sponsorshipsStudents as $sponsorshipsStudent): ?>
                <tr>
                    <td><?= $this->Number->format($sponsorshipsStudent->id) ?></td>
                    <td><?= $sponsorshipsStudent->has('student') ? $this->Html->link($sponsorshipsStudent->student->fname, ['controller' => 'Students', 'action' => 'view', $sponsorshipsStudent->student->id]) : '' ?></td>
                    <td><?= $sponsorshipsStudent->has('sponsorship') ? $this->Html->link($sponsorshipsStudent->sponsorship->id, ['controller' => 'Sponsorships', 'action' => 'view', $sponsorshipsStudent->sponsorship->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $sponsorshipsStudent->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $sponsorshipsStudent->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sponsorshipsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sponsorshipsStudent->id)]) ?>
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
