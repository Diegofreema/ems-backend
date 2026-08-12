<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Payslip $payslip
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Payslip'), ['action' => 'edit', $payslip->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Payslip'), ['action' => 'delete', $payslip->id], ['confirm' => __('Are you sure you want to delete # {0}?', $payslip->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Payslips'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Payslip'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="payslips view content">
            <h3><?= h($payslip->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Teacher') ?></th>
                    <td><?= $payslip->has('teacher') ? $this->Html->link($payslip->teacher->id, ['controller' => 'Teachers', 'action' => 'view', $payslip->teacher->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Formonth') ?></th>
                    <td><?= h($payslip->formonth) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($payslip->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deduction') ?></th>
                    <td><?= $this->Number->format($payslip->deduction) ?></td>
                </tr>
                <tr>
                    <th><?= __('Grosspay') ?></th>
                    <td><?= $this->Number->format($payslip->grosspay) ?></td>
                </tr>
                <tr>
                    <th><?= __('Netpay') ?></th>
                    <td><?= $this->Number->format($payslip->netpay) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dategenerated') ?></th>
                    <td><?= h($payslip->dategenerated) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
