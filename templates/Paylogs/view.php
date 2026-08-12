<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paylog $paylog
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Paylog'), ['action' => 'edit', $paylog->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Paylog'), ['action' => 'delete', $paylog->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paylog->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Paylogs'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Paylog'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="paylogs view content">
            <h3><?= h($paylog->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $paylog->has('student') ? $this->Html->link($paylog->student->fname, ['controller' => 'Students', 'action' => 'view', $paylog->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Responsecode') ?></th>
                    <td><?= h($paylog->responsecode) ?></td>
                </tr>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= h($paylog->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Paymethod') ?></th>
                    <td><?= h($paylog->paymethod) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($paylog->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tref') ?></th>
                    <td><?= $this->Number->format($paylog->tref) ?></td>
                </tr>
                <tr>
                    <th><?= __('Transdate') ?></th>
                    <td><?= h($paylog->transdate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
