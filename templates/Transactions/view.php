<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Transaction'), ['action' => 'edit', $transaction->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Transaction'), ['action' => 'delete', $transaction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transaction->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Transactions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Transaction'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="transactions view content">
            <h3><?= h($transaction->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $transaction->has('student') ? $this->Html->link($transaction->student->id, ['controller' => 'Students', 'action' => 'view', $transaction->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= h($transaction->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Paystatus') ?></th>
                    <td><?= h($transaction->paystatus) ?></td>
                </tr>
                <tr>
                    <th><?= __('Payref') ?></th>
                    <td><?= h($transaction->payref) ?></td>
                </tr>
                <tr>
                    <th><?= __('Gresponse') ?></th>
                    <td><?= h($transaction->gresponse) ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $transaction->has('session') ? $this->Html->link($transaction->session->name, ['controller' => 'Sessions', 'action' => 'view', $transaction->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $transaction->has('fee') ? $this->Html->link($transaction->fee->name, ['controller' => 'Fees', 'action' => 'view', $transaction->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Invoice') ?></th>
                    <td><?= $transaction->has('invoice') ? $this->Html->link($transaction->invoice->id, ['controller' => 'Invoices', 'action' => 'view', $transaction->invoice->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Pgateway') ?></th>
                    <td><?= h($transaction->pgateway) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($transaction->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Transdate') ?></th>
                    <td><?= h($transaction->transdate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
