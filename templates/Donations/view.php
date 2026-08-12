<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Donation $donation
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Donation'), ['action' => 'edit', $donation->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Donation'), ['action' => 'delete', $donation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $donation->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Donations'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Donation'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="donations view content">
            <h3><?= h($donation->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Donator') ?></th>
                    <td><?= h($donation->donator) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($donation->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($donation->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($donation->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= h($donation->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Rrr') ?></th>
                    <td><?= h($donation->rrr) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($donation->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($donation->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Donationdate') ?></th>
                    <td><?= h($donation->donationdate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
