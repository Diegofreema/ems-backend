<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cafcredit $cafcredit
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Cafcredit'), ['action' => 'edit', $cafcredit->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Cafcredit'), ['action' => 'delete', $cafcredit->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cafcredit->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Cafcredit'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Cafcredit'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="cafcredit view content">
            <h3><?= h($cafcredit->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Matricnum') ?></th>
                    <td><?= h($cafcredit->matricnum) ?></td>
                </tr>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= $this->Number->format($cafcredit->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($cafcredit->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date1') ?></th>
                    <td><?= h($cafcredit->date1) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
