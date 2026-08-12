<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Spending $spending
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Spending'), ['action' => 'edit', $spending->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Spending'), ['action' => 'delete', $spending->id], ['confirm' => __('Are you sure you want to delete # {0}?', $spending->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Spendings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Spending'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="spendings view content">
            <h3><?= h($spending->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= h($spending->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Description') ?></th>
                    <td><?= h($spending->description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($spending->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Datecreated') ?></th>
                    <td><?= h($spending->datecreated) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
